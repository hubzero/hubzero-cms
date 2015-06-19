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

namespace Hubzero\Config;

use Hubzero\Error\Exception\InvalidArgumentException;

/**
 * Abstract Registry Processor
 */
abstract class Processor
{
	/**
	 * Registry instances container
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Returns a reference to a Processor object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $type  The format to load
	 * @return  object  Registry format handler
	 * @throws  Exception
	 */
	public static function instance($type)
	{
		// Sanitize format type.
		$type = strtolower(preg_replace('/[^A-Z0-9_]/i', '', $type));

		// Only instantiate the object if it doesn't already exist.
		if (!isset(self::$instances[$type]))
		{
			// Only load the file if the class does not exist.
			$class = __NAMESPACE__ . '\\Processor\\' . ucfirst($type);

			if (!class_exists($class))
			{
				$path = __DIR__ . DS . 'Processor' . DS . $type . '.php';

				if (!is_file($path))
				{
					throw new InvalidArgumentException('JLIB_REGISTRY_EXCEPTION_LOAD_FORMAT_CLASS', 500);
				}

				include_once $path;
			}

			self::$instances[$type] = new $class;
		}

		return self::$instances[$type];
	}

	/**
	 * Return a list of all available processors.
	 *
	 * @return  array
	 */
	public static function all()
	{
		foreach (glob(__DIR__ . DS . 'Processor' . DS . '*.php') as $path)
		{
			$type = basename($path, '.php');

			if (!isset(self::$instances[$type]))
			{
				$class = __NAMESPACE__ . '\\Processor\\' . ucfirst($type);

				if (!class_exists($class))
				{
					include_once $path;
				}

				self::$instances[$type] = new $class;
			}
		}

		return self::$instances;
	}

	/**
	 * Returns an array of allowed file extensions for this parser
	 *
	 * @return  array
	 */
	public function getSupportedExtensions()
	{
		return array();
	}

	/**
	 * Parses a file from `$path` and gets its contents as an array
	 *
	 * @param   string  $path
	 * @return  array
	 */
	public function parse($path)
	{
		return array();
	}

	/**
	 * Try to determine if the data can be parsed
	 *
	 * @param   string   $data
	 * @return  boolean
	 */
	public function canParse($data)
	{
		return false;
	}

	/**
	 * Converts an object into a formatted string.
	 *
	 * @param   object  $object   Data Source Object.
	 * @param   array   $options  An array of options for the formatter.
	 * @return  string  Formatted string.
	 */
	abstract public function objectToString($object, $options = null);

	/**
	 * Converts a formatted string into an object.
	 *
	 * @param   string  $data     Formatted string
	 * @param   array   $options  An array of options for the formatter.
	 * @return  object  Data Object
	 */
	abstract public function stringToObject($data, $options = null);
}
