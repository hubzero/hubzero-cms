<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . 'notes' . DS . 'tables' . DS . 'note.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

/**
 * Courses model class for a course note
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
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	protected $_notes = null;

	/**
	 * Serialized string of filers
	 *
	 * @var string
	 */
	protected $_filters = null;

	/**
	 * Returns a reference to a course note model
	 *
	 * @param      integer $oid ID (int)
	 * @return     object CoursesPluginModelNote
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
	 * Get a list or count of notes
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

		if (!isset($this->_notes) || !($this->_notes instanceof CoursesModelIterator) || (!empty($filters) && serialize($filters) != $this->_filters))
		{
			$this->_filters = serialize($filters);

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

