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

namespace Components\Blog\Tables;

use Lang;
use Date;
use User;

/**
 * Blog Comment database class
 */
class Comment extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__blog_comments', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->content = trim($this->content);
		if ($this->content == '')
		{
			$this->setError(Lang::txt('COM_BLOG_ERROR_PROVIDE_CONTENT='));
		}

		if (!$this->entry_id)
		{
			$this->setError(Lang::txt('COM_BLOG_ERROR_MISSING_ENTRY_ID'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->created_by)
		{
			$this->created_by = User::get('id');
		}
		if (!$this->id)
		{
			$this->state   = ($this->state !== null ? $this->state : 1);
			$this->created = Date::toSql();
		}
		else
		{
			$this->modified    = Date::toSql();
			$this->modified_by = User::get('id');
		}

		return true;
	}

	/**
	 * Get a record from the database and bind it to this
	 *
	 * @param   integer  $entry_id  Blog entry
	 * @param   integer  $user_id   User ID
	 * @return  boolean  True if record found
	 */
	public function loadUserComment($entry_id, $user_id)
	{
		return parent::load(array(
			'entry_id'   => (int) $entry_id,
			'created_by' => (int) $user_id
		));
	}

	/**
	 * Get all comments off another comment on an entry
	 *
	 * @param   integer  $entry_id  Blog entry
	 * @param   integer  $parent    Parent comment
	 * @return  array
	 */
	public function getComments($entry_id=NULL, $parent=NULL)
	{
		if (!$entry_id)
		{
			$entry_id = $this->entry_id;
		}
		/*if (!$parent)
		{
			$parent = 0;
		}*/

		$sql  = "SELECT * FROM $this->_tbl WHERE entry_id=" . $this->_db->quote($entry_id);
		if (!is_null($parent))
		{
			$sql .= " AND parent=" . $this->_db->quote($parent);
		}
		$sql .= " ORDER BY created ASC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all comments on an entry
	 *
	 * @param   integer  $entry_id  Blog entry
	 * @param   array    $filters   Extra filters to apply to the query
	 * @return  array
	 */
	public function getAllComments($entry_id=NULL, $filters=array())
	{
		if (!$entry_id)
		{
			$entry_id = $this->entry_id;
		}

		$comments = array();

		$filters['entry_id'] = $entry_id;

		$rows = $this->getEntries($filters);
		if ($rows)
		{
			$children = array(
				0 => array()
			);

			$levellimit = 500;

			foreach ($rows as $v)
			{
				$v->replies = 0;

				$pt      = $v->parent;
				$list    = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}

			$comments = $this->_treeRecurse($children[0], $children);
		}
		return $comments;
	}

	/**
	 * Recursive function to build tree
	 *
	 * @param   integer  $id        Parent ID
	 * @param   string   $indent    Indent text
	 * @param   array    $list      List of records
	 * @param   array    $children  Container for parent/children mapping
	 * @param   integer  $maxlevel  Maximum levels to descend
	 * @param   integer  $level     Indention level
	 * @param   integer  $type      Indention type
	 * @return  void
	 */
	private function _treeRecurse($children, $list, $maxlevel=9999, $level=0)
	{
		if ($level <= $maxlevel)
		{
			foreach ($children as $v => $child)
			{
				if (isset($list[$child->id]))
				{
					$children[$v]->replies = $this->_treeRecurse($list[$child->id], $list, $maxlevel, $level+1);
				}
			}
		}
		return $children;
	}

	/**
	 * Delete descendants of a comment
	 *
	 * @param   integer  $id  Parent of comments to delete
	 * @return  boolean  True if comments were deleted
	 */
	public function deleteChildren($id=NULL)
	{
		if (!$id)
		{
			$id = $this->id;
		}

		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE parent=" . $this->_db->quote($id));
		$comments = $this->_db->loadObjectList();
		if ($comments)
		{
			foreach ($comments as $row)
			{
				// Delete children
				$this->_db->setQuery("DELETE FROM $this->_tbl WHERE parent=" . $this->_db->quote($row->id));
				if (!$this->_db->query())
				{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
			$this->_db->setQuery("DELETE FROM $this->_tbl WHERE parent=" . $this->_db->quote($id));
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Set the state of a comment and all descendants
	 *
	 * @param   integer  $id     ID of parent comment
	 * @param   integer  $state  State to set (0=unpublished, 1=published, 2=trashed)
	 * @return  boolean  true if successful otherwise returns and error message
	 */
	public function setState($oid=null, $state=0)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$oid = intval($oid);

		if (!$this->setDescendantState($oid, $state))
		{
			return false;
		}

		$this->_db->setQuery("UPDATE $this->_tbl SET state=" . $this->_db->quote($state) . " WHERE id=" . $this->_db->quote($oid));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Set the state of descendants of a comment
	 *
	 * @param   integer  $id     ID of parent comment
	 * @param   integer  $state  State to set (0=unpublished, 1=published, 2=trashed)
	 * @return  boolean  true if successful otherwise returns and error message
	 */
	public function setDescendantState($id=NULL, $state=0)
	{
		if (is_array($id))
		{
			$id = array_map('intval', $id);
			$id = implode(',', $id);
		}
		else
		{
			$id = intval($id);
		}

		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE parent IN ($id)");
		$rows = $this->_db->loadColumn();
		if ($rows && count($rows) > 0)
		{
			$state = intval($state);
			$rows = array_map('intval', $rows);
			$id = implode(',', $rows);

			$this->_db->setQuery("UPDATE $this->_tbl SET state=$state WHERE parent IN ($id)");
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return $this->setDescendantState($rows, $state);
		}
		return true;
	}

	/**
	 * Return a count of entries based off of filters passed
	 * Used for admin interface
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getEntriesCount($filters=array())
	{
		$filters['limit'] = 0;
		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get entries based off of filters passed
	 * Used for admin interface
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getEntries($filters=array())
	{
		$query = "SELECT c.*, u.name " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from filters passed
	 * Used for admin interface
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	private function _buildQuery($filters)
	{
		$query  = "FROM $this->_tbl AS c, #__xprofiles AS u";

		$where = array(
			"c.created_by=u.uidNumber"
		);

		if (isset($filters['created_by']) && (int) $filters['created_by'] != 0)
		{
			$where[] = "c.created_by=" . $this->_db->quote(intval($filters['created_by']));
		}
		if (isset($filters['modified_by']) && (int) $filters['modified_by'] != 0)
		{
			$where[] = "c.modified_by=" . $this->_db->quote(intval($filters['modified_by']));
		}
		if (isset($filters['entry_id']) && (int) $filters['entry_id'] != 0)
		{
			$where[] = "c.entry_id=" . $this->_db->quote(intval($filters['entry_id']));
		}
		if (isset($filters['parent']))
		{
			$where[] = "c.parent=" . $this->_db->quote(intval($filters['parent']));
		}
		if (isset($filters['state']))
		{
			if (is_array($filters['state']))
			{
				$filters['state'] = array_map('intval', $filters['state']);
				$where[] = "c.state IN (" . implode(',', $filters['state']) . ")";
			}
			else if ($filters['state'] >= 0)
			{
				$where[] = "c.state=" . $this->_db->quote(intval($filters['state']));
			}
		}
		if (isset($filters['anonymous']))
		{
			$where[] = "c.anonymous=" . $this->_db->quote(intval($filters['anonymous']));
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "LOWER(c.content) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'created';
		}
		if (!isset($filters['sort_Dir'])|| !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		/*if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}*/
		return $query;
	}

	/**
	 * Delete comments associated with an entry
	 *
	 * @param   integer  $entry_id  Blog entry
	 * @return  boolean  True on success, False on error
	 */
	public function deleteByEntry($entry_id=null)
	{
		if (!$entry_id)
		{
			$this->setError(Lang::txt('COM_BLOG_ERROR_MISSING_ENTRY_ID'));
			return false;
		}

		$this->_db->setQuery("DELETE FROM `$this->_tbl` WHERE `entry_id`=" . $this->_db->quote($entry_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

