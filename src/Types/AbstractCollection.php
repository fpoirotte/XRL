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
 *      An abstract XML-RPC type representing
 *      a collection of values.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
abstract class AbstractCollection extends \fpoirotte\XRL\Types\AbstractType implements
    \ArrayAccess,
    \Iterator,
    \Countable
{
    /// Current index in the collection.
    protected $index = 0;

    /**
     * Represent the collection as a string.
     *
     * \retval string
     *      String representation of the collection.
     */
    public function __toString()
    {
        return print_r($this->get(), true);
    }

    /**
     * Return the number of items in the collection.
     *
     * \retval int
     *      Items count.
     */
    public function count()
    {
        return count($this->value);
    }

    /**
     * Get an item from the collection
     * based on its index.
     *
     * \param mixed $offset
     *      Index of the item to retrieve.
     *
     * \retval mixed
     *      Item at the given index.
     */
    public function & offsetGet($offset)
    {
        return $this->value[$offset];
    }

    /**
     * Check whether the specified index
     * exists within the collection.
     *
     * \param mixed $offset
     *      Index to test.
     *
     * \retval bool
     *      \c true if the index exists
     *      in the collection, \c false
     *      otherwise.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->value);
    }

    /**
     * Return the item at the current position
     * in the collection.
     *
     * \retval mixed
     *      Item at the current position.
     */
    public function current()
    {
        return $this->value[$this->key()];
    }

    /**
     * Move the collection's cursor forward.
     *
     * \return
     *      This method does not return any value.
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Reset the position of the collection's cursor.
     *
     * \return
     *      This method does not return any value.
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Test whether the collection's cursor
     * points to a valid position.
     *
     * \retval bool
     *      \c true if the current position
     *      is valid, \c false otherwise.
     */
    public function valid()
    {
        return ($this->index >= 0 && $this->index < count($this->value));
    }
}
