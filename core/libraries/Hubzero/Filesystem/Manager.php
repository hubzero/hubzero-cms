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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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