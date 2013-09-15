#!/usr/bin/env php
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
if (version_compare(phpversion(), '5.3.1', '<')) {
    if (substr(phpversion(), 0, 5) != '5.3.1') {
        // this small hack is because of running RCs of 5.3.1
        echo "XRL requires PHP 5.3.1 or newer." . PHP_EOL;
        exit -1;
    }
}
foreach (array('phar', 'spl', 'xmlreader', 'xmlwriter') as $ext) {
    if (!extension_loaded($ext)) {
        echo "Extension $ext is required" . PHP_EOL;
        exit -1;
    }
}
try {
    Phar::mapPhar();
} catch (Exception $e) {
    echo "Cannot process XRL phar:" . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    exit -1;
}

require('phar://' . __FILE__ . '/src/XRL/Autoload.php');
spl_autoload_register(array("XRL_Autoload", "load"));

$cli = new XRL_CLI();
die($cli->run($_SERVER['argv']));

__HALT_COMPILER();
