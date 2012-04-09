<?php
/*
    This file is part of XRL.

    XRL is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    XRL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with XRL.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * \brief
 *      Interface for the response to an XML-RPC
 *      request, as produced by an XML-RPC server.
 *
 * \note
 *      This interfaces is never used by XML-RPC
 *      clients.
 */
interface XRL_ResponseInterface
{
    /**
     * Return the XML-RPC response this object
     * represents, as serialized XML.
     *
     * \retval string
     *      This XML-RPC response, as serialized XML.
     */
    public function __toString();

    /**
     * Publish this XML-RPC response to a browser.
     *
     * This method sets the proper HTTP headers
     * and then sends the XML-RPC response to a
     * browser.
     *
     * \warning
     *      This method never returns.
     */
    public function publish();
}
