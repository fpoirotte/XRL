<?php

require_once("XRL/Autoload.php");

// Create a dummy function for the demo.
function foo($bar)
{
    return $bar + 42;
}

// Create a new server.
$server = new XRL_Server();

// Any request for the "qux" XML-RPC procedure
// will call foo().
$server->register('qux', 'foo');

// Let the server handle the current request.
$server->handle()->publish();

