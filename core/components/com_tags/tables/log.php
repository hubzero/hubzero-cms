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

namespace Components\Tags\Tables;

use User;
use Date;
use Lang;

/**
 * Tags class for log table
 * This is used to track events on tags (adding, editing, merging, deleting)
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
		parent::__construct('#__tags_log', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->tag_id = intval($this->tag_id);
		if (!$this->tag_id)
		{
			$this->setError(Lang::txt('TAGS_LOGS_MUST_HAVE_TAG_ID'));
			return false;
		}

		if (!$this->id)
		{
			$this->timestamp = Date::toSql();
			$this->actorid   = User::get('id');
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->user_id = User::get('id');
		}

		return true;
	}

	/**
	 * Get all records for a tag
	 *
	 * @param   integer  $tag_id    Tag ID
	 * @param   string   $action    Action taken
	 * @param   string   $comments  Comments
	 * @return  boolean
	 */
	public function log($tag_id=null, $action=null, $comments=null)
	{
		$tag_id = $tag_id ?: $this->tag_id;

		if (!$tag_id || !$action)
		{
			$this->setError(Lang::txt('Missing argument.'));
			return false;
		}

		$this->tag_id = $tag_id;
		$this->action = $action;
		if ($comments)
		{
			$this->comments = $comments;
		}

		if (!$this->check())
		{
			return false;
		}
		if (!$this->store())
		{
			return false;
		}
		return true;
	}

	/**
	 * Get all records for a tag
	 *
	 * @param   integer  $tag_id  Tag ID
	 * @return  array
	 */
	public function getLogs($tag_id=null)
	{
		$tag_id = $tag_id ?: $this->tag_id;

		if (!$tag_id)
		{
			return null;
		}

		return $this->find(array('tag_id' => $tag_id));
	}

	/**
	 * Delete logs for a tag
	 *
	 * @param   integer  $tag_id  Tag ID
	 * @return  boolean  True on success
	 */
	public function deleteLogs($tag_id=null)
	{
		$tag_id = $tag_id ?: $this->tag_id;

		if (!$tag_id)
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE `tag_id`=" . $this->_db->quote($tag_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Format a log
	 *
	 * @param   object  $log  Database row
	 * @return  string
	 */
	public function formatLog($log=null)
	{
		if (!$log)
		{
			return '';
		}
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to determien hwo to build query
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters)
	{
		$query  = " FROM $this->_tbl AS o";

		$where = array();

		if (isset($filters['timestamp']) && (string) $filters['timestamp'] != '')
		{
			$where[] = "o.timestamp >= " . $this->_db->quote($filters['timestamp']);
		}
		if (isset($filters['action']) && (string) $filters['action'] != '')
		{
			$where[] = "o.action=" . $this->_db->quote($filters['action']);
		}
		if (isset($filters['tag_id']) && (int) $filters['tag_id'] > 0)
		{
			$where[] = "o.tag_id=" . $this->_db->quote(intval($filters['tag_id']));
		}
		if (isset($filters['user_id']) && (int) $filters['user_id'] >= 0)
		{
			$where[] = "o.user_id=" . $this->_db->quote(intval($filters['user_id']));
		}
		if (isset($filters['actorid']) && (int) $filters['actorid'] > 0)
		{
			$where[] = "o.actorid=" . $this->_db->quote(intval($filters['actorid']));
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to determien hwo to build query
	 * @return  integer
	 */
	public function count($filters=array())
	{
		$this->_db->setQuery("SELECT COUNT(*)" . $this->_buildQuery($filters));
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to determien hwo to build query
	 * @return  array
	 */
	public function find($filters=array())
	{
		$query = "SELECT o.*" . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || $filters['sort'] == '')
		{
			$filters['sort'] = 'timestamp';
		}

		if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all')
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

