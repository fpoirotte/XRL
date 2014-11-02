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
 *      A factory that returns pretty encoders.
 *
 * A pretty encoder is one that adds extra indentation
 * to an XML document to make it easier to read.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class PrettyEncoderFactory implements \fpoirotte\XRL\EncoderFactoryInterface
{
    /// Timezone used to encode date/times.
    protected $timezone;

    /**
     * Creates a new factory for pretty encoders.
     *
     * \param DateTimeZone $timezone
     *      Information on the timezone for which
     *      date/times should be encoded.
     */
    public function __construct(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    /// \copydoc fpoirotte::XRL::EncoderFactoryInterface::createEncoder()
    public function createEncoder()
    {
        return new \fpoirotte\XRL\Encoder($this->timezone, true);
    }
}
