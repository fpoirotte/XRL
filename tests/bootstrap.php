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

// Avoid harmless warning on some
// badly-configured PHP installations.
date_default_timezone_set('UTC');

require_once(
    dirname(__DIR__) .
    DIRECTORY_SEPARATOR . 'vendor' .
    DIRECTORY_SEPARATOR . 'autoload.php'
);

$stubs = array(
    'Server',
);
foreach ($stubs as $stub) {
    require_once(
        __DIR__ .
        DIRECTORY_SEPARATOR . 'stub' .
        DIRECTORY_SEPARATOR . 'Test' . $stub . '.php'
    );
}

// HACK: backward compatibility with PHPUnit releases that lacked namespaces.
if (!class_exists('PHPUnit\\Framework\\TestResult')) {
    class_alias('PHPUnit_Framework_TestResult', 'PHPUnit\\Framework\\TestResult');
}
if (!class_exists('PHPUnit\\Framework\\TestCase')) {
    class_alias('PHPUnit_Framework_TestCase', 'PHPUnit\\Framework\\TestCase');
}
