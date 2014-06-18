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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for job category
 */
class JobCategory extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         	= NULL;

	/**
	 * varchar(150)
	 *
	 * @var string
	 */
	var $category		= NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $description	= NULL;

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
	public function getCat($id = NULL, $default = 'unspecified')
	{
		if ($id === NULL)
		{
			 return false;
		}
		if ($id == 0)
		{
			return $default;
		}

		$query  = "SELECT category FROM $this->_tbl WHERE id=" . $this->_db->Quote($id);
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
	public function updateOrder($id = NULL, $ordernum = 1)
	{
		if ($id === NULL or !intval($ordernum))
		{
			 return false;
		}

		$query  = "UPDATE $this->_tbl SET ordernum=" . $this->_db->Quote($ordernum) . " WHERE id=" . $this->_db->Quote($id);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}

