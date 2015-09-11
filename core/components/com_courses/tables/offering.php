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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use User;
use Date;
use Lang;

/**
 * Course Instances table class
 */
class Offering extends \JTable
{
	/**
	 * Contructor method for JTable class
	 *
	 * @param   object  &$db  Database object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_offerings', 'id', $db);
	}

	/**
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param   string  $type    The page to load
	 * @param   string  $prefix  The page scope
	 * @param   array   $config  Config options
	 * @return  object
	 */
	public static function getInstance($type, $prefix = 'JTable', $config = array())
	{
		static $instances;

		$alias = $type;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$alias]))
		{
			$inst = new self(\App::get('db'));
			$inst->load($alias);

			$instances[$alias] = $inst;
		}

		return $instances[$alias];
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   mixed    $oid        Record ID or alias
	 * @param   integer  $course_id  Course ID
	 * @return  boolean  True on success
	 */
	public function load($oid=NULL, $course_id=null)
	{
		if ($oid === NULL)
		{
			return false;
		}
		if (is_numeric($oid))
		{
			return parent::load($oid);
		}

		return parent::load(array(
			'alias'     => trim($oid),
			'course_id' => intval($course_id)
		));
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return  boolean
	 */
	public function check()
	{
		$this->course_id = intval($this->course_id);
		if (!$this->course_id)
		{
			$this->setError(Lang::txt('Please provide a course ID.'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(Lang::txt('Please provide a title.'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = strtolower($this->title);
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->alias);
		$this->makeAliasUnique();
		if (is_numeric($this->alias))
		{
			$this->setError(Lang::txt('Alias must contain at least one non-numeric character.'));
			return false;
		}

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
		}

		return true;
	}

	/**
	 * Return a unique alias based on given alias
	 *
	 * @return  void
	 */
	private function makeAliasUnique()
	{
		$sql = "SELECT alias from $this->_tbl WHERE `course_id`=" . $this->_db->quote(intval($this->course_id));
		if ($this->id)
		{
			$sql .= " AND `id`!=" . $this->_db->quote(intval($this->id));
		}
		$this->_db->setQuery($sql);
		$result = $this->_db->loadColumn();

		$original_alias = $this->alias;

		if ($result)
		{
			for ($i=1; in_array($this->alias, $result); $i++)
			{
				$this->alias = $original_alias . $i;
			}
		}
	}

	/**
	 * Build query method
	 *
	 * @param   array   $filters
	 * @return  string  SQL
	 */
	private function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS ci";
		$query .= " INNER JOIN #__courses AS c ON c.id=ci.course_id";

		$where = array();

		if (isset($filters['course_alias']) && $filters['course_alias'])
		{
			$where[] = "c.alias=" . $this->_db->quote($filters['course_alias']);
		}
		else if (isset($filters['course_id'])) // && $filters['course_id'])
		{
			$where[] = "c.id=" . $this->_db->quote(intval($filters['course_id']));
		}

		if (isset($filters['available']) && $filters['available'])
		{
			$now = Date::toSql();

			$where[] = "(ci.publish_up = '0000-00-00 00:00:00' OR ci.publish_up <= " . $this->_db->quote($now) . ")";
			$where[] = "(ci.publish_down = '0000-00-00 00:00:00' OR ci.publish_down >= " . $this->_db->quote($now) . ")";

			$filters['state'] = 1;
		}

		if (isset($filters['state']))
		{
			if (is_array($filters['state']))
			{
				$filters['state'] = array_map('intval', $filters['state']);
				$where[] = "ci.state IN (" . implode(',', $filters['state']) . ")";
			}
			else if ($filters['state'] >= 0)
			{
				$where[] = "ci.state=" . $this->_db->quote(intval($filters['state']));
			}
		}

		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "(LOWER(ci.alias) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(ci.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of course offerings
	 *
	 * @param   array    $filters
	 * @return  integer
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(ci.id)";
		$query .= $this->_buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of course units
	 *
	 * @param   array  $filters
	 * @return  array
	 */
	public function find($filters=array())
	{
		$query  = "SELECT ci.*, c.alias AS course_alias";
		$query .= $this->_buildquery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'publish_up';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}