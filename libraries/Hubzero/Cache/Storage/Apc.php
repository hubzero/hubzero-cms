<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Cache\Storage;

use Hubzero\Error\Exception\RuntimeException;

/**
 * APC storage for Cache manager
 */
class Apc extends None
{
	/**
	 * Indicates if APCu is supported.
	 *
	 * @var bool
	 */
	protected $apcu = false;

	/**
	 * Create a new file cache store instance.
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		parent::__construct($options);

		$this->apcu = function_exists('apcu_fetch');

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
		return extension_loaded('apc');
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
		$key = $this->id($key);
		$seconds = $minutes * 60;

		return $this->apcu ? apcu_add($key, $value, $seconds) : apc_add($key, $value, $seconds);
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
		$key = $this->id($key);
		$seconds = $minutes * 60;

		return $this->apcu ? apcu_store($key, $value, $seconds) : apc_store($key, $value, $seconds);
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
		$key = $this->id($key);
		return $this->apcu ? apcu_fetch($key) : apc_fetch($key);
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
		return $this->apcu ? apcu_exists($key) : apc_exists($key);
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
		return $this->apcu ? apcu_delete($key) : apc_delete($key);
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @param   string  $group
	 * @return  void
	 */
	public function clean($group = null)
	{
		//$this->apcu ? apcu_clear_cache() : apc_clear_cache('user');

		$hash = $this->options['hash'];

		$allinfo = $this->apcu ? apcu_cache_info() : apc_cache_info('user');

		foreach ($allinfo['cache_list'] as $key)
		{
			if (strpos($key['info'], $hash . '-cache-' . $group . '-') === 0) // xor $mode != 'group')
			{
				$this->apcu ? apcu_delete($key['info']) : apc_delete($key['info']);
			}
		}
		return true;
	}
}
