<?php

include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers.php');

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
            $this->_server->register($func, array('ServerTestMethods', $func));
    }

    public function testCountProcedures()
    {
        $this->assertEquals(
            count(get_class_methods('ServerTestMethods')),
            count($this->_server)
        );
    }
}

