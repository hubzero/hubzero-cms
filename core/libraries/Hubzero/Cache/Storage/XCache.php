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
 * XCache storage for Cache manager
 */
class XCache extends None
{
	/**
	 * Create a new file cache store instance.
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		parent::__construct($options);

		if (!self::isAvailable())
		{
			throw new RuntimeException('Cannot use Apc cache storage. Apc extension is not loaded.');
		}
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		return extension_loaded('xcache');
	}

	/**
	 * Add an item to the cache only if it doesn't already exist
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @param   int     $minutes
	 * @return  void
	 */
	public function add($key, $value, $minutes)
	{
		trigger_error('XCache add method is not monatomic. Please use another cache storage.');

		$key = $this->id($key);

		if (xcache_isset($key))
		{
			return false;
		}

		return $this->put($key, $value, $minutes * 60);
	}

	/**
	 * Store an item in the cache for a given number of minutes.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @param   int     $minutes
	 * @return  void
	 */
	public function put($key, $value, $minutes)
	{
		return xcache_set($this->id($key), $value, $minutes * 60);
	}

	/**
	 * Store an item in the cache indefinitely.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  void
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
		$value = xcache_get($this->id($key));

		if (isset($value))
		{
			return $value;
		}
	}

	/**
	 * Check if an item exists in the cache
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function has($key)
	{
		return xcache_isset($this->id($key));
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function forget($key)
	{
		return xcache_unset($this->id($key));
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @param   string  $group
	 * @return  void
	 */
	public function clean($group = null)
	{
		$hash = $this->options['hash'];

		$allinfo = xcache_list(XC_TYPE_VAR, 0);

		foreach ($allinfo['cache_list'] as $key)
		{
			if (strpos($key['name'], $hash . '-cache-' . $group . '-') === 0) // xor $mode != 'group')
			{
				xcache_unset($key['name']);
			}
		}

		return true;
	}

	/**
	 * Get all cached data
	 *
	 * This requires the php.ini setting xcache.admin.enable_auth = Off.
	 *
	 * @return  array
	 */
	public function all()
	{
		$hash = $this->options['hash'];

		$allinfo = xcache_list(XC_TYPE_VAR, 0);

		$data = array();

		foreach ($allinfo['cache_list'] as $key)
		{
			$namearr = explode('-', $key['name']);

			if ($namearr !== false && $namearr[0] == $secret && $namearr[1] == 'cache')
			{
				$group = $namearr[2];

				if (!isset($data[$group]))
				{
					$item = new Auditor($group);
				}
				else
				{
					$item = $data[$group];
				}

				$item->tally($key['size'] / 1024);

				$data[$group] = $item;
			}
		}

		return $data;
	}
}
