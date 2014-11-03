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
 * Table class for resource license
 */
class ResourcesLicense extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_licenses', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   mixed  $oid  Integer or string (alias)
	 * @return  void
	 */
	public function load($keys = NULL, $reset = true)
	{
		if (is_numeric($keys))
		{
			return parent::load($keys);
		}

		return parent::load(array(
			'name' => trim($keys)
		));
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->text = trim($this->text);
		if (!$this->text)
		{
			$this->setError(JText::_('Please provide some text.'));
			return false;
		}

		if (!$this->title)
		{
			$this->title = substr($this->text, 0, 70);
			if (strlen($this->title >= 70))
			{
				$this->title .= '...';
			}
		}

		if (!$this->name)
		{
			$this->name = $this->title;
		}
		$this->name = str_replace(' ', '-', strtolower($this->name));
		$this->name = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->name);

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
		$query  = "FROM `$this->_tbl` AS c";

		$where = array();
		if (isset($filters['state']))
		{
			$where[] = "c.state=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(c.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
				OR LOWER(c.`text`) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get record counts
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		$query = "SELECT c.*" . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get licenses
	 *
	 * @param   integer  $id  Resource ID
	 * @return  array
	 */
	public function getLicenses($id=null)
	{
		$query  = "SELECT c.* FROM `$this->_tbl` AS c";

		$where = array();

		if (!is_null($id))
		{
			$id = intval($id);
			$where[] = "name NOT LIKE 'custom%' OR name = 'custom$id'";
		}
		else
		{
			$where[] = "name != 'custom'";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'DESC';
		}
		if (!in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

