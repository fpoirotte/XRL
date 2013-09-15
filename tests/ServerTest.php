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

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers.php');

class   ServerTestMethods
{
    static public function intResult()
    {
        return 42;
    }

    static public function boolResult()
    {
        return TRUE;
    }

    static public function boolResult2()
    {
        return FALSE;
    }

    static public function stringResult()
    {
        return '';
    }

    static public function stringResult2()
    {
        return 'test';
    }

    static public function doubleResult()
    {
        return 3.14;
    }
}

class   ServerTest
extends PHPUnit_Framework_TestCase
{
    protected $_server;

    public function setUp()
    {
        $this->_server = new XRL_Server();
        foreach (get_class_methods('ServerTestMethods') as $func)
            $this->_server->$func = array('ServerTestMethods', $func);
    }

    public function testCountProcedures()
    {
        $this->assertEquals(
            count(get_class_methods('ServerTestMethods')),
            count($this->_server)
        );
    }
}

