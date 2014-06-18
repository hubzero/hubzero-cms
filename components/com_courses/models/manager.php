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
class CoursesModelManager extends CoursesModelMember
{
	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'manager';

	/**
	 * Constructor
	 *
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($uid, $cid=0, $oid=0, $sid=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableMember($this->_db);

		if (is_numeric($uid) || is_string($uid))
		{
			$this->_tbl->load($uid, $cid, $oid, $sid, 0);
		}
		else if (is_object($uid) || is_array($uid))
		{
			$this->bind($uid);
		}

		if (!$this->get('role_permissions'))
		{
			$result = new CoursesTableRole($this->_db);
			if ($result->load($this->get('role_id')))
			{
				foreach ($result->getProperties() as $key => $property)
				{
					$this->_tbl->set('__role_' . $key, $property);
				}
			}
		}
	}

	/**
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param      string $pagename The page to load
	 * @param      string $scope    The page scope
	 * @return     object CoursesModelMember
	 */
	static function &getInstance($uid=null, $cid=0, $oid=0, $sid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid . '_' . $uid]))
		{
			$instances[$oid . '_' . $uid] = new self($uid, $cid, $oid, $sid);
		}

		return $instances[$oid . '_' . $uid];
	}
}

