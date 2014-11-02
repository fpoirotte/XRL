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

namespace fpoirotte\XRL\tests\stub;

class TestServer
{
    public static function intResult()
    {
        return 42;
    }

    public static function boolResult()
    {
        return true;
    }

    public static function boolResult2()
    {
        return false;
    }

    public static function stringResult()
    {
        return '';
    }

    public static function stringResult2()
    {
        return 'test';
    }

    public static function doubleResult()
    {
        return 3.14;
    }
}
