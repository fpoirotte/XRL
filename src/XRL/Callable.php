<?php

/**
 * \brief
 *      Class used to represent anything that is callable.
 *
 * This class can represent a wild range of callable items
 * supported by PHP (functions, lambdas, methods, closures, etc.).
 */
class XRL_Callable
{
    /// Inner callable object, as used by PHP.
    protected $_callable;

    /// Human representation of the inner callable.
    protected $_representation;

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
        if (!is_callable($callable, FALSE, $representation))
            throw new InvalidArgumentException('Not a valid callable');

        // This happens for anonymous functions
        // created with create_function().
        if (is_string($callable) && $representation == "")
            $representation = $callable;

        $this->_callable        = $callable;
        $this->_representation  = $representation;
    }

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
     *      Both of these are only possible with PHP >= 5.3.0.
     */
    public function getCallable()
    {
        return $this->_callable;
    }

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
    public function getRepresentation()
    {
        return $this->_representation;
    }

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
    public function invoke(/* ... */)
    {
        // HACK:    we use debug_backtrace() to get (and pass along)
        //          references for call_user_func_array().

        // Starting with PHP 5.4.0, it is possible to limit
        // the number of stack frames returned.
        if (version_compare(PHP_VERSION, '5.4', '>='))
            $bt = debug_backtrace(0, 1);
        // Starting with PHP 5.3.6, the first argument
        // to debug_backtrace() is a bitmask of options.
        else if (version_compare(PHP_VERSION, '5.3.6', '>='))
            $bt = debug_backtrace(0);
        else
            $bt = debug_backtrace(FALSE);

        if (isset($bt[0]['args']))
            $args =& $bt[0]['args'];
        else
            $args = array();
        return call_user_func_array($this->_callable, $args);
    }

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
    public function invokeArgs(array &$args)
    {
        return call_user_func_array($this->_callable, $args);
    }

    /**
     * Implementation of the __invoke() magic method.
     *
     * This method is present only for forward-compatibility
     * and because it turns instances of XRL_Callable
     * into callbables themselves (ain't that neat?).
     *
     * \deprecated
     *      Use XRL_Callable::invoke()
     *      instead of calling this method directly
     *      or relying on its magic with code such as:
     *      \code
     *          $c = new XRL_Callable("var_dump");
     *          $c(42);
     *      \endcode
     */
    public function __invoke(/* ... */)
    {
        // HACK:    we use debug_backtrace() to get (and pass along)
        //          references for call_user_func_array().

        // Starting with PHP 5.4.0, it is possible to limit
        // the number of stack frames returned.
        if (version_compare(PHP_VERSION, '5.4', '>='))
            $bt = debug_backtrace(0, 1);
        // Starting with PHP 5.3.6, the first argument
        // to debug_backtrace() is a bitmask of options.
        else if (version_compare(PHP_VERSION, '5.3.6', '>='))
            $bt = debug_backtrace(0);
        else
            $bt = debug_backtrace(FALSE);

        if (isset($bt[0]['args']))
            $args =& $bt[0]['args'];
        else
            $args = array();
        return call_user_func(array($this, 'invokeArgs'), $args);
    }

    /**
     * Alias for XRL_Callable::getRepresentation().
     *
     * \retval string
     *      Human representation of this callable.
     *
     * \see XRL_Callable::getRepresentation()
     */
    public function __toString()
    {
        return $this->_representation;
    }
}

