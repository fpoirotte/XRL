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

if (!class_exists('PHPUnit_Framework_TestCase')) {
    class_alias('\\PHPUnit\\Framework\\TestCase', 'PHPUnit_Framework_TestCase');
}

if (!class_exists('PHPUnit_Framework_Error')) {
    class_alias('\\PHPUnit\\Framework\\Error', 'PHPUnit_Framework_Error');
}
