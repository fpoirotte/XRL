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

namespace fpoirotte\XRL\Types;

/**
 * \brief
 *      A class representing an abstract XML-RPC type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
abstract class AbstractType
{
    /// Current value associated with this object.
    protected $value;

    /**
     * Construct a new instance with some
     * initial value.
     *
     * \param mixed $value
     *      Initial value.
     *
     * \throws InvalidArgumentException
     *      The given value is invalid
     *      for this type of object.
     */
    public function __construct($value)
    {
        $this->set($value);
    }

    /**
     * Return this object's current value.
     *
     * \retval mixed
     *      This object's current value.
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Construct a new instance of the type
     * with the content from some string.
     *
     * \param string $value
     *      Raw value to parse.
     *
     * \retval object
     *      New object instanciated from the value.
     *
     * \param DateTimeZone $timezone
     *      Timezone to use when converting dates/times.
     *
     * \throws InvalidArgumentException
     *      The given value is invalid
     *      for this type of object.
     */
    final public static function read($value, \DateTimeZone $timezone = null)
    {
        return new static(static::parse($value, $timezone));
    }

    /**
     * Return a string representation
     * of this object's value.
     *
     * \retval string
     *      String representation of this object's value.
     */
    public function __toString()
    {
        return (string) $this->get();
    }

    /**
     * Parse a string into a value
     * compatible with this type.
     *
     * \param string $value
     *      String to parse.
     *
     * \param DateTimeZone $timezone
     *      Timezone to use when converting dates/times.
     *
     * \retval mixed
     *      A value compatible with this type.
     */
    static protected function parse($value, \DateTimeZone $timezone = null)
    {
        return $value;
    }

    /**
     * Change the value associated
     * with the object.
     *
     * \param mixed $value
     *      New value.
     *
     * \return
     *      This method does not return any value.
     *
     * \throws InvalidArgumentException
     *      The given value is invalid
     *      for this type of object.
     */
    abstract public function set($value);

    /**
     * Export this object's value to XML.
     *
     * \param XMLWriter $writer
     *      Writer the value will be exported to.
     *
     * \param DateTimeZone $timezone
     *      Timezone to use when exporting dates/times.
     *
     * \param bool $stringTag
     *      Whether to use \<string\> tags at all
     *      when encoding strings.
     *
     * \return
     *      This method does not return any value.
     */
    abstract public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag);
}
