<?php

require_once("XRL/Autoload.php");

// Create a new client.
$client = new XRL_Client("http://localhost/xmlrpc/server.php");

// Call the remote server's qux() procedure.
$res = $client->qux(23);

// Display the result: "int(65)" (42 + 23).
var_dump($res);

