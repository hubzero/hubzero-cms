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

namespace Components\Blog\Tables;

use Lang;
use Date;
use User;

/**
 * Blog Entry database class
 */
class Entry extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__blog_entries', 'id', $db);
	}

	/**
	 * Load an entry from the database and bind to $this
	 *
	 * @param   string   $oid       Entry alias
	 * @param   string   $scope     Entry scope [site, group, member]
	 * @param   integer  $scope_id  ID of scope object
	 * @return  boolean  True if data was retrieved and loaded
	 */
	public function loadAlias($oid=NULL, $scope=NULL, $scope_id=NULL)
	{
		return parent::load(array(
			'alias'    => (string) $oid,
			'scope'    => (string) $scope,
			'scope_id' => (int) $scope_id
		));
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);
		if ($this->title == '')
		{
			$this->setError(Lang::txt('COM_BLOG_ERROR_PROVIDE_TITLE'));
			return false;
		}

		if (!$this->alias)
		{
			$text = strip_tags($this->title);
			$text = trim($text);
			if (strlen($text) > 100)
			{
				$text = $text . ' ';
				$text = substr($text, 0, 100);
				$text = substr($text, 0, strrpos($text,' '));
			}

			$this->alias = $text;
		}
		$this->alias = str_replace(' ', '-', $this->alias);
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->alias));

		$this->content = trim($this->content);
		if ($this->content == '')
		{
			$this->setError(Lang::txt('COM_BLOG_ERROR_PROVIDE_CONTENT'));
			return false;
		}

		if (!$this->created_by)
		{
			$this->created_by = User::get('id');
		}

		if (!$this->id)
		{
			$this->created = Date::toSql();
		}

		if (!$this->publish_up || $this->publish_up == $this->_db->getNullDate())
		{
			$this->publish_up = $this->created;
		}

		if (!$this->publish_down)
		{
			$this->publish_down = $this->_db->getNullDate();
		}

		return true;
	}

	/**
	 * Get a count of, single entry, or list of entries
	 * 
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   array    $select   List of fields to select
	 * @return  mixed
	 */
	public function find($what='', $filters=array(), $select=array(), $admin=true)
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

				return $this->find('one', $filters);
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
				if (!isset($filters['sort']))
				{
					$filters['sort'] = 'publish_up';
				}
				if (!isset($filters['sort_Dir']))
				{
					$filters['sort_Dir'] = 'DESC';
				}
				if ($filters['sort_Dir'])
				{
					$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
					if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
					{
						$filters['sort_Dir'] = 'DESC';
					}
				}

				if (empty($select))
				{
					if ($admin)
					{
						$select = array(
							'm.*',
							'(SELECT COUNT(*) FROM `#__blog_comments` AS c WHERE c.entry_id=m.id) AS comments',
							'u.name'
						);
					}
					else
					{
						$select = array(
							'm.*',
							'(SELECT COUNT(*) FROM `#__blog_comments` AS c WHERE c.entry_id=m.id AND c.state = 1) AS comments',
							'u.name'
						);
					}

				}

				$query  = "SELECT " . implode(', ', $select) . " " . $this->_buildQuery($filters);
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

	/**
	 * Build a query from filters passed
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	private function _buildQuery($filters=array())
	{
		$nullDate = $this->_db->getNullDate();
		$now = Date::toSql();

		$query = "FROM `$this->_tbl` AS m LEFT JOIN `#__xprofiles` AS u ON u.uidNumber=m.created_by";

		$where = array();

		if (isset($filters['created_by']) && $filters['created_by'] != 0)
		{
			$where[] = "m.created_by=" . $this->_db->quote($filters['created_by']);
		}
		if (isset($filters['scope_id']) && $filters['scope_id'] != 0)
		{
			$where[] = "m.scope_id=" . $this->_db->quote($filters['scope_id']);
		}
		if (isset($filters['scope']) && $filters['scope'] != '')
		{
			$where[] = "m.scope=" . $this->_db->quote($filters['scope']);
		}
		if (isset($filters['state']) && $filters['state'] != '')
		{
			switch ($filters['state'])
			{
				case 'public':
					$where[] = "m.state=1";
				break;
				case 'registered':
					$where[] = "m.state>0";
				break;
				case 'private':
					$where[] = "m.state=0";
				break;
				case 'all':
					if (!\App::isAdmin() && (!isset($filters['scope']) || !$filters['scope']))
					{
						// This means we're pulling entries for all scopes
						$where[] = "((m.state>=0 AND m.scope=" . $this->_db->quote('site') . ") OR (m.state>0 AND m.scope!=" . $this->_db->quote('site') . "))";
					}
					else
					{
						$where[] = "m.state>=0";
					}
				break;
				case 'trashed':
					$where[] = "m.state<0";
				break;
			}
		}
		if (isset($filters['year']) && $filters['year'] != 0)
		{
			if (isset($filters['month']) && $filters['month'] != 0)
			{
				$startdate = $filters['year'] . '-' . $filters['month'] . '-01 00:00:00';

				if ($filters['month']+1 == 13)
				{
					$year  = $filters['year'] + 1;
					$month = 1;
				}
				else
				{
					$month = ($filters['month']+1);
					$year  = $filters['year'];
				}
				$enddate = sprintf("%4d-%02d-%02d 00:00:00", $year, $month, 1);
			}
			else
			{
				$startdate = $filters['year'] . '-01-01 00:00:00';
				$enddate   = ($filters['year']+1) . '-01-01 00:00:00';
			}

			$where[] = "m.publish_up >= " . $this->_db->quote($startdate);
			$where[] = "m.publish_up < " . $this->_db->quote($enddate);
		}
		else
		{
			if (!\App::isAdmin())
			{
				$created_by = " OR m.created_by=" .  $this->_db->quote(User::get('id'));
				$where[] = "(m.publish_up = " . $this->_db->quote($nullDate) . " OR m.publish_up <= " . $this->_db->quote($now) . "{$created_by})";
			}
		}

		if (!\App::isAdmin())
		{
			if ((isset($filters['state']) && $filters['state'] != 'all') || !isset($filters['state']))
			{
				if (!isset($filters['authorized']) || !$filters['authorized'])
				{
					$where[] = "(m.publish_down = " . $this->_db->quote($nullDate) . " OR m.publish_down >= " . $this->_db->quote($now) . ")";
				}
				else if (isset($filters['authorized']) && $filters['authorized'] && is_numeric($filters['authorized']))
				{
					$where[] = "((m.publish_down = " . $this->_db->quote($nullDate) . " OR m.publish_down >= " . $this->_db->quote($now) . ") OR m.created_by=" . $this->_db->quote($filters['authorized']) . ")";
				}
			}
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$filters['search'] = $this->_db->quote('%' . strtolower(stripslashes($filters['search'])) . '%');
			$where[] = "(LOWER(m.title) LIKE " . $filters['search'] . " OR LOWER(m.content) LIKE " . $filters['search'] . ")";
		}

		if (count($where))
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}
}

