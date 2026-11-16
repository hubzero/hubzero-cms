<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

/**
 * An application (what service providers are given) that only stores and
 * resolves services: none of the real one's set-up runs
 */
class TestContainer extends \Hubzero\Base\Application
{
    /**
     * @var  array<string, mixed>
     */
    private array $values = [];
    /**
     * @var  array<string, mixed>
     */
    private array $frozen = [];

    /**
     * Nothing to set up
     */
    public function __construct() {}

    /**
     * Nothing to tear down: the application's destructor flushes every
     * output buffer, PHPUnit's included
     */
    public function __destruct() {}

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
