<?php
/*
 * This file is part of XRL, a simple XML-RPC Library for PHP.
 *
 * Copyright (c) 2012, XRL Team. All rights reserved.
 * XRL is licensed under the 3-clause BSD License.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fpoirotte\XRL;

/// \example client.php

/**
 * \brief
 *      A simple XML-RPC client.
 *
 * To call a remote XML procedure, create a new instance
 * of this class (pass the server's URL to the constructor)
 * and then simply call the procedure as if it was a method
 * of the object returned:
 *
 * \code
 *      $client = new \\fpoirotte\\XRL\\Client("http://xmlrpc.example.com/");
 *      // This calls the remote procedure "foo"
 *      // and prints the result of that call.
 *      var_dump($client->foo(42));
 * \endcode
 *
 * In case the remote procedure's name is not a valid
 * PHP identifier, you may still call it using the
 * curly braces notation:
 *
 * \code
 *      // Calls the remote procedure named "foo.bar.baz".
 *      $client->{"foo.bar.baz"}(42);
 * \endcode
 *
 * \see
 *      The example in \ref client.php contains a complete example of a working
 *      XML-RPC client for use with XRL's example server (\ref server.php).
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Client
{
    /// The remote XML-RPC server's base URL.
    protected $baseURL;

    /// A stream context to use when querying the server.
    protected $context;

    /// Encoder for the request.
    protected $encoder;

    /// Decoder for the response.
    protected $decoder;

    /**
     * Create a new XML-RPC client.
     *
     * \param string $baseURL
     *      Base URL for the XML-RPC server,
     *      eg. "http://www.example.com/xmlrpc/".
     *
     * \param fpoirotte::XRL::EncoderInterface $encoder
     *      (optional) Encoder to use for requests.
     *      If omitted, an encoder that accepts native PHP types,
     *      does not use indentation, but uses the \<string\> tags
     *      is automatically created using the machine's timezone.
     *
     * \param fpoirotte::XRL::DecoderInterface $decoder
     *      (optional) Decoder to use for responses.
     *      If omitted, a decoder that performs XML validation and
     *      converts values to native PHP types is automatically
     *      created using the machine's timezone.
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
        \fpoirotte\XRL\EncoderInterface $encoder = null,
        \fpoirotte\XRL\DecoderInterface $decoder = null,
        array $options = array()
    ) {
        if ($encoder === null) {
            $encoder = new \fpoirotte\XRL\NativeEncoder(
                new \fpoirotte\XRL\Encoder(null, false, true)
            );
        }

        if ($decoder === null) {
            $decoder = new \fpoirotte\XRL\NativeDecoder(
                new \fpoirotte\XRL\Decoder(null, true)
            );
        }

        $this->baseURL      = $baseURL;
        $this->options      = $options;
        $this->encoder      = $encoder;
        $this->decoder      = $decoder;
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
     * \throw fpoirotte::XRL::Exception
     *      Raised in case the remote server's response
     *      indicates some kind of error. You may use
     *      this exception's getCode() and getMessage()
     *      methods to find out more about the error.
     *
     * \throw RuntimeException
     *      Raised when this client wasn't able to query
     *      the remote server (such as when no connection
     *      could be established to it).
     */
    public function __call($method, array $args)
    {
        $newArgs    = array_map('\\fpoirotte\\XRL\\NativeEncoder::convert', $args);
        $request    = new \fpoirotte\XRL\Request($method, $newArgs);
        $xml        = $this->encoder->encodeRequest($request);

        $headers    = array(
            'Content-Type: text/xml',
            'User-Agent: XRL/' . \fpoirotte\XRL\CLI::getVersion(),
        );

        $options    = array(
            'http' => array(
                'method'        => 'POST',
                'content'       => $xml,
                'header'        => $headers,
            ),
        );

        $context = stream_context_create(array_merge_recursive($this->options, $options));
        libxml_set_streams_context($context);
        return $this->decoder->decodeResponse($this->baseURL);
    }
}
