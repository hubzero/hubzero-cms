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

require_once(JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . 'notes' . DS . 'tables' . DS . 'note.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

/**
 * Courses model class for a course
 */
class CoursesPluginModelNote extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableMemberNote';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'note';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_notes = null;

	/**
	 * Returns a reference to a course model
	 *
	 * This method must be invoked as:
	 *     $offering = CoursesModelAnnouncement::getInstance($alias);
	 *
	 * @param      integer $oid ID (int)
	 * @return     object CoursesModelCourse
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new CoursesPluginModelNote($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get a list of assets for a unit
	 *   Accepts an array of filters to apply to the list of assets
	 * 
	 * @param      array $filters Filters to apply
	 * @return     object CoursesModelIterator
	 */
	public function notes($filters=array())
	{
		if (!isset($filters['created_by']))
		{
			$juser = JFactory::getUser();
			$filters['created_by'] = (int) $juser->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}

		if (isset($filters['count']) && $filters['count'])
		{
			return $this->_tbl->count($filters);
		}

		if (!isset($this->_notes) || !is_a($this->_notes, 'CoursesModelIterator'))
		{
			//$tbl = new CoursesTableMemberNote($this->_db);

			if ($results = $this->_tbl->find($filters))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesPluginModelNote($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_notes = new CoursesModelIterator($results);
		}

		return $this->_notes;
	}
}

