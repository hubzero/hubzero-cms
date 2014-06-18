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
 * Table class for job types
 */
class JobType extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id       = NULL;

	/**
	 * varchar(150)
	 *
	 * @var string
	 */
	var $category = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_types', 'id', $db);
	}

	/**
	 * Get all records
	 *
	 * @param      string $sortby  Sort by field
	 * @param      string $sortdir Sort direction ASC/DESC
	 * @return     array
	 */
	public function getTypes($sortby = 'id', $sortdir = 'ASC')
	{
		$types = array();

		$query  = "SELECT id, category FROM $this->_tbl ORDER BY $sortby $sortdir ";
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($result)
		{
			foreach ($result as $r)
			{
				$types[$r->id] = $r->category;
			}
		}

		return $types;
	}

	/**
	 * Load a record from the database
	 *
	 * @param      integer $id      Type ID
	 * @param      string  $default Default value to return
	 * @return     string
	 */
	public function getType($id = NULL, $default = 'unspecified')
	{
		if ($id === NULL)
		{
			 return false;
		}
		if ($id == 0)
		{
			return $default;
		}

		$query = "SELECT category FROM $this->_tbl WHERE id=" . $this->_db->Quote($id);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

