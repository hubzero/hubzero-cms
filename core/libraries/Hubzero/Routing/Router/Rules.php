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