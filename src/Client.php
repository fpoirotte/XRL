<?php
/**
 * \file
 *
 * Copyright (c) 2012, XRL Team
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace fpoirotte\XRL;

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
 * Complete example (located in example/client.php):
 * \include client.php
 */
class Client extends \fpoirotte\XRL\FactoryRegistry
{
    /// The remote XML-RPC server's base URL.
    protected $baseURL;

    /// A stream context to use when querying the server.
    protected $context;

    /// Callable used to fetch the response.
    protected $fetcher;

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
    public function __construct($baseURL, $timezone = null, $context = null)
    {
        if ($timezone === null) {
            $timezone = @date_default_timezone_get();
        }
        if ($context === null) {
            $context = stream_context_get_default();
        }

        if (!is_resource($context)) {
            throw new \InvalidArgumentException('Invalid context');
        }

        $this->baseURL = $baseURL;
        try {
            $timezone = new \DateTimeZone($timezone);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException($e->getMessage(), $e->getCode());
        }

        $this->context      = $context;
        $this->fetcher      = 'file_get_contents';
        $this->interfaces   = array(
            'fpoirotte\\xrl\\encoderfactoryinterface'   =>
                new \fpoirotte\XRL\CompactEncoderFactory($timezone),

            'fpoirotte\\xrl\\decoderfactoryinterface'   =>
                new \fpoirotte\XRL\ValidatingDecoderFactory($timezone),

            'fpoirotte\\xrl\\requestfactoryinterface'   =>
                new \fpoirotte\XRL\RequestFactory(),
        );
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
        $factory    = $this['fpoirotte\\XRL\\RequestFactoryInterface'];
        $request    = $factory->createRequest($method, $args);
        assert($request instanceof \fpoirotte\XRL\RequestInterface);

        $factory    = $this['fpoirotte\\XRL\\EncoderFactoryInterface'];
        $encoder    = $factory->createEncoder();
        assert($encoder instanceof \fpoirotte\XRL\EncoderInterface);

        $factory    = $this['fpoirotte\\XRL\\DecoderFactoryInterface'];
        $decoder    = $factory->createDecoder();
        assert($decoder instanceof \fpoirotte\XRL\DecoderInterface);

        $xml        = $encoder->encodeRequest($request);
        $options    = array(
            'http' => array(
                'method'    => 'POST',
                'content'   => $xml,
                'header'    => 'Content-Type: text/xml',
            ),
        );
        stream_context_set_option($this->context, $options);

        $data = @call_user_func(
            $this->fetcher,
            $this->baseURL,
            false,
            $this->context
        );
        if ($data === false) {
            throw new \RuntimeException('The server could not be queried');
        }

        $result = $decoder->decodeResponse($data);
        return $result;
    }
}
