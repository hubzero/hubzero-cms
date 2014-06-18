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
	 * Constructor
	 *
	 * @param      integer $id Course offering ID or alias
	 * @return     void
	 */
	public function __construct($oid=null, $section_id=null)
	{
		$this->_db = JFactory::getDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (is_numeric($oid) || is_string($oid))
			{
				$this->_tbl->load($oid, $section_id);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to a course offering model
	 *
	 * This method must be invoked as:
	 *     $offering = CoursesModelOffering::getInstance($alias);
	 *
	 * @param      mixed $oid ID (int) or alias (string)
	 * @return     object CoursesModelOffering
	 */
	static function &getInstance($oid=null, $section_id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = 0;

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid . ($section_id ? '_' . $section_id : '');
		}
		else if (is_object($oid))
		{
			$key = $oid->get('id') . ($section_id ? '_' . $section_id : '');
		}
		else if (is_array($oid))
		{
			$key = $oid['id'] . ($section_id ? '_' . $section_id : '');
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $section_id);
		}

		return $instances[$key];
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
		if (!($this->_redeemer instanceof JUser))
		{
			$this->_redeemer = JUser::getInstance($this->get('redeemed_by'));
		}
		if ($property)
		{
			return $this->_redeemer->get($property);
		}
		return $this->_redeemer;
	}

	/**
	 * Check if a code has expired
	 *
	 * @return    string
	 */
	public function isExpired()
	{
		if (!$this->exists())
		{
			return false;
		}

		if ($this->isRedeemed())
		{
			return true;
		}

		$now = JFactory::getDate()->toSql();

		if ($this->get('expires')
		 && $this->get('expires') != $this->_db->getNullDate()
		 && $this->get('expires') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if a code has been redeemed
	 *
	 * @return    string
	 */
	public function isRedeemed()
	{
		if (!$this->exists())
		{
			return false;
		}
		if ($this->get('redeemed_by'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Generate a coupon code
	 *
	 * @return    string
	 */
	public function redeem($redeemed_by=0, $code=null)
	{
		if (!$code)
		{
			$code = $this->get('code');
		}
		if (!$redeemed_by)
		{
			$redeemed_by = JFactory::getUser()->get('id');
		}
		$this->set('redeemed_by', $redeemed_by);
		$this->set('redeemed', JFactory::getDate()->toSql());
		return $this->store();
	}
}

