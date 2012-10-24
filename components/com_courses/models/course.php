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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Courses model class for a course
 */
class CoursesModelCourse extends JObject
{
	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_data = array();

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->course = new CoursesCourse($this->_db);
		$this->course->load($oid);
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
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$inst = new CoursesModelCourse($oid);

			$instances[$oid] = $inst;
		}

		return $instances[$oid];
	}

	/**
	 * Check if a property is set
	 * 
	 * @param      string $property Name of property to set
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) 
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Method to set the article id
	 *
	 * @access	public
	 * @param	int	Article ID number
	 */
	/*public function setId($id)
	{
		// Set new course ID and wipe data
		$this->_id = $id;
		if ($id)
		{
			$this->_course = CoursesCourse::getInstance($this->_id);
		}
	}*/

	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	/*public function offerings()
	{
		if (!$this->_offerings)
		{
			$inst = JTable::getInstance('instance', 'CoursesTable');
			$this->_offerings = $inst->getCourseInstances(array(
				'course_cn' => $this->course->get('cn')
			));
		}
		return $this->_offerings;
	}*/

	/**
	 * Get a list of offerings for a course
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function offerings($idx=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->offerings))
		{
			$this->offerings = array();

			$inst = JTable::getInstance('instance', 'CoursesTable');
			$this->_offerings = $inst->getCourseInstances(array(
				'course_cn' => $this->course->get('cn')
			));

			$this->_db->setQuery($sql);
			if (($results = $this->_db->loadObjectList()))
			{
				$this->contributors = $results;
			}
		}

		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->offerings[$idx]))
				{
					return $this->offerings[$idx];
				}
				else
				{
					$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
					return false;
				}
			}
			else if (is_string($idx))
			{
				$idx = strtolower(trim($idx));
				switch ($idx)
				{
					case 'id':
						$ids = array();
						foreach ($this->offerings as $offering)
						{
							$ids[] = (int) $offering->id;
						}
						return $ids;
					break;

					case 'name':
						$names = array();
						foreach ($this->offerings as $offering)
						{
							$names[] = stripslashes($offering->title);
						}
						return $names;
					break;

					default:
						return $this->offerings;
					break;
				}
			}
		}

		return $this->offerings;
	}
}

