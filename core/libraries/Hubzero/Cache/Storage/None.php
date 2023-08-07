<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Storage;

/**
 * Null storage for Cache manager
 */
class None implements StorageInterface
{
	/**
	 * The file cache directory
	 *
	 * @var string
	 */
	protected $options = array();

	/**
	 * Create a new file cache store instance.
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		$this->options = array_merge($this->options, $options);

		if (!isset($this->options['client']))
		{
			$this->options['client'] = '';
		}
		if (!isset($this->options['language']))
		{
			$this->options['language'] = 'en-GB';
		}

		if (!isset($this->options['hash']))
		{
			$config = new \Hubzero\Config\Repository('site');
			$this->options['hash'] = md5($config->get('secret', 'secret'));
		}
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		return true;
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
		return false;
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
		return false;
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
		return false;
	}

	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function get($key)
	{
		return null;
	}

	/**
	 * Check if an item exists in the cache
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function has($key)
	{
		return false;
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function forget($key)
	{
		return true;
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @param   string  $group
	 * @return  void
	 */
	public function clean($group = null)
	{
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @return  boolean
	 */
	public function gc()
	{
		return true;
	}

	/**
	 * Get all cached data
	 *
	 * @return  array
	 */
	public function all()
	{
		return array();
	}

	/**
	 * Get a cache_id string from an id/group pair
	 *
	 * @param   string  $id  The cache data id
	 * @return  string  The cache id string
	 */
	protected function id($key)
	{
		$parts = explode('.', $key);
		$group = array_shift($parts);
		$name  = implode('.', $parts);

		$id = md5($this->options['client'] . '-' . $name . '-' . $this->options['language']);

		return $this->options['hash'] . '-cache-' . $group . '-' . $id;
	}
}
