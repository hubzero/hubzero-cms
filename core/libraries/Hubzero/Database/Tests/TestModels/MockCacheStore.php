<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Mock cache store for testing
 *
 * Implements the minimal interface needed for query caching:
 * get(), put(), forget(), has()
 */
class MockCacheStore implements \Hubzero\Database\CacheStoreInterface
{
    /**
     * Storage for cached items
     *
     * @var  array<mixed>
     **/
    private $storage = [];

    /**
     * Count of get() calls
     *
     * @var  int
     **/
    public $getCount = 0;

    /**
     * Count of put() calls
     *
     * @var  int
     **/
    public $putCount = 0;

    /**
     * Count of forget() calls
     *
     * @var  int
     **/
    public $forgetCount = 0;

    /**
     * Last key used in put()
     *
     * @var  string
     **/
    public $lastKey = '';

    /**
     * Last TTL used in put()
     *
     * @var  int
     **/
    public $lastTtl = 0;

    /**
     * Get a value from cache
     *
     * @param   string  $key
     * @return  mixed
     **/
    public function get(string $key)
    {
        $this->getCount++;
        return $this->storage[$key] ?? null;
    }

    /**
     * Store a value in cache
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   int     $minutes
     * @return  bool
     **/
    public function put(string $key, $value, int $minutes = 0): bool
    {
        $this->putCount++;
        $this->lastKey = $key;
        $this->lastTtl = $minutes;
        $this->storage[$key] = $value;
        return true;
    }

    /**
     * Remove a value from cache
     *
     * @param   string  $key
     * @return  bool
     **/
    public function forget(string $key): bool
    {
        $this->forgetCount++;
        unset($this->storage[$key]);
        return true;
    }

    /**
     * Check if a value exists in cache
     *
     * @param   string  $key
     * @return  bool
     **/
    public function has($key)
    {
        return isset($this->storage[$key]);
    }
}
