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

require_once("src/Autoload.php");
\fpoirotte\XRL\Autoload::register();

// Create a dummy function for the demo.
function foo($bar)
{
    return $bar + 42;
}

// Create a new server.
$server = new \fpoirotte\XRL\Server();

// Any request for the "qux" XML-RPC procedure
// will call foo().
$server->qux = 'foo';

// Let the server handle the current request.
$server->handle()->publish();
