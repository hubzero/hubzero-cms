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

namespace Hubzero\Routing\Router;

use Countable;
use Closure;
use Iterator;

/**
 * Iterator class for Rules
 */
class Rules implements Countable, Iterator
{
	/**
	 * Container for data
	 *
	 * @var  array
	 */
	protected $data = array();

	/**
	 * Constructor
	 *
	 * @param   array  $data  Array of data
	 * @return  void
	 */
	public function __construct(array $data = array())
	{
		foreach ($data as $key => $value)
		{
			$this->data[$key] = $this->close($value);
		}
	}

	/**
	 * Wrap value in a Closure
	 *
	 * @param   mixed   $value
	 * @return  object
	 */
	public function close($value)
	{
		if ( ! $value instanceof Closure)
		{
			$value = function() use ($value)
			{
				return $value;
			};
		}

		return $value;
	}

	/**
	 * Add item to the end of the array
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  object
	 */
	public function append($key, $value)
	{
		if (!$this->has($key, $value))
		{
			$this->data[$key] = $this->close($value);
		}

		return $this;
	}

	/**
	 * Add item to the beginning of the array
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  object
	 */
	public function prepend($key, $value)
	{
		if ($this->has($key))
		{
			unset($this->data[$key]);
		}

		$data = array($key => $this->close($value));

		$this->data = $data + $this->data;

		return $this;
	}

	/**
	 * Add item to the array before specificed $idx
	 *
	 * @param   string  $idx
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  object
	 */
	public function insertBefore($idx, $key, $value=null)
	{
		if ($this->has($key))
		{
			$value = $value ?: $this->data[$key];

			unset($this->data[$key]);
		}

		$data = array();
		foreach ($this->data as $k => $v)
		{
			if ($idx == $k)
			{
				$data[$key] = $this->close($value);
			}
			$data[$k] = $v;
		}
		$this->data = $data;

		if (!$this->has($key))
		{
			$this->append($key, $value);
		}

		return $this;
	}

	/**
	 * Add item to the array after specificed $idx
	 *
	 * @param   string  $idx
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  object
	 */
	public function insertAfter($idx, $key, $value=null)
	{
		if ($this->has($key))
		{
			$value = $value ?: $this->data[$key];

			unset($this->data[$key]);
		}

		$data = array();
		foreach ($this->data as $k => $v)
		{
			$data[$k] = $v;
			if ($idx == $k)
			{
				$data[$key] = $this->close($value);
			}
		}
		$this->data = $data;

		if (!$this->has($key))
		{
			$this->append($key, $value);
		}

		return $this;
	}

	/**
	 * Determine if rule exist for a given key
	 *
	 * @param   string   $key
	 * @return  boolean
	 */
	public function has($key)
	{
		return isset($this->data[$key]);
	}

	/**
	 * Get a rule for a specific key
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function get($key)
	{
		if (array_key_exists($key, $this->data))
		{
			return $this->close($this->data[$key]);
		}

		return null;
	}

	/**
	 * Get all of the rules from the bag for a given key
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  mixed
	 */
	public function set($key, $value)
	{
		$this->data[$key] = $this->close($value);

		return $this;
	}

	/**
	 * Get all of the rules
	 *
	 * @return  array
	 */
	public function all()
	{
		return $this->data;
	}

	/**
	 * Get the number of rules
	 *
	 * @return  integer
	 */
	public function count()
	{
		return count($this->data);
	}

	/**
	 * Are there any rules?
	 *
	 * @return  boolean
	 */
	public function any()
	{
		return ($this->count() > 0);
	}

	/**
	 * Clear all rules
	 *
	 * @return  object
	 */
	public function clear()
	{
		$this->data = array();

		return $this;
	}

	/**
	 * Merge a new array of rules into the bag.
	 *
	 * @param   array   $data
	 * @return  object
	 */
	public function merge(array $data)
	{
		$this->data = array_merge($this->data, $data);

		return $this;
	}

	/**
	 * Rewind to beginning of array
	 *
	 * @return  void
	 */
	public function rewind()
	{
		reset($this->data);
	}

	/**
	 * Get current item in the array
	 *
	 * @return  object
	 */
	public function current()
	{
		return current($this->data);
	}

	/**
	 * Get the key of the current item
	 *
	 * @return  string
	 */
	public function key()
	{
		return key($this->data);
	}

	/**
	 * Move to next item in the array
	 *
	 * @return  void
	 */
	public function next()
	{
		next($this->data);
	}

	/**
	 * Is array position valid?
	 *
	 * @return  boolean
	 */
	public function valid()
	{
		return key($this->data) !== null;
	}
}