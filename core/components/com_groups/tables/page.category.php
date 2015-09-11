<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Groups\Tables;

use Lang;

/**
 * Groups Pages Category table
 */
class PageCategory extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_pages_categories', 'id', $db);
	}

	/**
	 * Check method overload
	 */
	public function check()
	{
		// make sure we have a title
		if (!$this->gidNumber || $this->gidNumber == "")
		{
			$this->setError(Lang::txt('Category Must Contain Group ID Number'));
			return false;
		}

		// make sure we have a title
		if (!$this->title || $this->title == "")
		{
			$this->setError(Lang::txt('Category Must Contain Title'));
			return false;
		}

		return true;
	}

	public function find($filters = array())
	{
		$sql = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	public function count($filters = array())
	{
		$sql = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	private function _buildQuery($filters = array())
	{
		//vars
		$sql   = '';
		$where = array();

		// check for gidNumber
		if (isset($filters['gidNumber']))
		{
			$where[] = 'gidNumber=' . $this->_db->quote($filters['gidNumber']);
		}

		// did we have any conditions
		if (count($where) > 0)
		{
			$sql = ' WHERE ' . implode(' AND', $where);
		}

		// check for gidNumber
		if (isset($filters['orderby']))
		{
			$sql .= ' ORDER BY ' . $filters['orderby'];
		}

		return $sql;
	}

	public function getCategories($group)
	{
		$categories = array();

		// make sure we have a valid group
		if (!is_object($group) || $group->get('gidNumber') == '')
		{
			return $categories;
		}

		$sql = "SELECT * FROM {$this->_tbl} WHERE gidNumber=" . $this->_db->quote($group->get('gidNumber'));
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}