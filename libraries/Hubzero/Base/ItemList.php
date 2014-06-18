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
	 * @var array
	 */
	protected $_pos = 0;

	/**
	 * Current array count
	 *
	 * @var array
	 */
	protected $_total = 0;

	/**
	 * Container for data
	 *
	 * @var array
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
	 * @param      mixed $value
	 * @return     void
	 */
	public function add($value)
	{
		//$this->_data[$this->_total++] = $value;
		return $this->offsetSet(null, $value);
	}

	/**
	 * Remove item from the array
	 *
	 * @param      mixed $offset
	 * @return     void
	 */
	public function remove($offset)
	{
		return $this->offsetUnset($offset);
	}

	/**
	 * Reset cursor to starting point
	 *
	 * @return     void
	 */
	public function rewind()
	{
		$this->_pos = 0;
		//reset($this->_data);
	}

	/**
	 * Reset cursor to starting point and reset list (in case we unset some)
	 *
	 * @return     void
	 */
	public function reset()
	{
		$this->_pos = 0;
		$this->_data = array_values($this->_data);
	}

	/**
	 * Is the current position the first one?
	 *
	 * @return     boolean
	 */
	public function isFirst()
	{
		return !isset($this->_data[$this->_pos - 1]);
	}

	/**
	 * Is the current position the last one?
	 *
	 * @return     boolean
	 */
	public function isLast()
	{
		return !isset($this->_data[$this->_pos + 1]);
	}

	/**
	 * Seek to an absolute position
	 *
	 * @param  int $index
	 * @throws OutOfBoundsException When the seek position is invalid
	 * @return void
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
			throw new \OutOfBoundsException(\JText::_('Invalid seek position'));
		}
	}

	/**
	 * Return the current array value if the cursor is at
	 * a valid index
	 *
	 * @return     mixed
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
	 * @return     integer
	 */
	public function total()
	{
		return $this->count();
	}

	/**
	 * Return the array count
	 *
	 * @return     integer
	 */
	public function count()
	{
		return $this->_total;
	}

	/**
	 * Return the first array value
	 *
	 * @return     mixed
	 */
	public function first()
	{
		$this->rewind();
		return $this->current();
	}

	/**
	 * Return the last array value
	 *
	 * @return     mixed
	 */
	public function last()
	{
		$this->_pos = ($this->_total - 1);
		return $this->current();
	}

	/**
	 * Return the key for the current cursor position
	 *
	 * @return     mixed
	 */
	public function key()
	{
		return $this->_pos;
	}

	/**
	 * Set cursor position to previous position and return array value
	 *
	 * @return     mixed
	 */
	public function prev()
	{
		--$this->_pos;
		return $this->current();
	}

	/**
	 * Set cursor position to next position and return array value
	 *
	 * @return     mixed
	 */
	public function next()
	{
		++$this->_pos;
		return $this->current();
	}

	/**
	 * Check if the current cursor position is valid
	 *
	 * @return     mixed
	 */
	public function valid()
	{
		return isset($this->_data[$this->_pos]);
	}

	/**
	 * Check if an offset exists
	 *
	 * @param  mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_data);
	}

	/**
	 * Get the value of an offset
	 *
	 * @param  mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

	/**
	 * Append a new item
	 *
	 * @param  mixed $offset
	 * @param  mixed $item
	 * @return void
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
	 * @param  mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
		$this->_total = count($this->_data);
	}

	/**
	 * Run a map over each of the items
	 *
	 * @param  Closure  $callback
	 * @return array
	 */
	public function map(Closure $callback)
	{
		return (array_map($callback, $this->_data));
	}

	/**
	 * Merge Item Lists
	 *
	 * @param  object $data Hubzero\Base\ItemList
	 * @return Hubzero\Base\ItemList
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
}