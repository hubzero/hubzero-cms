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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Tags class for log table
 * This is used to track events on tags (adding, editing, merging, deleting)
 */
class TagsTableLog extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
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
			$this->setError(JText::_('TAGS_LOGS_MUST_HAVE_TAG_ID'));
			return false;
		}

		$juser = JFactory::getUser();
		if (!$this->id)
		{
			$this->timestamp = JFactory::getDate()->toSql();
			$this->actorid = $juser->get('id');
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->user_id = $juser->get('id');
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
			$this->setError(JText::_('Missing argument.'));
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

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE `tag_id`=" . $this->_db->Quote($tag_id));
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
	 * @param   object $log Database row (TagsTableLog)
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
			$where[] = "o.timestamp >= " . $this->_db->Quote($filters['timestamp']);
		}
		if (isset($filters['action']) && (string) $filters['action'] != '')
		{
			$where[] = "o.action=" . $this->_db->Quote($filters['action']);
		}
		if (isset($filters['tag_id']) && (int) $filters['tag_id'] > 0)
		{
			$where[] = "o.tag_id=" . $this->_db->Quote(intval($filters['tag_id']));
		}
		if (isset($filters['user_id']) && (int) $filters['user_id'] >= 0)
		{
			$where[] = "o.user_id=" . $this->_db->Quote(intval($filters['user_id']));
		}
		if (isset($filters['actorid']) && (int) $filters['actorid'] > 0)
		{
			$where[] = "o.actorid=" . $this->_db->Quote(intval($filters['actorid']));
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

