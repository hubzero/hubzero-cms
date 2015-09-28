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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base;

use SeekableIterator;
use Countable;
use ArrayAccess;
use Closure;

/**
 * Iterator class
 */
class ItemList implements SeekableIterator, Countable, ArrayAccess
{
	/**
	 * Current cursor position
	 *
	 * @var  array
	 */
	protected $_pos = 0;

	/**
	 * Current array count
	 *
	 * @var  array
	 */
	protected $_total = 0;

	/**
	 * Container for data
	 *
	 * @var  array
	 */
	protected $_data = array();

	/**
	 * Constructor
	 *
	 * @param      array $data Array of data
	 * @return     void
	 */
	public function __construct($data=null)
	{
		if (is_array($data))
		{
			$this->_data = $data;
		}
		$this->_total = count($this->_data);
	}

	/**
	 * Add item to the array
	 *
	 * @param   mixed  $value
	 * @return  void
	 */
	public function add($value)
	{
		return $this->offsetSet(null, $value);
	}

	/**
	 * Remove item from the array
	 *
	 * @param   mixed  $offset
	 * @return  void
	 */
	public function remove($offset)
	{
		return $this->offsetUnset($offset);
	}

	/**
	 * Reset cursor to starting point
	 *
	 * @return  void
	 */
	public function rewind()
	{
		$this->_pos = 0;
	}

	/**
	 * Reset cursor to starting point and reset list (in case we unset some)
	 *
	 * @return  void
	 */
	public function reset()
	{
		$this->_pos = 0;
		$this->_data = array_values($this->_data);
	}

	/**
	 * Is the current position the first one?
	 *
	 * @return  boolean
	 */
	public function isFirst()
	{
		return !isset($this->_data[$this->_pos - 1]);
	}

	/**
	 * Is the current position the last one?
	 *
	 * @return  boolean
	 */
	public function isLast()
	{
		return !isset($this->_data[$this->_pos + 1]);
	}

	/**
	 * Seek to an absolute position
	 *
	 * @param   integer               $index
	 * @throws  OutOfBoundsException  When the seek position is invalid
	 * @return  void
	 */
	public function seek($index)
	{
		$this->rewind();

		while ($this->_pos < $index && $this->valid())
		{
			$this->next();
		}

		if (!$this->valid())
		{
			throw new \OutOfBoundsException(\Lang::txt('Invalid seek position'));
		}
	}

	/**
	 * Return the current array value if the cursor is at
	 * a valid index
	 *
	 * @return  mixed
	 */
	public function current()
	{
		if ($this->valid())
		{
			return $this->_data[$this->_pos];
		}
		return null;
	}

	/**
	 * Return the array count
	 *
	 * @return  integer
	 */
	public function total()
	{
		return $this->count();
	}

	/**
	 * Return the array count
	 *
	 * @return  integer
	 */
	public function count()
	{
		return $this->_total;
	}

	/**
	 * Return the first array value
	 *
	 * @return  mixed
	 */
	public function first()
	{
		$this->rewind();
		return $this->current();
	}

	/**
	 * Return the last array value
	 *
	 * @return  mixed
	 */
	public function last()
	{
		$this->_pos = ($this->_total - 1);
		return $this->current();
	}

	/**
	 * Return the key for the current cursor position
	 *
	 * @return  mixed
	 */
	public function key()
	{
		return $this->_pos;
	}

	/**
	 * Set cursor position to previous position and return array value
	 *
	 * @return  mixed
	 */
	public function prev()
	{
		--$this->_pos;
		return $this->current();
	}

	/**
	 * Set cursor position to next position and return array value
	 *
	 * @return  mixed
	 */
	public function next()
	{
		++$this->_pos;
		return $this->current();
	}

	/**
	 * Check if the current cursor position is valid
	 *
	 * @return  mixed
	 */
	public function valid()
	{
		return isset($this->_data[$this->_pos]);
	}

	/**
	 * Check if an offset exists
	 *
	 * @param   mixed  $offset
	 * @return  bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_data);
	}

	/**
	 * Get the value of an offset
	 *
	 * @param   mixed  $offset
	 * @return  mixed
	 */
	public function offsetGet($offset)
	{
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

	/**
	 * Append a new item
	 *
	 * @param   mixed  $offset
	 * @param   mixed  $item
	 * @return  void
	 */
	public function offsetSet($offset, $item)
	{
		if ($offset === null)
		{
			$this->_data[] = $item;
			$this->_total = count($this->_data);
		}
		else
		{
			$this->_data[$offset] = $item;
		}
	}

	/**
	 * Unset an item
	 *
	 * @param   mixed  $offset
	 * @return  void
	 */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
		$this->_total = count($this->_data);
	}

	/**
	 * Run a map over each of the items
	 *
	 * @param   object  $callback  Closure
	 * @return  array
	 */
	public function map(Closure $callback)
	{
		return (array_map($callback, $this->_data));
	}

	/**
	 * Run a filter over each of the items
	 *
	 * @param   object  $callback  Closure
	 * @return  array
	 */
	public function filter(Closure $callback)
	{
		return new static(array_filter($this->_data, $callback));
	}

	/**
	 * Merge Item Lists
	 *
	 * @param   object  $data  ItemList
	 * @return  object  ItemList
	 */
	public function merge()
	{
		foreach (func_get_args() as $list)
		{
			if ($list instanceof self)
			{
				$this->_data = array_merge($this->_data, $list->_data);
			}
		}

		return new static($this->_data);
	}

	/**
	 * Reverse data order.
	 * 
	 * @return  object  ItemList
	 */
	public function reverse()
	{
		return new static(array_reverse($this->_data));
	}
}