<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

class TestContainer implements \ArrayAccess
{
    private array $values = [];
    private array $frozen = [];

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->values);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $value = $this->values[$offset];
        if ($value instanceof \Closure) {
            if (!array_key_exists($offset, $this->frozen)) {
                $this->frozen[$offset] = $value($this);
            }
            return $this->frozen[$offset];
        }

        return $value;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset], $this->frozen[$offset]);
    }
}
