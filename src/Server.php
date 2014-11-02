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

/// \example server.php

/**
 * \brief
 *      A simple XML-RPC server.
 *
 * This class uses dynamic properties to manager
 * XML-RPC procedures:
 * \code
 *      // This registers the procedure "foo"
 *      // on this XML-RPC server. The function
 *      // "bar" will be called to handle calls
 *      // to "foo".
 *      $server->foo = 'bar';
 *
 *      // This returns the callable used to handle
 *      // calls to "foo", wrapped in an object
 *      // implementing the "\\fpoirotte\\XRL\\CallableInterface"
 *      // interface.
 *      $foo = $server->foo;
 *
 *      // This tests whether the "foo" procedure
 *      // has been registered on this server.
 *      if (isset($server->foo)) {
 *          ...
 *      }
 *
 *      // This unregisters the "foo" procedure
 *      // from this XML-RPC server.
 *      unset($server->foo);
 * \endcode
 *
 * You may also count how many XML-RPC procedures
 * are currently registered on this server:
 * \code
 *      $nbProcedures = count($server);
 * \endcode
 *
 * Last but not least, you may also iterate over
 * this server's registered XML-RPC procedures:
 * \code
 *      foreach ($server as $procedure) {
 *          ...
 *      }
 * \endcode
 *
 * \see
 *      The example in \ref server.php contains a complete example of a working
 *      XML-RPC server which may be queried using the corresponding client
 *      (\ref client.php) or XRL's command-line query tool.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Server extends \fpoirotte\XRL\FactoryRegistry implements \Countable, \IteratorAggregate
{
    /// Registered "procedures".
    protected $funcs;

    /**
     * Create a new XML-RPC server.
     *
     * \param string|DateTimeZone $timezone
     *      (optional) The name of the timezone the remote server
     *      is in (eg. "Europe/Paris"). This parameter is used
     *      to represent dates and times using the proper timezone
     *      before sending them to the server.
     *      If omitted, the client's current timezone is used.
     *
     * \note
     *      See http://php.net/manual/en/timezones.php for a list
     *      of valid timezone names supported by PHP.
     *
     * \throw InvalidArgumentException
     *      The given timezone is invalid.
     */
    public function __construct($timezone = null)
    {
        if ($timezone === null) {
            $timezone = @date_default_timezone_get();
        }

        if (!is_object($timezone) || !($timezone instanceof \DateTimeZone)) {
            try {
                $timezone = new \DateTimeZone($timezone);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException($e->getMessage(), $e->getCode());
            }
        }

        $this->funcs        = array();
        $this->interfaces   = array(
            'fpoirotte\\xrl\\encoderfactoryinterface'   =>
                new \fpoirotte\XRL\CompactEncoderFactory($timezone),

            'fpoirotte\\xrl\\decoderfactoryinterface'   =>
                new \fpoirotte\XRL\ValidatingDecoderFactory($timezone),

            'fpoirotte\\xrl\\callablefactoryinterface'  =>
                new \fpoirotte\XRL\CallableFactory(),

            'fpoirotte\\xrl\\responsefactoryinterface'  =>
                new \fpoirotte\XRL\ResponseFactory(),
        );
    }

    /**
     * Register a new procedure with this XML-RPC server.
     *
     * \param string $func
     *      A valid name for the procedure.
     *
     * \param mixed $callback
     *      Any valid PHP callback.
     *
     * \note
     *      See the "Payload format" section at
     *      http://xmlrpc.scripting.com/spec.html
     *      for information on valid procedure names.
     *
     * \note
     *      Several syntaxes can be used to refer to a PHP callback, see
     *      http://php.net/language.pseudo-types.php#language.types.callback
     *      for the full list of supported constructs.
     */
    public function __set($func, $callback)
    {
        $factory    = $this['\\fpoirotte\\XRL\\CallableFactoryInterface'];
        $callable   = $factory->fromPHP($callback);
        assert($callable instanceof \fpoirotte\XRL\CallableInterface);
        $this->funcs[$func] = $callable;
    }

    /**
     * Return a procedure previously registered
     * with this XML-RPC server.
     *
     * \param string $func
     *      The name of the registered XML-RPC procedure
     *      to return.
     *
     * \retval mixed
     *      The callable responsible for the XML-RPC
     *      procedure registered with the given name,
     *      as an object implementing the
     *      fpoirotte::XRL::CallableInterface interface,
     *      or \c null if the given name does not refer
     *      to an XML-RPC procedure known to this server.
     *
     * \note
     *      In case the given procedure has not been
     *      registered, a PHP notice will be issued.
     */
    public function __get($func)
    {
        return $this->funcs[$func];
    }

    /**
     * Test whether a procedure has been registered
     * with the given name on this server.
     *
     * \param string $func
     *      Name of the procedure whose existence
     *      must be verified.
     *
     * \retval bool
     *      \c true if the procedure exists,
     *      \c false otherwise.
     */
    public function __isset($func)
    {
        return isset($this->funcs[$func]);
    }

    /**
     * Unregister a procedure.
     *
     * \param string $func
     *      The name of the procedure to unregister.
     *
     * \note
     *      No warning will be emitted if the given
     *      procedure has not been registered
     *      on this XML-RPC server.
     */
    public function __unset($func)
    {
        unset($this->funcs[$func]);
    }

    /**
     * Return the number of XML-RPC procedures
     * currently registered on this server.
     *
     * \retval int
     *      Number of currently registered
     *      procedures on this server.
     */
    public function count()
    {
        return count($this->funcs);
    }

    /**
     * Get an iterator over this server's
     * registered XML-RPC procedures.
     *
     * \retval ArrayIterator
     *      An iterator over this server's
     *      registered procedures.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->funcs);
    }

    /**
     * Handles an XML-RPC request and returns a response
     * for that request.
     *
     * \param string $data
     *      (optional) An XML-RPC request to process,
     *      as serialized XML. If omitted, this method
     *      will try to retrieve the request directly
     *      from the POST data sent to this PHP script.
     *
     * \retval fpoirotte::XRL::ResponseInterface
     *      The response for that request. This response
     *      may indicate either success or failure of the
     *      Remote Procedure Call 
     */
    public function handle($data = null)
    {
        if ($data === null) {
            $data = file_get_contents('php://input');
        }

        $factory    = $this['\\fpoirotte\\XRL\\EncoderFactoryInterface'];
        $encoder    = $factory->createEncoder();
        assert($encoder instanceof \fpoirotte\XRL\EncoderInterface);

        $factory    = $this['\\fpoirotte\\XRL\\DecoderFactoryInterface'];
        $decoder    = $factory->createDecoder();
        assert($decoder instanceof \fpoirotte\XRL\DecoderInterface);

        try {
            $request    = $decoder->decodeRequest($data);
            $procedure  = $request->getProcedure();

            if (!isset($this->funcs[$procedure])) {
                throw new \BadFunctionCallException(
                    "No such procedure ($procedure)"
                );
            }

            $callable   = $this->funcs[$procedure];
            $result     = $callable->invokeArgs($request->getParams());
            $response   = $encoder->encodeResponse($result);
        } catch (\Exception $result) {
            $response   = $encoder->encodeError($result);
        }

        $factory = $this['\\fpoirotte\\XRL\\ResponseFactoryInterface'];
        $returnValue = $factory->createResponse($response);
        assert($returnValue instanceof \fpoirotte\XRL\ResponseInterface);
        return $returnValue;
    }
}
