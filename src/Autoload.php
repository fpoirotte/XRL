<?php
/**
 * \file
 *
 * Copyright (c) 2012, XRL Team
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace fpoirotte\XRL;

/**
 * \brief
 *      An helper class that wraps XRL's autoloader.
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
        if (strpos($class, ':') !== false) {
            throw new \Exception('Possible remote execution attempt');
        }

        $class = ltrim($class, '\\');
        if (strncmp($class, 'fpoirotte\\XRL\\', 14)) {
            return false;
        }

        $class = substr($class, 14);
        $class = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class);
        if (strncmp($class, 'tests\\', 6)) {
            $path = dirname(__DIR__);
        } else {
            $path = __DIR__;
        }
        require($path . DIRECTORY_SEPARATOR . $class . '.php');
        $res = (class_exists($class, false) || interface_exists($class, false));
        return $res;
    }

    /**
     * Register XRL's autoloader.
     */
    public static function register()
    {
        static $registered = false;

        if (!$registered) {
            spl_autoload_register(array(__CLASS__, "load"));
        }
    }
}
