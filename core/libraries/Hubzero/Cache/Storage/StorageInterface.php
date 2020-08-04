<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Storage;

/**
 * Cache storage interface
 */
interface StorageInterface
{
	/**
	 * Add cache item. If item already exist in storage return false.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @param   int     $ttl
	 * @return  bool
	 */
	public function add($key, $value, $minutes);

	/**
	 * Set cache item.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @param   int     $ttl
	 * @return  bool
	 */
	public function put($key, $value, $minutes);

	/**
	 * Get cache item
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function get($key);

	/**
	 * Check cache item.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function has($key);

	/**
	 * Delete cache item.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function forget($key);
}
