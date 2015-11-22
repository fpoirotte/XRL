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

namespace fpoirotte\XRL\tests;

class Autoload extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \fpoirotte\XRL\Autoload::register();
    }

    /**
     * @covers                      \fpoirotte\XRL\Autoload
     */
    public function testAutoload()
    {
        $this->assertTrue(class_exists('\\fpoirotte\\XRL\\tests\\stub\\Autoload'));
    }

    /**
     * @covers                      \fpoirotte\XRL\Autoload
     */
    public function testAutoload2()
    {
        $this->assertFalse(class_exists('\\fpoirotte\\some_inexistent_class'));
    }

    /**
     * @covers                      \fpoirotte\XRL\Autoload
     * @expectedException           \PHPUnit_Framework_Error
     */
    public function testAutoload3()
    {
        $dummy = class_exists('\\fpoirotte\\XRL\\some_inexistent_class');
    }

    /**
     * See https://bugs.php.net/bug.php?id=55475
     * for more information about the meaning of this test.
     *
     * @covers                      \fpoirotte\XRL\Autoload
     */
    public function testAutoload4()
    {
        if (version_compare(PHP_VERSION, '5.3.7', '<') ||
            version_compare(PHP_VERSION, '5.3.8', '>')) {
            return;
        }
        $this->setExpectedException(
            '\\Exception',
            'Possible remote execution attempt'
        );
        $dummy = is_a('http://example.com/foo', '\\stdClass');
    }
}
