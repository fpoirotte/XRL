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

namespace fpoirotte\XRL;

/**
 * \brief
 *      An helper class that wraps XRL's autoloader.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Autoload
{
    /**
     * Load an XRL class or interface.
     *
     * \param string $class
     *      Name of the class/interface to load.
     *
     * \note
     *      The autoloader will only try to load classes/interfaces
     *      whose name starts with "fpoirotte\\XRL\\" (case-sensitive).
     *
     * \warning
     *      The autoloader will throw an exception (which will
     *      most likely result in a fatal error in your application)
     *      in case the class or interface's name contains a colon.
     *      This is a protection against a possible remote inclusion
     *      vulnerability introduced in PHP 5.3.8 using is_a().
     *
     * \retval bool
     *      \c true if the class or interface could be loaded,
     *      \c false otherwise.
     */
    public static function load($class)
    {
        // This code only applies to PHP 5.3.7 & 5.3.8.
        // It prevents a case of remote code execution (CVE 2011-3379).
        if (strpos($class, ':') !== false) {
            // @codeCoverageIgnoreStart
            throw new \Exception('Possible remote execution attempt');
            // @codeCoverageIgnoreEnd
        }

        $class = ltrim($class, '\\');
        if (strncmp($class, 'fpoirotte\\XRL\\', 14)) {
            return false;
        }

        $class = substr($class, 14);
        if (!strncmp($class, 'tests\\', 6)) {
            $path = dirname(__DIR__);
        } else {
            $path = __DIR__;
        }

        $class = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class);
        include($path . DIRECTORY_SEPARATOR . $class . '.php');
        $res = (class_exists($class, false) || interface_exists($class, false));
        return $res;
    }

    /**
     * Register XRL's autoloader.
     *
     * \return
     *      This method does not return any meaningful value.
     */
    public static function register()
    {
        static $registered = false;

        if (!$registered) {
            spl_autoload_register(array(__CLASS__, "load"));
        }
    }
}
