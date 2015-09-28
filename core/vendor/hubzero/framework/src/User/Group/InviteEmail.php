<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
