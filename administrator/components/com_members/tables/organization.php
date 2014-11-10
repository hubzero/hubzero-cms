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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for organizations
 */
class MembersTableOrganization extends JTable
{
	/**
	 * Object constructor to set table and key field
	 *
	 * @param   object  $db  JDatabase object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xorganizations', 'id', $db);
	}

	/**
	 * Method for checking that fields are valid before sending to the database
	 *
	 * @return  boolean  True if the object is ok
	 */
	public function check()
	{
		$this->organization = trim($this->organization);
		if (!$this->organization)
		{
			$this->setError(JText::_('Organization must contain text'));
			return false;
		}
		return true;
	}

	/**
	 * Returns a list, count or single record
	 *
	 * @param   string  $what     Data to return?
	 * @param   array   $filters  An associative array of filters used to construct a query
	 * @param   array   $select   List of fields to return
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
				$query  = "SELECT * " . $this->_buildQuery($filters);

				if (!isset($filters['sort']))
				{
					$filters['sort'] = 'organization';
				}
				if (!isset($filters['sort_Dir']))
				{
					$filters['sort_Dir'] = 'ASC';
				}
				$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
				if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
				{
					$filters['sort_Dir'] = 'ASC';
				}

				$query  = "SELECT " . implode(', ', $select) . " " . $this->_buildQuery($filters);
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

	/**
	 * Build SQL based on filters passed
	 *
	 * @param   array   $filters
	 * @return  string
	 */
	private function _buildQuery($filters=array())
	{
		$query = "FROM $this->_tbl ";

		$where = array();

		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "`state`=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['id']) && $filters['id'])
		{
			if (!is_array($filters['id']))
			{
				$filters['id'] = array($filters['id']);
			}
			$filters['id'] = array_map('intval', $filters['id']);

			$where[] = "`id` IN (" . implode(',', $filters['state']) . ")";
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "`organization` LIKE " . $this->_db->quote('%' . $filters['search'] . '%');
		}

		if (count($where))
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}
}
