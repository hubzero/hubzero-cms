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
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Description for '"GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION"'
 */
define('GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION', 'receive-forum-email');

/**
 * Groups member options table class
 */
class GroupsTableMemberoption extends JTable
{
	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $gidNumber = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $userid = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $optionname = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $optionvalue = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_memberoption', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid, False if not
	 */
	public function check()
	{
		if (trim($this->gidNumber) == '')
		{
			$this->setError(JText::_('Please provide a gidNumber'));
			return false;
		}

		if (trim($this->userid) == '')
		{
			$this->setError(JText::_('Please provide a userid'));
			return false;
		}

		if (trim($this->optionname) == '')
		{
			$this->setError(JText::_('Please provide an optionname'));
			return false;
		}

		if (trim($this->optionvalue) == '')
		{
			$this->setError(JText::_('Please provide an optionvalue'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      unknown $gidNumber Parameter description (if any) ...
	 * @param      unknown $userid Parameter description (if any) ...
	 * @param      unknown $optionname Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
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

