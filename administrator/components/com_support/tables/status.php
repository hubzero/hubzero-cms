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
 * Table class for support ticket category
 */
class SupportTableStatus extends JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__support_statuses', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
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

		return true;
	}

	/**
	 * Build an SQL statement based off of filters passed
	 *
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query = "FROM $this->_tbl";

		$where = array();

		if (isset($filters['id']))
		{
			$where[] = "`id`=" . $this->_db->Quote($filters['uid']);
		}
		if (isset($filters['alias']) && $filters['alias'])
		{
			$where[] = "`alias`=" . $this->_db->Quote($filters['alias']);
		}
		if (isset($filters['title']) && $filters['title'])
		{
			$where[] = "`title`=" . $this->_db->Quote($filters['title']);
		}
		if (isset($filters['open']) && $filters['open'] >= 0)
		{
			$where[] = "`open`=" . $this->_db->Quote($filters['open']);
		}

		if (count($where))
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function find($what='', $filters=array())
	{
		$what = strtolower($what);

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
				$query  = "SELECT * " . $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'title';
				}
				if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
				{
					$filters['sort_Dir'] = 'DESC';
				}
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] > 0)
				{
					$filters['start'] = (isset($filters['start']) ? $filters['start'] : 0);

					$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}
}

