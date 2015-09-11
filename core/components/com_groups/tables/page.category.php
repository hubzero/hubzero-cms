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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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