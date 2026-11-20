<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Storage;

use Hubzero\Error\Exception\RuntimeException;
use Hubzero\Cache\Auditor;

/**
 * APC storage for Cache manager, backed by the APCu extension
 *
 * The original APC extension does not exist for PHP 7 and later; APCu is its
 * user cache, so the handler keeps its name and uses APCu.
 */
class Apc extends None
{
    /**
     * Create a new file cache store instance.
     *
     * @param   array<mixed>  $options
     * @return  void
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (!self::isAvailable()) {
            throw new RuntimeException('Cannot use Apc cache storage. APCu extension is not loaded.');
        }
    }

    /**
     * Test to see if the cache storage is available.
     *
     * @return  boolean  True on success, false otherwise.
     */
    public static function isAvailable()
    {
        return extension_loaded('apcu');
    }

    /**
     * Add an item to the cache only if it doesn't already exist
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   int|null     $minutes
     * @return  bool
     */
    public function add($key, $value, $minutes)
    {
        $key = $this->id($key);
        $seconds = $minutes * 60;

        return apcu_add($key, $value, $seconds);
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   int|null     $minutes
     * @return  bool
     */
    public function put($key, $value, $minutes)
    {
        $key = $this->id($key);
        $seconds = $minutes * 60;

        return apcu_store($key, $value, $seconds);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param   string  $key
     * @param   mixed   $value
     * @return  bool
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param   string  $key
     * @return  mixed
     */
    public function get($key)
    {
        $key = $this->id($key);
        return apcu_fetch($key);
    }

    /**
     * Check if an item exists in the cache
     *
     * @param   string  $key
     * @return  bool
     */
    public function has($key)
    {
        $key = $this->id($key);
        return apcu_exists($key);
    }

    /**
     * Remove an item from the cache.
     *
     * @param   string  $key
     * @return  bool
     */
    public function forget($key)
    {
        $key = $this->id($key);

        // Like the other stores, an item that is not there counts as removed
        if (!apcu_exists($key)) {
            return true;
        }

        return apcu_delete($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @param   string|null  $group
     * @return  bool
     */
    public function clean($group = null)
    {
        $hash = $this->options['hash'];

        $allinfo = apcu_cache_info() ?: [];

        foreach ($allinfo['cache_list'] as $key) {
            if (str_starts_with($key['info'], $hash . '-cache-' . $group . '-')) { // xor $mode != 'group')
                apcu_delete($key['info']);
            }
        }
        return true;
    }

    /**
     * Force garbage collect expired cache data as items are removed only on fetch!
     *
     * @return  bool
     */
    public function gc()
    {
        $hash = $this->options['hash'];

        $allinfo = apcu_cache_info() ?: [];

        foreach ($allinfo['cache_list'] as $key) {
            // Fetching an expired entry is what removes it
            if (str_starts_with($key['info'], $hash . '-cache-')) {
                apcu_fetch($key['info']);
            }
        }

        return true;
    }

    /**
     * Get all cached data
     *
     * @return  array<mixed>
     */
    public function all()
    {
        $allinfo = apcu_cache_info() ?: [];

        $keys = $allinfo['cache_list'];

        $hash = $this->options['hash'];

        $data = [];

        foreach ($keys as $key) {
            $name    = $key['info'];
            $namearr = explode('-', $name);

            if ($namearr[0] == $hash && $namearr[1] == 'cache') {
                $group = $namearr[2];

                if (!isset($data[$group])) {
                    $item = new Auditor($group);
                } else {
                    $item = $data[$group];
                }

                $item->tally($key['mem_size'] / 1024);

                $data[$group] = $item;
            }
        }

        return $data;
    }
}
