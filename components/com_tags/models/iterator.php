<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Courses model class for a course
 */
class TagsModelIterator implements Iterator
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_pos = 0;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_total = 0;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_data = array();

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($data)
	{
		if (is_array($data))
		{
			$this->_data = $data;
		}
		$this->_total = count($this->_data);
	}

	/**
	 * Reset cursor to starting point
	 *
	 * @return     void
	 */
	public function add($value) 
	{
		$this->_data[$this->_total++] = $value;
	}

	/**
	 * Reset cursor to starting point
	 *
	 * @return     void
	 */
	public function rewind() 
	{
		$this->_pos = 0;
	}

	/**
	 * Fetch an array value
	 * Accepts either a numerical index value or string for 
	 * previous or next record based on current cursor position
	 *
	 * @return     mixed
	 */
	public function fetch($key) 
	{
		// Get cursor position
		$cur = $this->_pos;
		// Is it a number?
		if (is_numeric($key))
		{
			// Set the cursor
			$this->_pos = $key;
			// Get current item
			$res = $this->current();
		}
		// Is it a string?
		else if (is_string($key))
		{
			$key = strtolower(trim($key));
			switch ($key)
			{
				// First record
				case 'first':
					$this->first();
					$res = $this->current();
				break;
				// Previous record from the cursor point
				case 'prev':
					$this->prev();
					$res = $this->current();
				break;
				// Next record from the cursor point
				case 'next':
					$this->next();
					$res = $this->current();
				break;
				// Last record
				case 'last':
					$this->last();
					$res = $this->current();
				break;
				// FAIL!
				default:
					$res = null;
				break;
			}
		}
		// Reset cursor to starting position
		$this->_pos = $cur;
		// Return result
		return $res;
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
	 * Return the current array value if the cursor is at a 
	 * valid index
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
	}

	/**
	 * Return the last array value
	 *
	 * @return     mixed
	 */
	public function last() 
	{
		$this->_pos = ($this->_total - 1);
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
	}

	/**
	 * Set cursor position to next position and return array value
	 *
	 * @return     mixed
	 */
	public function next() 
	{
		++$this->_pos;
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
}

