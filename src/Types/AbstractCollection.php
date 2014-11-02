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

abstract class AbstractCollection extends \fpoirotte\XRL\Types\AbstractType implements \ArrayAccess, \Iterator, \Countable
{
    protected $index = 0;

    public function __toString()
    {
        return print_r($this->get(), true);
    }

    public function count()
    {
        return count($this->value);
    }

    public function & offsetGet($offset)
    {
        return $this->value[$offset];
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->value);
    }

    public function current()
    {
        return $this->value[$this->key()];
    }

    public function next()
    {
        $this->index++;
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function valid()
    {
        return ($this->index >= 0 && $this->index < count($this->value));
    }
}
