<?php
/**
 * \file
 *
 * Copyright (c) 2012, XRL Team
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace fpoirotte\XRL;

/**
 * \brief
 *      Interface for something that can be called.
 *
 * This interface provides a generic way to define something that
 * can be invoked to execute some code, like a function, a method
 * (with the usual array representation used by PHP), a closure, etc.
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
     * instance.
     *
     * \retval mixed
     *      Value returned by the inner callable.
     *
     * \note
     *      Any argument passed to this method will
     *      be propagated to the inner callable.
     *
     * \note
     *      This method is smart enough to preserve
     *      references.
     */
    public function invoke();

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
     *
     * \note
     *      This method is smart enough to preserve
     *      references.
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
