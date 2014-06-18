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
 * Members Plugin class for courses
 */
class plgGroupsCourses extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => JText::_('PLG_GROUPS_COURSES'),
			'default_access'   => $this->params->get('plugin_access', 'anyone'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f09c'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$return = 'html';
		$active = $this->_name;
		$active_real = 'discussion';

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'name' => $active
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = 'metadata';
			}
		}

		$this->group    = $group;
		$this->database = JFactory::getDBO();

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
		$model = CoursesModelCourses::getInstance();

		$filters = array(
			'group'    => $group->get('cn'),
			'group_id' => $group->get('gidNumber'),
			'count'    => true
		);

		$arr['metadata']['count'] = $model->courses($filters);

		// Build the HTML
		if ($return == 'html')
		{
			$this->app = JFactory::getApplication();
			$this->jconfig = JFactory::getConfig();

			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'display'
				)
			);
			$view->option = $option;
			$view->group  = $group;

			$view->filters = $filters;
			$view->filters['count'] = false;
			$view->filters['limit'] = $this->app->getUserStateFromRequest(
				$option . '.plugin.courses.limit',
				'limit',
				$this->jconfig->getValue('config.list_limit'),
				'int'
			);
			$view->filters['start'] = $this->app->getUserStateFromRequest(
				$option . '.plugin.courses.limitstart',
				'limitstart',
				0,
				'int'
			);
			$view->filters['sortby'] = $this->app->getUserStateFromRequest(
				$option . '.plugin.courses.sortby',
				'sortby',
				''
			);
			$view->filters['search'] = $this->app->getUserStateFromRequest(
				$option . '.plugin.courses.search',
				'search',
				''
			);
			$view->filters['index'] = '';
			$view->filters['tag'] = '';

			if (!in_array($view->filters['sortby'], array('alias', 'title', 'popularity')))
			{
				$view->filters['sortby'] = 'title';
			}
			switch ($view->filters['sortby'])
			{
				case 'popularity':
					$view->filters['sort']  = 'students';
					$view->filters['sort_Dir'] = 'DESC';
				break;
				case 'title':
				case 'alias':
				default:
					$view->filters['sort']  = $view->filters['sortby'];
					$view->filters['sort_Dir'] = 'ASC';
				break;
			}

			$view->total   = $arr['metadata']['count'];
			$view->results = $model->courses($view->filters);

			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}
}
