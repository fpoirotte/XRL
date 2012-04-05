<?php

/**
 * \brief
 *      A simple XML-RPC client.
 */
class XRL_Client
{
    /// The remote XML-RPC server's base URL.
    protected $_baseURL;

    /// A DateTimeZone object representing the server's timezone.
    protected $_timezone;

    /// A stream context to use when querying the server.
    protected $_context;

    /// Encoder to use to produce XML-RPC requests.
    protected $_encoder;

    /// Decoder to use to parse XML-RPC responses.
    protected $_decoder;

    /// Callable used to fetch the response.
    protected $_fetcher;

    protected $_requestCls = 'XRL_Request';

    protected $_defaultEncoderCls   = 'XRL_Encoder';

    protected $_defaultDecoderCls   = 'XRL_Decoder';

    /**
     * Create a new XML-RPC client.
     *
     * \param string $baseURL
     *      Base URL for the XML-RPC server,
     *      eg. "http://www.example.com/xmlrpc/".
     *
     * \param string $timezone
     *      (optional) The name of the timezone the remote server
     *      is in (eg. "Europe/Paris"). This parameter is used
     *      to represent dates and times using the proper timezone
     *      before sending them to the server.
     *      If omitted, the client's current timezone is used.
     *
     * \param resource $context
     *      (optional) A PHP stream context to use
     *      when querying the remote XML-RPC server.
     *
     * \note
     *      See http://php.net/manual/en/timezones.php for a list
     *      of valid timezone names supported by PHP.
     *
     * \note
     *      See http://php.net/manual/en/stream.contexts.php
     *      for more information about PHP stream contexts.
     *
     * \throw InvalidArgumentException
     *      The given timezone or context is invalid.
     */
    public function __construct(
                                $baseURL,
                                $timezone   = NULL,
                                $context    = NULL,
        XRL_EncoderInterface    $encoder    = NULL,
        XRL_DecoderInterface    $decoder    = NULL
    )
    {
        if ($timezone === NULL)
            $timezone = date_default_timezone_get();
        if ($context === NULL)
            $context = stream_context_get_default();
        if ($encoder === NULL) {
            $cls        = $this->_defaultEncoderCls;
            $encoder    = new $cls();
        }
        if ($decoder === NULL) {
            $cls        = $this->_defaultDecoderCls;
            $decoder    = new $cls();
        }

        if (!is_resource($context))
            throw new InvalidArgumentException('Invalid context');

        $this->_baseURL = $baseURL;
        try {
            $this->_timezone = new DateTimeZone($timezone);
        }
        catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode());
        }

        $this->_context = $context;
        $this->_encoder = $encoder;
        $this->_decoder = $decoder;
        $this->_fetcher = 'file_get_contents';
    }

    /**
     * A magic method that forwards all method calls
     * to the remote XML-RPC server and returns
     * that server's response on success or throws
     * an exception on failure.
     *
     * \param string $method
     *      The remote procedure to call.
     *
     * \param array $args
     *      A list of arguments to pass to the remote
     *      procedure.
     *
     * \retval mixed
     *      The remote server's response, as a native
     *      type (string, int, boolean, float or
     *      DateTime object).
     *
     * \throw XRL_Exception
     *      Raised in case the remote server's response
     *      indicates some kind of error. You may use
     *      this exception's getCode() and getMessage()
     *      methods to find out more about the error.
     *
     * \throw @TODO: decide on what exception must be raised here.
     *      Raised when this client wasn't able to query
     *      the remote server (such as when no connection
     *      could be established to it).
     */
    public function __call($method, $args)
    {
        $requestCls = $this->_requestCls;
        $request    = new $requestCls($method, $args);
        $xml        = $this->_encoder->encodeRequest($request);
        $options    = array(
            'http' => array(
                'method'    => 'POST',
                'content'   => $xml,
                'header'    => 'Content-Type: text/xml',
            ),
        );
        stream_context_set_option($this->_context, $options);

        $data = call_user_func(
            $this->_fetcher,
            $this->_baseURL,
            FALSE,
            $this->_context
        );
        $result = $this->_decoder->decodeResponse($data);
        return $result;
    }
}

