<?php
// © copyright XRL Team, 2012. All rights reserved.
/*
    This file is part of XRL.

    XRL is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    XRL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with XRL.  If not, see <http://www.gnu.org/licenses/>.
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
