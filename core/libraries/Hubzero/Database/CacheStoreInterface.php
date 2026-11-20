<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * A persistent store for cached query results (Query::setCacheStore())
 */
interface CacheStoreInterface
{
    /**
     * Get a cached value
     *
     * @param   string  $key
     * @return  mixed   The value, or false or null when there is none
     */
    public function get(string $key);

    /**
     * Cache a value
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   int     $minutes  How long to keep it; 0 for as long as the store will
     * @return  bool
     */
    public function put(string $key, $value, int $minutes = 0): bool;

    /**
     * Remove a cached value
     *
     * @param   string  $key
     * @return  bool
     */
    public function forget(string $key): bool;
}
