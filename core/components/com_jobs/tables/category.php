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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

/**
 * Table class for job category
 */
class JobCategory extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_categories', 'id', $db);
	}

	/**
	 * Get all records
	 *
	 * @param      string  $sortby    Field to sort by
	 * @param      string  $sortdir   Sort direction (ASC/DESC)
	 * @param      integer $getobject Return records as objects?
	 * @return     array
	 */
	public function getCats($sortby = 'ordernum', $sortdir = 'ASC', $getobject = 0)
	{
		$cats = array();

		$query  = $getobject ? "SELECT * " : "SELECT id, category ";
		$query .= "FROM $this->_tbl ORDER BY $sortby $sortdir";
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($getobject)
		{
			return $result;
		}

		if ($result)
		{
			foreach ($result as $r)
			{
				$cats[$r->id] = $r->category;
			}
		}

		return $cats;
	}

	/**
	 * Get a category
	 *
	 * @param      itneger $id      Category ID
	 * @param      string  $default Default value if no record found
	 * @return     mixed False if errors, String upon success
	 */
	public function getCat($id = null, $default = 'Unspecified')
	{
		if ($id === null)
		{
			 return false;
		}
		if ($id == 0)
		{
			return $default;
		}

		$query  = "SELECT category FROM $this->_tbl WHERE id=" . $this->_db->quote($id);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Update the ordering of records
	 *
	 * @param      integer $id       Category ID
	 * @param      integer $ordernum ORder number to make it
	 * @return     boolean True upon success
	 */
	public function updateOrder($id = null, $ordernum = 1)
	{
		if ($id == null or !intval($ordernum))
		{
			 return false;
		}

		$query  = "UPDATE $this->_tbl SET ordernum=" . $this->_db->quote($ordernum) . " WHERE id=" . $this->_db->quote($id);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
