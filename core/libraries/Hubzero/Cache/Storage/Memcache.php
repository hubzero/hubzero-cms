<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Storage;

use Hubzero\Error\Exception\RuntimeException;
use Hubzero\Cache\Auditor;

/**
 * Memcache storage for Cache manager
 */
class Memcache extends None
{
    /**
     * Memcached engine
     *
     * @var  \Memcache
     */
    protected $engine;

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
            throw new RuntimeException('Cannot use memcached cache storage. Memcached extension is not loaded.');
        }

        $engine = $this->options['engine'] ?? null;

        if (!$engine) {
            if (!isset($this->options['servers']) || empty($this->options['servers'])) {
                $config = \Hubzero\Facades\App::get('config');
                $this->options['compress'] = $config->get(
                    'memcache_compress',
                    false,
                ) == false ? 0 : MEMCACHE_COMPRESSED;
                $this->options['servers'] = [
                    [
                        'host'    => $config->get('memcache_server_host', 'localhost'),
                        'port'    => $config->get('memcache_server_port', 11211),
                        'persist' => $config->get('memcache_persist', true),
                    ],
                ];
            }

            $engine = $this->connect($this->options['servers']);
        }

        $this->engine = $engine;
    }

    /**
     * Test to see if the cache storage is available.
     *
     * @return  boolean  True on success, false otherwise.
     */
    public static function isAvailable()
    {
        if ((extension_loaded('memcache') && class_exists('Memcache')) != true) {
            return false;
        }
        return true;
    }

    /**
     * Create a new Memcached connection.
     *
     * @param   array<mixed>   $servers
     * @return  \Memcache  \Memcached
     * @throws  \RuntimeException
     */
    public function connect(array $servers)
    {
        $memcache = $this->getEngine();

        // For each server in the array, we'll just extract the configuration and add
        // the server to the Memcached connection. Once we have added all of these
        // servers we'll verify the connection is successful and return it back.
        foreach ($servers as $server) {
            $memcache->addServer(
                $server['host'],
                $server['port'],
                $server['weight'],
            );
        }

        if (!$memcache->getVersion()) {
            throw new RuntimeException('Could not establish Memcached connection.');
        }

        // Memcahed has no list keys, we do our own accounting, initialise key index
        if ($memcache->get($this->options['hash'] . '-index') === false) {
            $empty = [];
            $this->engine->set($this->options['hash'] . '-index', $empty, $this->options['compress'], 0);
        }

        return $memcache;
    }

    /**
     * Get a new Memcached instance.
     *
     * @return  \Memcache  \Memcache
     */
    public function getEngine()
    {
        return new \Memcache();
    }

    /**
     * Add an item to the cache only if it doesn't already exist
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   int|null     $minutes
     * @return  mixed
     */
    public function add($key, $value, $minutes)
    {
        return $this->engine->add($this->id($key), $value, $minutes * 60);
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
        return $this->engine->set($this->id($key), $value, $minutes * 60);
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
        // The memcache extension has no getResultCode() (memcached does;
        // this was copied from that storage and failed on every read): a miss
        // gives false
        $value = $this->engine->get($this->id($key));

        return $value === false ? null : $value;
    }

    /**
     * Check if an item exists in the cache
     *
     * @param   string  $key
     * @return  bool
     */
    public function has($key)
    {
        return $this->engine->get($this->id($key)) !== false;
    }

    /**
     * Remove an item from the cache.
     *
     * @param   string  $key
     * @return  bool
     */
    public function forget($key)
    {
        return $this->engine->delete($this->id($key));
    }

    /**
     * Remove all items from the cache.
     *
     * @param   string|null  $group
     * @return  void
     */
    public function clean($group = null)
    {
        $hash = $this->options['hash'];

        $index = $this->engine->get($hash . '-index');
        if ($index === false) {
            $index = [];
        }

        foreach ($index as $key => $value) {
            if (str_starts_with($value->name, $hash . '-cache-' . $group . '-')) { // xor $mode != 'group')
                $this->engine->delete($value->name, 0);
                unset($index[$key]);
            }
        }

        $this->engine->replace($hash . '-index', $index, 0);
    }

    /**
     * Get all cached data
     *
     * @return  array<mixed>
     */
    public function all()
    {
        $hash  = $this->options['hash'];

        $index = $this->engine->get($hash . '-index');
        if ($index === false) {
            $index = [];
        }

        $data = [];

        foreach ($index as $key) {
            if (empty($key)) {
                continue;
            }

            $namearr = explode('-', $key->name);

            if ($namearr[0] == $hash && $namearr[1] == 'cache') {
                $group = $namearr[2];

                if (!isset($data[$group])) {
                    $item = new Auditor($group);
                } else {
                    $item = $data[$group];
                }

                $item->tally($key->size / 1024);

                $data[$group] = $item;
            }
        }

        return $data;
    }
}
