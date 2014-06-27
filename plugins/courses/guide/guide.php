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

/**
 * Courses Plugin class for pages
 */
class plgCoursesGuide extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call after course outline
	 *
	 * @param      object $course   Current course
	 * @param      object $offering Current offering
	 * @return     void
	 */
	public function onCourseAfterOutline($course, $offering)
	{
		$member = $offering->member(JFactory::getUser()->get('id'));
		if ($member->get('first_visit') && $member->get('first_visit') != '0000-00-00 00:00:00')
		{
			return;
		}
		elseif (!$member->get('id')
			&& is_object(\Hubzero\Utility\Cookie::eat('plugin.courses.guide'))
			&& isset(\Hubzero\Utility\Cookie::eat('plugin.courses.guide')->first_visit))
		{
			return;
		}

		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => $this->_name,
				'layout'  => 'overlay'
			)
		);
		$this->view->option     = JRequest::getCmd('option', 'com_courses');
		$this->view->controller = JRequest::getWord('controller', 'course');
		$this->view->course     = $course;
		$this->view->offering   = $offering;
		$this->view->juser      = JFactory::getUser();
		$this->view->plugin     = $this->_name;

		return $this->view->loadTemplate();
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onCourseAreas()
	{
		$tmpl = JRequest::getWord('tmpl', NULL);

		if (!isset($tmpl) || $tmpl != 'component')
		{
			\Hubzero\Document\Assets::addPluginStylesheet($this->_type, $this->_name);
			\Hubzero\Document\Assets::addPluginScript($this->_type, $this->_name, 'guide.overlay');
		}

		$area = array(
			'name'  => $this->_name,
			'title' => JText::_('PLG_COURSES_' . strtoupper($this->_name)),
			'default_access'  => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => true,
			'icon' => 'f059'
		);
		return $area;
	}

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param      object  $course      Current course
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onCourse($config, $course, $offering, $action='', $areas=null)
	{
		$return = 'html';
		$active = $this->_name;
		$active_real = $this->_name;

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'name' => $active
		);

		//get this area details
		$this_area = $this->onCourseAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = 'metadata';
			}
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html')
		{
			$active = strtolower(JRequest::getWord('unit', ''));

			if ($active == 'mark')
			{
				$action = 'mark';
			}
			if ($act = strtolower(JRequest::getWord('action', '')))
			{
				$action = $act;
			}

			$this->view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'courses',
					'element' => $this->_name,
					'name'    => $this->_name
				)
			);
			$this->view->option     = JRequest::getCmd('option', 'com_courses');
			$this->view->controller = JRequest::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $config;
			$this->view->juser      = JFactory::getUser();
			$this->view->plugin     = $this->_name;

			switch ($action)
			{
				case 'mark':   $this->_mark();   break;

				default: $this->_default(); break;
			}

			if (JRequest::getInt('no_html', 0))
			{
				ob_clean();
				header('Content-type: text/plain');
				echo $this->view->loadTemplate();
				exit();
			}
			$arr['html'] = $this->view->loadTemplate();
		}

		// Return the output
		return $arr;
	}

	/**
	 * Set redirect and message
	 *
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _default()
	{
		$this->view->setLayout('overlay');
	}

	/**
	 * Set redirect and message
	 *
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _mark()
	{
		$this->view->setLayout('mark');

		$member = $this->view->offering->member(JFactory::getUser()->get('id'));
		if ($member->get('first_visit') && $member->get('first_visit') != '0000-00-00 00:00:00')
		{
			return;
		}
		elseif (!$member->get('id'))
		{
			$cookie = \Hubzero\Utility\Cookie::eat('plugin.courses.guide');
			if (!is_object($cookie) || !isset($cookie->first_visit))
			{
				// Drop cookie
				$prefs                = array();
				$prefs['first_visit'] = JFactory::getDate()->toSql();
				$lifetime             = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake('plugin.courses.guide', $lifetime, $prefs);
			}
		}
		$member->set('first_visit', JFactory::getDate()->toSql());
		$member->store();
	}
}
