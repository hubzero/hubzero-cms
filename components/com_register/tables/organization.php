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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for organizations
 *
 * @package       hubzero-cms
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright     Copyright 2005-2011 Purdue University. All rights reserved.
 * @license       http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
class RegisterOrganization extends JTable
{

	/**
	 * Primary key field in the table
	 *
	 * @var		integer
	 */
	public $id = null;

	/**
	 * The title of the organization
	 *
	 * @var		string
	 */
	public $organization = null;

	/**
	 * Object constructor to set table and key field
	 *
	 * @param object $db JDatabase object
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xorganizations', 'id', $db);
	}

	/**
	 * Method for checking that fields are valid before sending to the database
	 *
	 * @return boolean True if the object is ok
	 */
	public function check()
	{
		if (trim($this->organization) == '') {
			$this->setError(JText::_('Organization must contain text'));
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
			$query .= " WHERE organization LIKE '%" . $filters['search'] . "%'";
		}

		$this->_db->setQuery( $query );
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
			$query .= " WHERE organization LIKE '%" . $filters['search'] . "%'";
		}
		$query .= " ORDER BY organization ASC";
		if (isset($filters['limit']) && $filters['limit'] != 'all') {
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Returns an array of organizations
	 *
	 * @param	array	$filters	An associative array of filters used to construct a query
	 * @return	array
	 */
	public function getOrgs($filters = array())
	{
		$orgs = array();
		if ($records = $this->getRecords($filters)) {
			foreach ($records as $record) {
				$orgs[] = $record->organization;
			}
		}

		return $orgs;
	}
}
