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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'member.php');

/**
 * Courses model class for a course
 */
class CoursesModelStudent extends CoursesModelMember
{
	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'student';

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid, $sid=null)
	{
		$this->_db = JFactory::getDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (is_numeric($oid) || is_string($oid))
			{
				if ($sid !== null)
				{
					$this->_tbl->load($oid);
				}
				else
				{
					$this->_tbl->loadBySection($oid, $sid);
				}
			}
			else if (is_object($oid))
			{
				$this->_tbl->bind($oid);

				$properties = $this->_tbl->getProperties();
				foreach (get_object_vars($oid) as $key => $property)
				{
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $property);
					}
				}
			}
			else if (is_array($oid))
			{
				$this->_tbl->bind($oid);

				$properties = $this->_tbl->getProperties();
				foreach (array_keys($oid) as $key)
				{
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $oid[$key]);
					}
				}
			}
		}
	}

	/**
	 * Returns a reference to a CoursesModelStudent object
	 *
	 * @param      integer $uid User ID
	 * @param      integer $sid Section ID
	 * @return     object CoursesModelStudent
	 */
	static function &getInstance($uid=null, $sid=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$sid . '_' . $uid])) 
		{
			$instances[$sid . '_' . $uid] = new CoursesModelStudent($uid, $sid);
		}

		return $instances[$sid . '_' . $uid];
	}

	/**
	 * Delete an entry and associated data
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function gradebook()
	{
		return true;
	}
}

