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
 *      Interface for a factory that creates
 *      XML-RPC decoders.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
interface DecoderFactoryInterface
{
    /**
     * Creates a new XML-RPC capable of processing
     * XML-RPC requests and responses.
     *
     * \retval fpoirotte::XRL::DecoderInterface
     *      An XML-RPC decoder.
     */
    public function createDecoder();
}
