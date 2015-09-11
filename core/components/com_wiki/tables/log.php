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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Tables;

use Lang;

/**
 * Wiki table class for logging events
 */
class Log extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wiki_log', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, false if not
	 */
	public function check()
	{
		$this->pid = intval($this->pid);
		if (!$this->pid)
		{
			$this->setError(Lang::txt('COM_WIKI_LOGS_MUST_HAVE_PAGE_ID'));
		}

		$this->uid = intval($this->uid);
		if (!$this->uid)
		{
			$this->setError(Lang::txt('COM_WIKI_LOGS_MUST_HAVE_USER_ID'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Retrieve all entries for a specific page
	 *
	 * @param   integer  $pid  Page ID
	 * @return  array
	 */
	public function getLogs($pid=null)
	{
		$pid = $pid ?: $this->pid;

		if (!$pid)
		{
			return null;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE pid=" . $this->_db->quote($pid) . " ORDER BY `timestamp` DESC");
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete all entries for a specific page
	 *
	 * @param   integer  $pid  Page ID
	 * @return  boolean  True on success
	 */
	public function deleteLogs($pid=null)
	{
		$pid = $pid ?: $this->pid;

		if (!$pid)
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE pid=" . $this->_db->quote($pid));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}

