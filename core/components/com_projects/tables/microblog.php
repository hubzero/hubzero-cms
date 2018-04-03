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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Table class for project updates
 */
class Blog extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_microblog', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (!$this->projectid)
		{
			$this->setError(Lang::txt('Missing project ID.'));
			return false;
		}
		if (trim($this->blogentry) == '')
		{
			$this->setError(Lang::txt('Please provide content.'));
			return false;
		}
		if (!$this->posted_by)
		{
			$this->setError(Lang::txt('Missing creator ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Get items
	 *
	 * @param   integer  $projectid
	 * @param   array    $filters
	 * @param   integer  $id
	 * @return  mixed
	 */
	public function getEntries($projectid = null, $filters=array(), $id = 0)
	{
		if ($projectid === null)
		{
			return false;
		}
		$pc = new \Components\Projects\Tables\Comment($this->_db);

		$query = "SELECT m.*, (SELECT COUNT(*) FROM " . $pc->getTableName()." AS c
			WHERE c.itemid=m.id AND c.tbl='blog')
			AS comments, u.name " . $this->_buildQuery($projectid, $filters, $id);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Check if identical entry is made (prevents duplicates on multiple 'save' click)
	 *
	 * @param   integer  $uid
	 * @param   integer  $projectid
	 * @param   string   $entry
	 * @param   string   $today
	 * @return  mixed    integer or null
	 */
	public function checkDuplicate($uid = 0, $projectid = 0, $entry = null, $today = null)
	{
		if (!$projectid || !$uid)
		{
			return false;
		}
		$query = "SELECT id FROM $this->_tbl WHERE posted_by="
				. $this->_db->quote($uid) . " AND projectid="
				. $this->_db->quote($projectid)
				. " AND blogentry=" . $this->_db->quote($entry)
				. " AND posted  LIKE " . $this->_db->quote($today .  '%');
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Build query
	 *
	 * @param   integer  $projectid
	 * @param   array    $filters
	 * @param   integer  $id
	 * @return  string
	 */
	private function _buildQuery($projectid = 0, $filters = array(), $id = 0)
	{
		if (!$projectid)
		{
			return false;
		}
		$query  = "FROM $this->_tbl AS m,
					#__users AS u
					WHERE m.projectid=" . $this->_db->quote($projectid) . " AND m.posted_by=u.id ";

		if ($id)
		{
			$query .= " AND m.id=" . $this->_db->quote($id);
		}
		else
		{
			if (isset($filters['posted_by']) && $filters['posted_by'] != 0)
			{
				$query .= " AND m.posted_by=" . intval($filters['posted_by']);
			}
			if (isset($filters['managers_only']) && $filters['managers_only'] != 0)
			{
				$query .= " AND m.managers_only=1";
			}
			if (isset($filters['activityid']) && $filters['activityid'] != 0)
			{
				$query .= " AND m.activityid=" . intval($filters['activityid']);
			}
			if (isset($filters['search']) && $filters['search'] != '')
			{
				$filters['search'] = strtolower(stripslashes($filters['search']));
				$query .= " AND (LOWER(m.blogentry) LIKE " . $this->_db->quote('%' . $filters['search'] . '%') . ")";
			}
		}
		$query .= " AND m.state != 2";
		if (isset($filters['order']) && $filters['order'] != '')
		{
			$query .= " ORDER BY " . $filters['order'];
		}
		else
		{
			$query .= " ORDER BY m.posted DESC";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " LIMIT " . intval($filters['start']) . ", " . intval($filters['limit']);
		}
		return $query;
	}

	/**
	 * Get item count
	 *
	 * @param   integer  $projectid
	 * @param   array    $filters
	 * @return  integer
	 */
	public function getCount($projectid = 0, $filters=array())
	{
		if (!$projectid)
		{
			return false;
		}
		$filters['limit'] = 0;
		$query = "SELECT COUNT(*) " . $this->_buildQuery($projectid, $filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Delete item
	 *
	 * @param   integer  $id
	 * @param   boolean  $permanent
	 * @return  boolean  True on success
	 */
	public function deletePost($id = 0, $permanent = 0)
	{
		if (!$id)
		{
			$id = $this->id;
		}
		if (!$id)
		{
			return false;
		}

		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE id=" . $this->_db->quote($id);

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete items
	 *
	 * @param   integer  $projectid
	 * @param   boolean  $permanent
	 * @return  boolean  True on success
	 */
	public function deletePosts($projectid = 0, $permanent = 0)
	{
		if (!$projectid)
		{
			$projectid = $this->projectid;
		}
		if (!$projectid)
		{
			return false;
		}

		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE projectid=" . intval($projectid);

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
