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


function upper(&$s)
{
    $s = \strtoupper($s);
}


function strtoupper($s)
{
    return \strtoupper($s);
}


class InstanceByRef
{
    public function __invoke(&$s)
    {
        upper($s);
    }
}


class InstanceByValue
{
    public function __invoke($s)
    {
        return \strtoupper($s);
    }
}


class CallableObject extends \PHPUnit_Framework_TestCase
{
    public function referenceProvider()
    {
        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            return array();
        }

        $instance   = new InstanceByRef();
        $lambda     = create_function('&$s', '$s = \strtoupper($s);');

        return array(
            // Function
            array(
                'fpoirotte\XRL\tests\upper',
                __NAMESPACE__ . '\\upper',
                true,
            ),

            // Invokable object
            array(
                $instance,
                __NAMESPACE__ . '\\InstanceByRef::__invoke',
                true,
            ),

            // Object method
            array(
                array($instance, '__invoke'),
                __NAMESPACE__ . '\\InstanceByRef::__invoke',
                true,
            ),

            // Lambda function (anonymous function)
            array(
                $lambda,
                $lambda,
                true,
            ),

            // Closure
            array(
                function (&$s) { $s = \strtoupper($s); },
                'Closure::__invoke',
                true,
            ),
        );
    }

    public function valueProvider()
    {
        $instance   = new InstanceByValue();
        $lambda     = create_function('$s', 'return strtoupper($s);');

        return array(
            // Function
            array(
                'fpoirotte\XRL\tests\strtoupper',
                __NAMESPACE__ . '\\strtoupper',
                false,
            ),

            // Invokable object
            array(
                $instance,
                __NAMESPACE__ . '\\InstanceByValue::__invoke',
                false,
            ),

            // Object method
            array(
                array($instance, '__invoke'),
                __NAMESPACE__ . '\\InstanceByValue::__invoke',
                false,
            ),

            // Lambda function (anonymous function)
            array(
                $lambda,
                $lambda,
                false,
            ),

            // Closure
            array(
                function ($s) { return strtoupper($s); },
                'Closure::__invoke',
                false,
            ),
        );
    }

    public function provider()
    {
        return array_merge($this->referenceProvider(), $this->valueProvider());
    }

    /**
     * @dataProvider provider
     * @covers \fpoirotte\XRL\CallableObject::__construct
     * @covers \fpoirotte\XRL\CallableObject::getCallable
     * @covers \fpoirotte\XRL\CallableObject::__toString
     * @covers \fpoirotte\XRL\CallableObject::getRepresentation
     * @covers \fpoirotte\XRL\CallableObject::__invoke
     * @covers \fpoirotte\XRL\CallableObject::invokeArgs
     */
    public function testGeneric($callable, $repr, $isRef)
    {
        $obj = new \fpoirotte\XRL\CallableObject($callable);
        $this->assertSame($callable, $obj->getCallable());
        $this->assertSame($repr, (string) $obj);
        $this->assertSame($repr, $obj->getRepresentation());

        // __invoke() magic method (implicit)
        $value = 'abc';
        if ($isRef) {
            $obj($value);
            $this->assertSame('ABC', $value);
        } else {
            $this->assertSame('ABC', $obj($value));
        }

        // __invoke() magic method (explicit)
        $value = 'abc';
        if ($isRef) {
            $obj->__invoke($value);
            $this->assertSame('ABC', $value);
        } else {
            $this->assertSame('ABC', $obj->__invoke($value));
        }

        // invokeArgs
        $values = array('abc');
        if ($isRef) {
            $obj->invokeArgs($values);
            $this->assertSame('ABC', $values[0]);
        } else {
            $this->assertSame('ABC', $obj->invokeArgs($values));
        }
    }
}
