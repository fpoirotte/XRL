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
 *      Interface for something that can be called.
 *
 * This interface provides a generic way to define something that
 * can be invoked to execute some code, like a function, a method
 * (with the usual array representation used by PHP), a closure, etc.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
interface CallableInterface
{
    /**
     * Returns the callable object in its raw form
     * (as used by PHP).
     *
     * \retval string
     *      The name of the function this callable represents,
     *      which can be either a core function, a user-defined
     *      function, or the result of a call to create_function().
     *
     * \retval array
     *      An array whose contents matches the definition
     *      of a PHP callback, that is:
     *      -   The first element refers to either an object,
     *          a class name or one of the reserved keywords
     *          (self, parent, static, etc.).
     *      -   The second element is the name of a method
     *          from that object/class.
     *
     * \retval object
     *      Either a Closure object or an instance of a class
     *      that implements the __invoke() magic method.
     */
    public function getCallable();

    /**
     * Returns a human representation of this callable.
     * For (anonymous) functions, this is a string containing
     * the name of that function.
     * For methods and classes that implement the __invoke()
     * magic method (including Closures), this is a string
     * of the form "ClassName::methodname".
     *
     * \retval string
     *      Human representation of this callable.
     */
    public function getRepresentation();

    /**
     * Implementation of the __invoke() magic method.
     *
     * This method is present only for forward-compatibility
     * and because it turns instances of fpoirotte::XRL::CallableInterface
     * into callables themselves (ain't that neat?).
     *
     * \warning
     *      Use fpoirotte::XRL::CallableInterface::invoke(...)
     *      instead of calling this method directly
     *      or relying on its magic with code such as:
     *      \code
     *          $c = new \\fpoirotte\\XRL\\CallableObject("var_dump");
     *          $c(42);
     *      \endcode
     */
    public function __invoke();

    /**
     * Invokes the callable object represented by this
     * instance, using the given array as a list of arguments.
     *
     * \param array $args
     *      An array whose values will become the arguments
     *      for the inner callable.
     *
     * \retval mixed
     *      Value returned by the inner callable.
     */
    public function invokeArgs(array &$args);

    /**
     * Alias for fpoirotte::XRL::CallableInterface::getRepresentation().
     *
     * \retval string
     *      Human representation of this callable.
     *
     * \see fpoirotte::XRL::CallableInterface::getRepresentation()
     */
    public function __toString();

    /**
     * Get a reflection objets for the function/method/object
     * represented by this callable.
     *
     * \retval Reflector
     *      Reflection object for this callable's inner
     *      PHP callback.
     */
    public function getReflector();
}
