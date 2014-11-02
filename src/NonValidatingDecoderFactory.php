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
 *      Factory for an XML-RPC decoder that
 *      does not validate its input.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class NonValidatingDecoderFactory implements \fpoirotte\XRL\DecoderFactoryInterface
{
    /// Timezone used to decode date/times.
    protected $timezone;

    /**
     * Creates a new factory for a decoder that
     * does not validate its input.
     *
     * \param DateTimeZone $timezone
     *      Information on the timezone incoming
     *      date/times come from.
     */
    public function __construct(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    /// \copydoc fpoirotte::XRL::DecoderFactoryInterface::createDecoder()
    public function createDecoder()
    {
        return new \fpoirotte\XRL\Decoder($this->timezone, false);
    }
}
