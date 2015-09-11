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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\User\Group;

/**
 * Group email invite table class
 */
Class InviteEmail extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_inviteemails', 'id', $db);
	}

	/**
	 * Get a list of email invites for a group
	 *
	 * @param   integer  $gid         Group ID
	 * @param   boolean  $email_only  Resturn only email addresses?
	 * @return  array
	 */
	public function getInviteEmails($gid, $email_only = false)
	{
		$final = array();

		$sql = "SELECT * FROM $this->_tbl WHERE gidNumber=" . $this->_db->quote($gid);
		$this->_db->setQuery($sql);
		$invitees = $this->_db->loadAssocList();

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
	 * Add a list of emails to a group as invitees
	 *
	 * @param   integer  $gid     Group ID
	 * @param   array    $emails  Array of email addresses
	 * @return  array
	 */
	public function addInvites($gid, $emails)
	{
		$exists = array();
		$added  = array();

		$current = $this->getInviteEmails($gid, true);

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
				$sql_values[] = "(" . $this->_db->quote($a) . "," . $this->_db->quote($gid) . "," . $this->_db->quote(md5($a)) . ")";
			}
			$sql = $sql . implode(',', $sql_values);

			$this->_db->setQuery($sql);
			$this->_db->query();
		}

		$return['exists'] = $exists;
		$return['added']  = $added;

		return $return;
	}

	/**
	 * Remove Invite Emails
	 *
	 * @param   integer  $gid     Group ID
	 * @param   array    $emails  Array of email addresses
	 * @return  void
	 */
	public function removeInvites($gid, $emails)
	{
		foreach ($emails as $email)
		{
			$sql = "DELETE FROM {$this->_tbl} WHERE gidNumber=" . $this->_db->quote($gid) . " AND email=" . $this->_db->quote($email);
			$this->_db->setQuery($sql);
			$this->_db->query();
		}
	}
}
