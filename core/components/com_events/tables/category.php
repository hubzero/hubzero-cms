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

namespace Components\Events\Tables;

/**
 * Events table class for category
 */
class Category extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__categories', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		// check for valid name
		if (trim($this->title) == '')
		{
			$this->_error = Lang::txt('EVENTS_CATEGORY_MUST_HAVE_TITLE');
			return false;
		}
		return true;
	}

	/**
	 * Update the count of an entry
	 *
	 * @param      integer $oid Category ID
	 * @return     void
	 */
	public function updateCount($oid=NULL)
	{
		if ($oid == NULL)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET count = count-1 WHERE id=" . $this->_db->quote($oid));
		$this->_db->query();
	}

	/**
	 * Set en entry to unpublished
	 *
	 * @param      integer $oid Category ID
	 * @return     void
	 */
	public function publish($oid = NULL, $state = 1, $userId = 0)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET published=1 WHERE id=" . $this->_db->quote($oid));
		$this->_db->query();
	}

	/**
	 * Set an entry to published
	 *
	 * @param      integer $oid Category ID
	 * @return     void
	 */
	public function unpublish($oid=NULL)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET published=0 WHERE id=" . $this->_db->quote($oid));
		$this->_db->query();
	}

	/**
	 * Get a count of categories in a section
	 *
	 * @param      integer $section Section ID
	 * @return     integer
	 */
	public function getCategoryCount($section=NULL)
	{
		if (!$section)
		{
			$section = $this->section;
		}
		$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE extension=" . $this->_db->quote($section));
		return $this->_db->loadResult();
	}
}

