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
 *      A class that formats messages
 *      before sending them to a stream.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Output
{
    /// Stream to send the messages to.
    protected $stream;

    /**
     * Create a new outputter.
     *
     * \param resource $stream
     *      The PHP stream this outputter
     *      will write to.
     */
    public function __construct($stream)
    {
        $this->stream = $stream;
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
    public function write($format /* , ... */)
    {
        $args = func_get_args();
        array_shift($args);
        // Protection against format attacks.
        if (!count($args)) {
            $args[] = $format;
            $format = "%s";
        }
        vfprintf($this->stream, $format.PHP_EOL, $args);
    }
}
