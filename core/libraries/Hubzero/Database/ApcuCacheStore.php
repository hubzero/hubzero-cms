<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * APCu-backed cache store adapter for query persistent caching.
 */
class ApcuCacheStore
{
    /**
     * Determine whether APCu extension functions are available.
     *
     * @return  bool
     */
    public static function isAvailable(): bool
    {
        return function_exists('apcu_fetch')
            && function_exists('apcu_store')
            && function_exists('apcu_delete');
    }

    /**
     * Get a value from APCu.
     *
     * @param   string  $key
     * @return  mixed
     */
    public function get(string $key)
    {
        $success = false;
        $result = apcu_fetch($key, $success);

        return $success ? $result : null;
    }

    /**
     * Store a value in APCu.
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   int     $minutes
     * @return  bool
     */
    public function put(string $key, $value, int $minutes = 0): bool
    {
        $seconds = max(0, $minutes * 60);

        return apcu_store($key, $value, $seconds);
    }

    /**
     * Remove a value from APCu.
     *
     * @param   string  $key
     * @return  bool
     */
    public function forget(string $key): bool
    {
        return apcu_delete($key);
    }

    /**
     * Check whether a key exists in APCu.
     *
     * @param   string  $key
     * @return  bool
     */
    public function has(string $key): bool
    {
        $success = false;
        apcu_fetch($key, $success);

        return $success;
    }
}

