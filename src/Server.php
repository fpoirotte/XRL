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
class Server implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /// Registered "procedures".
    protected $XRLFunctions;

    /// Encoder for the request.
    protected $XRLEncoder;

    /// Decoder for the response.
    protected $XRLDecoder;


    /**
     * Create a new XML-RPC server.
     *
     * \param fpoirotte::XRL::EncoderInterface $encoder
     *      (optional) Encoder to use to build responses.
     *      If omitted, an encoder that accepts native PHP types,
     *      does not use indentation, but uses the \<string\> tags
     *      is automatically created using the machine's timezone.
     *
     * \param fpoirotte::XRL::DecoderInterface $decoder
     *      (optional) Decoder to use to parse responses.
     *      If omitted, a decoder that performs XML validation and
     *      converts values to native PHP types is automatically
     *      created using the machine's timezone.
     *
     * \throw InvalidArgumentException
     *      The given timezone is invalid.
     */
    public function __construct(
        \fpoirotte\XRL\EncoderInterface $encoder = null,
        \fpoirotte\XRL\DecoderInterface $decoder = null
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

        $this->XRLEncoder   = $encoder;
        $this->XRLDecoder   = $decoder;
        $this->XRLFunctions = array();
    }

    /**
     * Register a new procedure with this XML-RPC server.
     *
     * \param string $func
     *      A valid name for the procedure.
     *      Names starting with the string "XRL" (case-insensitive)
     *      are reserved.
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
        $this->XRLFunctions[$func] = new \fpoirotte\XRL\CallableObject($callback);
    }

    /**
     * \copydoc fpoirotte::XRL::Server::__set()
     *
     * This method is an alias for fpoirotte::XRL::Server::__set().
     */
    public function offsetSet($func, $callback)
    {
        $this->XRLFunctions[$func] = new \fpoirotte\XRL\CallableObject($callback);
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
        return $this->XRLFunctions[$func];
    }

    /**
     * \copydoc fpoirotte::XRL::Server::__get()
     *
     * This method is an alias for fpoirotte::XRL::Server::__get().
     */
    public function offsetGet($func)
    {
        return $this->XRLFunctions[$func];
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
        return isset($this->XRLFunctions[$func]);
    }

    /**
     * \copydoc fpoirotte::XRL::Server::__isset()
     *
     * This method is an alias for fpoirotte::XRL::Server::__isset().
     */
    public function offsetExists($func)
    {
        return isset($this->XRLFunctions[$func]);
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
        unset($this->XRLFunctions[$func]);
    }

    /**
     * \copydoc fpoirotte::XRL::Server::__unset()
     *
     * This method is an alias for fpoirotte::XRL::Server::__unset().
     */
    public function offsetUnset($func)
    {
        unset($this->XRLFunctions[$func]);
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
        return count($this->XRLFunctions);
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
        return new \ArrayIterator($this->XRLFunctions);
    }

    /**
     * Expose the public methods of a class or object,
     * with an optional prefix.
     *
     */
    public function expose($other, $prefix = '')
    {
        if (!is_string($other) && !is_object($other)) {
            throw new \InvalidArgumentException('Invalid adoption');
        }

        if (!is_string($prefix)) {
            throw new \InvalidArgumentException('Invalid prefix');
        }

        $prefix = rtrim($prefix, '.');
        if ($prefix !== '') {
            $prefix .= '.';
        }

        if (is_object($other)) {
            // An object was passed.
            $class = get_class($other);
            foreach (get_class_methods($class) as $method) {
                // Only adopt public methods of the object,
                // excluding the constructor and static methods.
                // To also register static methods, call this method
                // a second time with get_class($other).
                $reflector = new \ReflectionMethod($class, $method);
                if ($reflector->isPublic() && !$reflector->isConstructor() &&
                    !$reflector->isStatic()) {
                    $this[$prefix . $method] = array($other, $method);
                }
            }
        } else {
            // A class was passed.
            foreach (get_class_methods($other) as $method) {
                // Only adopt methods which are both public and static.
                $reflector = new \ReflectionMethod($other, $method);
                if ($reflector->isPublic() && $reflector->isStatic()) {
                    $this[$prefix . $method] = array($other, $method);
                }
            }
        }
    }

    /**
     * Handle an XML-RPC request and return a response for it.
     *
     * \param string $URI
     *      (optional) URI to the XML-RPC request to process,
     *      If omitted, this method will try to retrieve the request
     *      directly from the data POST'ed to this script.
     *
     * \retval fpoirotte::XRL::ResponseInterface
     *      The response for that request. This response
     *      may indicate either success or failure of the
     *      Remote Procedure Call.
     *
     * \note
     *      Use the "data://" wrapper to pass the serialized
     *      request as raw data.
     *
     * \see
     *      See http://php.net/wrappers.data.php for information
     *      on how to use the "data://" wrapper.
     */
    public function handle($URI = null)
    {
        if ($URI === null) {
            $URI = 'php://input';
        }

        try {
            $request    = $this->XRLDecoder->decodeRequest($URI);
            $procedure  = $request->getProcedure();
            // Necessary to keep references.
            $params     = $request->getParams();

            $result     = $this->call($procedure, $params);
            $response   = $this->XRLEncoder->encodeResponse($result);
        } catch (\Exception $result) {
            $response   = $this->XRLEncoder->encodeError($result);
        }

        return new \fpoirotte\XRL\Response($response);
    }

    /**
     * Call an XML-RPC procedure.
     *
     * \param string $procedure
     *      Name of the procedure to call.
     *
     * \param array $params
     *      Parameters for that procedure.
     *
     * \retval mixed
     *      The procedure's return value.
     */
    public function call($procedure, array $params)
    {
        if (!is_string($procedure)) {
            throw new \BadFunctionCallException('Expected a string');
        }

        if (!isset($this->XRLFunctions[$procedure])) {
            throw new \BadFunctionCallException(
                "No such procedure ($procedure)"
            );
        }

        $callable = $this->XRLFunctions[$procedure];
        return $callable->invokeArgs($params);
    }
}
