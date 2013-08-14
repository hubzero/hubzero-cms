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

ximport('Hubzero_Plugin');

/**
 * Courses Plugin class for the outline
 */
class plgCoursesOutline extends Hubzero_Plugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onCourseAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => JText::_('PLG_COURSES_' . strtoupper($this->_name)),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => true
		);
		return $area;
	}

	/**
	 * Return html for display on top of the outline
	 * 
	 * @return     string
	 */
	public function onCourseBeforeOutline($course, $offering)
	{
		$html = '';

		if ($course->access('manage'))
		{
			$url = 'index.php?option=com_courses'
				. '&controller=offering'
				. '&gid=' . $course->get('alias')
				. '&offering=' . $offering->get('alias')
				. ($offering->section()->get('alias') != '__default' ? ':'
				. $offering->section()->get('alias') : '')
				. '&active=outline&action=build';

			$html = '<a class="btn edit" href="' . JRoute::_($url) . '">Build outline</a>';
		}

		return $html;
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
				//return $arr;
				$return = 'metadata';
			}
		}

		// Check to see if user is member and plugin access requires members
		if (!$course->offering()->section()->access('view')) 
		{
			$arr['html'] = '<p class="info">' . JText::sprintf('COURSES_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
			return $arr;
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('courses', $this->_name);

			// Course and action
			$this->course = $course;
			$action = strtolower(JRequest::getWord('action', ''));

			ximport('Hubzero_Plugin_View');
			$this->view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => $this->_name,
					'name'    => 'outline'
				)
			);
			$this->view->option     = JRequest::getCmd('option', 'com_courses');
			$this->view->controller = JRequest::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $config;
			$this->view->juser      = JFactory::getUser();

			switch ($action)
			{
				case 'build':
					$this->_build();
				break;

				default:
					Hubzero_Document::addPluginScript('courses', $this->_name);
					Hubzero_Document::addSystemScript('jquery.masonry');
					$this->_display();
				break;
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
	private function _display()
	{
		if (($unit = JRequest::getVar('unit', '')))
		{
			$this->view->setLayout('unit');
		}
		if (($group = JRequest::getVar('group', '')))
		{
			$this->view->setLayout('lecture');
		}

		if (isset($unit))
		{
			$this->view->unit = $unit;
		}
		if (isset($group))
		{
			$this->view->group = $group;
		}
	}

	/**
	 * Set redirect and message
	 * 
	 * @return     string
	 */
	private function _build()
	{
		if (!$this->course->access('manage'))
		{
			JError::raiseError(401, JText::_('Not Authorized'));
			return;
		}

		// If we have a scope set, we're loading a specific outline piece (ex: a unit)
		if ($scope = JRequest::getWord('scope', false))
		{
			// Setup view
			$this->view->setLayout("edit{$scope}");

			Hubzero_Document::addPluginStylesheet('courses', $this->_name, 'build.css');
			Hubzero_Document::addPluginStylesheet('courses', $this->_name, $scope.'.css');
			Hubzero_Document::addPluginScript('courses', $this->_name, $scope);

			// Add file uploader JS
			Hubzero_Document::addSystemScript('jquery.iframe-transport');
			Hubzero_Document::addSystemScript('jquery.fileupload');

			$this->view->title         = "Edit {$scope}";
			$this->view->scope         = $scope;
			$this->view->scope_id      = JRequest::getInt('scope_id');

			return;
		}

		// Add outline builder style and script
		Hubzero_Document::addPluginStylesheet('courses', $this->_name, 'build.css');
		Hubzero_Document::addPluginScript('courses', $this->_name, 'build');

		// Add underscore
		Hubzero_Document::addSystemScript('underscore-min');
		Hubzero_Document::addSystemScript('jquery.hoverIntent');

		// Add 'uniform' js and css
		Hubzero_Document::addSystemStylesheet('uniform.css');
		Hubzero_Document::addSystemScript('jquery.uniform');

		// Add file uploader JS
		Hubzero_Document::addSystemScript('jquery.iframe-transport');
		Hubzero_Document::addSystemScript('jquery.fileupload');

		// Setup view
		$this->view->setLayout('build');

		$this->view->title = 'Build Outline';
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      string $url  URL to redirect to
	 * @param      string $msg  Message to send
	 * @param      string $type Message type (message, error, warning, info)
	 * @return     void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}
}
