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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Courses table class for reviews
 */
class CoursesPluginReviewTable extends JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_reviews', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if valid, false if not
	 */
	public function check()
	{
		if (!$this->rating)
		{
			$this->setError(Lang::txt('Your review must have a rating.'));
			return false;
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
		$query = " FROM $this->_tbl AS r LEFT JOIN #__users AS u ON u.id=r.created_by";

		$where = array();
		if (isset($filters['course_id']))
		{
			$where[] = "r.`course_id`=" . $this->_db->Quote($filters['course_id']);
		}
		if (isset($filters['offering_id']))
		{
			$where[] = "r.`offering_id`=" . $this->_db->Quote($filters['offering_id']);
		}
		if (isset($filters['state']))
		{
			$where[] = "r.`state`=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['access']))
		{
			$where[] = "r.`access`=" . $this->_db->Quote($filters['access']);
		}
		if (isset($filters['parent']))
		{
			$where[] = "r.`parent`=" . $this->_db->Quote($filters['parent']);
		}
		if (isset($filters['created_by']))
		{
			$where[] = "r.`created_by`=" . $this->_db->Quote($filters['created_by']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "LOWER(r.`content`) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of course reviews
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
	 * Get an object list of course reviews
	 *
	 * @param  array $filters
	 * @return array
	 */
	public function find($filters=array())
	{
		$query  = "SELECT r.*, u.name";
		$query .= $this->_buildquery($filters);

		if (isset($filters['sort']) && $filters['sort'])
		{
			if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
			{
				$filters['sort_Dir'] = 'ASC';
			}
			$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		}
		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get an object list ratings for a course
	 *
	 * @param  array $filters
	 * @return array
	 */
	public function ratings($filters=array())
	{
		$query  = "SELECT r.rating";
		$query .= $this->_buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

