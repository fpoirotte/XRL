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
 *      Class used to represent anything that is callable.
 *
 * This class can represent a wild range of callable items
 * supported by PHP (functions, lambdas, methods, closures, etc.).
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class CallableObject implements \fpoirotte\XRL\CallableInterface
{
    /// Inner callable object, as used by PHP.
    protected $callableObj;

    /// Human representation of the inner callable.
    protected $representation;

    /**
     * Constructs a new callable object, abstracting
     * differences between the different constructs
     * PHP supports.
     *
     * \param mixed $callable
     *      A callable item. It must be compatible
     *      with the PHP callback pseudo-type.
     *
     * \throw InvalidArgumentException
     *      The given item is not compatible
     *      with the PHP callback pseudo-type.
     *
     * \see
     *      More information on the callback pseudo-type can be found here:
     *      http://php.net/language.pseudo-types.php#language.types.callback
     */
    public function __construct($callable)
    {
        if (!is_callable($callable, false, $representation)) {
            throw new \InvalidArgumentException('Not a valid callable');
        }

        // This happens for anonymous functions
        // created with create_function().
        if (is_string($callable) && $representation == "") {
            $representation = $callable;
        }

        $this->callableObj    = $callable;
        $this->representation = $representation;
    }

    /// \copydoc fpoirotte::XRL::CallableInterface::getCallable()
    public function getCallable()
    {
        return $this->callableObj;
    }

    /// \copydoc fpoirotte::XRL::CallableInterface::getRepresentation()
    public function getRepresentation()
    {
        return $this->representation;
    }

    /// \copydoc fpoirotte::XRL::CallableInterface::invoke(...)
    public function invoke()
    {
        // HACK:    we use debug_backtrace() to get (and pass along)
        //          references for call_user_func_array().

        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            // Starting with PHP 5.4.0, it is possible to limit
            // the number of stack frames returned.
            $bt = debug_backtrace(0, 1);
        } elseif (version_compare(PHP_VERSION, '5.3.6', '>=')) {
            // Starting with PHP 5.3.6, the first argument
            // to debug_backtrace() is a bitmask of options.
            $bt = debug_backtrace(0);
        } else {
            $bt = debug_backtrace(false);
        }

        if (isset($bt[0]['args'])) {
            $args =& $bt[0]['args'];
        } else {
            $args = array();
        }
        return call_user_func_array($this->callableObj, $args);
    }

    /// \copydoc fpoirotte::XRL::CallableInterface::invokeArgs()
    public function invokeArgs(array &$args)
    {
        return call_user_func_array($this->callableObj, $args);
    }

    /// \copydoc fpoirotte::XRL::CallableInterface::__invoke(...)
    public function __invoke()
    {
        // HACK:    we use debug_backtrace() to get (and pass along)
        //          references for call_user_func_array().

        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            // Starting with PHP 5.4.0, it is possible to limit
            // the number of stack frames returned.
            $bt = debug_backtrace(0, 1);
        } elseif (version_compare(PHP_VERSION, '5.3.6', '>=')) {
            // Starting with PHP 5.3.6, the first argument
            // to debug_backtrace() is a bitmask of options.
            $bt = debug_backtrace(0);
        } else {
            $bt = debug_backtrace(false);
        }

        if (isset($bt[0]['args'])) {
            $args =& $bt[0]['args'];
        } else {
            $args = array();
        }
        return call_user_func(array($this, 'invokeArgs'), $args);
    }

    /// \copydoc fpoirotte::XRL::CallableInterface::__toString()
    public function __toString()
    {
        return $this->representation;
    }

    /// \copydoc fpoirotte::XRL::CallableInterface::getReflector()
    public function getReflector()
    {
        $parts = explode('::', $this->representation);

        // Did we wrap a function?
        if (count($parts) == 1) {
            return new \ReflectionFunction($this->callableObj);
        }

        // Did we wrap a Closure or some invokable object?
        if (!is_array($this->callableObj)) {
            $callable = array($this->callableObj, $parts[1]);
        } else {
            $callable = $this->callableObj;
        }
        return new \ReflectionMethod($callable[0], $callable[1]);
    }
}
