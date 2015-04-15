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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Utility;

/**
 * Utility class for doing all sorts of odds and ends with arrays.
 */
class Arr
{
	/**
	 * Option to perform case-sensitive sorts.
	 *
	 * @var  mixed  Boolean or array of booleans.
	 */
	protected static $sortCase;

	/**
	 * Option to set the sort direction.
	 *
	 * @var  mixed  Integer or array of integers.
	 */
	protected static $sortDirection;

	/**
	 * Option to set the object key to sort on.
	 *
	 * @var  string
	 */
	protected static $sortKey;

	/**
	 * Option to perform a language aware sort.
	 *
	 * @var  mixed  Boolean or array of booleans.
	 */
	protected static $sortLocale;

	/**
	 * Function to convert array to integer values
	 *
	 * @param   array  &$array   The source array to convert
	 * @param   mixed  $default  A default value (int|array) to assign if $array is not an array
	 * @return  void
	 */
	public static function toInteger(&$array, $default = null)
	{
		if (is_array($array))
		{
			foreach ($array as $i => $v)
			{
				$array[$i] = (int) $v;
			}
		}
		else
		{
			if ($default === null)
			{
				$array = array();
			}
			elseif (is_array($default))
			{
				self::toInteger($default, null);
				$array = $default;
			}
			else
			{
				$array = array((int) $default);
			}
		}
	}

	/**
	 * Utility function to map an array to a stdClass object.
	 *
	 * @param   array   &$array  The array to map.
	 * @param   string  $class   Name of the class to create
	 * @return  object  The object mapped from the given array
	 */
	public static function toObject(&$array, $class = 'stdClass')
	{
		$obj = null;
		if (is_array($array))
		{
			$obj = new $class;
			foreach ($array as $k => $v)
			{
				if (is_array($v))
				{
					$obj->$k = self::toObject($v, $class);
				}
				else
				{
					$obj->$k = $v;
				}
			}
		}
		return $obj;
	}

	/**
	 * Utility function to map an array to a string.
	 *
	 * @param   array    $array         The array to map.
	 * @param   string   $inner_glue    The glue (optional, defaults to '=') between the key and the value.
	 * @param   string   $outer_glue    The glue (optional, defaults to ' ') between array elements.
	 * @param   boolean  $keepOuterKey  True if final key should be kept.
	 * @return  string   The string mapped from the given array
	 */
	public static function toString($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false)
	{
		$output = array();

		if (is_array($array))
		{
			foreach ($array as $key => $item)
			{
				if (is_array($item))
				{
					if ($keepOuterKey)
					{
						$output[] = $key;
					}
					// This is value is an array, go and do it again!
					$output[] = self::toString($item, $inner_glue, $outer_glue, $keepOuterKey);
				}
				else
				{
					$output[] = $key . $inner_glue . '"' . $item . '"';
				}
			}
		}

		return implode($outer_glue, $output);
	}

	/**
	 * Utility function to map an object or array to an array
	 *
	 * @param   mixed    $item     The source object or array
	 * @param   boolean  $recurse  True to recurse through multi-level objects
	 * @param   string   $regex    An optional regular expression to match on field names
	 * @return  array  The array mapped from the given object
	 */
	public static function fromObject($item, $recurse = true, $regex = null)
	{
		if (is_object($item))
		{
			$result = array();
			foreach (get_object_vars($item) as $k => $v)
			{
				if (!$regex || preg_match($regex, $k))
				{
					if ($recurse)
					{
						$result[$k] = self::fromObject($v, $recurse, $regex);
					}
					else
					{
						$result[$k] = $v;
					}
				}
			}
		}
		elseif (is_array($item))
		{
			$result = array();
			foreach ($item as $k => $v)
			{
				$result[$k] = self::fromObject($v, $recurse, $regex);
			}
		}
		else
		{
			$result = $item;
		}
		return $result;
	}

	/**
	 * Extracts a column from an array of arrays or objects
	 *
	 * @param   array   &$array  The source array
	 * @param   string  $index   The index of the column or name of object property
	 * @return  array  Column of values from the source array
	 */
	public static function getColumn(&$array, $index)
	{
		$result = array();

		if (is_array($array))
		{
			$n = count($array);

			for ($i = 0; $i < $n; $i++)
			{
				$item = &$array[$i];

				if (is_array($item) && isset($item[$index]))
				{
					$result[] = $item[$index];
				}
				elseif (is_object($item) && isset($item->$index))
				{
					$result[] = $item->$index;
				}
				// else ignore the entry
			}
		}
		return $result;
	}

	/**
	 * Utility function to return a value from a named array or a specified default
	 *
	 * @param   array   &$array   A named array
	 * @param   string  $name     The key to search for
	 * @param   mixed   $default  The default value to give if no key found
	 * @param   string  $type     Return type for the variable (INT, FLOAT, STRING, WORD, BOOLEAN, ARRAY)
	 * @return  mixed   The value from the source array
	 */
	public static function getValue(&$array, $name, $default = null, $type = '')
	{
		// Initialise variables.
		$result = null;

		if (isset($array[$name]))
		{
			$result = $array[$name];
		}

		// Handle the default case
		if (is_null($result))
		{
			$result = $default;
		}

		// Handle the type constraint
		switch (strtoupper($type))
		{
			case 'INT':
			case 'INTEGER':
				// Only use the first integer value
				@preg_match('/-?[0-9]+/', $result, $matches);
				$result = @(int) $matches[0];
				break;

			case 'FLOAT':
			case 'DOUBLE':
				// Only use the first floating point value
				@preg_match('/-?[0-9]+(\.[0-9]+)?/', $result, $matches);
				$result = @(float) $matches[0];
				break;

			case 'BOOL':
			case 'BOOLEAN':
				$result = (bool) $result;
				break;

			case 'ARRAY':
				if (!is_array($result))
				{
					$result = array($result);
				}
				break;

			case 'STRING':
				$result = (string) $result;
				break;

			case 'WORD':
				$result = (string) preg_replace('#\W#', '', $result);
				break;

			case 'NONE':
			default:
				// No casting necessary
				break;
		}
		return $result;
	}

	/**
	 * Method to determine if an array is an associative array.
	 *
	 * @param   array    $array  An array to test.
	 * @return  boolean  True if the array is an associative array.
	 */
	public static function isAssociative($array)
	{
		if (is_array($array))
		{
			foreach (array_keys($array) as $k => $v)
			{
				if ($k !== $v)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Pivots an array to create a reverse lookup of an array of scalars, arrays or objects.
	 *
	 * @param   array   $source  The source array.
	 * @param   string  $key     Where the elements of the source array are objects or arrays, the key to pivot on.
	 * @return  array   An array of arrays pivoted either on the value of the keys, or an individual key of an object or array.
	 */
	public static function pivot($source, $key = null)
	{
		$result = array();
		$counter = array();

		foreach ($source as $index => $value)
		{
			// Determine the name of the pivot key, and its value.
			if (is_array($value))
			{
				// If the key does not exist, ignore it.
				if (!isset($value[$key]))
				{
					continue;
				}

				$resultKey = $value[$key];
				$resultValue = &$source[$index];
			}
			elseif (is_object($value))
			{
				// If the key does not exist, ignore it.
				if (!isset($value->$key))
				{
					continue;
				}

				$resultKey = $value->$key;
				$resultValue = &$source[$index];
			}
			else
			{
				// Just a scalar value.
				$resultKey = $value;
				$resultValue = $index;
			}

			// The counter tracks how many times a key has been used.
			if (empty($counter[$resultKey]))
			{
				// The first time around we just assign the value to the key.
				$result[$resultKey] = $resultValue;
				$counter[$resultKey] = 1;
			}
			elseif ($counter[$resultKey] == 1)
			{
				// If there is a second time, we convert the value into an array.
				$result[$resultKey] = array(
					$result[$resultKey],
					$resultValue,
				);
				$counter[$resultKey]++;
			}
			else
			{
				// After the second time, no need to track any more. Just append to the existing array.
				$result[$resultKey][] = $resultValue;
			}
		}

		unset($counter);

		return $result;
	}

	/**
	 * Utility function to sort an array of objects on a given field
	 *
	 * @param   array  &$a             An array of objects
	 * @param   mixed  $k              The key (string) or a array of key to sort on
	 * @param   mixed  $direction      Direction (integer) or an array of direction to sort in [1 = Ascending] [-1 = Descending]
	 * @param   mixed  $caseSensitive  Boolean or array of booleans to let sort occur case sensitive or insensitive
	 * @param   mixed  $locale         Boolean or array of booleans to let sort occur using the locale language or not
	 * @return  array  The sorted array of objects
	 */
	public static function sortObjects(&$a, $k, $direction = 1, $caseSensitive = true, $locale = false)
	{
		if (!is_array($locale) or !is_array($locale[0]))
		{
			$locale = array($locale);
		}

		self::$sortCase      = (array) $caseSensitive;
		self::$sortDirection = (array) $direction;
		self::$sortKey       = (array) $k;
		self::$sortLocale    = $locale;

		usort($a, array(__CLASS__, '_sortObjects'));

		self::$sortCase      = null;
		self::$sortDirection = null;
		self::$sortKey       = null;
		self::$sortLocale    = null;

		return $a;
	}

	/**
	 * Callback function for sorting an array of objects on a key
	 *
	 * @param   array    &$a  An array of objects
	 * @param   array    &$b  An array of objects
	 * @return  integer  Comparison status
	 */
	protected static function _sortObjects(&$a, &$b)
	{
		$key = self::$sortKey;

		for ($i = 0, $count = count($key); $i < $count; $i++)
		{
			if (isset(self::$sortDirection[$i]))
			{
				$direction = self::$sortDirection[$i];
			}

			if (isset(self::$sortCase[$i]))
			{
				$caseSensitive = self::$sortCase[$i];
			}

			if (isset(self::$sortLocale[$i]))
			{
				$locale = self::$sortLocale[$i];
			}

			$va = $a->$key[$i];
			$vb = $b->$key[$i];

			if ((is_bool($va) or is_numeric($va)) and (is_bool($vb) or is_numeric($vb)))
			{
				$cmp = $va - $vb;
			}
			elseif ($caseSensitive)
			{
				$cmp = \JString::strcmp($va, $vb, $locale);
			}
			else
			{
				$cmp = \JString::strcasecmp($va, $vb, $locale);
			}

			if ($cmp > 0)
			{

				return $direction;
			}

			if ($cmp < 0)
			{
				return -$direction;
			}
		}

		return 0;
	}

	/**
	 * Multidimensional array safe unique test
	 *
	 * @param   array  $myArray  The array to make unique.
	 * @return  array
	 */
	public static function arrayUnique($myArray)
	{
		if (!is_array($myArray))
		{
			return $myArray;
		}

		foreach ($myArray as &$myvalue)
		{
			$myvalue = serialize($myvalue);
		}

		$myArray = array_unique($myArray);

		foreach ($myArray as &$myvalue)
		{
			$myvalue = unserialize($myvalue);
		}

		return $myArray;
	}
}
