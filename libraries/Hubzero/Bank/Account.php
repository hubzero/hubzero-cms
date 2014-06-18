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

namespace Hubzero\Bank;

/**
 * Table class for bank accounts
 */
class Account extends \JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id       = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $uid      = NULL;

	/**
	 * decimal(11,2)
	 *
	 * @var number
	 */
	var $balance  = NULL;

	/**
	 * decimal(11,2)
	 *
	 * @var number
	 */
	var $earnings = NULL;

	/**
	 * decimal(11,2)
	 *
	 * @var number
	 */
	var $credit   = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__users_points', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->uid) == '')
		{
			$this->setError(\JText::_('Entry must have a user ID.'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record based on user ID and bind results to $this
	 *
	 * @param      integer $oid User ID
	 * @return     boolean True on success
	 */
	public function load_uid($oid=NULL)
	{
		if ($oid === NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uid='$oid'");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}
}

