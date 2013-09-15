<?php
/**
 * \file
 *
 * \copyright XRL Team, 2012. All rights reserved.
 *
    This file is part of XRL, a simple XML-RPC Library for PHP.
 *
 *  XRL is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  XRL is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with XRL.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \brief
 *      An helper class that wraps XRL's autoloader.
 */
class XRL_Autoload
{
    /**
     * Autoloader for XRL's classes and interfaces.
     *
     * \param string $class
     *      Name of the class/interface to load.
     *
     * \note
     *      The autoloader will only try to load classes/interfaces
     *      whose name starts with "XRL_" (case-insensitive).
     *
     * \warning
     *      The autoloader will throw an exception (which will
     *      most likely result in a fatal error in your application)
     *      in case the class or interface's name contains a colon.
     *      This is a protection against a possible remote inclusion
     *      vulnerability introduced in PHP 5.3.8 using is_a().
     *
     * \retval bool
     *      \c TRUE if the class or interface could be loaded,
     *      \c FALSE otherwise.
     */
    public static function load($class)
    {
        if (strpos($class, ':') !== FALSE)
            throw new Exception('Possible remote execution attempt');

        $class = ltrim($class, '\\');
        if (strncasecmp($class, 'XRL_', 4))
            return FALSE;

        $class = substr($class, 4);
        $class = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class);
        require(dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php');
        $res = (class_exists($class, FALSE) || interface_exists($class, FALSE));
        return $res;
    }
}

