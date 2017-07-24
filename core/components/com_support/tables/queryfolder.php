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

namespace Components\Support\Tables;

use Lang;
use User;
use Date;

/**
 * Table class for support query folders
 */
class QueryFolder extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
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
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_BLANK_FIELD'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = $this->title;
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($this->alias));

		$this->user_id = intval($this->user_id);

		if (!$this->user_id)
		{
			$this->user_id = User::get('id');
		}

		if (!$this->id)
		{
			$this->created = Date::toSql();
			$this->created_by = User::get('id');

			$this->_db->setQuery("SELECT `ordering` FROM $this->_tbl WHERE `user_id`=" . $this->user_id . " ORDER BY `ordering` DESC LIMIT 1");

			$this->ordering = $this->_db->loadResult();
			$this->ordering = intval($this->ordering) + 1;
		}
		else
		{
			$this->modified = Date::toSql();
			$this->modified_by = User::get('id');
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

		$sq = new Query($this->_db);

		if (count($folders) <= 0)
		{
			$defaults = array(
				1 => array('Common', 'Mine', 'Custom'),
				2 => array('Common', 'Mine'),
			);

			foreach ($defaults as $iscore => $fldrs)
			{
				$i = 1;

				foreach ($fldrs as $fldr)
				{
					$f = new self($this->_db);
					$f->iscore = $iscore;
					$f->title = $fldr;
					$f->check();
					$f->ordering = $i;
					$f->user_id = 0;
					$f->store();

					switch ($f->alias)
					{
						case 'common':
							$j = ($iscore == 1 ? $sq->populateDefaults('common', $f->id) : $sq->populateDefaults('commonnotacl', $f->id));
						break;

						case 'mine':
							$sq->populateDefaults('mine', $f->id);
						break;

						default:
							// Nothing for custom folder
						break;
					}

					$i++;

					if ($iscore == 1)
					{
						$folders[] = $f;
					}
				}
			}
		}

		$user_id = $user_id ?: User::get('id');
		$fid = 0;

		// Loop through each folder
		foreach ($folders as $k => $folder)
		{
			// Copy the folder for the user
			$stqf = new self($this->_db);
			$stqf->bind($folder);
			$stqf->created_by = $user_id;
			$stqf->created    = Date::toSql();
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
				$stq = new Query($this->_db);
				$stq->bind($query);
				$stq->created_by = $user_id;
				$stq->created    = Date::toSql();
				$stq->id         = null;
				$stq->user_id    = $user_id;
				$stq->folder_id  = $stqf->get('id');
				$stq->iscore     = 0;
				$stq->store();
			}

			// If the folder is "custom", get its ID
			if ($folder->alias == 'custom')
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
