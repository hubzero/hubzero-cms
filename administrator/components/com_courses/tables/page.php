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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for course page
 */
Class CoursesTablePage extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 * 
	 * @var string
	 */
	var $course_id = NULL;

	/**
	 * int(11)
	 * 
	 * @var string
	 */
	var $offering_id = NULL;

	/**
	 * int(11)
	 * 
	 * @var string
	 */
	var $section_id = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $url = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $title = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $content = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $ordering = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $active = NULL;

	/**
	 * varchar(10)
	 * 
	 * @var string
	 */
	var $privacy = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_pages', 'id', $db);
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
			$this->setError(JText::_('Missing title'));
			return false;
		}

		if (!$this->content) 
		{
			$this->setError(JText::_('Missing content'));
			return false;
		}

		if (!$this->url)
		{
			$this->url = strtolower(str_replace(' ', '_', trim($this->title)));
		}
		$this->url = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->url);

		if (!$this->id)
		{
			$high = $this->getHighestPageOrder($this->course_id, $this->offering_id);
			$this->ordering = ($high + 1);
			$this->active = 1;
		}

		return true;
	}

	/**
	 * Build query method
	 * 
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS r";

		$where = array();
		if (isset($filters['course_id']))
		{
			$where[] = "r.`course_id`=" . $this->_db->Quote($filters['course_id']);
		}
		if (isset($filters['offering_id']))
		{
			if (is_array($filters['offering_id']))
			{
				$filters['offering_id'] = array_map('intval', $filters['offering_id']);
				$filters['offering_id'] = implode(',', $filters['offering_id']);
			}
			else
			{
				$filters['offering_id'] = intval($filters['offering_id']);
			}
			$where[] = "r.`offering_id` IN (" . $filters['offering_id'] . ")";
		}
		if (isset($filters['section_id']))
		{
			if (is_array($filters['section_id']))
			{
				$filters['section_id'] = array_map('intval', $filters['section_id']);
				$filters['section_id'] = implode(',', $filters['section_id']);
			}
			else
			{
				$filters['section_id'] = intval($filters['section_id']);
			}
			$where[] = "r.`section_id` IN (" . $filters['section_id'] . ")";
		}
		if (isset($filters['url']) && $filters['url'])
		{
			if (substr($filters['url'], 0, 1) == '!')
			{
				$where[] = "r.`url`!=" . $this->_db->Quote(ltrim($filters['url'], '!'));
			}
			else
			{
				$where[] = "r.`url`=" . $this->_db->Quote($filters['url']);
			}
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "LOWER(r.`title`) LIKE '%" . $this->_db->getEscaped(strtolower($filters['title'])) . "%'";
		}
		if (isset($filters['active']))
		{
			if (is_array($filters['active']))
			{
				$filters['active'] = array_map('intval', $filters['active']);
				$where[] = "r.`active` IN (" . implode(',', $filters['active']) . ")";
			}
			else
			{
				$where[] = "r.`active`=" . $this->_db->getEscaped(intval($filters['active']));
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get an object list of course units
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*) ";
		$query .= $this->_buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get pages for a course
	 * 
	 * @param      string  $offering_id    Course alias (cn)
	 * @param      boolean $active Parameter description (if any) ...
	 * @return     array
	 */
	public function find($filters=array())
	{
		$sql = "SELECT r.*" . $this->_buildquery($filters);

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'ordering';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$sql .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$sql .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the last page in the ordering
	 * 
	 * @param      string  $offering_id    Course alias (cn)
	 * @return     integer
	 */
	public function getHighestPageOrder($course_id, $offering_id)
	{
		$sql = "SELECT ordering from $this->_tbl WHERE course_id=" . $this->_db->Quote(intval($course_id)) . " AND offering_id=" . $this->_db->Quote(intval($offering_id)) . " ORDER BY ordering DESC LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Record a page hit
	 * 
	 * @param      integer $pid Page ID
	 * @return     void
	 */
	public function hit($offering_id=null, $page_id=null)
	{
		if (!$offering_id)
		{
			$offering_id = $this->offering_id;
		}
		if (!$page_id)
		{
			$page_id = $this->page_id;
		}

		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'page.hit.php');

		$tbl = new CoursesTablePageHit($this->_db);
		if (!$tbl->hit($offering_id, $page_id))
		{
			$this->setError($tbl->getError());
			return false;
		}
		return true;
	}
}
