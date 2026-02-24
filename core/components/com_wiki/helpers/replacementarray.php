<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

/**
 * Wrapper around strtr() for managing replacement arrays
 */
class ReplacementArray
{
    /**
     * Replacement data array
     *
     * @var array
     */
    private $data = [];

    /**
     * Create an object with the specified replacement array.
     * The array should have the same form as the replacement array for strtr().
     *
     * @param  array  $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Set the whole replacement array at once
     *
     * @param  array  $data
     */
    public function setArray(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Get the replacement array
     *
     * @return array
     */
    public function getArray(): array
    {
        return $this->data;
    }

    /**
     * Set a single replacement pair
     *
     * @param  string  $from
     * @param  string  $to
     */
    public function setPair(string $from, string $to): void
    {
        $this->data[$from] = $to;
    }

    /**
     * Merge a raw array into the replacement data
     *
     * @param  array  $data
     */
    public function mergeArray(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Merge another ReplacementArray into this one
     *
     * @param  self  $other
     */
    public function merge(self $other): void
    {
        $this->data = array_merge($this->data, $other->data);
    }

    /**
     * Perform the replacement on the given subject string
     *
     * @param  string  $subject
     * @return string
     */
    public function replace(string $subject): string
    {
        return strtr($subject, $this->data);
    }
}
