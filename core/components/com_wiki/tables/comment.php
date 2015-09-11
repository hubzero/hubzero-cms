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

namespace Components\Wiki\Tables;

/**
 * Wiki table class for comment
 */
class Comment extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
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
				$where[] = "c.status=" . $this->_db->quote(intval($filters['status']));
			}
		}
		if (isset($filters['created_by']) && $filters['created_by'] != 0)
		{
			$where[] = "c.created_by=" . $this->_db->quote($filters['created_by']);
		}
		if (isset($filters['pageid']) && $filters['pageid'] != 0)
		{
			$where[] = "c.pageid=" . $this->_db->quote($filters['pageid']);
		}
		if (isset($filters['parent']) && $filters['parent'] != '')
		{
			$where[] = "c.parent=" . $this->_db->quote($filters['parent']);
		}
		if (isset($filters['anonymous']) && $filters['anonymous'] != '')
		{
			$where[] = "c.anonymous=" . $this->_db->quote($filters['anonymous']);
		}
		if (isset($filters['version']) && $filters['version'] != 0)
		{
			$where[] = "c.version=" . $this->_db->quote($filters['version']);
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
	 * @param   array   $select   SQL selection statement
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

