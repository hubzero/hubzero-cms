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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'section.code.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

/**
 * Courses model class for a course
 */
class CoursesModelSectionCode extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableSectionCode';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'section_code';

	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_redeemer = NULL;

	/**
	 * Returns a reference to a course offering model
	 *
	 * @param      mixed $oid ID (int) or code (string)
	 * @return     object CoursesModelSectionCode
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
			$instances[$oid] = new CoursesModelSectionCode($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get the creator of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function redeemer($property=null)
	{
		if (!isset($this->_redeemer) || !is_object($this->_redeemer))
		{
			$this->_redeemer = JUser::getInstance($this->get('redeemed_by'));
		}
		if ($property && is_a($this->_redeemer, 'JUser'))
		{
			return $this->_redeemer->get($property);
		}
		return $this->_redeemer;
	}

	/**
	 * Generate a coupon code
	 *
	 * @return    string
	 */
	public function redeem($code=null)
	{
		if (!$code)
		{
			$code = $this->get('code');
		}
		return $this->_tbl->redeem($code);
	}
}

