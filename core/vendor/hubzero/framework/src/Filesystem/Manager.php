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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

/**
 * Hubzero filesystem manager
 */
class Manager
{
	/**
	 * Cached adapters for reuse
	 *
	 * @var  array
	 **/
	private static $adapters = [];

	/**
	 * Parses the location input into mount and mount names
	 *
	 * @param   mixed  $location  The location to parse
	 * @return  array
	 **/
	protected static function parseLocation($location)
	{
		if (is_string($location))
		{
			$locationName  = self::getMountNameFromPath($location);
			$locationMount = self::findMountByName($locationName);
		}
		else
		{
			$locationName  = uniqid('', true);
			$locationMount = $location->getAdapter();
			$location      = $locationName . '://' . $location->getPath();
		}

		return [$location, $locationName, $locationMount];
	}

	/**
	 * Finds a mount by name, irrelevant of params
	 *
	 * @param   string  $name  The mount name to look for
	 * @return  static|bool
	 **/
	protected static function findMountByName($name)
	{
		if (array_key_exists($name, self::$adapters))
		{
			return self::$adapters[$name];
		}

		foreach (self::$adapters as $key => $adapter)
		{
			if (strpos($key, $name) === 0)
			{
				return self::$adapters[$key];
			}
		}

		return false;
	}

	/**
	 * Grabs the mount name from a given path
	 *
	 * @param   string  $path  The path to parse for mount names
	 * @return  void
	 **/
	protected static function getMountNameFromPath($path)
	{
		preg_match('/([[:alpha:]]*):\/\//', $path, $name);

		if (!isset($name[1]) || !$name[1])
		{
			throw new \Exception('Could not determine source mount type', 500);
		}

		return $name[1];
	}

	/**
	 * Grabs the relative path from the given path, removing any mount prefix
	 *
	 * @param   string  $path  The path to un-prefix
	 * @return  string
	 **/
	protected static function getRelativePath($path)
	{
		preg_match('/([[:alpha:]]*):\/\/(.*)/', $path, $name);

		if (isset($name[2]) && $name[2])
		{
			return $name[2];
		}

		return $path;
	}

	/**
	 * Returns the appropriate adapter
	 *
	 * @param   string  $name    The adapter name to instantiate
	 * @param   array   $params  Any initialization parameters
	 * @param   string  $key     A custom key under which to store the adapter
	 * @return  object
	 **/
	public static function adapter($name, $params = [], $key = null)
	{
		$key = $key ?: $name . '.' . md5(serialize($params));

		if (!isset(self::$adapters[$key]))
		{
			// Import filesystem plugins
			Plugin::import('filesystem');

			// Get the adapter
			$plugin  = 'plgFilesystem' . ucfirst($name);
			$adapter = $plugin::init($params);

			self::$adapters[$key] = new Flysystem($adapter);
		}

		// Return the filesystem connection
		return self::$adapters[$key];
	}

	/**
	 * Copys file from one location to another, between mounts
	 *
	 * @param   mixed  $source     The source path or object
	 * @param   mixed  $dest       The destination path or object
	 * @param   bool   $overwrite  Whether or not to overwrite any existing files by the same name
	 * @return  bool
	 **/
	public static function copy($source, $dest, $overwrite = false)
	{
		list($source, $sourceName, $sourceMount) = self::parseLocation($source);
		list($dest,   $destName,   $destMount)   = self::parseLocation($dest);

		// Make sure we got the mounts we need
		if (!$sourceMount) throw new \Exception("'{$sourceMount}' has not been mounted", 500);
		if (!$destMount) throw new \Exception("'{$destMount}' has not been mounted", 500);

		// Check to see if destination already exists
		if ($destMount->has(self::getRelativePath($dest)))
		{
			if ($overwrite)
			{
				$destMount->delete(self::getRelativePath($dest));
			}
			else
			{
				return true;
			}
		}

		// Create mount manager
		$manager = new \League\Flysystem\MountManager([
			$sourceName => $sourceMount,
			$destName   => $destMount
		]);

		// Do copy
		return $manager->copy($source, $dest);
	}

	/**
	 * Creates a filesystem handle to the PHP temp directory
	 *
	 * @param   string  $path  The relative path within the temp dir to use
	 * @return  object
	 **/
	public static function getTempPath($path = '')
	{
		return Entity::fromPath($path, self::adapter('local', ['path' => sys_get_temp_dir()], 'temp'));
	}
}