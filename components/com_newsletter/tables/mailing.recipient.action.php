<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Newsletter\Tables;

/**
 * Table class for recipient actions
 */
class MailingRecipientAction extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  $db  Database Object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__newsletter_mailing_recipient_actions', 'id', $db);
	}

	/**
	 * Get newsletter mailing actions
	 *
	 * @param   integer  $id  ID of mailing action
	 * @return  array
	 */
	public function getActions($id = null)
	{
		$sql = "SELECT * FROM {$this->_tbl}";

		if (isset($id) && $id != '')
		{
			$sql .= " WHERE id=" . $this->_db->quote($id);
		}

		$sql .= " ORDER BY date DESC";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get unconverted mailing actions
	 *
	 * @return  array
	 */
	public function getUnconvertedActions()
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE (ipLATITUDE = '' OR ipLATITUDE IS NULL OR ipLONGITUDE = '' OR ipLONGITUDE IS NULL)";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get mailing actions for mailing ID
	 *
	 * @param   integer  $mailingid  Mailing ID #
	 * @param   string   $action     Mailing Action
	 * @return 	array
	 */
	public function getMailingActions($mailingid, $action = 'open')
	{
		$sql = "SELECT * FROM {$this->_tbl}
				WHERE mailingid=" . $this->_db->quote($mailingid) . "
				AND action=" . $this->_db->quote($action);
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Check to see if mailing action already exists for mailing ID and email
	 *
	 * @param   integer  $mailingid  Mailing ID #
	 * @param   string   $email      Email Address
	 * @param   string   $action     Mailing Action
	 * @return  boolean
	 */
	public function actionExistsForMailingAndEmail($mailingid, $email, $action = 'open')
	{
		$sql = "SELECT * FROM {$this->_tbl}
				WHERE mailingid=" . $this->_db->quote($mailingid) ."
				AND email=" . $this->_db->quote($email) . "
				AND action=" . $this->_db->quote($action);
		$this->_db->setQuery($sql);
		$result = $this->_db->loadObject();
		return (is_object($result)) ? true : false;
	}
}