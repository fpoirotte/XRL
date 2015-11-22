<?php
/*
 * This file is part of XRL, a simple XML-RPC Library for PHP.
 *
 * Copyright (c) 2015, XRL Team. All rights reserved.
 * XRL is licensed under the 3-clause BSD License.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fpoirotte\XRL\tests;

class BadClass
{
    public function undocumented()
    {
    }

    /**
     * This method has an incomplete documentation.
     * More precisely, it lacks documentation about
     * the "$bar" parameter.
     *
     * \param int $foo
     *      Your regular integer (eg. 42).
     *
     * \retval null
     *      This method does not return any value.
     */
    public function incomplete($foo, $bar)
    {
    }

    /**
     * This method's uses a complex return value
     * which is incompatible with XML-RPC types.
     *
     * \retval ComplexType
     *      A complex return value.
     */
    public function badReturnType()
    {
    }

    /**
     * This method's uses a complex parameter
     * which is incompatible with XML-RPC types.
     *
     * \param ComplexType $foo
     *      A complex parameter.
     */
    public function badParamType($foo)
    {
    }

    /**
     * This method uses invalid markup for its documentation
     * (unterminated "\param" command).
     *
     * \param $foo
     *      Some foo.
     *
     * \retval null
     *      This method does not return any value.
     */
    public function invalidMarkup($foo)
    {
    }
}

class CapableServer extends \PHPUnit_Framework_TestCase
{
    protected $server;
    protected $capableServer;
    protected $callableObject;

    public function setUp()
    {
        $capableServer = null;
        $this->server = $this->getMockBuilder('\\fpoirotte\\XRL\\Server')->getMock();
        $this->server
            ->expects($this->once())
            ->method('expose')
            ->with(
                $this->isInstanceOf('\\fpoirotte\\XRL\\CapableServer'),
                $this->equalTo('system')
            )->will($this->returnCallback(
                function ($wrapper, $base) use (&$capableServer) {
                    $capableServer = $wrapper;
                }
            ));
        \fpoirotte\XRL\CapableServer::enable($this->server, array('bar'));
        $this->capableServer = $capableServer;

        $this->callableObject = $this->getMockBuilder('\\fpoirotte\\XRL\\CallableInterface')->getMock();
    }

    /**
     * @covers      \fpoirotte\XRL\CapableServer::__construct
     * @covers      \fpoirotte\XRL\CapableServer::enable
     */
    public function testEnable()
    {
        $server = $this->getMockBuilder('\\fpoirotte\\XRL\\Server')->getMock();
        \fpoirotte\XRL\CapableServer::enable($server);
    }

    /**
     * @covers      \fpoirotte\XRL\CapableServer::getCapabilities
     */
    public function testGetCapabilities()
    {
        $res = $this->capableServer->getCapabilities();
        $this->assertCount(3, $res);
    }

    /**
     * @covers      \fpoirotte\XRL\CapableServer::listMethods
     */
    public function testListMethods()
    {
        $this->server
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(
                new \ArrayIterator(array('foo' => null, 'bar' => null))
            ));
        $this->assertSame(array('bar'), $this->capableServer->listMethods());
    }

    /**
     * @covers      \fpoirotte\XRL\CapableServer::methodSignature
     * @covers      \fpoirotte\XRL\CapableServer::extractTypes
     */
    public function testMethodSignature()
    {
        $reflector1 = new \ReflectionMethod($this->capableServer, 'methodSignature');
        $reflector2 = new \ReflectionMethod(__NAMESPACE__ . '\\BadClass', 'undocumented');
        $reflector3 = new \ReflectionMethod(__NAMESPACE__ . '\\BadClass', 'incomplete');
        $reflector4 = new \ReflectionMethod(__NAMESPACE__ . '\\BadClass', 'badReturnType');
        $reflector5 = new \ReflectionMethod(__NAMESPACE__ . '\\BadClass', 'badParamType');
        $reflector6 = new \ReflectionMethod(__NAMESPACE__ . '\\BadClass', 'invalidMarkup');
        $count      = 6;

        $this->server
            ->expects($this->exactly($count))
            ->method('offsetExists')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue(true));

        $this->server
            ->expects($this->exactly($count))
            ->method('offsetGet')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue($this->callableObject));

        $this->callableObject
            ->expects($this->exactly($count))
            ->method('getReflector')
            ->will($this->onConsecutiveCalls($reflector1, $reflector2,
                                             $reflector3, $reflector4,
                                             $reflector5, $reflector6));

        // First pass: return "methodSignature"'s signature.
        $expected   = array(
            // methodSignature expects a string (method name)
            // and returns an array (list of valid signatures
            // for the method).
            array('array', 'string'),
        );
        $res = $this->capableServer->methodSignature('foo');
        $this->assertSame($expected, $res);

        // Second pass: the method lacks a documentation.
        $res = $this->capableServer->methodSignature('foo');
        $this->assertSame('undef', $res);

        // Third pass: the method's documentation is incomplete.
        $res = $this->capableServer->methodSignature('foo');
        $this->assertSame('undef', $res);

        // Fourth pass: the method's return value is too complex.
        $res = $this->capableServer->methodSignature('foo');
        $this->assertSame('undef', $res);

        // Fifth pass: the method's parameter is too complex.
        $res = $this->capableServer->methodSignature('foo');
        $this->assertSame('undef', $res);

        // Sixth pass: the method's documentation uses invalid markup.
        $res = $this->capableServer->methodSignature('foo');
        $this->assertSame('undef', $res);
    }

    /**
     * @covers                      \fpoirotte\XRL\CapableServer::methodSignature
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    Invalid method
     */
    public function testMethodSignature2()
    {
        $this->capableServer->methodSignature(42);
    }

    /**
     * @covers      \fpoirotte\XRL\CapableServer::methodHelp
     */
    public function testMethodHelp()
    {
        $reflector1 = new \ReflectionMethod($this->capableServer, 'methodHelp');
        $reflector2 = new \ReflectionMethod(__NAMESPACE__ . '\\BadClass', 'undocumented');

        $this->server
            ->expects($this->exactly(2))
            ->method('offsetExists')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue(true));

        $this->server
            ->expects($this->exactly(2))
            ->method('offsetGet')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue($this->callableObject));

        $this->callableObject
            ->expects($this->exactly(2))
            ->method('getReflector')
            ->will($this->onConsecutiveCalls($reflector1, $reflector2));

        // First pass: help about methodHelp is returned.
        $expected   =   "Get help about a procedure.\n\n" .
                        "\\param string \$method\n" .
                        "     Name of the procedure.\n\n" .
                        "\\retval string\n" .
                        "     Human readable help message " .
                        "for the given procedure.";
        $this->assertSame("\n$expected\n", $this->capableServer->methodHelp('foo'));

        // Second pass: no help for the given method.
        $this->assertSame('', $this->capableServer->methodHelp('foo'));
    }

    /**
     * @covers                      \fpoirotte\XRL\CapableServer::methodHelp
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    Invalid method
     */
    public function testMethodHelp2()
    {
        $this->capableServer->methodHelp(42);
    }

    /**
     * @covers      \fpoirotte\XRL\Server::call
     * @covers      \fpoirotte\XRL\CapableServer::multicall
     */
    public function testMulticall()
    {
        $i = 0;

        // Basic server.
        $server = new \fpoirotte\XRL\Server();
        $server->bar = function ($throw) {
            if ($throw)
                throw new \Exception('Oops');
            return 42;
        };

        // Enable advanced capabilities.
        \fpoirotte\XRL\CapableServer::enable($server);

        $requests = array(
            // 0: Invalid call: garbage input.
            null,

            // 1: Invalid call: missing call information.
            array(),

            // 2: Invalid call: missing procedure name.
            array('params' => array()),

            // 3: Invalid call: missing parameters.
            array('methodName' => 'bar'),

            // 4: Invalid call: non-existent method.
            array('methodName' => '', 'params' => array()),

            // 5: Invalid call: invalid type for "methodName".
            array('methodName' => 42, 'params' => array()),

            // 6: Invalid call: invalid type for "params".
            array('methodName' => '', 'params' => 42),

            // 7: Invalid call: recursive call.
            array('methodName' => 'system.multicall', 'params' => array()),

            // 8: Valid call throwing an exception.
            array('methodName' => 'bar', 'params' => array(true)),

            // 9: Valid call returning a result.
            array('methodName' => 'bar', 'params' => array(false)),
        );

        $res = $server->call('system.multicall', array($requests));
        $this->assertCount(count($requests), $res);

        // 0: Invalid call: garbage input.
        $this->assertInstanceOf('\\BadFunctionCallException', $res[$i]);
        $this->assertSame('Expected struct', $res[$i++]->getMessage());

        // 1: Invalid call: missing call information.
        $this->assertInstanceOf('\\BadFunctionCallException', $res[$i]);
        $this->assertSame('Missing methodName', $res[$i++]->getMessage());

        // 2: Invalid call: missing procedure name.
        $this->assertInstanceOf('\\BadFunctionCallException', $res[$i]);
        $this->assertSame('Missing methodName', $res[$i++]->getMessage());

        // 3: Invalid call: missing parameters.
        $this->assertInstanceOf('\\BadFunctionCallException', $res[$i]);
        $this->assertSame('Missing params', $res[$i++]->getMessage());

        // 4: Invalid call: non-existent method.
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Exception', $res[$i]);
        $this->assertSame('server error. requested method not found', $res[$i++]->getMessage());

        // 5: Invalid call: invalid type for "methodName".
        $this->assertInstanceOf('\\BadFunctionCallException', $res[$i]);
        $this->assertSame('Expected a string', $res[$i++]->getMessage());

        // 6: Invalid call: invalid type for "params".
        $this->assertInstanceOf('\\BadFunctionCallException', $res[$i]);
        $this->assertSame('Invalid params', $res[$i++]->getMessage());

        // 7: Invalid call: recursive call.
        $this->assertInstanceOf('\\BadFunctionCallException', $res[$i]);
        $this->assertSame('Recursive call', $res[$i++]->getMessage());

        // 8: Valid call throwing an exception.
        $this->assertInstanceOf('\\Exception', $res[$i]);
        $this->assertSame('Oops', $res[$i++]->getMessage());

        // 9: Valid call returning a result.
        $this->assertInternalType('array', $res[$i]);
        $this->assertSame(array(42), $res[$i++]);
    }
}
