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

/**
 * \brief
 *      A class that adds various capabilities
 *      to an XML-RPC server.
 */
class CapableServer
{
    /// Original XML-RPC server.
    protected $server;

    /// Whitelist of XML-RPC methods to announce.
    protected $whitelist;

    /**
     * Wrap an XML-RPC server to add capabilities support.
     *
     * \param fpoirotte::XRL::Server $server
     *      Server to wrap.
     *
     * \param array $whitelist
     *      (optional) Whitelist of methods that can
     *      be announced by the newly upgraded server.
     *      Pass an empty array to disable announces.
     *      By default, all methods are announced.
     */
    protected function __construct(\fpoirotte\XRL\Server $server, array $whitelist = null)
    {
        $this->server       = $server;
        $this->whitelist    = $whitelist;
    }

    /**
     * Turn a regular XML-RPC server into one
     * that supports various XML-RPC capabilities.
     *
     * \param fpoirotte::XRL::Server $server
     *      An XML-RPC to upgrade.
     *
     * \param array $whitelist
     *      (optional) Whitelist of methods that can
     *      be announced by the newly upgraded server.
     *      Pass an empty array to disable announces.
     *      By default, all methods are announced.
     *
     * \retval fpoirotte::XRL::Server
     *      The upgraded server.
     */
    public static function enable(\fpoirotte\XRL\Server $server, array $whitelist = null)
    {
        $wrapper = new static($server, $whitelist);
        $server->expose($wrapper, 'system');
        return $server;
    }

    /**
     * Extract parameter and return type information
     * from a documentation comment.
     *
     * \param string $doc
     *      Documentation comment the types
     *      will be extracted from.
     *
     * \retval array
     *      Array with type information.
     *      The "retval" key contains information about the return type,
     *      while the array in the "params" key contains information
     *      about each parameter's type (indexed by its name).
     */
    protected static function extractTypes($doc)
    {
        $doc    = trim(substr($doc, 3, -2), " \t\r\n*");
        $doc    = str_replace(array("\r\n", "\r"), "\n", $doc);
        $lines  = explode("\n", $doc . "\n");

        $tag    = null;
        $tags   = array(
            'params'    => array(),
            'retval'    => null,
        );
        $buffer = '';

        foreach ($lines as $line) {
            $line = trim($line, " \r\n\t*");

            if ($tag !== null && $line === '') {
                switch ($tag) {
                    case 'param':
                        $type = (string) substr($buffer, 0, strcspn($buffer, " \r\n\t"));
                        $buffer = ltrim(substr($buffer, strcspn($buffer, " \r\n\t")));
                        if (strncmp($buffer, '$', 1)) {
                            break;
                        }

                        $name = (string) substr($buffer, 1, strcspn($buffer, " \r\n\t") - 1);
                        $tags['params'][$name] = $type;
                        break;

                    case 'retval':
                        $type = (string) substr($buffer, 0, strcspn($buffer, " \r\n\t"));
                        $tags['retval'] = $type;
                        break;
                }

                $tag    = null;
                $buffer = '';
                continue;
            }

            if ($tag === null) {
                // \command or @command.
                if (!strncmp($line, '\\', 1) || !strncmp($line, '@', 1)) {
                    $tag    = (string) substr($line, 1, strcspn($line, " \r\n\t") - 1);
                    $buffer = ltrim(substr($line, strcspn($line, " \r\n\t")));
                }
            } else {
                // Continuation of previous paragraph.
                $buffer .= "\n$line";
            }
        }
        return $tags;
    }

    /**
     * Return XML-RPC type that comes the closest
     * to some type used in documentation comments.
     *
     * \param string $type
     *      A type used in a documentation comment.
     *
     * \retval string
     *      The closest XML-RPC, or \c null if none
     *      could be derived.
     */
    protected static function adaptType($type)
    {
        switch ($type) {
            case 'integer':
                return 'int';

            case 'float':
                return 'double';

            case 'null':
                return 'nil';

            case 'DateTime':
                return 'dateTime.iso8601';

            case 'boolean':
                return 'bool';

            case 'int':
            case 'double':
            case 'string':
            case 'bool':
            case 'array':
                return $type;
        }
        return null;
    }

    /**
     * Get the server's capabilities.
     *
     * \retval array
     *      Array of capabilities supported by this server.
     *      Each entry contains two keys:
     *      - specURL (URL to that capability's specification)
     *      - specVersion (version of the capability)
     */
    public function getCapabilities()
    {
        return array(
            'xmlrpc' => array(
                'specUrl'       => 'http://www.xmlrpc.com/spec',
                'specVersion'   => 1,
            ),

            'introspect' => array(
                'specUrl'       => 'http://xmlrpc-c.sourceforge.net/xmlrpc-c/introspection.html',
                'specVersion'   => 1,
            ),

            'faults_interop' => array(
                'specUrl'       => 'http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php',
                'specVersion'   => 20010516,
            ),
        );
    }

    /**
     * List the methods supported by the server.
     *
     * \retval array
     *      Array with the names of available methods.
     */
    public function listMethods()
    {
        $methods = array_keys($this->server->getIterator()->getArrayCopy());
        if ($this->whitelist !== null) {
            $methods = array_intersect($methods, $this->whitelist);
        }
        return $methods;
    }

    /**
     * Return possible signatures for a method.
     *
     * \param string $method
     *      Name of the method.
     *
     * \retval array
     *      An array containing:
     *      - The return type of the method as its first value
     *      - The types for the method's parameters in the following values
     */
    public function methodSignature($method)
    {
        if (!is_string($method) || !isset($this->server[$method])) {
            throw new \InvalidArgumentException('Invalid method');
        }

        $reflector  = $this->server[$method]->getReflector();
        $doc        = $reflector->getDocComment();
        if ($doc === false) {
            return 'undef';
        }

        $tags       = static::extractTypes($doc);
        $returnType = static::adaptType($tags['retval']);
        if ($returnType === null) {
            return 'undef';
        }

        $params     = array();
        foreach ($reflector->getParameters() as $param) {
            if (!isset($tags['params'][$param->getName()])) {
                return 'undef';
            }
            $type       = static::adaptType($tags['params'][$param->getName()]);
            if ($type === null) {
                return 'undef';
            }
            $params[]   = $type;
        }

        return array(array_merge(array($returnType), $params));
    }

    /**
     * Get help about a procedure.
     *
     * \param string $method
     *      Name of the procedure.
     *
     * \retval string
     *      Human readable help message for the given procedure.
     */
    public function methodHelp($method)
    {
        if (!is_string($method) || !isset($this->server[$method])) {
            throw new \InvalidArgumentException('Invalid method');
        }

        $reflector  = $this->server[$method]->getReflector();
        $doc        = $reflector->getDocComment();
        if ($doc === false) {
            return '';
        }

        // Remove comment delimiters.
        $doc    = substr($doc, 2, -2);

        // Normalize line endings.
        $doc    = str_replace(array("\r\n", "\r"), "\n", $doc);

        // Trim leading/trailing whitespace and '*' for every line.
        $help   = array_map(
            function ($l) {
                return trim(trim($l), '*');
            },
            explode("\n", $doc)
        );

        // Count number of empty columns on non-empty lines
        // before the actual start of the text.
        $cols = min(
            array_map(
                function ($l) {
                    return strspn($l, " \t");
                },
                array_filter($help, 'strlen')
            )
        );

        // Remove those columns from the result.
        $help = array_map(
            function ($l) use ($cols) {
                return (string) substr($l, $cols);
            },
            $help
        );

        // Produce the final output.
        return implode("\n", $help);
    }

    /**
     * Perform several calls to XML-RPC methods
     * in a single go.
     *
     * \param array $requests
     *      Array of requests, each described as a struct
     *      with the following information:
     *      - "methodName": name of the method to call
     *      - "params": array of parameters for the method
     *
     * \retval array
     *      Array of responses, one for each request,
     *      in the same order.
     *      Each response may be either a fault or an array
     *      with a single element (the call's result).
     *
     * \note
     *      Recursive calls to system.multicall are forbidden.
     */
    public function multicall(array $requests)
    {
        $responses = array();
        foreach ($requests as $request) {
            try {
                if (!is_array($request)) {
                    throw new \BadFunctionCallException('Expected struct');
                }
                if (!isset($request['methodName'])) {
                    throw new \BadFunctionCallException('Missing methodName');
                }
                if (!isset($request['params'])) {
                    throw new \BadFunctionCallException('Missing params');
                }
                if ($request['methodName'] === 'system.multicall') {
                    throw new \BadFunctionCallException('Recursive call');
                }

                $result = $this->server->call($request['methodName'], $request['params']);
                // Results are wrapped in an array to make it possible
                // to distinguish faults from regular structs.
                $responses[] = array($result);
            } catch (\Exception $error) {
                $responses[] = $error;
            }
        }
        return $responses;
    }
}
