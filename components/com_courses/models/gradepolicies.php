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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'grade.policies.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section.php');

/**
 * Courses model class for grade book
 */
class CoursesModelGradePolicies extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableGradePolicies';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'gradepolicies';

	/**
	 * Constructor
	 *
	 * @param      integer $id  Resource ID
	 * @param      integer $sid Section ID
	 * @return     void
	 */
	public function __construct($oid, $sid=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new $this->_tbl_name($this->_db);

		if (is_numeric($oid))
		{
			// Check if this is the default section
			if (!is_null($sid))
			{
				$section = new CoursesModelSection($sid);

				if (!$section->get('is_default'))
				{
					$config  = JComponentHelper::getParams('com_courses');
					$canEdit = $config->get('section_grade_policy', true);

					if (!$canEdit)
					{
						// We need to find the default section and use that grade policy
						$offering = new CoursesModelOffering($section->get('offering_id'));
						$default  = $offering->section('!!default!!');
						$oid      = $default->get('grade_policy_id');
					}
				}
			}
			$this->_tbl->load($oid);
		}
	}
}