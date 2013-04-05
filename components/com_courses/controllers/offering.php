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

ximport('Hubzero_Controller');

/**
 * Courses controller class for an offering
 */
class CoursesControllerOffering extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->gid = JRequest::getVar('gid', '');
		if (!$this->gid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option
			);
			return;
		}

		// Load the course page
		$this->course = CoursesModelCourse::getInstance($this->gid);

		// Ensure we found the course info
		if (!$this->course->exists()) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Ensure the course has been published or has been approved
		if (!$this->course->isAvailable())
		{
			JError::raiseError(404, JText::_('COURSES_NOT_PUBLISHED'));
			return;
		}

		// No offering provided
		if (!($offering = JRequest::getVar('offering', '')))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=course&gid=' . $this->course->get('alias'))
			);
			return;
		}

		// Ensure we found the course info
		if (!$this->course->offering($offering)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_NO_OFFERING_FOUND'));
			return;
		}

		parent::execute();
	}

	/**
	 * Method to set the document path
	 * 
	 * @param      array $course_pages List of roup pages
	 * @return     void
	 */
	public function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		if ($this->course->exists()) 
		{
			$pathway->addItem(
				stripslashes($this->course->get('title')),
				'index.php?option=' . $this->_option . '&gid=' . $this->course->get('alias')
			);

			if ($this->course->offering()->exists()) 
			{
				$pathway->addItem(
					stripslashes($this->course->offering()->get('title')),
					'index.php?option=' . $this->_option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias')
				);
			}
		}
	}

	/**
	 * Method to build and set the document title
	 * 
	 * @return     void
	 */
	public function _buildTitle()
	{
		//set title used in view
		$this->_title = JText::_(strtoupper($this->_option));

		if ($this->course->exists()) 
		{
			$this->_title .= ': ' . stripslashes($this->course->get('title'));

			if ($this->course->offering()->exists()) 
			{
				$this->_title .= ': ' . stripslashes($this->course->offering()->get('title'));
			}
		}

		//set title of browser window
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Redirect to login page
	 * 
	 * @return     void
	 */
	public function loginTask($message = '')
	{
		$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->gid . '&offering=' . $this->course->offering()->get('alias') . '&task=' . $this->_task, false, true));
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . $return),
			JText::_($message),
			'warning'
		);
		return;
	}

	/**
	 * View a course
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Check if the offering is available
		if (!$this->course->offering()->isAvailable())
		{
			JError::raiseError(404, JText::_('COM_COURSES_NO_OFFERING_FOUND'));
			return;
		}

		// Get the active tab (section)
		$default = 'outline';
		if ($this->course->offering()->access('manage', 'section'))
		{
			$default = 'dashboard';
		}
		$active = JRequest::getVar('active', $default);
		$this->view->active = $active;

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Push some needed styles to the template
		// Pass in course type to include special css for paying courses
		//$this->_getCourseStyles($this->course->get('type'));
		$this->_getStyles($this->_option, $this->_controller . '.css');

		// Push some needed scripts to the template
		$this->_getScripts();

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway($this->course->offering()->pages());

		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		// then add overview to array
		$plugins = $dispatcher->trigger('onCourseAreas', array());
		array_unshift($plugins, array(
			'name'             => 'outline',
			'title'            => JText::_('Outline'),
			'default_access'   => 'anyone',
			'display_menu_tab' => true
		));

		// Get tab access
		foreach ($plugins as $plugin)
		{
			$course_plugin_access[$plugin['name']] = $plugin['default_access'];
		}

		// If active tab not outline and an not one of available tabs
		if ($this->view->active != 'outline' && !in_array($this->view->active, array_keys($course_plugin_access))) 
		{
			$this->view->active = 'outline';
		}

		// Get the sections
		$sections = $dispatcher->trigger('onCourse', array(
				$this->config,
				$this->course,
				$this->course->offering(),
				$this->action,
				array($this->view->active)
			)
		);

		if ($this->view->active == 'outline') 
		{
			Hubzero_Document::addComponentScript($this->_option, 'assets/js/courses.offering');
			Hubzero_Document::addSystemScript('jquery.masonry');

			$layout = $this->view->active;
			if (($unit = JRequest::getVar('unit', '')))
			{
				$layout = 'unit';
			}
			if (($group = JRequest::getVar('group', '')))
			{
				$layout = 'lecture';
			}
			$view = new JView(array(
				'name'   => $this->_controller, 
				'layout' => $layout
			));
			if (isset($unit))
			{
				$view->unit = $unit;
			}
			if (isset($group))
			{
				$view->group = $group;
			}

			$view->option     = $this->_option;
			$view->controller = $this->_controller;
			$view->tab        = $this->view->active;
			$view->course     = $this->course;
			$view->database   = $this->database;
			$view->config     = $this->config;

			$body = $view->loadTemplate();
		} 
		else 
		{
			$body = '';
		}

		// Push the overview view to the array of sections we're going to output
		array_unshift(
			$sections, 
			array(
				'html' => $body, 
				'metadata' => ''
			)
		);

		// If we are a special course load the special template
		/*if ($this->course->get('type') == 3) 
		{
			$this->view->setLayout('special');
		}*/

		$this->view->course               = $this->course;
		$this->view->user                 = $this->juser;
		$this->view->config               = $this->config;
		$this->view->plugins              = $plugins;
		$this->view->course_plugin_access = $course_plugin_access;
		//$this->view->pages                = $this->course->offering()->pages();
		$this->view->sections             = $sections;
		$this->view->notifications        = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Display an offering asset
	 * 
	 * @return     void
	 */
	public function enrollTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to enroll in a course.');
			return;
		}

		$offering = $this->course->offering();

		// Is the user a manager or student?
		if ($offering->isManager() || $offering->isStudent()) 
		{
			// Yes! Already enrolled
			// Redirect back to the course page
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('alias') . '&offering=' . $offering->get('alias')),
				JText::_('You are already enrolled in this course')
			);
			return;
		}

		$this->view->course = $this->course;
		$this->view->juser  = $this->juser;

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Can the user enroll?
		if (!$offering->section()->canEnroll())
		{
			$this->view->setLayout('enroll_closed');
			$this->view->display();
			return;
		}

		$enrolled = false;

		// If enrollment is open OR a coupon code was posted
		if (!$offering->section()->get('enrollment') || ($code = JRequest::getVar('code', '')))
		{
			$section_id = $offering->section()->get('id');

			// If a coupon code was posted
			if (isset($code))
			{
				// Get the coupon
				$coupon = $offering->section()->code($code);
				// Is it a valid code?
				if (!$coupon->exists())
				{
					$this->setError(JText::sprintf('"%s" is not a valid coupon code.', $code));
				}
				// Has it already been redeemed?
				if ($coupon->isRedeemed())
				{
					$this->setError(JText::sprintf('The code "%s" has already been redeemed.', $code));
				}
				else
				{
					// Has it expired?
					if ($coupon->isExpired())
					{
						$this->setError(JText::sprintf('The code "%s" has expired.', $code));
					}
				}
				if (!$this->getError())
				{
					// Is this a coupon for a different section?
					if ($offering->section()->get('id') != $coupon->get('section_id'))
					{
						$section = CoursesModelSection::getInstance($coupon->get('section_id'));
						if ($section->exists() && $section->get('offering_id') != $offering->get('id'))
						{
							$offering = CoursesModelOffering::getInstance($section->get('offering_id'));
							if ($offering->exists() && $offering->get('course_id') != $this->course->get('id'))
							{
								$this->course = CoursesModelCourse::getInstance($offering->get('course_id'));
							}
						}
						$this->setRedirect(
							JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('alias') . '&offering=' . $offering->get('alias') . ($section->get('alias') !== '__default' ? ':' . $section->get('alias') : '') . '&task=enroll&code=' . $code)
						);
						return;
					}
					// Redeem the code
					$coupon->redeem($this->juser->get('id'));// set('redeemed_by', $this->juser->get('id'));
					//$coupon->store();
				}
			}

			// If no errors
			if (!$this->getError())
			{
				// Add the user to the course
				$model = new CoursesModelMember(0); //::getInstance($this->juser->get('id'), $offering->get('id'));
				$model->set('user_id', $this->juser->get('id'));
				$model->set('course_id', $this->course->get('id'));
				$model->set('offering_id', $offering->get('id'));
				$model->set('section_id', $offering->section()->get('id'));
				if ($roles = $offering->roles())
				{
					foreach ($roles as $role)
					{
						if ($role->alias == 'student')
						{
							$model->set('role_id', $role->id);
							break;
						}
					}
				}
				$model->set('student', 1);
				if ($model->store(true))
				{
					$enrolled = true;
				}
				else
				{
					$this->setError($model->getError());
				}
			}
		}

		// If enrollment is srestricted and the user isn't enrolled yet
		if ($offering->section()->get('enrollment') == 1 && !$enrolled)
		{
			// Show a form for entering a coupon code
			$this->view->setLayout('enroll_restricted');
		}

		if ($this->getError())
		{
			$this->addComponentMessage($this->getError(), 'error');
		}
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Show a form for editing a course
	 * 
	 * @return     void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing a course
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		$this->view->setLayout('edit');

		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Show a form for editing a coure offering outline
	 * 
	 * @return     void
	 */
	public function editoutlineTask()
	{
		$this->course->access('manage');
		if (!$this->course->offering()->access('manage'))
		{
			JError::raiseError(401, JText::_('Not Authorized'));
			return;
		}

		// If we have a scope set, we're loading a specific outline piece (ex: a unit)
		if ($scope = JRequest::getWord('scope', false))
		{
			// Setup view
			$this->setView('manage', "edit{$scope}");

			$this->_getStyles($this->_option, $this->_task . '.css');

			$this->view->title         = "Edit {$scope}";
			$this->view->scope         = $scope;
			$this->view->scope_id      = JRequest::getInt('scope_id');

			$this->view->tab           = $this->active;
			$this->view->course        = $this->course;
			$this->view->database      = $this->database;
			$this->view->config        = $this->config;
			$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
			$this->view->display();

			return;
		}

		// Push some needed styles to the template
		$this->_getStyles($this->_option, $this->_controller . '.css');
		$this->_getStyles($this->_option, $this->_task . '.css');

		// Add underscore
		Hubzero_Document::addSystemScript('underscore-min');
		Hubzero_Document::addSystemScript('jquery.hoverIntent');

		// Push some needed scripts to the template
		$this->_getScripts('/assets/js/courses.outline');

		// Add 'uniform' js and css
		Hubzero_Document::addSystemStylesheet('uniform.css');
		Hubzero_Document::addSystemScript('jquery.uniform');

		// Add file uploader JS
		Hubzero_Document::addSystemScript('jquery.iframe.transport');
		Hubzero_Document::addSystemScript('jquery.fileupload');

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway($this->course->offering()->pages());

		// Setup view
		$this->setView('manage', 'editoutline');

		$this->view->title         = 'Edit Outline';
		$this->view->tab           = $this->active;
		$this->view->course        = $this->course;
		$this->view->database      = $this->database;
		$this->view->config        = $this->config;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Display an offering asset
	 * 
	 * @return     void
	 */
	public function assetTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to save course settings.');
			return;
		}

		if (!$this->course->offering()->access('view'))
		{
			JError::raiseError(401, JText::_('Not Authorized'));
			return;
		}

		$asset = new CoursesModelAsset(JRequest::getInt('asset_id', null));

		if (!$this->course->offering()->access('manage') && !$asset->isAvailable())
		{
			// Redirect back to the course outline
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias')),
				'This asset is not currently available.',
				'warning'
			);
			return;
		}

		echo $asset->render($this->course);
	}

	/**
	 * Save a course
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to save course settings.');
			return;
		}

		// Redirect back to the course page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('alias') . '&task=offerings')
		);
	}

	/**
	 * Delete a course
	 * This method initially displays a form for confirming deletion
	 * then deletes course and associated information upon POST
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('alias') . '&task=offerings')
		);
	}
}

