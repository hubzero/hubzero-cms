<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Storage;

use Hubzero\Error\Exception\RuntimeException;
use Hubzero\Cache\Auditor;
use DirectoryIterator;

/**
 * File storage for Cache manager
 */
class File extends None
{
	/**
	 * The file cache directory
	 *
	 * @var  string
	 */
	protected $directory;

	/**
	 * A list of files to ignore
	 *
	 * @var  array
	 */
	protected static $skip = array(
		'.svn',
		'cvs',
		'.ds_store',
		'__macosx',
		'index.html',
		'site.css',
		'site.less.cache'
	);

	/**
	 * Create a new file cache store instance.
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		parent::__construct($options);

		if (!isset($this->options['chmod']))
		{
			$this->options['chmod'] = null;
		}

		$this->directory = $this->cleanPath($this->options['cachebase']);

		if (!is_dir($this->directory))
		{
			mkdir($this->directory, 0775);
		}

		if (!is_dir($this->directory) || !is_readable($this->directory) || !is_writable($this->directory))
		{
			throw new RuntimeException('Cache path should be directory with available read/write access.');
		}
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		$conf = new \Hubzero\Config\Repository('site');
		return is_writable($conf->get('cache_path', PATH_APP . '/cache'));
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
		$file = $this->path($key);

		$data = array(
			'time'  => time(),
			'value' => $value,
			'ttl'   => $this->expiration($minutes)
		);

		return $this->writeCacheFile($file, $data);
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
		$file = $this->path($key);

		if (!file_exists($file))
		{
			return null;
		}

		$data = @unserialize(file_get_contents($file));

		if (!$data)
		{
			throw new RuntimeException('Cache file is invalid.');
		}

		if ($this->isDataExpired($data))
		{
			$this->forget($key);
			return null;
		}

		return $data['value'];
	}

	/**
	 * Check if an item exists in the cache
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function has($key)
	{
		$file = $this->path($key);

		if (!file_exists($file))
		{
			return false;
		}

		$data = @unserialize(file_get_contents($file));

		if (!$data)
		{
			throw new RuntimeException('Cache file is invalid.');
		}

		if ($this->isDataExpired($data))
		{
			$this->forget($key);
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
		$file = $this->path($key);

		if (file_exists($file))
		{
			return unlink($file);
		}

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
		$path = $this->directory . ($group ? DIRECTORY_SEPARATOR . $group : '');
		$root = ($path == $this->directory);

		if (is_dir($path))
		{
			foreach (new DirectoryIterator($path) as $file)
			{
				if (!$root || (!$file->isDot() && !in_array(strtolower($file->getFilename()), static::$skip)))
				{
					if ($file->isDir())
					{
						//$this->clean(($group ? $group . DIRECTORY_SEPARATOR : '') . $file->getFilename());
					}
					else
					{
						unlink($file->getPathname());
					}
				}
			}

			if (!$root)
			{
				@rmdir($path);
			}
		}
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @param   string  $group
	 * @return  void
	 */
	public function gc($group = null)
	{
		$result = true;

		$path = $this->directory . ($group ? DIRECTORY_SEPARATOR . $group : '');

		if (is_dir($path))
		{
			foreach (new DirectoryIterator($path) as $file)
			{
				if ($file->isDot() || in_array(strtolower($file->getFilename()), static::$skip))
				{
					continue;
				}

				if ($file->isDir())
				{
					$result = $this->gc($file->getFilename());

					if (!$result)
					{
						break;
					}

					continue;
				}

				if (!file_exists($file->getPathname()))
				{
					continue;
				}

				$data = @unserialize(file_get_contents($file->getPathname()));

				if (!$data)
				{
					throw new RuntimeException(sprintf('Cache file "%s" is invalid.', $file->getPathname()));
				}

				if ($this->isDataExpired($data))
				{
					$result = unlink($file->getPathname());
				}
			}
		}

		return $result;
	}

	/**
	 * Get all cached data
	 *
	 * @return  array
	 */
	public function all()
	{
		$path = $this->directory;

		$data = array();

		$dirIterator = new DirectoryIterator($path);

		foreach ($dirIterator as $folder)
		{
			if ($folder->isDot() || !$folder->isDir())
			{
				continue;
			}

			$name = $folder->getFilename();

			$item = new Auditor($name);

			$files = new DirectoryIterator($path . DIRECTORY_SEPARATOR . $name);

			foreach ($files as $file)
			{
				if ($file->isDot() || $file->isDir() || in_array(strtolower($file->getFilename()), static::$skip))
				{
					continue;
				}

				$item->tally(filesize($path . '/' . $name . '/' . $file->getFilename()) / 1024);
			}

			$data[$name] = $item;
		}

		return $data;
	}

	/**
	 * Get the expiration time based on the given minutes.
	 *
	 * @param   integer  $minutes
	 * @return  integer
	 */
	protected function expiration($minutes)
	{
		if ($minutes === 0) return 9999999999;

		return time() + ($minutes * 60);
	}

	/**
	 * Get the working directory of the cache.
	 *
	 * @return  string
	 */
	public function getDirectory()
	{
		return $this->directory;
	}

	/**
	 * Get the full path for the given cache key.
	 *
	 * @param   string  $key
	 * @return  string
	 */
	protected function path($key)
	{
		$parts = explode('.', $key);

		$path = array_shift($parts);
		$path = $this->directory . ($path ? DIRECTORY_SEPARATOR . $this->cleanPath($path) : '');

		return $path . DIRECTORY_SEPARATOR . $this->id($key) . '.php';
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

	/**
	 * Get the full path for the given cache key.
	 *
	 * @param   string  $key
	 * @return  string
	 */
	protected function writeCacheFile($filename, $data)
	{
		$dir = pathinfo($filename, PATHINFO_DIRNAME);

		if (!file_exists($dir))
		{
			$mod = $this->options['chmod'] ? $this->options['chmod'] : 0777;
			mkdir($dir, $mod);
		}

		$isNew  = !file_exists($filename);
		$result = file_put_contents($filename, serialize($data), LOCK_EX) !== false;

		if ($isNew && $result !== false && $this->options['chmod'])
		{
			chmod($filename, $this->options['chmod']);
		}

		return $result;
	}

	/**
	 * Check if the given data is expired
	 *
	 * @param   array    $data
	 * @return  boolean
	 */
	protected function isDataExpired(array $data)
	{
		return $data['ttl'] !== 0 && time() > $data['ttl'];
	}
}
