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
 * This example expects an XML-RPC server providing a "hello"
 * procedure and supporting XML-RPC introspection. The server
 * should be served from a file called "server.php" in the
 * same folder as this script.
 *
 * WARNING!
 *  This script will not run properly when served from PHP's
 *  built-in web server since the built-in web server
 *  does not support concurrent requests.
 *
 *  When run under the built-in web server, it will just appear
 *  to hang until it dies with the following warning in the console:
 *
 *    PHP Warning:  XMLReader::open(http://xxx/server.php):
 *    failed to open stream: HTTP request failed!
 *
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

require_once(
    dirname(dirname(__DIR__)) .
    DIRECTORY_SEPARATOR . 'src' .
    DIRECTORY_SEPARATOR . 'Autoload.php'
);
\fpoirotte\XRL\Autoload::register();

// Create the URL that will be used to contact the XML-RPC server.
$pos        = strrpos($_SERVER['SCRIPT_NAME'], '/');
$serverUrl  =   (!empty($_SERVER['HTTP_SSL']) &&
                strcasecmp($_SERVER['HTTP_SSL'], 'off'))
                ? 'https'
                : 'http';
$serverUrl .= '://'.$_SERVER['HTTP_HOST'];
$serverUrl .= (string) substr($_SERVER['SCRIPT_NAME'], 0, $pos);
$serverUrl .= '/server.php';

// Create a new client.
$client     = new \fpoirotte\XRL\Client($serverUrl);

// List all available procedures.
var_dump($client->{'system.listMethods'}());

// Say hello :)
var_dump($client->hello('world'));

