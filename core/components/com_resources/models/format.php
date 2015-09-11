<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Models;

use Exception;

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
				$path = __DIR__ . '/format/' . $type . '.php';
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