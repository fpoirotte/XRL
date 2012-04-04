<?php

function XRL_autoload($class)
{
    if (strpos($class, ':') !== FALSE)
        throw new Exception('Possible remote execution attempt');

    $class = ltrim($class, '\\');
    if (strcasecmp($class, 'XRL_', 4))
        return FALSE;

    $class = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class);
    require(dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php');
}
spl_autoload_register("XRL_autoload");

