#!/usr/bin/env php
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

if (version_compare(phpversion(), '5.3.3', '<')) {
    echo "XRL requires PHP 5.3.3 or newer." . PHP_EOL;
    exit -1;
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

require('phar://' . __FILE__ . '/src/Autoload.php');
\fpoirotte\XRL\Autoload::register();

$cli = new \fpoirotte\XRL\CLI();
die($cli->run($_SERVER['argv']));

__HALT_COMPILER();
