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
 * Courses controller class for an offering
 */
class CoursesControllerOffering extends \Hubzero\Component\SiteController
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
		if (!$this->course->exists() || $this->course->isDeleted() || $this->course->isUnpublished())
		{
			JError::raiseError(404, JText::_('COM_COURSES_NO_COURSE_FOUND'));
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
		if (!$this->course->offering($offering)->exists() || $this->course->offering($offering)->isDeleted() || $this->course->offering($offering)->isUnpublished())
		{
			JError::raiseError(404, JText::_('COM_COURSES_NO_OFFERING_FOUND'));
			return;
		}

		// Ensure the course has been published or has been approved
		if (!$this->course->offering()->access('manage', 'section') && !$this->course->isAvailable())
		{
			JError::raiseError(404, JText::_('COM_COURSES_NOT_PUBLISHED'));
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
		$pathway = JFactory::getApplication()->getPathway();

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
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Redirect to login page
	 *
	 * @return     void
	 */
	public function loginTask($message = '')
	{
		$rtrn = base64_encode(JRoute::_($this->course->offering()->link() . '&task=' . $this->_task, false, true));
		$link = str_replace('&amp;', '&', JRoute::_('index.php?option=com_users&view=login&return=' . $rtrn));
		$this->setRedirect(
			$link,
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
		if (!$this->course->offering()->access('manage', 'section') && !$this->course->offering()->isAvailable())
		{
			JError::raiseError(404, JText::_('COM_COURSES_NO_OFFERING_FOUND'));
			return;
		}

		$tmpl = $this->config->get('tmpl', '');

		if ($tmpl && !JRequest::getWord('tmpl', false))
		{
			JRequest::setVar('tmpl', $tmpl);
		}

		// Get the active tab (section)
		$default = 'outline';
		$this->view->nonadmin = 0;
		if ($this->course->offering()->access('manage', 'section'))
		{
			$app = JFactory::getApplication();

			$this->view->nonadmin = JRequest::getInt('nonadmin', $app->getUserState(
				$this->_option . '.offering' . $this->course->offering()->get('id') . '.nonadmin',
				0
			));
			$app->setUserState(
				$this->_option . '.offering' . $this->course->offering()->get('id') . '.nonadmin',
				$this->view->nonadmin
			);

			$default = ($this->view->nonadmin ? $default : 'dashboard');
		}

		$this->view->active = JRequest::getVar('active', $default);

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher = JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		$plugins = $dispatcher->trigger('onCourseAreas', array());

		// Get tab access
		foreach ($plugins as $plugin)
		{
			$course_plugin_access[$plugin['name']] = $plugin['default_access'];
		}

		// If active tab is not one of available tabs
		if (!in_array($this->view->active, array_keys($course_plugin_access)))
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

		$this->view->course               = $this->course;
		$this->view->user                 = $this->juser;
		$this->view->config               = $this->config;
		$this->view->plugins              = $plugins;
		$this->view->course_plugin_access = $course_plugin_access;
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
			$this->loginTask(JText::_('COM_COURSES_ENROLLMENT_REQUIRES_LOGIN'));
			return;
		}

		$offering = $this->course->offering();

		// Is the user a manager or student?
		if ($offering->isManager() || $offering->isStudent())
		{
			// Yes! Already enrolled
			// Redirect back to the course page
			$this->setRedirect(
				JRoute::_($offering->link()),
				JText::_('COM_COURSES_ALREADY_ENROLLED')
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
					$this->setError(JText::sprintf('COM_COURSES_ERROR_CODE_INVALID', $code));
				}
				// Has it already been redeemed?
				if ($coupon->isRedeemed())
				{
					$this->setError(JText::sprintf('COM_COURSES_ERROR_CODE_ALREADY_REDEEMED', $code));
				}
				else
				{
					// Has it expired?
					if ($coupon->isExpired())
					{
						$this->setError(JText::sprintf('COM_COURSES_ERROR_CODE_EXPIRED', $code));
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
							JRoute::_($offering->link() . '&task=enroll&code=' . $code)
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

		if ($enrolled)
		{
			$link = $offering->link();

			JPluginHelper::importPlugin('courses');
			$data = JDispatcher::getInstance()->trigger('onCourseEnrolled', array(
				$this->course, $offering, $offering->section()
			));
			if ($data && count($data) > 0)
			{
				$link = implode('', $data);
			}
			$this->setRedirect(
				JRoute::_($link)
			);
			return;
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
	 * Display an offering asset
	 *
	 * @return     void
	 */
	public function assetTask()
	{
		$sparams    = new JRegistry($this->course->offering()->section()->get('params'));
		$section_id = $this->course->offering()->section()->get('id');
		$asset      = new CoursesModelAsset(JRequest::getInt('asset_id', null));
		$asset->set('section_id', $section_id);

		// First, check if current user has access to course
		if (!$this->course->offering()->access('view'))
		{
			// Is a preview available?
			$preview = $sparams->get('preview', 0);

			// If no preview is available or if type  is form (i.e. you can never preview forms)
			if (!$preview || $asset->get('type') == 'form')
			{
				// Check if they're logged in
				if ($this->juser->get('guest'))
				{
					$this->loginTask(JText::_('COM_COURSES_ENROLLMENT_REQUIRED_FOR_ASSET'));
					return;
				}
				else
				{
					// Redirect back to the course outline
					$this->setRedirect(
						JRoute::_($this->course->offering()->link()),
						JText::_('COM_COURSES_ENROLLMENT_REQUIRED_FOR_ASSET'),
						'warning'
					);
					return;
				}
			}
			elseif ($preview == 2) // Preview is only for first unit
			{
				$units = $asset->units();
				if ($units && count($units) > 0)
				{
					foreach ($units as $unit)
					{
						if ($unit->get('ordering') > 1)
						{
							// Check if they're logged in
							if ($this->juser->get('guest'))
							{
								$this->loginTask(JText::_('COM_COURSES_ENROLLMENT_REQUIRED_FOR_ASSET'));
								return;
							}
							else
							{
								// Redirect back to the course outline
								$this->setRedirect(
									JRoute::_($this->course->offering()->link()),
									JText::_('COM_COURSES_ENROLLMENT_REQUIRED_FOR_ASSET'),
									'warning'
								);
								return;
							}
						}
					}
				}
			}
		}

		// If not a manager and either the offering or section is unpublished...
		if (!$this->course->offering()->access('manage') && (!$this->course->offering()->isPublished() || !$this->course->offering()->section()->isPublished()))
		{
			JError::raiseError(403, JText::_('COM_COURSES_ERROR_ASSET_UNAVAILABLE'));
		}

		if (!$this->course->offering()->access('manage') && !$asset->isAvailable())
		{
			// Allow expired forms to pass through (i.e. so students can see their results)
			if (!$asset->get('type') == 'form' || !$asset->ended())
			{
				// Redirect back to the course outline
				$this->setRedirect(
					JRoute::_($this->course->offering()->link()),
					JText::_('COM_COURSES_ERROR_ASSET_UNAVAILABLE'),
					'warning'
				);
				return;
			}
		}

		// Check prerequisites
		$member = $this->course->offering()->section()->member(JFactory::getUser()->get('id'));
		if (is_null($member->get('section_id')))
		{
			$member->set('section_id', $section_id);
		}
		$prerequisites = $member->prerequisites($this->course->offering()->gradebook());

		if (!$this->course->offering()->access('manage') && !$prerequisites->hasMet('asset', $asset->get('id')))
		{
			$prereqs      = $prerequisites->get('asset', $asset->get('id'));
			$requirements = array();
			foreach ($prereqs as $pre)
			{
				$reqAsset = new CoursesModelAsset($pre['scope_id']);
				$requirements[] = $reqAsset->get('title');
			}

			$requirements = implode(', ', $requirements);

			// Redirect back to the course outline
			$this->setRedirect(
				JRoute::_($this->course->offering()->link()),
				JText::sprintf('COM_COURSES_ERROR_ASSET_HAS_PREREQ', $requirements),
				'warning'
			);
			return;
		}

		// If requesting a file from a wiki type asset, then serve that up directly
		if ($asset->get('subtype') == 'wiki' && JRequest::getVar('file', false))
		{
			echo $asset->download($this->course);
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
			$this->loginTask(JText::_('COM_COURSES_NOT_LOGGEDIN'));
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

