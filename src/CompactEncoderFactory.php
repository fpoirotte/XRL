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
 *      A factory that returns compact encoders.
 *
 * A compact encoder is one that uses the bare minimum
 * of text to represent an XML document. In particular,
 * the resulting document does not contain any extra
 * indentation and does not start with an XML declaration.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class CompactEncoderFactory implements \fpoirotte\XRL\EncoderFactoryInterface
{
    /// Timezone used to encode date/times.
    protected $timezone;

    /**
     * Creates a new factory for compact encoders.
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
        return new \fpoirotte\XRL\Encoder($this->timezone, false);
    }
}
