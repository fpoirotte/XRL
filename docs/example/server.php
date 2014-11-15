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

require_once(
    dirname(dirname(__DIR__)) .
    DIRECTORY_SEPARATOR . 'src' .
    DIRECTORY_SEPARATOR . 'Autoload.php'
);
\fpoirotte\XRL\Autoload::register();

// Create a dummy function for the demo.
function foo($bar)
{
    return $bar + 42;
}

// Dummy class with read-only access to property.
class Bar
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}

// Basic math operations.
class Maths
{
    public static function add($a, $b)
    {
        return $a + $b;
    }

    public static function subtract($a, $b)
    {
        return $a - $b;
    }

    public static function multiply($a, $b)
    {
        return $a * $b;
    }

    public static function divide($a, $b)
    {
        return $a / $b;
    }
}

// Create a new server.
$server = new \fpoirotte\XRL\Server();

// Add capabilities (system.*) to that server.
\fpoirotte\XRL\CapableServer::enable($server);

// Now, let's register some procedures!

// - inline closure
$server->hello = function ($s) { return "Hello $s"; };

// - global function
$server->qux = 'foo';

// - object method
$bar = new Bar(42);
$server->bar = array($bar, 'getValue');

// Expose the methods of the Maths class
// under the "maths." prefix (eg. "maths.add").
$server->expose('Maths', 'maths');

// Let the server handle the current request:
// - A full XML-RPC request may be passed using the "request"
//   parameter (for demonstration purpose only).
// - Otherwise, the default processing of POST'ed data applies.
if (isset($_GET['request'])) {
    $data = 'data://text/plain;base64,' . base64_encode($_GET['request']);
} else {
    $data = null;
}
$server->handle($data)->publish();
