<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Table class for course page
 */
class Page extends Table
{
	/**
	 * Constructor
	 *
	 * @param      object &$db Database
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
			$this->setError(Lang::txt('Missing title'));
			return false;
		}

		if (!$this->content)
		{
			$this->setError(Lang::txt('Missing content'));
			return false;
		}

		$this->course_id = intval($this->course_id);
		$this->offering_id = intval($this->offering_id);
		$this->section_id = intval($this->section_id);

		if (!$this->url)
		{
			$this->url = strtolower(str_replace(' ', '_', trim($this->title)));
		}
		$this->url = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->url);

		if (!$this->id)
		{
			$sql = "SELECT id FROM $this->_tbl
					WHERE course_id=" . $this->_db->quote($this->course_id) . "
					AND offering_id=" . $this->_db->quote($this->offering_id) . "
					AND section_id=" . $this->_db->quote($this->section_id) . "
					AND url=" . $this->_db->quote($this->url) . "
					LIMIT 1";
			$this->_db->setQuery($sql);
			if ($this->_db->loadResult())
			{
				$this->setError(Lang::txt('A page with the alias "%s" already exist.', $this->url));
				return false;
			}

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
			$where[] = "r.`course_id`=" . $this->_db->quote($filters['course_id']);
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
				$where[] = "r.`url`!=" . $this->_db->quote(ltrim($filters['url'], '!'));
			}
			else
			{
				$where[] = "r.`url`=" . $this->_db->quote($filters['url']);
			}
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "LOWER(r.`title`) LIKE " . $this->_db->quote('%' . strtolower($filters['title']) . '%');
		}
		if (isset($filters['active']))
		{
			if (is_array($filters['active']))
			{
				$filters['active'] = array_map('intval', $filters['active']);
				$where[] = "r.`active` IN (" . implode(',', $filters['active']) . ")";
			}
			else if ($filters['active'] >= 0)
			{
				$where[] = "r.`active`=" . $this->_db->quote(intval($filters['active']));
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
		$query  = "SELECT COUNT(DISTINCT r.`url`) ";
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
		$sql = "SELECT ordering from $this->_tbl WHERE course_id=" . $this->_db->quote(intval($course_id)) . " AND offering_id=" . $this->_db->quote(intval($offering_id)) . " ORDER BY ordering DESC LIMIT 1";
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

		require_once(__DIR__ . DS . 'page.hit.php');

		$tbl = new PageHit($this->_db);
		if (!$tbl->hit($offering_id, $page_id))
		{
			$this->setError($tbl->getError());
			return false;
		}
		return true;
	}
}
