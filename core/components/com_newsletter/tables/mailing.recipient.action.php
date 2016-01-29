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
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * Count mailing actions for mailing ID
	 *
	 * @param   integer  $mailingid  Mailing ID #
	 * @param   string   $action     Mailing Action
	 * @return 	integer
	 */
	public function countMailingActions($mailingid, $action = 'open')
	{
		$sql = "SELECT COUNT(*) FROM {$this->_tbl}
				WHERE mailingid=" . $this->_db->quote($mailingid) . "
				AND action=" . $this->_db->quote($action);
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
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