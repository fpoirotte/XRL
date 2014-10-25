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
 *      A class that formats messages
 *      before sending them to a stream.
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
