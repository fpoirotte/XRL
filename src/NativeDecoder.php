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
 *      A decoder that transparently converts
 *      XML-RPC types to native PHP types.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class NativeDecoder implements \fpoirotte\XRL\DecoderInterface
{
    /// Sub-decoder.
    protected $decoder;

    /**
     * Creates a new decoder.
     *
     * \param fpoirotte::XRL::DecoderInterface $decoder
     *      Sub-decoder to use.
     */
    public function __construct(\fpoirotte\XRL\DecoderInterface $decoder)
    {
        $this->decoder = $decoder;
    }

    /// \copydoc fpoirotte::XRL::DecoderInterface::decodeRequest()
    public function decodeRequest($data)
    {
        $request    = $this->decoder->decodeRequest($data);
        $params     = array_map(function ($p) { return $p->get(); }, $request->getParams());
        return new \fpoirotte\XRL\Request($request->getProcedure(), $params);
    }

    /// \copydoc fpoirotte::XRL::DecoderInterface::decodeResponse()
    public function decodeResponse($data)
    {
        return $this->decoder->decodeResponse($data)->get();
    }
}
