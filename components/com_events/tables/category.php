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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		$this->_db->setQuery("UPDATE $this->_tbl SET count = count-1 WHERE id=" . $this->_db->Quote($oid));
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
		$this->_db->setQuery("UPDATE $this->_tbl SET published=1 WHERE id=" . $this->_db->Quote($oid));
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
		$this->_db->setQuery("UPDATE $this->_tbl SET published=0 WHERE id=" . $this->_db->Quote($oid));
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
		$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE extension=" . $this->_db->Quote($section));
		return $this->_db->loadResult();
	}
}

