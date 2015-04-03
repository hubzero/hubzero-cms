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

namespace Hubzero\Breadcrumbs;

/**
 * Breadcrumbs class
 */
class Trail implements \Iterator, \ArrayAccess, \Countable
{
	/**
	 * Container for items
	 *
	 * @var  array
	 */
	private $crumbs = array();

	/**
	 * Cursor position
	 *
	 * @var  integer
	 */
	private $position = 0;

	/**
	 * Create and add an item to the pathway.
	 *
	 * @param   string  $text  The name of the item.
	 * @param   string  $url   The link to the item.
	 * @return  object
	 */
	public function append($text, $url = '')
	{
		$this->crumbs[] = new Crumb($text, $url);

		return $this;
	}

	/**
	 * Create and prepend an item to the pathway.
	 *
	 * @param   string  $text  The name of the item.
	 * @param   string  $url   The link to the item.
	 * @return  object
	 */
	public function prepend($text, $url = '')
	{
		$b = new Crumb($text, $url);
		array_unshift($this->crumbs, $b);

		return $this;
	}

	/**
	 * Create and return an array of the pathway names.
	 *
	 * @return  array  Array of names of pathway items
	 */
	public function names()
	{
		$names = array();
		foreach ($this->crumbs as $item)
		{
			$names[] = $item->text;
		}

		return array_values($names);
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
	 * Clear out the list of items
	 *
	 * @return  object
	 */
	public function clear()
	{
		$this->crumbs = array();

		return $this;
	}

	/**
	 * Rewind position
	 *
	 * @return  array
	 */
	public function rewind()
	{
		return reset($this->crumbs);
	}

	/**
	 * Return current item
	 *
	 * @return  object
	 */
	public function current()
	{
		return current($this->crumbs);
	}

	/**
	 * Return position key
	 *
	 * @return  integer
	 */
	public function key()
	{
		return key($this->crumbs);
	}

	/**
	 * Return next item
	 *
	 * @return  object
	 */
	public function next()
	{
		return next($this->crumbs);
	}

	/**
	 * Is current position valid?
	 *
	 * @return  voolean
	 */
	public function valid()
	{
		return key($this->crumbs) !== null;
	}

	/**
	 * Check if an item exists
	 *
	 * @param   integer  $offset
	 * @return  boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->crumbs[$offset]);
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
		$this->crumbs[$offset] = $value;
	}

	/**
	 * Get an item from the list
	 *
	 * @param   integer  $offset
	 * @return  mixed
	 */
	public function offsetGet($offset)
	{
		return isset($this->crumbs[$offset]) ? $this->crumbs[$offset] : null;
	}

	/**
	 * Unset an item
	 *
	 * @param   integer  $offset
	 * @return  void
	 */
	public function offsetUnset($offset)
	{
		unset($this->crumbs[$offset]);
	}

	/**
	 * Return a count of the number of items
	 *
	 * @return  integer
	 */
	public function count()
	{
		return count($this->crumbs);
	}
}
