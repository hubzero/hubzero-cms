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
 * MemcacheD storage for Cache manager
 */
class Memcached extends None
{
	/**
	 * Memcached engine
	 *
	 * @var  object
	 */
	protected $engine;

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
			throw new RuntimeException('Cannot use memcached cache storage. Memcached extension is not loaded.');
		}

		if (isset($this->options['engine']))
		{
			$this->engine = $this->options['engine'];
		}

		if (!$this->engine)
		{
			if (!isset($this->options['servers']) || empty($this->options['servers']))
			{
				$conf = new \Hubzero\Config\Repository('site');

				$this->options['servers'] = array(
					array(
						'host'   => $config->get('memcache_server_host', 'localhost'),
						'port'   => $config->get('memcache_server_port', 11211),
						'weight' => 1
					)
				);
			}

			$this->engine = $this->connect($this->options['servers']);
		}
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		if ((extension_loaded('memcached') && class_exists('Memcached')) != true)
		{
			return false;
		}
		return true;
	}

	/**
	 * Create a new Memcached connection.
	 *
	 * @param   array   $servers
	 * @return  object  \Memcached
	 * @throws  \RuntimeException
	 */
	public function connect(array $servers)
	{
		$memcached = $this->getEngine();

		// For each server in the array, we'll just extract the configuration and add
		// the server to the Memcached connection. Once we have added all of these
		// servers we'll verify the connection is successful and return it back.
		foreach ($servers as $server)
		{
			$memcached->addServer(
				$server['host'], $server['port'], $server['weight']
			);
		}

		if ($memcached->getVersion() === false)
		{
			throw new RuntimeException("Could not establish Memcached connection.");
		}

		return $memcached;
	}

	/**
	 * Get a new Memcached instance.
	 *
	 * @return  object  \Memcached
	 */
	public function getEngine()
	{
		return new \Memcached;
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
		return $this->engine->add($this->id($key), $value, $minutes * 60);
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
		return $this->engine->set($this->id($key), $value, $minutes * 60);
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
		$value = $this->engine->get($this->id($key));

		if ($this->engine->getResultCode() == 0)
		{
			return $value;
		}

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
		return $this->engine->get($this->id($key)) !== false || $this->engine->getResultCode() != \Memcached::RES_NOTFOUND;
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
	 * @param   string  $group
	 * @return  void
	 */
	public function clean($group = null)
	{
		$hash = $this->options['hash'];

		$index = $this->engine->get($hash . '-index');
		if ($index === false)
		{
			$index = array();
		}

		foreach ($index as $key => $value)
		{
			if (strpos($value->name, $hash . '-cache-' . $group . '-') === 0) // xor $mode != 'group')
			{
				$this->engine->delete($value->name, 0);
				unset($index[$key]);
			}
		}
		$this->engine->replace($hash . '-index', $index, 0);
	}

	/**
	 * Get all cached data
	 *
	 * @return  array
	 */
	public function all()
	{
		$hash  = $this->options['hash'];

		$index = $this->engine->get($hash . '-index');
		if ($index === false)
		{
			$index = array();
		}

		foreach ($index as $key)
		{
			if (empty($key))
			{
				continue;
			}

			$namearr = explode('-', $key->name);

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

				$item->tally($key->size / 1024);

				$data[$group] = $item;
			}
		}

		return $data;
	}
}
