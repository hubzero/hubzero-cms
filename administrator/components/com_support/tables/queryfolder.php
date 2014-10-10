<?php
/**
 * @package     HUBzero CMS
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2014 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2014 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for support query folders
 */
class SupportTableQueryFolder extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct($db)
	{
		parent::__construct('#__support_query_folders', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(JText::_('SUPPORT_ERROR_BLANK_FIELD'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = $this->title;
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($this->alias));

		$this->user_id = intval($this->user_id);

		$juser = JFactory::getUser();
		if (!$this->user_id)
		{
			$this->user_id = $juser->get('id');
		}

		if (!$this->id)
		{
			$this->created = JFactory::getDate()->toSql();
			$this->created_by = $juser->get('id');

			$this->_db->setQuery("SELECT `ordering` FROM $this->_tbl WHERE `user_id`=" . $this->user_id . " ORDER BY `ordering` DESC LIMIT 1");

			$this->ordering = $this->_db->loadResult();
			$this->ordering = intval($this->ordering) + 1;
		}
		else
		{
			$this->modified = JFactory::getDate()->toSql();
			$this->modified_by = $juser->get('id');
		}

		return true;
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query  = "FROM $this->_tbl";

		$where = array();
		if (isset($filters['user_id']))
		{
			$where[] = "user_id=" . $this->_db->quote($filters['user_id']);
		}
		if (isset($filters['created_by']) && $filters['created_by'] >= 0)
		{
			$where[] = "created_by=" . $this->_db->quote($filters['created_by']);
		}
		if (isset($filters['modified_by']) && $filters['modified_by'] >= 0)
		{
			$where[] = "modified_by=" . $this->_db->quote($filters['modified_by']);
		}
		if (isset($filters['iscore']))
		{
			if (is_array($filters['iscore']))
			{
				$filters['iscore'] = array_map('intval', $filters['iscore']);
				$where[] = "iscore IN (" . implode(',', $filters['iscore']) . ")";
			}
			else
			{
				$where[] = "iscore=" . $this->_db->quote($filters['iscore']);
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count or list of records
	 *
	 * @param   string  $what     Data to return
	 * @param   array   $filters  Filters to build query from
	 * @return  mixed
	 */
	public function find($what='list', $filters=array())
	{
		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);
				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['start'] = 0;
				$filters['limit'] = 1;
				$result = $this->find('list', $filters);
				return $result[0];
			break;

			case 'all':
				$filters['start'] = 0;
				$filters['limit'] = 0;
				return $this->find('list', $filters);
			break;

			case 'list':
				$query = "SELECT * " . $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'ordering';
				}

				if (!in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')) || !isset($filters['sort_Dir']))
				{
					$filters['sort_Dir'] = 'ASC';
				}
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] != 0)
				{
					$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}

	/**
	 * Clone core folders and queries and assign 
	 * them to a given user ID
	 *
	 * @param   integer  $user_id  User ID
	 * @return  array
	 */
	public function cloneCore($user_id=0)
	{
		// Get all the default folders
		$folders = $this->find('list', array(
			'user_id'  => 0,
			'sort'     => 'ordering',
			'sort_Dir' => 'asc',
			'iscore'   => 1
		));

		$sq = new SupportQuery($this->_db);

		$user_id = $user_id ?: JFactory::getUser();
		$fid = 0;

		// Loop through each folder
		foreach ($folders as $k => $folder)
		{
			// Copy the folder for the user
			$stqf = new SupportTableQueryFolder($this->_db);
			$stqf->bind($folder);
			$stqf->created_by = $user_id;
			$stqf->created    = JFactory::getDate()->toSql();
			$stqf->id         = null;
			$stqf->user_id    = $user_id;
			$stqf->iscore      = 0;
			$stqf->store();

			$queries = $sq->find('list', array(
				'folder_id' => $folder->id
			));

			// Copy all the queries from the folder to the user
			foreach ($queries as $query)
			{
				$stq = new SupportQuery($this->_db);
				$stq->bind($query);
				$stq->created_by = $user_id;
				$stq->created    = JFactory::getDate()->toSql();
				$stq->id         = null;
				$stq->user_id    = $user_id;
				$stq->folder_id  = $stqf->get('id');
				$stq->iscore     = 0;
				$stq->store();
			}

			// If the folder is "common", get its ID
			if ($folder->alias == 'common')
			{
				$fid = $stqf->get('id');
			}

			$folders[$k] = $stqf;
		}

		if ($fid)
		{
			$this->_db->setQuery("UPDATE `#__support_queries` SET `folder_id`=" . $this->_db->quote($fid) . " WHERE `user_id`=" . $this->_db->quote($user_id) . " AND `iscore`=0 AND `folder_id`=0");
			$this->_db->query();
		}

		return $folders;
	}
}
