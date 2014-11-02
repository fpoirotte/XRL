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

/*
 * This example expects an XML-RPC server providing a "qux"
 * procedure (such as the one in "server.php"). The server
 * should be served from a file called "server.php" in the
 * same folder as this script on this machine's webserver.
 *
 * This script will automatically generate the URL required
 * to query the XML-RPC server by substituting this script's
 * filename with "server.php". For example:
 *
 * Client: http://localhost/xmlrpc/client.php
 * Server: http://localhost/xmlrpc/server.php
 *
 * Client: https://foobar.example.com:4443/tests/xrl/client.php/foo.bar?baz=1
 * Server: https://foobar.example.com:4443/tests/xrl/server.php
 */

require_once("src/Autoload.php");
\fpoirotte\XRL\Autoload::register();

// Create the URL that will be used to contact the XML-RPC server.
$pos        = strrpos($_SERVER['SCRIPT_NAME'], '/');
$serverUrl  =   (!empty($_SERVER['HTTP_SSL']) &&
                strcasecmp($_SERVER['HTTP_SSL'], 'off'))
                ? 'https'
                : 'http';
$serverUrl .= '://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'];
$serverUrl .= (string) substr($_SERVER['SCRIPT_NAME'], 0, $pos);
$serverUrl .= '/server.php';

// Create a new client.
$client     = new \fpoirotte\XRL\Client($serverUrl);

// Call the remote server's qux() procedure.
$res = $client->qux(23);

// Display the result: "int(65)" (42 + 23).
var_dump($res);
