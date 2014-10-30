<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Blog Entry database class
 */
class BlogTableEntry extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
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
			$this->setError(JText::_('COM_BLOG_ERROR_PROVIDE_TITLE'));
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
			$this->setError(JText::_('COM_BLOG_ERROR_PROVIDE_CONTENT'));
			return false;
		}

		$juser = JFactory::getUser();
		if (!$this->created_by)
		{
			$this->created_by = $juser->get('id');
		}

		if (!$this->id)
		{
			$this->created = JFactory::getDate()->toSql();
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
	public function find($what='', $filters=array(), $select=array())
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
					$select = array(
						'm.*',
						'(SELECT COUNT(*) FROM `#__blog_comments` AS c WHERE c.entry_id=m.id) AS comments',
						'u.name'
					);
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
		$now = JFactory::getDate()->toSql();

		$query = "FROM `$this->_tbl` AS m LEFT JOIN `#__xprofiles` AS u ON u.uidNumber=m.created_by";

		$where = array();

		if (isset($filters['created_by']) && $filters['created_by'] != 0)
		{
			$where[] = "m.created_by=" . $this->_db->Quote($filters['created_by']);
		}
		if (isset($filters['scope_id']) && $filters['scope_id'] != 0)
		{
			$where[] = "m.scope_id=" . $this->_db->Quote($filters['scope_id']);
		}
		if (isset($filters['scope']) && $filters['scope'] != '')
		{
			$where[] = "m.scope=" . $this->_db->Quote($filters['scope']);
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
					$where[] = "m.state>=0";
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

			$where[] = "m.publish_up >= " . $this->_db->Quote($startdate);
			$where[] = "m.publish_up < " . $this->_db->Quote($enddate);
		}
		else
		{
			if (!JFactory::getApplication()->isAdmin())
			{
				$created_by = " OR m.created_by=" .  $this->_db->quote(JFactory::getUser()->get('id'));
				$where[] = "(m.publish_up = " . $this->_db->Quote($nullDate) . " OR m.publish_up <= " . $this->_db->Quote($now) . "{$created_by})";
			}
		}

		if (!JFactory::getApplication()->isAdmin())
		{
			if ((isset($filters['state']) && $filters['state'] != 'all') || !isset($filters['state']))
			{
				if (!isset($filters['authorized']) || !$filters['authorized'])
				{
					$where[] = "(m.publish_down = " . $this->_db->Quote($nullDate) . " OR m.publish_down >= " . $this->_db->Quote($now) . ")";
				}
				else if (isset($filters['authorized']) && $filters['authorized'] && is_numeric($filters['authorized']))
				{
					$where[] = "((m.publish_down = " . $this->_db->Quote($nullDate) . " OR m.publish_down >= " . $this->_db->Quote($now) . ") OR m.created_by=" . $this->_db->Quote($filters['authorized']) . ")";
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

