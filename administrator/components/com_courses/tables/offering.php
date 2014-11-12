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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 *
 * Course Instances table class
 *
 */
class CoursesTableOffering extends JTable
{
	/**
	 * ID, primary key for course instances table
	 *
	 * @var int(11)
	 */
	var $id = NULL;

	/**
	 * Course id of this instance (references #__courses.gidNumber)
	 *
	 * @var int(11)
	 */
	var $course_id = NULL;

	/**
	 * Instance alias
	 *
	 * @var varchar(255)
	 */
	var $alias = NULL;

	/**
	 * Instance title
	 *
	 * @var varchar(255)
	 */
	var $title = NULL;

	/**
	 * Instance term (i.e. semester, but more generic language)
	 *
	 * @var varchar(255)
	 */
	var $term = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $state = NULL;

	/**
	 * Start date for instance
	 *
	 * @var date
	 */
	//var $start_date = NULL;

	/**
	 * End date for instance
	 *
	 * @var date
	 */
	//var $end_date = NULL;

	/**
	 * Start publishing date
	 *
	 * @var datetime
	 */
	var $publish_up = NULL;

	/**
	 * End publishing date
	 *
	 * @var datetime
	 */
	var $publish_down = NULL;

	/**
	 * Created date for unit
	 *
	 * @var datetime
	 */
	var $created = NULL;

	/**
	 * Who created the unit (reference #__users.id)
	 *
	 * @var int(11)
	 */
	var $created_by = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $params = NULL;

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
	 * @return  object  CoursesTableOffering
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
			$inst = new CoursesTableOffering(JFactory::getDBO());
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
			$this->setError(JText::_('Please provide a course ID.'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(JText::_('Please provide a title.'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = strtolower($this->title);
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->alias);
		$this->makeAliasUnique();

		if (!$this->id)
		{
			$this->created    = JFactory::getDate()->toSql();
			$this->created_by = JFactory::getUser()->get('id');
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
		$sql = "SELECT alias from $this->_tbl WHERE `course_id`=" . $this->_db->Quote(intval($this->course_id));
		if ($this->id)
		{
			$sql .= " AND `id`!=" . $this->_db->Quote(intval($this->id));
		}
		$this->_db->setQuery($sql);
		$result = $this->_db->loadResultArray();

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
			$where[] = "c.alias=" . $this->_db->Quote($filters['course_alias']);
		}
		else if (isset($filters['course_id'])) // && $filters['course_id'])
		{
			$where[] = "c.id=" . $this->_db->Quote(intval($filters['course_id']));
		}

		if (isset($filters['available']) && $filters['available'])
		{
			$now = JFactory::getDate()->toSql();

			$where[] = "(ci.publish_up = '0000-00-00 00:00:00' OR ci.publish_up <= " . $this->_db->Quote($now) . ")";
			$where[] = "(ci.publish_down = '0000-00-00 00:00:00' OR ci.publish_down >= " . $this->_db->Quote($now) . ")";

			$filters['state'] = 1;
		}

		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "ci.state=" . $this->_db->Quote(intval($filters['state']));
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