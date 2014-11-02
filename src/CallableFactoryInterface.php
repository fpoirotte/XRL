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
 *      Interface for a factory meant to create
 *      callable objects.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
interface CallableFactoryInterface
{
    /**
     * Constructs a new callable object from any
     * PHP representation of a callable.
     *
     * \param mixed $callable
     *      A callable item. It must be compatible
     *      with the PHP callback pseudo-type.
     *
     * \retval fpoirotte::XRL::CallableInterface
     *      A callable object wrapping the PHP callback.
     *
     * \throw InvalidArgumentException
     *      The given item is not compatible
     *      with the PHP callback pseudo-type.
     *
     * \see
     *      More information on the callback pseudo-type can be found here:
     *      http://php.net/language.pseudo-types.php#language.types.callback
     */
    public function fromPHP($callable);
}
