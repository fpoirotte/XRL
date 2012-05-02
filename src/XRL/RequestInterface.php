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
 *      Interface for an object representing an XML-RPC
 *      request.
 */
interface XRL_RequestInterface
{
    /**
     * Return the name of the procedure this request
     * refers to.
     *
     * \retval string
     *      Name of the XML-RPC procedure this request
     *      refers to.
     */
    public function getProcedure();

    /**
     * Return the parameters that will be passed
     * to that request's procedure.
     *
     * \retval array
     *      Parameters for the XML-RPC procedure,
     *      using native PHP types.
     */
    public function getParams();
}
