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
 * Table class for mailing recipients
 */
class MailingRecipient extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  $db  Database Object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__newsletter_mailing_recipients', 'id', $db);
	}

	/**
	 * Get newsletter mailing actions
	 *
	 * @param   integer  $mid     ID of mailing
	 * @param   string   $status  Status
	 * @return  array
	 */
	public function getRecipients($mid, $status = 'all')
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE mid=" . $this->_db->quote($mid);
		if ($status != 'all')
		{
			$sql .= " AND status=" . $this->_db->quote($status);
		}
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Count newsletter mailing actions
	 *
	 * @param   integer  $mid     ID of mailing
	 * @param   string   $status  Status
	 * @return  integer
	 */
	public function countRecipients($mid, $status = 'all')
	{
		$sql = "SELECT COUNT(*) FROM {$this->_tbl} WHERE mid=" . $this->_db->quote($mid);
		if ($status != 'all')
		{
			$sql .= " AND status=" . $this->_db->quote($status);
		}
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}