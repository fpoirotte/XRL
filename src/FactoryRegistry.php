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
 *      An abstract class that keeps track
 *      of dependencies using factories and
 *      the array notation.
 *
 * This abstract class makes it easy for subclasses
 * to declare their dependencies on other classes
 * and to inject factories for these dependencies.
 *
 * Classes that extend this class must setup
 * their dependencies as factories in their
 * constructor:
 * \code
 *      public function __construct(...)
 *      {
 *          $this->interfaces = array(
 *              'interface_for_dependency_factory' =>
 *                  new DefaultFactoryForThatDependency(), 
 *              'fpoirotte\\\\xrl\\\\requestfactoryinterface' =>
 *                  new \\fpoirotte\\XRL\\RequestFactory(),
 *          );
 *          // Do some other stuff here...
 *      }
 * \endcode
 *
 * \warning
 *      You \b MUST write the name of the interface
 *      for each factory in lowercase, otherwise
 *      it won't be possible to swap the factory.
 *
 * \warning
 *      In your constructor, you \b CANNOT use the array
 *      notation to register the factories. Any attempt to
 *      do so will result in an exception being thrown.
 *
 * Instances of subclasses can then be customized
 * by replacing the default factory for some interface
 * with another one:
 * \code
 *      $foo = new SubClassOfXRLFactoryRegistry();
 *
 *      // Any case can be used when customizing the factory.
 *      $foo['Interface_For_Dependency_Factory'] = new CustomFactory();
 *
 *      // You may also access the current factory.
 *      $currentFactory = $foo['Interface_For_Dependency_Factory'];
 * \endcode
 *
 * \note
 *      An exception will be raised if someone tries
 *      to delete a factory, tries to setup a factory
 *      for an interface you did not declare in your
 *      constructor or if the given factory does not
 *      implement the associated interface.
 *      \code
 *          $foo  = new SubClassOfXRLFactoryRegistry();
 *
 *          // This will raise an exception:
 *          // you cannot undeclare a dependency.
 *          unset($foo['Interface_For_Dependency_Factory']);
 *
 *          // The following line will raise an exception:
 *          // you cannot add new dependencies.
 *          $foo['UnregisteredInterface'] = new CustomFactory();
 *
 *          // This will also raise an exception,
 *          // as \\fpoirotte\\XRL\\ResponseFactory does not implement
 *          // the \\fpoirotte\\XRL\\RequestFactory interface.
 *          $foo['fpoirotte\\XRL\\RequestFactoryInterface'] =
 *              new \\fpoirotte\\XRL\\ResponseFactory();
 *      \endcode
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
abstract class FactoryRegistry implements \ArrayAccess
{
    /**
     * Mapping between factory interfaces
     * and actual factories implementing
     * these interfaces.
     */
    protected $interfaces;

    /**
     * Set the factory to use to produce instances
     * for the given interface.
     *
     * \param string $interface
     *      The name of an interface that describes
     *      the factory.
     *
     * \param object $obj
     *      A factory implementing the $interface.
     *
     * \throw InvalidArgumentException
     *      Thrown when the given interface is not a string,
     *      \c $obj is not an object or it does not implement
     *      that interface, or the given interface was not
     *      registered as a dependency of this class.
     */
    public function offsetSet($interface, $obj)
    {
        if (!is_string($interface)) {
            throw new \InvalidArgumentException(
                'The interface name should be a string, not '.
                gettype($interface)
            );
        }

        if (!is_object($obj)) {
            throw new \InvalidArgumentException(
                'The factory should be an object, not '.gettype($obj)
            );
        }

        $interface = strtolower(ltrim($interface, '\\'));
        if (!isset($this->interfaces[$interface])) {
            throw new \InvalidArgumentException(
                'No such interface "'.$interface.'"'
            );
        }

        if (!($obj instanceof $interface)) {
            throw new \InvalidArgumentException(
                'Instance of '.get_class($obj).
                " does not implement ".$interface
            );
        }

        $this->interfaces[$interface] = $obj;
    }

    /**
     * Return the factory associated with the given
     * interface.
     *
     * \param string $interface
     *      Name of the interface whose factory
     *      we're interested in.
     *
     * \retval mixed
     *      A factory implementing the given interface,
     *      or \c NULL if that interface was not registered
     *      as a dependency of this class.
     *
     * \throw InvalidArgumentException
     *      The given interface name was not a string.
     */
    public function offsetGet($interface)
    {
        if (!is_string($interface)) {
            throw new \InvalidArgumentException(
                'The interface name should be a string, not '.
                gettype($interface)
            );
        }

        $interface = strtolower(ltrim($interface, '\\'));
        return $this->interfaces[$interface];
    }

    /**
     * Test whether this class depends on a particular
     * factory interface.
     *
     * \param string $interface
     *      Factory interface to test.
     *
     * \retval bool
     *      \c TRUE if this class depends on that factory
     *      interface, \c FALSE otherwise.
     *
     * \throw InvalidArgumentException
     *      The given interface name was not a string.
     */
    public function offsetExists($interface)
    {
        if (!is_string($interface)) {
            throw new \InvalidArgumentException(
                'The interface name should be a string, not '.
                gettype($interface)
            );
        }

        $interface = strtolower(ltrim($interface, '\\'));
        return isset($this->interfaces[$interface]);
    }

    /**
     * Prevent factory interfaces undeclarations.
     *
     * \param string $interface
     *      (unused)
     *
     * \throw LogicException
     *      This exception is thrown for any attempt
     *      to undeclare a factory interface.
     */
    public function offsetUnset($interface)
    {
        throw new \LogicException(
            'Cannot undeclare factory for "'.$interface.'"'
        );
    }
}
