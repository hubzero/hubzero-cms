<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Pathway;

/**
 * Pathway trail class
 */
class Trail implements \Iterator, \ArrayAccess, \Countable
{
	/**
	 * Container for items
	 *
	 * @var  array
	 */
	private $items = array();

	/**
	 * Cursor position
	 *
	 * @var  integer
	 */
	private $position = 0;

	/**
	 * Create and add an item to the pathway.
	 *
	 * @param   string  $name  The name of the item.
	 * @param   string  $link  The link to the item.
	 * @return  object
	 */
	public function append($name, $link = '')
	{
		$this->items[] = new Item($name, $link);

		return $this;
	}

	/**
	 * Create and prepend an item to the pathway.
	 *
	 * @param   string  $name  The name of the item.
	 * @param   string  $link  The link to the item.
	 * @return  object
	 */
	public function prepend($name, $link = '')
	{
		$b = new Item($name, $link);
		array_unshift($this->items, $b);

		return $this;
	}

	/**
	 * Create and return an array of the crumb names.
	 *
	 * @return  array
	 */
	public function names()
	{
		$names = array();
		foreach ($this->items as $item)
		{
			$names[] = $item->text;
		}

		return array_values($names);
	}

	/**
	 * Return the list of crumbs
	 *
	 * @return  array
	 */
	public function items()
	{
		return $this->items;
	}

	/**
	 * Set an item in the list
	 *
	 * @param   integer  $offset
	 * @param   object   $value
	 * @return  void
	 */
	public function set($offset, $value)
	{
		return $this->offsetSet($offset, $value);
	}

	/**
	 * Get an item from the list
	 *
	 * @param   integer  $offset
	 * @return  mixed
	 */
	public function get($offset)
	{
		return $this->offsetGet($offset);
	}

	/**
	 * Check if an item exists
	 *
	 * @param   integer  $offset
	 * @return  boolean
	 */
	public function has($offset)
	{
		return $this->offsetExists($offset);
	}

	/**
	 * Unset an item
	 *
	 * @param   integer  $offset
	 * @return  void
	 */
	public function forget($offset)
	{
		return $this->offsetUnset($offset);
	}

	/**
	 * Clear out the list of items
	 *
	 * @return  object
	 */
	public function clear()
	{
		$this->items = array();

		return $this;
	}

	/**
	 * Rewind position
	 *
	 * @return  array
	 */
	public function rewind()
	{
		return reset($this->items);
	}

	/**
	 * Return current item
	 *
	 * @return  object
	 */
	public function current()
	{
		return current($this->items);
	}

	/**
	 * Return position key
	 *
	 * @return  integer
	 */
	public function key()
	{
		return key($this->items);
	}

	/**
	 * Return next item
	 *
	 * @return  object
	 */
	public function next()
	{
		return next($this->items);
	}

	/**
	 * Is current position valid?
	 *
	 * @return  voolean
	 */
	public function valid()
	{
		return key($this->items) !== null;
	}

	/**
	 * Check if an item exists
	 *
	 * @param   integer  $offset
	 * @return  boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->items[$offset]);
	}

	/**
	 * Set an item in the list
	 *
	 * @param   integer  $offset
	 * @param   object   $value
	 * @return  void
	 */
	public function offsetSet($offset, $value)
	{
		$this->items[$offset] = $value;
	}

	/**
	 * Get an item from the list
	 *
	 * @param   integer  $offset
	 * @return  mixed
	 */
	public function offsetGet($offset)
	{
		return isset($this->items[$offset]) ? $this->items[$offset] : null;
	}

	/**
	 * Unset an item
	 *
	 * @param   integer  $offset
	 * @return  void
	 */
	public function offsetUnset($offset)
	{
		unset($this->items[$offset]);
	}

	/**
	 * Return a count of the number of items
	 *
	 * @return  integer
	 */
	public function count()
	{
		return count($this->items);
	}
}
