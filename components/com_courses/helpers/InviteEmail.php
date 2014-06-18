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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Course invite table class
 */
Class CoursesHelperInviteEmail extends JTable
{

	/**
	 * Description for 'id'
	 *
	 * @var unknown
	 */
	var $id = NULL;

	/**
	 * Description for 'email'
	 *
	 * @var unknown
	 */
	var $email = NULL;

	/**
	 * Description for 'gidNumber'
	 *
	 * @var unknown
	 */
	var $gidNumber = NULL;

	/**
	 * Description for 'token'
	 *
	 * @var unknown
	 */
	var $token = NULL;

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__courses_inviteemails', 'id', $db );
	}

	/**
	 * Short description for 'getInviteEmails'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $gid Parameter description (if any) ...
	 * @param      boolean $email_only Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getInviteEmails( $gid, $email_only = false )
	{
		$final = array();
		$db = JFactory::getDBO();

		$sql = "SELECT * FROM $this->_tbl WHERE gidNumber=".$db->Quote($gid);
		$db->setQuery($sql);
		$invitees = $db->loadAssocList();

		if ($email_only)
		{
			foreach ($invitees as $invitee)
			{
				$final[] = $invitee['email'];
			}
		}
		else
		{
			$final = $invitees;
		}

		return $final;
	}


	/**
	 * Short description for 'addInvites'
	 *
	 * Long description (if any) ...
	 *
	 * @param      int $gid Parameter description (if any) ...
	 * @param      array $emails Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function addInvites( $gid, $emails )
	{
		$exists = array();
		$added = array();

		$current = $this->getInviteEmails( $gid, true );

		foreach ($emails as $e)
		{
			if (in_array($e, $current))
			{
				$exists[] = $e;
			}
			else
			{
				$added[] = $e;
			}
		}

		if (count($added) > 0)
		{
			$sql = "INSERT INTO {$this->_tbl}(`email`,`gidNumber`,`token`) VALUES ";
			foreach ($added as $a)
			{
				$sql_values[] = "('".$a."',".$gid.",'".md5($a)."')";
			}
			$sql = $sql . implode(",", $sql_values);
			$db = JFactory::getDBO();
			$db->setQuery($sql);
			$db->query();
		}

		$return['exists'] = $exists;
		$return['added'] = $added;
		return $return;
	}
}
