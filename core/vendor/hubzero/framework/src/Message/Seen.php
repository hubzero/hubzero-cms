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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

/**
 * Table class for recording if a user has viewed a message
 */
class Seen extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xmessage_seen', 'uid', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->mid = intval($this->mid);
		if (!$this->mid)
		{
			$this->setError(\Lang::txt('Please provide a message ID.'));
			return false;
		}
		$this->uid = intval($this->uid);
		if (!$this->uid)
		{
			$this->setError(\Lang::txt('Please provide a user ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Load a record by message ID and user ID and bind to $this
	 *
	 * @param   integer  $mid  Message ID
	 * @param   integer  $uid  User ID
	 * @return  boolean  True on success
	 */
	public function loadRecord($mid=NULL, $uid=NULL)
	{
		$mid = $mid ?: $this->mid;
		$uid = $uid ?: $this->uid;

		if (!$mid || !$uid)
		{
			return false;
		}

		return parent::load(array(
			'mid' => $mid,
			'uid' => $uid
		));
	}

	/**
	 * Save a record
	 *
	 * @param   boolean  $new  Create a new record? (updates by default)
	 * @return  boolean  True on success, false on errors
	 */
	public function store($new=false)
	{
		$ret = false;

		if (!$new)
		{
			$this->_db->setQuery("UPDATE $this->_tbl SET whenseen=" . $this->_db->quote($this->whenseen) . " WHERE mid=" . $this->_db->quote($this->mid) . " AND uid=" . $this->_db->quote($this->uid));
			if ($this->_db->query())
			{
				$ret = true;
			}
		}
		else
		{
			$this->_db->setQuery("INSERT INTO $this->_tbl (mid, uid, whenseen) VALUES (" . $this->_db->quote($this->mid) . ", " . $this->_db->quote($this->uid). ", " . $this->_db->quote($this->whenseen) . ")");
			if ($this->_db->query())
			{
				$ret = true;
			}
		}

		if (!$ret)
		{
			$this->setError(__CLASS__ . '::store failed <br />' . $this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

