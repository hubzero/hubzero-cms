<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Htmx\Tests;

class ContainerStub implements \ArrayAccess
{
    private array $values = [];
    private array $resolved = [];

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function get(string $key)
    {
        return $this->offsetGet($key);
    }

    public function set(string $key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists((string) $offset, $this->values);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $key = (string) $offset;
        $value = $this->values[$key] ?? null;

        if ($value instanceof \Closure) {
            if (!array_key_exists($key, $this->resolved)) {
                $this->resolved[$key] = $value($this);
            }

            return $this->resolved[$key];
        }

        return $value;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $key = (string) $offset;
        $this->values[$key] = $value;
        unset($this->resolved[$key]);
    }

    public function offsetUnset(mixed $offset): void
    {
        $key = (string) $offset;
        unset($this->values[$key], $this->resolved[$key]);
    }
}
