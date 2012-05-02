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
 *      A factory that returns compact encoders.
 *
 * A compact encoder is one that uses the bare minimum
 * of text to represent an XML document. In particular,
 * the resulting document does not contain any extra
 * indentation and does not start with an XML declaration.
 */
class       XRL_CompactEncoderFactory
implements  XRL_EncoderFactoryInterface
{
    /// Timezone used to encode date/times.
    protected $_timezone;

    /**
     * Creates a new factory for compact encoders.
     *
     * \param DateTimeZone $timezone
     *      Information on the timezone for which
     *      date/times should be encoded.
     */
    public function __construct(DateTimeZone $timezone)
    {
        $this->_timezone = $timezone;
    }

    /// \copydoc XRL_EncoderFactoryInterface::createEncoder()
    public function createEncoder()
    {
        return new XRL_Encoder($this->_timezone, FALSE);
    }
}

