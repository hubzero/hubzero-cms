<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config;

use Hubzero\Error\Exception\InvalidArgumentException;
use Hubzero\Config\Exception\ParseException;

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
				foreach (self::all() as $inst)
				{
					if (in_array($type, $inst->getSupportedExtensions()))
					{
						$class = get_class($inst);
					}
				}

				if (!class_exists($class))
				{
					throw new InvalidArgumentException(sprintf('Unable to load format class for format "%s"', $type), 500);
				}
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
		foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . 'Processor' . DIRECTORY_SEPARATOR . '*.php') as $path)
		{
			$type = strtolower(basename($path, '.php'));

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
	/**
	 * Make sure a config file can actually be read before parsing it
	 *
	 * Deliberately opens the file rather than asking is_readable(). That
	 * function tests the real user id, and under sudo the real id stays root
	 * while the effective id is whatever the process dropped to, so it reports
	 * success for files the parse will then fail to open.
	 *
	 * @param   string  $path
	 * @return  void
	 * @throws  ParseException  If the file is missing or cannot be opened
	 */
	protected function assertReadable($path)
	{
		if (!is_file($path))
		{
			throw new ParseException(array('message' => 'Config file not found: ' . $path));
		}

		$handle = @fopen($path, 'r');

		if ($handle === false)
		{
			throw new ParseException(array(
				'message' => 'Config file is not readable by the current user: ' . $path
			));
		}

		fclose($handle);
	}

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
