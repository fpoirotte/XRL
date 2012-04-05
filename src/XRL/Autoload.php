<?php

function XRL_autoload($class)
{
    if (strpos($class, ':') !== FALSE)
        throw new Exception('Possible remote execution attempt');

    $class = ltrim($class, '\\');
    if (strncasecmp($class, 'XRL_', 4))
        return FALSE;

    $class = substr($class, 4);
    $class = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class);
    require(dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php');
}
spl_autoload_register("XRL_autoload");

