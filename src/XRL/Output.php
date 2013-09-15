<?php
/**
 * \file
 *
 * \copyright XRL Team, 2012. All rights reserved.
 *
 *  This file is part of XRL, a simple XML-RPC Library for PHP.
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
 *      A class that formats messages
 *      before sending them to a stream.
 */
class XRL_Output
{
    /// Stream to send the messages to.
    protected $_stream;

    /**
     * Create a new outputter.
     *
     * \param resource $stream
     *      The PHP stream this outputter
     *      will write to.
     */
    public function __construct($stream)
    {
        $this->_stream = $stream;
    }

    /**
     * Write some message to the stream,
     * in a \c printf() fashion.
     *
     * \param string $format
     *      A format string to use to send the message.
     *
     * \note
     *      You may pass additional parameters to this
     *      method. They will serve as arguments for
     *      the format string.
     *
     * \note
     *      You don't need to add an end-of-line sequence
     *      to the format string, one will automatically
     *      be added for you by this method.
     */
    public function _($format /* , ... */)
    {
        $args = func_get_args();
        array_shift($args);
        // Protection against format attacks.
        if (!count($args)) {
            $args[] = $format;
            $format = "%s";
        }
        vfprintf($this->_stream, $format.PHP_EOL, $args);
    }
}

