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
 * @package       hubzero-cms
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright     Copyright 2005-2011 Purdue University. All rights reserved.
 * @license       http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for organization types
 *
 * @package       hubzero-cms
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright     Copyright 2005-2011 Purdue University. All rights reserved.
 * @license       http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
class RegisterOrganizationType extends JTable
{
	/**
	 * Primary key field in the table
	 *
	 * @var		integer
	 */
	public $id = null;

	/**
	 * A normalized key (no spaces or punctuation)
	 *
	 * @var		string
	 */
	public $type = null;

	/**
	 * The title of the organization type
	 *
	 * @var		string
	 */
	public $title = null;

	/**
	 * Object constructor to set table and key field
	 *
	 * @param object $db JDatabase object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xorganization_types', 'id', $db);
	}

	/**
	 * Method for checking that fields are valid before sending to the database
	 *
	 * @return boolean True if the object is ok
	 */
	public function check()
	{
		if (trim($this->type) == '') {
			$this->setError(JText::_('Organization Type must contain text'));
			return false;
		}
		return true;
	}

	/**
	 * Returns a record count
	 *
	 * @param	array	$filters	An associative array of filters used to construct a query
	 * @return	integer
	 */
	public function getCount($filters = array())
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl";
		if (isset($filters['search']) && $filters['search'] != '') {
			$query .= " WHERE type LIKE '%" . $filters['search'] . "%' OR title LIKE '%" . $filters['search'] . "%'";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Returns an array of objects
	 *
	 * @param	array	$filters	An associative array of filters used to construct a query
	 * @return	array
	 */
	public function getRecords($filters = array())
	{
		$query  = "SELECT * FROM $this->_tbl";
		if (isset($filters['search']) && $filters['search'] != '') {
			$query .= " WHERE type LIKE '%" . $filters['search'] . "%' OR title LIKE '%" . $filters['search'] . "%'";
		}
		$query .= " ORDER BY title ASC";
		if (isset($filters['limit']) && $filters['limit'] != 'all') {
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Returns an associative array of types
	 *
	 * @param	array	$filters	An associative array of filters used to construct a query
	 * @return	array
	 */
	public function getTypes($filters = array())
	{
		$types = array();
		if ($records = $this->getRecords($filters)) {
			foreach ($records as $record) {
				$types[$record->type] = stripslashes($record->title);
			}
		}

		return $types;
	}

	/**
	 * Loads a database row into the RegisterOrganizationType object
	 *
	 * @param	string	$type	The organization type field
	 * @return	boolean
	 */
	public function loadType($type = null)
	{
		if ($type === null) {
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE `type`='$type' LIMIT 1");
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind($result);
		} else {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}
}
