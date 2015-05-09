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
use Hubzero\Filesystem\Filesystem;
use Hubzero\Utility\Arr;
use stdClass;

/**
 * Registry class
 */
class Registry implements \JsonSerializable, \ArrayAccess, \IteratorAggregate, \Countable
{
	/**
	 * Data container
	 *
	 * @var  object
	 */
	protected $data;

	/**
	 * Path separator
	 *
	 * @var  string
	 */
	public $separator = '.';

	/**
	 * Constructor
	 *
	 * @param   mixed   $data    The data to bind to the new object.
	 * @param   string  $format  Optional format to parse string data as
	 * @return  void
	 */
	public function __construct($data = null, $format = null)
	{
		// Instantiate the internal data object.
		$this->data = new stdClass;

		// Optionally load supplied data.
		if ($data)
		{
			$this->parse($data, $format);
		}
	}

	/**
	 * Magic function to clone the registry object.
	 *
	 * @return  object
	 */
	public function __clone()
	{
		$this->data = unserialize(serialize($this->data));
	}

	/**
	 * Magic function to render this object as a string using default args of toString method.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Gets this object represented as an ArrayIterator.
	 *
	 * This allows the data properties to be accessed via a foreach statement.
	 *
	 * @return  object  This object represented as an ArrayIterator.
	 * @see     IteratorAggregate::getIterator()
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->data);
	}

	/**
	 * Count elements of the data object
	 *
	 * @return  integer  The custom count as an integer.
	 */
	public function count()
	{
		return count(get_object_vars($this->data));
	}

	/**
	 * Implementation for the JsonSerializable interface.
	 * Allows us to pass Registry objects to json_encode.
	 *
	 * @return  object
	 */
	public function jsonSerialize()
	{
		return $this->data;
	}

	/**
	 * Sets a default value if not already assigned.
	 *
	 * @param   string  $key      The name of the parameter.
	 * @param   string  $default  An optional value for the parameter.
	 * @return  string  The value set, or the default if the value was not previously set (or null).
	 */
	public function def($key, $default = '')
	{
		$value = $this->get($key, (string) $default);

		$this->set($key, $value);

		return $value;
	}

	/**
	 * Check if a registry path exists.
	 *
	 * @param   string  $path  Registry path
	 * @return  boolean
	 */
	public function has($path)
	{
		$default = microtime(true);

		return $this->get($path, $default) !== $default;
	}

	/**
	 * Get a registry value.
	 *
	 * @param   string  $path     Registry path (e.g. config.cache.file)
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 * @return  mixed   Value of entry or null
	 */
	public function get($path, $default = null)
	{
		// Return default value if path is empty
		if (empty($path))
		{
			return $default;
		}

		if (!strpos($path, $this->separator))
		{
			return (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '') ? $this->data->$path : $default;
		}

		// Explode the registry path into an array
		$nodes = explode($this->separator, $path);

		// Initialize the current node to be the registry root.
		$node  = $this->data;
		$found = false;

		// Traverse the registry to find the correct node for the result.
		foreach ($nodes as $n)
		{
			if (is_array($node) && isset($node[$n]))
			{
				$node  = $node[$n];
				$found = true;
				continue;
			}

			if (!isset($node->$n))
			{
				return $default;
			}

			$node  = $node->$n;
			$found = true;
		}

		if (!$found || $node === null || $node === '')
		{
			return $default;
		}

		return $node;
	}

	/**
	 * Set a registry value.
	 *
	 * @param   string  $path       Registry Path (e.g. config.cache.file)
	 * @param   mixed   $value      Value of entry
	 * @param   string  $separator  The key separator
	 * @return  object  This method is chainable
	 */
	public function set($path, $value, $separator = null)
	{
		if (empty($separator))
		{
			$separator = $this->separator;
		}

		// Explode the registry path into an array and remove empty
		// nodes that occur as a result of a double separator. ex: joomla..test
		// Finally, re-key the array so they are sequential.
		$nodes = array_values(array_filter(explode($separator, $path), 'strlen'));

		if ($nodes)
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++)
			{
				if (is_object($node))
				{
					if (!isset($node->{$nodes[$i]}) && ($i != $n))
					{
						$node->{$nodes[$i]} = new stdClass;
					}
					// Pass the child as pointer in case it is an object
					$node = &$node->{$nodes[$i]};
					continue;
				}

				if (is_array($node))
				{
					if (!isset($node[$nodes[$i]]) && ($i != $n))
					{
						$node[$nodes[$i]] = new stdClass;
					}
					// Pass the child as pointer in case it is an array
					$node = &$node[$nodes[$i]];
				}
			}
		}

		return $this;
	}

	/**
	 * Load the contents of a file into the registry
	 *
	 * @param   string  $file     Path to file to load
	 * @param   string  $format   Format of the file [optional: defaults to JSON]
	 * @param   mixed   $options  Options used by the formatter
	 * @return  boolean  True on success
	 */
	public function read($file)
	{
		return with(new Filesystem)->get($file);
	}

	/**
	 * Write the contents of the registry to a file
	 *
	 * @param   string  $file     Path to file to load
	 * @param   string  $format   Format of the file [optional: defaults to JSON]
	 * @param   mixed   $options  Options used by the formatter
	 * @return  boolean  True on success
	 */
	public function write($file, $format = 'json', $options = array())
	{
		return with(new Filesystem)->put($file, $this->processor($format)->objectToString($this->data, $options));
	}

	/**
	 * Load a string into the registry
	 *
	 * @param   string   $data     String to load into the registry
	 * @param   string   $format   Format of the string
	 * @param   mixed    $options  Options used by the formatter
	 * @return  boolean  True on success
	 */
	public function parse($data, $format = '', $options = array())
	{
		if (is_array($data) || is_object($data))
		{
			$this->bind($this->data, $data);
		}
		else if (!empty($data) && is_string($data))
		{
			// See if it's a file or a string
			if (is_file($data) && is_readable($data))
			{
				$data = $this->read($data);
			}

			if (!$format)
			{
				foreach ($this->processors() as $name => $processor)
				{
					if ($processor->canParse($data))
					{
						$format = $name;
						
						break;
					}
				}

				if (!$format)
				{
					throw new InvalidArgumentException('JLIB_REGISTRY_EXCEPTION_LOAD_FORMAT_CLASS', 500);
				}
			}

			$obj = $this->processor($format)->stringToObject($data, $options);

			$this->bind($this->data, $obj);
		}

		return true;
	}

	/**
	 * Merge a Registry object into this one
	 *
	 * @param   mixed    $source     Source data to merge.
	 * @param   boolean  $recursive  True to support recursive merge the children values.
	 * @return  boolean  True on success
	 */
	public function merge($source, $recursive = false)
	{
		if (!$source)
		{
			return false;
		}

		// If the source isn't already a Registry
		// we'll turn it into one
		if (!($source instanceof Registry))
		{
			$source = new self($source);
		}

		// Load the variables into the registry's default namespace.
		/*foreach ($source->toArray() as $k => $v)
		{
			if (($v !== null) && ($v !== ''))
			{
				$this->data->$k = $v;
			}
		}*/
		$this->bind($this->data, $source->toArray(), $recursive, false);

		return true;
	}

	/**
	 * Transforms a namespace to an array
	 *
	 * @return  array  An associative array holding the namespace data
	 */
	public function toArray()
	{
		return (array) $this->asArray($this->data);
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @return  object   An an object holding the namespace data
	 */
	public function toObject()
	{
		return $this->data;
	}

	/**
	 * Get a namespace in a given string format
	 *
	 * @param   string  $format   Format to return the string in
	 * @param   mixed   $options  Parameters used by the formatter, see formatters for more info
	 * @return  string  Namespace in string format
	 */
	public function toString($format = 'json', $options = array())
	{
		return $this->processor($format)->objectToString($this->data, $options);
	}

	/**
	 * Get the list of all available processors
	 *
	 * @return  array
	 */
	public function processors()
	{
		return Processor::all();
	}

	/**
	 * Get the processor for a specific format
	 *
	 * @param   string  $format  Format to return the prcoessor for
	 * @return  object
	 */
	public function processor($format = 'json')
	{
		return Processor::instance($format);
	}

	/**
	 * Method to recursively bind data to a parent object.
	 *
	 * @param   object   $parent     The parent object on which to attach the data values.
	 * @param   mixed    $data       An array or object of data to bind to the parent object.
	 * @param   boolean  $recursive  True to support recursive bindData.
	 * @param   boolean  $allowNull  True to allow null values.
	 * @return  void
	 */
	protected function bind($parent, $data, $recursive = true, $allowNull = true)
	{
		// Ensure the input data is an array.
		$data = is_object($data)
			? get_object_vars($data)
			: (array) $data;

		foreach ($data as $k => $v)
		{
			if (!$allowNull && !(($v !== null) && ($v !== '')))
			{
				continue;
			}

			if ($recursive && ((is_array($v) && Arr::isAssociative($v)) || is_object($v)))
			{
				if (!isset($parent->$k))
				{
					$parent->$k = new stdClass;
				}

				$this->bind($parent->$k, $v);
				continue;
			}

			$parent->$k = $v;
		}
	}

	/**
	 * Method to recursively convert an object of data to an array.
	 *
	 * @param   object  $data  An object of data to return as an array.
	 * @return  array   Array representation of the input object.
	 */
	protected function asArray($data)
	{
		$array = array();

		if (is_object($data))
		{
			$data = get_object_vars($data);
		}

		foreach ($data as $k => $v)
		{
			if (is_object($v) || is_array($v))
			{
				$array[$k] = $this->asArray($v);
				continue;
			}
			$array[$k] = $v;
		}

		return $array;
	}

	/**
	 * Method to extract a sub-registry from path
	 *
	 * @param   string  $path  Registry path (e.g. joomla.content.showauthor)
	 * @return  mixed   Registry object if data is present
	 */
	public function extract($path)
	{
		$data = $this->get($path);

		if (is_null($data))
		{
			return null;
		}

		return new self($data);
	}

	/**
	 * Dump to one dimension array.
	 *
	 * @param   string  $separator  The key separator.
	 * @return  array   Dumped array.
	 */
	public function flatten($separator = null)
	{
		$array = array();

		if (empty($separator))
		{
			$separator = $this->separator;
		}

		$this->toFlatten($separator, $this->data, $array);

		return $array;
	}

	/**
	 * Method to recursively convert data to one dimension array.
	 *
	 * @param   string        $separator  The key separator.
	 * @param   array|object  $data       Data source of this scope.
	 * @param   array         &$array     The result array, it is pass by reference.
	 * @param   string        $prefix     Last level key prefix.
	 * @return  void
	 */
	protected function toFlatten($separator = null, $data = null, &$array = array(), $prefix = '')
	{
		$data = (array) $data;

		if (empty($separator))
		{
			$separator = $this->separator;
		}

		foreach ($data as $k => $v)
		{
			$key = $prefix ? $prefix . $separator . $k : $k;

			if (is_object($v) || is_array($v))
			{
				$this->toFlatten($separator, $v, $array, $key);
				continue;
			}

			$array[$key] = $v;
		}
	}

	/**
	 * Determine if the given configuration option exists.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function offsetExists($key)
	{
		return $this->has($key);
	}

	/**
	 * Get a configuration option.
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function offsetGet($key)
	{
		return $this->get($key);
	}

	/**
	 * Set a configuration option.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  void
	 */
	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Unset a configuration option.
	 *
	 * @param   string  $key
	 * @return  void
	 */
	public function offsetUnset($key)
	{
		$this->set($key, null);
	}
}
