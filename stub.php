#!/usr/bin/env php
<?php
/**
 * If your package does special stuff in phar format, use this file.  Remove if
 * no phar format is ever generated
 * More information: http://pear.php.net/manual/en/pyrus.commands.package.php#pyrus.commands.package.stub
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
