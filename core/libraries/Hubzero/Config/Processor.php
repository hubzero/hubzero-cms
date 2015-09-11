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

namespace Hubzero\Config;

use Hubzero\Error\Exception\InvalidArgumentException;

/**
 * Abstract Registry Processor
 *
 * Based, in part, on Joomla's JRegistry classes
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
