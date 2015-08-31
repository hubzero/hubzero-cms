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
use Hubzero\Cache\Auditor;

/**
 * Eaccelerator storage for Cache manager
 */
class Eaccelerator extends None
{
	/**
	 * Data container
	 *
	 * @var  array
	 */
	protected $data = array();

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
			throw new RuntimeException('Cannot use Eaccelerator cache storage. Eaccelerator extension is not loaded.');
		}
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		return (extension_loaded('eaccelerator') && function_exists('eaccelerator_get'));
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
		if ($this->has($key))
		{
			return false;
		}

		return $this->put($key, $value, $minutes);
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
		return eaccelerator_put($this->id($key), $value, $minutes * 60);
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
		return eaccelerator_get($this->id($key));
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

		if (eaccelerator_get($key) !== null)
		{
			return false;
		}

		return true;
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function forget($key)
	{
		return eaccelerator_rm($this->has($key));
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @param   string  $group
	 * @return  void
	 */
	public function clean($group = null)
	{
		$keys = eaccelerator_list_keys();

		if (is_array($keys))
		{
			$hash = $this->options['hash'];

			foreach ($keys as $key)
			{
				// Trim leading ":" to work around list_keys namespace bug in eAcc.
				// This will still work when bug is fixed.
				$key['name'] = ltrim($key['name'], ':');

				if (strpos($key['name'], $hash . '-cache-' . $group . '-') === 0) // xor $mode != 'group')
				{
					eaccelerator_rm($key['name']);
				}
			}
		}

		return true;
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function gc()
	{
		return eaccelerator_gc();
	}

	/**
	 * Get all cached data
	 *
	 * @return  array
	 */
	public function getAll()
	{
		$keys = eaccelerator_list_keys();

		$hash = $this->options['hash'];

		$data = array();

		foreach ($keys as $key)
		{
			// Trim leading ":" to work around list_keys namespace bug in eAcc.
			// This will still work when bug is fixed.
			$name    = ltrim($key['name'], ':');
			$namearr = explode('-', $name);

			if ($namearr !== false && $namearr[0] == $hash && $namearr[1] == 'cache')
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
