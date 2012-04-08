#!/usr/bin/env php
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
if (version_compare(phpversion(), '5.3.1', '<')) {
    if (substr(phpversion(), 0, 5) != '5.3.1') {
        // this small hack is because of running RCs of 5.3.1
        echo "XRL requires PHP 5.3.1 or newer." . PHP_EOL;
        exit -1;
    }
}
foreach (array('phar', 'spl', 'pcre', 'simplexml') as $ext) {
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
function XRL_autoload($class)
{
    $class = str_replace(array('_', '\\'), '/', $class);
    if (file_exists('phar://' . __FILE__ . '/XRL-@PACKAGE_VERSION@/php/' . $class . '.php')) {
        return include 'phar://' . __FILE__ . '/XRL-@PACKAGE_VERSION@/php/' . $class . '.php';
    }
}
spl_autoload_register("XRL_autoload");
$phar = new Phar(__FILE__);
$sig  = $phar->getSignature();
define('XRL_SIG', $sig['hash']);
define('XRL_SIGTYPE', $sig['hash_type']);

// your package-specific stuff here, for instance, here is what Pyrus does:

/**
 * $frontend = new \Pyrus\ScriptFrontend\Commands;
 * @array_shift($_SERVER['argv']);
 * $frontend->run($_SERVER['argv']);
 */
__HALT_COMPILER();
