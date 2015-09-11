<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Description for '"GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION"'
 */
define('GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION', 'receive-forum-email');

/**
 * Groups member options table class
 */
class GroupsTableMemberoption extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_memberoption', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid, False if not
	 */
	public function check()
	{
		if (trim($this->gidNumber) == '')
		{
			$this->setError(Lang::txt('Please provide a gidNumber'));
		}

		if (trim($this->userid) == '')
		{
			$this->setError(Lang::txt('Please provide a userid'));
		}

		if (trim($this->optionname) == '')
		{
			$this->setError(Lang::txt('Please provide an optionname'));
		}

		if (trim($this->optionvalue) == '')
		{
			$this->setError(Lang::txt('Please provide an optionvalue'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $userid
	 * @param   string   $optionname
	 * @return  boolean
	 */
	public function loadRecord($gidNumber=NULL, $userid=NULL, $optionname=NULL)
	{
		if (!$gidNumber)
		{
			$gidNumber = $this->gidNumber;
		}

		if (!$userid)
		{
			$usuerid = $this->userid;
		}

		if (!$optionname)
		{
			$optionname = $this->optionname;
		}

		if (!$gidNumber || !$userid || !$optionname)
		{
			return false;
		}

		$sql = "SELECT * FROM $this->_tbl WHERE userid='$userid' AND gidNumber='$gidNumber' and optionname='$optionname'";


		$this->_db->setQuery($sql);
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

