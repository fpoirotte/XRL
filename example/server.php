<?php
// © copyright XRL Team, 2012. All rights reserved.
/*
    This file is part of XRL, a simple XML-RPC Library for PHP.

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

require_once("XRL/Autoload.php");
spl_autoload_register(array("XRL_Autoload", "load"));

// Create a dummy function for the demo.
function foo($bar)
{
    return $bar + 42;
}

// Create a new server.
$server = new XRL_Server();

// Any request for the "qux" XML-RPC procedure
// will call foo().
$server->qux = 'foo';

// Let the server handle the current request.
$server->handle()->publish();

