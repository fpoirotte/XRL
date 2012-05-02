<?php
/**
 * \file
 *
 * \copyright XRL Team, 2012. All rights reserved.
 *
 *  This file is part of XRL.
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
 *      Factory for an XML-RPC decoder that
 *      does not validate its input.
 */
class       XRL_NonValidatingDecoderFactory
implements  XRL_DecoderFactoryInterface
{
    /// Timezone used to decode date/times.
    protected $_timezone;

    /**
     * Creates a new factory for a decoder that
     * does not validate its input.
     *
     * \param DateTimeZone $timezone
     *      Information on the timezone incoming
     *      date/times come from.
     */
    public function __construct(DateTimeZone $timezone)
    {
        $this->_timezone = $timezone;
    }

    /// \copydoc XRL_DecoderFactoryInterface::createDecoder()
    public function createDecoder()
    {
        return new XRL_Decoder($this->_timezone, FALSE);
    }
}

