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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for member addresses
 */
class MembersAddress extends JTable
{
	/**
	 * Object constructor to set table and key field
	 *
	 * @param   object  $db  JDatabase object
	 * @return  void
	 */
	public function __construct($db)
	{
		parent::__construct('#__xprofiles_address', 'id', $db);
	}

	/**
	 * Method for checking that fields are valid before sending to the database
	 *
	 * @return  boolean  True if the object is ok
	 */
	public function check()
	{
		if (!isset($this->uidNumber) || $this->uidNumber == '')
		{
			$this->setError( JText::_('You must supply a user id.') );
			return false;
		}

		return true;
	}

	/**
	 * Method to verify we can delete address
	 *
	 * @param   unknown  $pk
	 * @param   unknown  $joins
	 * @return  boolean
	 */
	public function canDelete($pk = NULL, $joins = NULL)
	{
		return true;
	}

	/**
	 * Method to get addressed for member
	 *
	 * @param   integer  $uidNumber  Member User Id
	 * @return  array
	 */
	public function getAddressesForMember($uidNumber)
	{
		// Make sure we have a user id
		if (!isset($uidNumber))
		{
			$this->setError(JText::_('You must supply a user id.'));
			return false;
		}

		// Query database for addresses for user id
		$sql = "SELECT * FROM {$this->_tbl} WHERE uidNumber=" . $this->_db->quote($uidNumber);
		$this->_db->setQuery($sql);

		return $this->_db->loadObjectList();
	}
}
