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
 * Wiki table class for comment
 */
class WikiTableComment extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wiki_comments', 'id', $db);
	}

	/**
	 * Build a query from filters passed
	 * Used for admin interface
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	private function _buildQuery($filters=array())
	{
		$query  = "FROM $this->_tbl AS c LEFT JOIN #__xprofiles AS u ON c.created_by=u.uidNumber";

		$where = array();

		if (isset($filters['status']))
		{
			if (is_array($filters['status']))
			{
				$filters['status'] = array_map('intval', $filters['status']);
				$where[] = "c.status IN (" . implode(',', $filters['status']) . ")";
			}
			else if ($filters['status'] >= 0)
			{
				$where[] = "c.status=" . $this->_db->Quote(intval($filters['status']));
			}
		}
		if (isset($filters['created_by']) && $filters['created_by'] != 0)
		{
			$where[] = "c.created_by=" . $this->_db->Quote($filters['created_by']);
		}
		if (isset($filters['pageid']) && $filters['pageid'] != 0)
		{
			$where[] = "c.pageid=" . $this->_db->Quote($filters['pageid']);
		}
		if (isset($filters['parent']) && $filters['parent'] != '')
		{
			$where[] = "c.parent=" . $this->_db->Quote($filters['parent']);
		}
		if (isset($filters['anonymous']) && $filters['anonymous'] != '')
		{
			$where[] = "c.anonymous=" . $this->_db->Quote($filters['anonymous']);
		}
		if (isset($filters['version']) && $filters['version'] != 0)
		{
			$where[] = "c.version=" . $this->_db->Quote($filters['version']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "LOWER(c.ctext) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Returns either a count or list of records
	 *
	 * @param   string  $what     What type of data to return (count, one, first, all, list)
	 * @param   array   $filters  An associative array of filters used to construct a query
	 * @return  mixed
	 */
	public function find($what='', $filters=array(), $select=array('*'))
	{
		$what = strtolower($what);
		$select = (array) $select;

		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'first':
				$filters['start'] = 0;
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'all':
				if (isset($filters['limit']))
				{
					unset($filters['limit']);
				}
				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				$query  = "SELECT c.*, u.name " . $this->_buildQuery($filters);

				if (!isset($filters['sort']))
				{
					$filters['sort'] = 'created';
				}
				if (!isset($filters['sort_Dir']))
				{
					$filters['sort_Dir'] = 'DESC';
				}
				$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
				if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
				{
					$filters['sort_Dir'] = 'DESC';
				}

				$query .= " ORDER BY `" . $filters['sort'] . "` " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] > 0)
				{
					$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}
}

