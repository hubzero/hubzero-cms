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
 * CacheLite storage for Cache manager
 */
class CacheLite extends None
{
	/**
	 * CahceLite engine
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
			throw new RuntimeException('Cannot use CacheLite cache storage. CacheLite extension is not loaded.');
		}

		$this->directory = $this->cleanPath($this->options['cachebase']);

		if (!is_dir($this->directory) || !is_readable($this->directory) || !is_writable($this->directory))
		{
			throw new RuntimeException('Cache path should be directory with available read/write access.');
		}

		if (isset($this->options['engine']))
		{
			$this->engine = $this->options['engine'];
		}

		if (!$this->engine)
		{
			$cloptions = array(
				'cacheDir'                => $this->directory . DS,
				'lifeTime'                => isset($this->options['lifetime']) ? $this->options['lifetime'] : 15,
				'fileLocking'             => isset($this->options['locking']) ? $this->options['locking'] : false,
				'automaticCleaningFactor' => isset($this->options['autoclean']) ? $this->options['autoclean'] : 200,
				'fileNameProtection'      => false,
				'hashedDirectoryLevel'    => 0,
				'caching'                 => true
			);

			$this->engine = $this->getEngine($options);
		}
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		@include_once 'Cache' . DS . 'Lite.php';

		if (class_exists('Cache_Lite'))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get a new Cache_Lite instance
	 *
	 * @param   array   $options
	 * @return  object  Cache_Lite
	 */
	public function getEngine($options = array())
	{
		return new Cache_Lite($options);
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
		@list($group, $name) = $this->id($key);

		$dir = $this->directory . DS . $group;

		// If the folder doesn't exist try to create it
		if (!is_dir($dir))
		{
			// Make sure the index file is there
			$indexFile = $dir . DS . 'index.html';
			@mkdir($dir) && file_put_contents($indexFile, '<!DOCTYPE html><title></title>');
		}

		// Make sure the folder exists
		if (!is_dir($dir))
		{
			return false;
		}

		$this->engine->setOption('cacheDir', $this->directory . DS . $group . DS);

		return $this->engine->save($value, $name, $group);
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
		@list($group, $name) = $this->id($key);

		$this->engine->setOption('cacheDir', $this->directory . DS . $group . DS);

		return $this->engine->get($name, $group);
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
		@list($group, $name) = $this->id($key);

		$this->engine->setOption('cacheDir', $this->directory . DS . $group . DS);

		$success = $this->engine->remove($name, $group);

		if ($success == true)
		{
			return $success;
		}

		return false;
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @param   string  $group
	 * @return  void
	 */
	public function clean($group = null)
	{
		$success = true;

		if (is_dir($this->directory . DS . $group))
		{
			$this->engine->setOption('cacheDir', $this->directory . DS . $group . DS);
			$success = $this->engine->clean($group, 'group');
		}

		if ($success == true)
		{
			return $success;
		}

		return false;
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function gc()
	{
		$this->engine->setOption('automaticCleaningFactor', 1);
		$this->engine->setOption('hashedDirectoryLevel', 1);

		$success1 = $this->engine->clean($this->directory . DS, false, 'old');

		if (!($dh = opendir($this->directory . DS)))
		{
			return false;
		}

		$result = true;

		while ($file = readdir($dh))
		{
			if ($file != '.' && $file != '..' && $file != '.svn')
			{
				$file2 = $this->directory . DS . $file;

				if (is_dir($file2))
				{
					$result = ($result and $this->engine->clean($file2 . DS, false, 'old'));
				}
			}
		}

		$success = ($success1 && $result);

		return $success;
	}

	/**
	 * Get the full path for the given cache key.
	 *
	 * @param   string  $key
	 * @return  array
	 */
	protected function id($key)
	{
		$parts = explode('.', $key);

		$name  = array_pop($parts);
		$group = implode('.', $parts);

		return array($group, $name);
	}

	/**
	 * Strip additional / or \ in a path name
	 *
	 * @param   string  $path  The path to clean
	 * @param   string  $ds    Directory separator (optional)
	 * @return  string  The cleaned path
	 */
	protected function cleanPath($path, $ds = DIRECTORY_SEPARATOR)
	{
		$path = trim($path);

		// Remove double slashes and backslahses and convert
		// all slashes and backslashes to DIRECTORY_SEPARATOR
		return preg_replace('#[/\\\\]+#', $ds, $path);
	}
}
