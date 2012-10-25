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
class CoursesTableInstance extends JTable
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
	 * Instance section number
	 * 
	 * @var int(11)
	 */
	var $section = NULL;

	/**
	 * Instance instructor id (would default to course creator id) - (references #__users.id)
	 * 
	 * @var int(11)
	 */
	var $instructor_id = NULL;

	/**
	 * Start date for instance
	 * 
	 * @var date
	 */
	var $start_date = NULL;

	/**
	 * End date for instance
	 * 
	 * @var date
	 */
	var $end_date = NULL;

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
	 * Contructor method for JTable class
	 * 
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__course_instances', 'id', $db);
	}

	/**
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param      string $pagename The page to load
	 * @param      string $scope    The page scope
	 * @return     object WikiPage
	 */
	static function &getInstance($alias=NULL)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$alias])) 
		{
			$inst = new CoursesTableInstance(JFactory::getDBO());
			$inst->load($alias);

			$instances[$alias] = $inst;
		}

		return $instances[$alias];
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function load($oid=NULL)
	{
		if ($oid === NULL) 
		{
			return false;
		}
		if (is_numeric($oid))
		{
			return parent::load($oid);
		}
		$oid = trim($oid);

		$query = "SELECT * FROM $this->_tbl WHERE alias='$oid'";

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Override the check function to do a little input cleanup
	 * 
	 * @return return true
	 */
	public function check()
	{
		parent::check();
	}

	/**
	 * Build query method
	 * 
	 * @param  array $filters
	 * @return $query database query
	 */
	public function buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS ci";
		$query .= " INNER JOIN #__courses AS c ON c.id=ci.course_id";

		$where = array();

		if (isset($filters['course_alias']) && $filters['course_alias']) 
		{
			$where[] = "c.alias=" . $this->_db->Quote($filters['course_alias']);
		}
		else if (isset($filters['course_id']) && $filters['course_id']) 
		{
			$where[] = "c.id=" . $this->_db->Quote($filters['course_id']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get an object list of course units
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function getCourseInstances($filters=array())
	{
		$query  = "SELECT ci.*";
		$query .= $this->buildquery($filters);

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