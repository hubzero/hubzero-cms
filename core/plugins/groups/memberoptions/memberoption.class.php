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
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

