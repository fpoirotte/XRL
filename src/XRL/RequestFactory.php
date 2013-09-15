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
 *      A factory that produces objects meant
 *      to represent XML-RPC requests.
 */
class       XRL_RequestFactory
implements  XRL_RequestFactoryInterface
{
    /// \copydoc XRL_RequestFactoryInterface::createRequest()
    public function createRequest($method, array $params)
    {
        return new XRL_Request($method, $params);
    }
}

