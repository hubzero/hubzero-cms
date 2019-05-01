<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Exception;
use Lang;

/**
 * Abstract Format for resources elements
 */
abstract class Format
{
	/**
	 * Returns a reference to a Format object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $type  The format to load
	 * @return  object  Registry format handler
	 */
	public static function getInstance($type)
	{
		// Initialize static variable.
		static $instances;

		if (!isset ($instances))
		{
			$instances = array();
		}

		// Sanitize format type.
		$type = strtolower(preg_replace('/[^A-Z0-9_]/i', '', $type));

		// Only instantiate the object if it doesn't already exist.
		if (!isset($instances[$type]))
		{
			// Only load the file the class does not exist.
			$class = __NAMESPACE__ . '\\Format\\' . $type;
			if (!class_exists($class))
			{
				$path = dirname(__FILE__) . '/format/' . $type . '.php';
				if (is_file($path))
				{
					include_once $path;
				}
				else
				{
					throw new Exception(Lang::txt('Format not found.'), 500, E_ERROR);
				}
			}

			$instances[$type] = new $class;
		}
		return $instances[$type];
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
