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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'member.badge.php');

/**
 * Courses model class for badges
 */
class CoursesModelMemberBadge extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableMemberBadge';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'memberbadge';

	/**
	 * Constructor
	 *
	 * @param   integer $oid Record ID
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new $this->_tbl_name($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
	}

	/**
	 * Load by member id
	 *
	 * Member id is unique to a course and section, and badges are unique to members.
	 * Therefore, member id also serves as a primary key of this table.
	 *
	 * @param   integer $id Member ID
	 * @return  mixed   Object on success, False on error
	 */
	public static function loadByMemberId($id)
	{
		if (is_numeric($id))
		{
			$obj = new CoursesModelMemberBadge();
			$obj->_tbl->loadByMemberId($id);
			return $obj;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Load by validation token
	 *
	 * Validation token is a unique hash that allows us to identify a users badge evidence without exposing their user id
	 *
	 * @param    string $token Badge assertion token
	 * @return   mixed  Object on success, False on error
	 */
	public static function loadByToken($token)
	{
		$obj = new CoursesModelMemberBadge();
		$obj->_tbl->load(array('validation_token' => $token));

		if (isset($obj->_tbl->id))
		{
			return $obj;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Store member badge
	 *
	 * @param   boolean $check Perform data validation? 
	 * @return  boolean
	 */
	public function store($check=true)
	{
		if (!$this->get('validation_token'))
		{
			// Generate validation token
			$token = str_replace(array('/', '+'), array('-', '-'), substr(base64_encode(openssl_random_pseudo_bytes(21)), 0, 20));
			$this->set('validation_token', $token);
		}

		return parent::store();
	}

	/**
	 * Check whether or not a student has earned the badge
	 *
	 * @return  bool
	 */
	public function hasEarned()
	{
		return ($this->get('earned') == 1) ? true : false;
	}
}