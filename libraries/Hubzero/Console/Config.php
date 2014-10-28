<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console;

use Hubzero\Config\Processor\Yaml;
use Hubzero\Config\Processor\Ini;
use Hubzero\Error\Exception\RuntimeException;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Console configuration class
 **/
class Config
{
	/**
	 * Parsed config vars
	 *
	 * @var array
	 **/
	private $config = array();

	/**
	 * Config file path
	 *
	 * @var string
	 **/
	private $path = null;

	/**
	 * Constructs a new config instance
	 *
	 * Parse for muse configuration file from user home directory
	 *
	 * @return void
	 **/
	public function __construct()
	{
		// Build path
		$home = getenv('HOME');
		$path = $home . DS . '.muse';
		$this->path = $path;

		// See if there's an existing file
		if (is_file($path))
		{
			// Try to parse as Yaml and fall back to Ini if failed
			try
			{
				// Parse the path
				$this->config = Yaml::parse($path);
			}
			catch (RuntimeException $e)
			{
				// Parse the file as Ini
				$this->config = Ini::parse($path);

				// Now write it back out as Yaml for future use
				$this->write();
			}
		}
	}

	/**
	 * Creates a new instance of self
	 *
	 * @return self
	 **/
	public static function getInstance()
	{
		static $instance;

		if (!isset($instance))
		{
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Gets the specified config var
	 *
	 * @param  string $key the key to fetch
	 * @param  mixed  $default the default to return, should the key not exist
	 * @return mixed
	 **/
	public static function get($key, $default=false)
	{
		$instance = self::getInstance();

		return (isset($instance->config[$key])) ? $instance->config[$key] : $default;
	}

	/**
	 * Saves the data to the config file
	 *
	 * Passed data will be merged with existing data.
	 *
	 * @param  array $data the data to save
	 * @return bool
	 **/
	public static function save($data)
	{
		$instance = self::getInstance();

		// Merge and make sure values are unique
		$data = $instance->merge($instance->config, $data);
		$data = $instance->unique($data);

		// Set data back to the instance
		$instance->config = $data;

		// Actually write out the data
		$instance->write();

		return true;
	}

	/**
	 * Writes the data to the configuration file
	 *
	 * @return void
	 **/
	private function write()
	{
		Yaml::write($this->config, $this->path);
	}

	/**
	 * Merge multiple arrays into one, recursively
	 *
	 * Dear future developer who comes in and says, "Why, there's a PHP function for that!
	 * It's called array_merge_recursive".  Don't do it!  This function works slightly 
	 * differently.  Namely, if a nested array is not associative, we want it to append items
	 * to it, rather than completely overwrite the value of the nested element.
	 *
	 * @param  array $existing the existing data
	 * @param  array $incoming the new data
	 * @return array
	 **/
	private function merge($existing, $incoming)
	{
		foreach ($incoming as $k => $v)
		{
			if (is_array($v))
			{
				$existing[$k] = $this->merge($existing[$k], $v);
			}
			else
			{
				if (is_numeric($k))
				{
					$existing[] = $v;
				}
				else
				{
					$existing[$k] = $v;
				}
			}
		}

		return $existing;
	}

	/**
	 * Multi-dimensional array_unique function
	 *
	 * @param  array $var the array to make unique
	 * @return array
	 **/
	private function unique($var)
	{
		if (is_array($var))
		{
			// Serialize vars, unique them, then unserialize
			$var = array_map('unserialize', array_unique(array_map('serialize', $var)));

			foreach ($var as &$sub)
			{
				if (is_array($sub))
				{
					$sub = $this->unique($sub);
				}
			}
		}

		return $var;
	}
}