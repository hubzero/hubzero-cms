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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'member.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'role.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'memberBadge.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'prerequisite.php');

/**
 * Member model class for a course
 */
class CoursesModelMember extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableMember';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'manager';

	/**
	 * CoursesModelMemberBadge
	 *
	 * @var object
	 */
	private $_badge = NULL;

	/**
	 * CoursesModelPrerequisites
	 *
	 * @var object
	 **/
	private $_prerequisites = null;

	/**
	 * Constructor
	 *
	 * @param   string $uid User ID
	 * @param   string $cid Course ID
	 * @param   string $oid Offering ID
	 * @param   string $sid Section ID
	 * @return  void
	 */
	public function __construct($uid, $cid=0, $oid=0, $sid=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableMember($this->_db);

		if (is_numeric($uid) || is_string($uid))
		{
			$this->_tbl->load($uid, $cid, $oid, $sid);
		}
		else if (is_object($uid) || is_array($uid))
		{
			$this->bind($uid);
		}

		if (!$this->get('role_alias'))
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
	 * Returns a reference to a member object
	 *
	 * @param   string $uid User ID
	 * @param   string $cid Course ID
	 * @param   string $oid Offering ID
	 * @param   string $sid Section ID
	 * @return  object CoursesModelMember
	 */
	static function &getInstance($uid=null, $cid=0, $oid=0, $sid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid . '_' . $uid . '_' . $sid]))
		{
			$instances[$oid . '_' . $uid . '_' . $sid] = new self($uid, $cid, $oid, $sid);
		}

		return $instances[$oid . '_' . $uid . '_' . $sid];
	}

	/**
	 * Get member badge
	 *
	 * @return  object CoursesModelMemberBadge
	 */
	public function badge()
	{
		if (!isset($this->_badge))
		{
			$this->_badge = CoursesModelMemberBadge::loadByMemberId($this->get('id'));
		}

		return $this->_badge;
	}

	/**
	 * Get courses prerequisites per member
	 *
	 * @param   object $gradebook
	 * @return  object CoursesModelPrerequisite
	 */
	public function prerequisites($gradebook)
	{
		if (!isset($this->_prerequisites))
		{
			$this->_prerequisites = new CoursesModelPrerequisite($this->get('section_id'), $gradebook, $this->get('id'));
		}

		return $this->_prerequisites;
	}

	/**
	 * Delete an entry and associated data
	 *
	 * @return  boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove gradebook information

		return parent::delete();
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string  $action Action to check
	 * @param   string  $item   Item type to check action against
	 * @return  boolean True if authorized, false if not
	 */
	public function access($action='', $item='offering')
	{
		if (!$action)
		{
			return $this->get('permissions');
		}
		return $this->get('permissions')->get('access-' . strtolower($action) . '-' . $item);
	}

	/**
	 * Get a unique token, generating one if it doesn't exist
	 *
	 * @return  string
	 */
	public function token()
	{
		if (!$this->get('token'))
		{
			$this->set('token', $this->generateToken());
			$this->store(false);
		}

		return $this->get('token');
	}

	/**
	 * Generate a unique token
	 *
	 * @return  string
	 */
	public function generateToken()
	{
		$chars = array(0,1,2,3,4,5,6,7,8,9); //,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$sn = '';
		$max = count($chars)-1;
		for ($i=0;$i<20;$i++)
		{
			$sn .= (!($i % 5) && $i ? '-' : '') . $chars[rand(0, $max)];
		}

		if ($this->_tbl->tokenExists($sn))
		{
			return $this->generateToken();
		}

		return $sn;
	}
}

