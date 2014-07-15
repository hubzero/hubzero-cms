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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Time - hub contacts database class
 */
Class TimeContacts extends JTable
{
	/**
	 * id, primary key
	 *
	 * @var int(11)
	 */
	var $id = null;

	/**
	 * hub name
	 *
	 * @var varchar(255)
	 */
	var $name = null;

	/**
	 * phone number
	 *
	 * @var varchar(255)
	 */
	var $phone = null;

	/**
	 * email address
	 *
	 * @var varchar(255)
	 */
	var $email = null;

	/**
	 * role
	 *
	 * @var varchar(255)
	 */
	var $role = null;

	/**
	 * hub_id that contact is associated with
	 *
	 * @var int(11)
	 */
	var $hub_id = null;

	/**
	 * Constructor
	 *
	 * @param   database object
	 * @return  void
	 */
	function __construct( &$db )
	{
		parent::__construct('#__time_hub_contacts', 'id', $db );
	}

	/**
	 * Override check function to perform validation
	 *
	 * @return boolean true if all checks pass, else false
	 */
	public function check()
	{
		// Trim whitespace from variables
		$this->name  = trim($this->name);
		$this->phone = trim($this->phone);
		$this->email = trim($this->email);
		$this->role  = trim($this->role);

		// Everything passed, return true
		return true;
	}

	/**
	 * Build query
	 *
	 * @param  $filters (not needed yet...)
	 * @return $query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS c";

		return $query;
	}

	/**
	 * Get count of contacts
	 *
	 * @return query result number of contacts
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(c.id)";
		$query .= $this->buildquery();

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get set of contacts
	 *
	 * @param  $filters (examples: hub_id)
	 * @return object list of contacts
	 */
	public function getRecords($filters)
	{
		$query  = "SELECT c.*";
		$query .= $this->buildquery($filters);

		// Only contacts for a certain hub
		if (!empty($filters['hub_id']))
		{
			$query .= " WHERE c.hub_id = ".$this->_db->quote($filters['hub_id']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}