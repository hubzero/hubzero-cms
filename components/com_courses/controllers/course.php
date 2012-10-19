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
 * Courses controller class
 */
class CoursesControllerCourse extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		// Get the task
		//$this->_task  = JRequest::getVar('task', '');
		$this->gid    = JRequest::getVar('gid', '');
		if (!$this->gid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option
			);
			return;
		}

		// Load the course page
		$this->course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$this->course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Ensure it's an allowable course type to display
		if ($this->course->get('type') != 1 && $this->course->get('type') != 3)
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Ensure the course has been published or has been approved
		if ($this->course->get('published') != 1)
		{
			JError::raiseError(404, JText::_('COURSES_NOT_PUBLISHED'));
			return;
		}

		// Check authorization
		$this->_authorize('course');
		//$this->_authorize('page');

		$this->active = JRequest::getVar('active', '');

		/*if ($this->gid && !$this->_task) 
		{
			$this->_task = 'view';
		}*/
		if ($this->active && $this->_task) 
		{
			$this->action = ($this->_task == 'instance') ? '' : $this->_task;
			$this->_task = 'instance';
		}
		/*if ($this->_task == '') 
		{
			$this->_task = 'intro';
		}*/

		//are we serving up a file
		$uri = $_SERVER['REQUEST_URI'];
		$name = substr(strrchr($uri, '/'), 1);
		/*if (strstr($uri, 'Image:')) 
		{
			$file = strstr($uri, 'Image:');
			$this->_task = 'download';
		}
		elseif (strstr($uri, 'File:'))
		{
			$file = strstr($uri, 'File:');
			$this->_task = 'download';
		}*/
		
		if (substr(strtolower($name), 0, strlen('image:')) == 'image:'
		 || substr(strtolower($name), 0, strlen('file:')) == 'file:') 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=media&task=download&file=' . $file
			);
			return;
		}

		//$this->registerTask('__default', 'intro');

		parent::execute();
	}

	/**
	 * Set a notification
	 * 
	 * @param      string $message Message to set
	 * @param      string $type    Type [error, passed, warning]
	 * @return     void
	 */
	/*public function setNotification($message, $type)
	{
		//if type is not set, set to error message
		$type = ($type == '') ? 'error' : $type;

		//if message is set push to notifications
		if ($message != '') 
		{
			$this->addComponentMessage($message, $type);
		}
	}*/

	/**
	 * Get norifications
	 * 
	 * @return     array
	 */
	/*public function getNotifications()
	{
		//getmessages in quene 
		$messages = $this->getComponentMessage();

		//if we have any messages return them
		if ($messages) 
		{
			return $messages;
		}
	}*/

	/**
	 * Method to add stylesheets to the document.
	 * 
	 * @param      integer $course_type Parameter description (if any) ...
	 * @return     void
	 */
	public function _getCourseStyles($course_type = null)
	{
		$this->_getStyles($this->_option, $this->_task);

		if ($course_type == 3) 
		{
			JRequest::setVar('tmpl', 'course');
			$doc->addStyleSheet('/components' . DS . 'com_courses' . DS . 'assets' . DS . 'css' . DS . 'special.css');
		}
	}

	/**
	 * Push scripts to document head
	 * 
	 * @return     void
	 */
	public function _getCourseScripts()
	{
		$this->_getScripts('assets/js/' . $this->_name);
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
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);

			if ($this->gid) 
			{
				//$course = new Hubzero_Course();
				//$this->course->read($this->gid);

				$pathway->addItem(
					stripslashes($this->course->get('description')),
					'index.php?option=' . $this->_option . '&gid=' . $this->gid
				);
			}

			if ($this->_task == 'new') 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&task=' . $this->_task
				);
			}

			if ($this->_task && in_array($this->_task, array('display', 'intro', 'new'))) 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&gid=' . $this->gid . '&task=' . $this->_task
				);
			}

			if ($this->active && $this->active != 'overview') 
			{
				/*if (in_array($this->active, array_keys($course_pages))) 
				{
					$text = JText::_($course_pages[$this->active]['title']);
				} 
				else 
				{
					$text = JText::_('COURSE_' . strtoupper($this->active));
				}*/

				$pathway->addItem(
					JText::_('COURSE_' . strtoupper($this->active)), 
					'index.php?option=' . $this->_option . '&gid=' . $this->gid . '&active=' . $this->active
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
		//$option = substr($this->_option,4);

		//set title used in view
		$this->_title = JText::_(strtoupper($this->_name));

		if ($this->_task && $this->_task != 'intro') 
		{
			$this->_title = JText::_(strtoupper($this->_name . '_' . $this->_task));
		}

		if ($this->gid) 
		{
			$course = new Hubzero_Course();
			$this->course->read($this->gid);

			$this->_title = JText::_('COURSE') . ': ' . stripslashes($this->course->get('description'));
		}

		//set title of browser window
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Look up username in profiles
	 * 
	 * @param      string $user Username or user ID
	 * @return     array
	 */
	public function getMemberProfile($user)
	{
		/*if (is_numeric($user)) 
		{
			$sql = "SELECT * FROM #__xprofiles WHERE uidNumber='" . $user . "'";
		} 
		else 
		{
			$sql = "SELECT * FROM #__xprofiles WHERE username='" . $user . "'";
		}

		$this->_db->setQuery($sql);
		$profile = $this->_db->loadAssoc();

		return $profile;*/
		ximport('Hubzero_User_Profile');
		return Hubzero_User_Profile::getInstance($user);
	}

	/**
	 * Redirect to login page
	 * 
	 * @return     void
	 */
	public function loginTask($message = '')
	{
		$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->gid . '&task=' . $this->_task));
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . $return),
			$message,
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
		$inst = new CoursesInstance($this->database);
		$this->view->instances = $inst->getCourseInstances(array(
			'course_cn' => $this->course->get('cn')
		));

		if ($this->view->instances && count($this->view->instances) == 1)
		{
			JRequest::setVar('instance', $this->view->instances[0]->alias);
			return $this->instanceTask();
		}

		// Check authorization
		//$authorized = $this->_authorize();

		// Get the active tab (section)
		$tab = JRequest::getVar('active', 'overview');

		if ($tab == 'wiki') 
		{
			$path = '';
		} 
		else 
		{
			$path = DS . ltrim($this->config->get('uploadpath', '/site/courses'), DS);
		}

		$this->view->wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $this->course->get('cn'),
			'pageid'   => $this->course->get('gidNumber'),
			'filepath' => $path,
			'domain'   => $this->course->get('cn')
		);

		ximport('Hubzero_Wiki_Parser');
		$this->view->parser = Hubzero_Wiki_Parser::getInstance();

		// Push some needed styles to the template
		// Pass in course type to include special css for paying courses
		//$this->_getCourseStyles($this->course->get('type'));
		$this->_getStyles($this->_option, $this->_controller . '.css');

		// Push some needed scripts to the template
		$this->_getCourseScripts();

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		$this->view->course               = $this->course;
		$this->view->user                 = $this->juser;
		$this->view->config               = $this->config;
		$this->view->notifications        = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * View a course instance
	 * 
	 * @return     void
	 */
	public function instanceTask()
	{
		$this->view->setLayout('instance');

		$inst = JRequest::getVar('instance', '');
		if (!$inst)
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_INSTANCE_FOUND'));
			return;
		}

		$this->view->instance = CoursesInstance::getInstance($inst);

		// Check authorization
		$authorized = $this->_authorize();

		// Get the active tab (section)
		$tab = JRequest::getVar('active', 'overview');

		if ($tab == 'wiki') 
		{
			$path = '';
		} 
		else 
		{
			$path = DS . ltrim($this->config->get('uploadpath', '/site/courses'), DS);
		}

		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $this->course->get('cn'),
			'pageid'   => $this->course->get('gidNumber'),
			'filepath' => $path,
			'domain'   => $this->course->get('cn')
		);

		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		// Get the course pages if any
		$GPages = new CoursePages($this->database);
		$pages = $GPages->getPages($this->course->get('gidNumber'), true);

		if (in_array($tab, array_keys($pages)))
		{
			$wikiconfig['pagename'] .= DS . $tab;
		}

		// Push some vars to the course pages
		$GPages->parser     = $p;
		$GPages->config     = $wikiconfig;
		$GPages->course     = $this->course;
		$GPages->authorized = $authorized;
		$GPages->tab        = $tab;
		$GPages->pages      = $pages;

		// Get the content to display course pages
		$course_overview = $GPages->displayPage();

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$start = JRequest::getInt('limitstart', 0);

		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		// then add overview to array
		$hub_course_plugins = $dispatcher->trigger('onCourseAreas', array());
		array_unshift($hub_course_plugins, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone',
			'display_menu_tab' => true
		));

		// Get plugin access
		$course_plugin_access = Hubzero_Course_Helper::getPluginAccess($this->course);

		// If active tab not overview and an not one of available tabs
		if ($tab != 'overview' && !in_array($tab, array_keys($course_plugin_access))) 
		{
			$tab = 'overview';
		}

		// Limit the records if we're on the overview page
		if ($tab == 'overview') 
		{
			$limit = 5;
		}

		$limit = ($limit == 0) ? 'all' : $limit;

		// Get the sections
		$sections = $dispatcher->trigger('onCourse', array(
				$this->course,
				$this->_option,
				$authorized,
				$limit,
				$start,
				$this->action,
				$course_plugin_access,
				array($tab)
			)
		);

		// Push some needed styles to the template
		// Pass in course type to include special css for paying courses
		//$this->_getCourseStyles($this->course->get('type'));
		$this->_getStyles($this->_option, $this->_controller . '.css');

		// Push some needed scripts to the template
		$this->_getCourseScripts();

		// Add the courses JavaScript in for "special" courses
		if (Hubzero_Course::getInstance($this->gid)->get('type') == 3)
		{
			$this->_getScripts('assets/js/courses.jquery.js');
		}

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway($pages);

		// Add the default "About" section to the beginning of the lists
		if ($tab == 'overview') 
		{
			// Add the plugins.js
			$doc =& JFactory::getDocument();
			$doc->addScript('/components/com_courses/assets/js/plugins.js');

			$view = new JView(array('name' => $this->_controller, 'layout' => 'overview'));
			$view->course_overview = $course_overview;
			$view->tab = $GPages->tab;
			$view->course = $this->course;
			$view->authorized = $authorized;
			$view->database = $this->database;

			$body = $view->loadTemplate();
		} 
		else 
		{
			$body = '';
		}

		// Push the overview view to the array of sections we're going to output
		array_unshift($sections, array('html' => $body, 'metadata' => ''));

		// If we are a special course load the special template
		if ($this->course->get('type') == 3) 
		{
			$this->view->setLayout('special');
		}

		$this->view->course               = $this->course;
		$this->view->user                 = $this->juser;
		$this->view->gparams              = $this->config;
		$this->view->hub_course_plugins   = $hub_course_plugins;
		$this->view->course_plugin_access = $course_plugin_access;
		$this->view->pages                = $pages;
		$this->view->sections             = $sections;
		$this->view->tab                  = $tab;
		$this->view->authorized           = $authorized;
		$this->view->notifications        = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * View a course instance
	 * 
	 * @return     void
	 */
	public function instancesTask()
	{
		$this->view->setLayout('instance');

		$inst = JRequest::getVar('instance', '');
		if (!$inst)
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_INSTANCE_FOUND'));
			return;
		}

		$this->view->instance = CoursesInstance::getInstance($inst);

		// Check authorization
		$authorized = $this->_authorize();

		// Get the active tab (section)
		$tab = JRequest::getVar('active', 'overview');

		if ($tab == 'wiki') 
		{
			$path = '';
		} 
		else 
		{
			$path = DS . ltrim($this->config->get('uploadpath', '/site/courses'), DS);
		}

		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $this->course->get('cn'),
			'pageid'   => $this->course->get('gidNumber'),
			'filepath' => $path,
			'domain'   => $this->course->get('cn')
		);

		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		// Get the course pages if any
		$GPages = new CoursePages($this->database);
		$pages = $GPages->getPages($this->course->get('gidNumber'), true);

		if (in_array($tab, array_keys($pages)))
		{
			$wikiconfig['pagename'] .= DS . $tab;
		}

		// Push some vars to the course pages
		$GPages->parser     = $p;
		$GPages->config     = $wikiconfig;
		$GPages->course     = $this->course;
		$GPages->authorized = $authorized;
		$GPages->tab        = $tab;
		$GPages->pages      = $pages;

		// Get the content to display course pages
		$course_overview = $GPages->displayPage();

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$start = JRequest::getInt('limitstart', 0);

		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		// then add overview to array
		$hub_course_plugins = $dispatcher->trigger('onCourseAreas', array());
		array_unshift($hub_course_plugins, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone',
			'display_menu_tab' => true
		));

		// Get plugin access
		$course_plugin_access = Hubzero_Course_Helper::getPluginAccess($this->course);

		// If active tab not overview and an not one of available tabs
		if ($tab != 'overview' && !in_array($tab, array_keys($course_plugin_access))) 
		{
			$tab = 'overview';
		}

		// Limit the records if we're on the overview page
		if ($tab == 'overview') 
		{
			$limit = 5;
		}

		$limit = ($limit == 0) ? 'all' : $limit;

		// Get the sections
		$sections = $dispatcher->trigger('onCourse', array(
				$this->course,
				$this->_option,
				$authorized,
				$limit,
				$start,
				$this->action,
				$course_plugin_access,
				array($tab)
			)
		);

		// Push some needed styles to the template
		// Pass in course type to include special css for paying courses
		//$this->_getCourseStyles($this->course->get('type'));
		$this->_getStyles($this->_option, $this->_controller . '.css');

		// Push some needed scripts to the template
		$this->_getCourseScripts();

		// Add the courses JavaScript in for "special" courses
		if (Hubzero_Course::getInstance($this->gid)->get('type') == 3)
		{
			$this->_getScripts('assets/js/courses.jquery.js');
		}

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway($pages);

		// Add the default "About" section to the beginning of the lists
		if ($tab == 'overview') 
		{
			// Add the plugins.js
			$doc =& JFactory::getDocument();
			$doc->addScript('/components/com_courses/assets/js/plugins.js');

			$view = new JView(array('name' => $this->_controller, 'layout' => 'overview'));
			$view->course_overview = $course_overview;
			$view->tab = $GPages->tab;
			$view->course = $this->course;
			$view->authorized = $authorized;
			$view->database = $this->database;

			$body = $view->loadTemplate();
		} 
		else 
		{
			$body = '';
		}

		// Push the overview view to the array of sections we're going to output
		array_unshift($sections, array('html' => $body, 'metadata' => ''));

		// If we are a special course load the special template
		if ($this->course->get('type') == 3) 
		{
			$this->view->setLayout('special');
		}

		$this->view->course               = $this->course;
		$this->view->user                 = $this->juser;
		$this->view->gparams              = $this->config;
		$this->view->hub_course_plugins   = $hub_course_plugins;
		$this->view->course_plugin_access = $course_plugin_access;
		$this->view->pages                = $pages;
		$this->view->sections             = $sections;
		$this->view->tab                  = $tab;
		$this->view->authorized           = $authorized;
		$this->view->notifications        = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Perform necessary actions for when a member clicks 'join'
	 *   Closed membership - show the course page
	 *   Invite only - show the course page
	 *   Open membership - Go ahead and make them a member
	 *   Restricted membership - show a form for requesting membership
	 * 
	 * @return     void
	 */
	public function joinTask()
	{
		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Ensure we have a course to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course page
		$course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to join the ' . $this->course->get('description') . ' course.');
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the course params
		$gparams = new $paramsClass($this->course->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->addComponentMessage('Course membership is not managed in the course interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('cn'));
			return;
		}

		// Check if the user is already a member, applicant, invitee, or manager
		if ($this->course->is_member_of('applicants', $this->juser->get('id')) ||
			$this->course->is_member_of('members', $this->juser->get('id')) ||
			$this->course->is_member_of('managers', $this->juser->get('id')) ||
			$this->course->is_member_of('invitees', $this->juser->get('id'))) 
		{
			// Already a member - show the course page
			$this->_task = 'view';
			$this->view();
			return;
		}

		// Based on join policy is what happens
		switch ($this->course->get('join_policy'))
		{
			case 3:
				// Closed membership - show the course page
				$this->_task = 'view';
				$this->view();
			break;
			case 2:
				// Invite only - show the course page
				$this->_task = 'view';
				$this->view();
			break;
			case 1:
				// Output HTML
				$view = new JView(array('name' => 'join'));
				$this->view->option = $this->_option;
				$this->view->title = $this->_title;
				$this->view->course = $course;
				$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
				$this->view->display();
			break;
			case 0:
			default:
				// Open membership - Go ahead and make them a member
				$this->confirm();
			break;
		}
	}

	/**
	 * Cancel membership in a course
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$app = JFactory::getApplication();

		$return = strtolower(trim(JRequest::getVar('return', '', 'get')));

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to cancel your course membership.');
			return;
		}

		// Ensure we have a course to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course
		$course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the course params
		$gparams = new $paramsClass($this->course->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->addComponentMessage('Course membership is not managed in the course interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('cn'));
			return;
		}

		// Remove the user from the course
		$this->course->remove('managers', $this->juser->get('id'));
		$this->course->remove('members', $this->juser->get('id'));
		$this->course->remove('applicants', $this->juser->get('id'));
		$this->course->remove('invitees', $this->juser->get('id'));
		if ($this->course->update() === false) 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_CANCEL_MEMBERSHIP_FAILED'), 'error');
		}

		// Log the membership cancellation
		$log = new XCourseLog($this->database);
		$log->gid = $this->course->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action = 'membership_cancelled';
		$log->actorid = $this->juser->get('id');
		if (!$log->store())
		{
			$this->addComponentMessage($log->getError(), 'error');
		}

		// Remove record of reason wanting to join course
		$reason = new CoursesReason($this->database);
		$reason->deleteReason($this->juser->get('id'), $this->course->get('gidNumber'));

		$jconfig =& JFactory::getConfig();

		// Email subject
		$subject = JText::sprintf('COURSES_SUBJECT_MEMBERSHIP_CANCELLED', $this->course->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails', 'layout' => 'cancelled'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->course = $course;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the managers' e-mails
		$emailmanagers = $this->course->getEmails('managers');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('courses_cancelled_me', $subject, $message, $from, $this->course->get('managers'), $this->_option)))
		{
			$this->setError(JText::_('COURSES_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}

		// Action Complete. Redirect to appropriate page
		if ($return == 'browse') 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option));
		} 
		else 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->course->get('cn')));
		}
	}

	/**
	 * Confirm membership requests
	 * 
	 * @return     void
	 */
	public function confirmTask()
	{
		$app = JFactory::getApplication();

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to confirm your course status.');
			return;
		}
		// Ensure we have a course to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course
		$course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the course params
		$gparams = new $paramsClass($this->course->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->addComponentMessage('Course membership is not managed in the course interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('cn'));
			return;
		}

		// Get the managers' e-mails
		$emailmanagers = $this->course->getEmails('managers');

		// Auto-approve member for course without any managers
		if (count($emailmanagers) < 1) 
		{
			$this->course->add('managers', array($this->juser->get('id')));
		} 
		else 
		{
			if ($this->course->get('join_policy') == 0) 
			{
				$this->course->add('members', array($this->juser->get('id')));
			}
			else 
			{
				$this->course->add('applicants', array($this->juser->get('id')));
			}
		}
		
		if ($this->course->update() === false) 
		{
			$this->setError(JText::_('COURSES_ERROR_REGISTER_MEMBERSHIP_FAILED'));
		}

		if ($this->course->get('join_policy') == 1) 
		{
			// Instantiate the reason object and bind the incoming data
			$row = new CoursesReason($this->database);
			$row->uidNumber = $this->juser->get('id');
			$row->gidNumber = $this->course->get('gidNumber');
			$row->reason    = JRequest::getVar('reason', JText::_('COURSES_NO_REASON_GIVEN'), 'post');
			$row->reason    = Hubzero_View_Helper_Html::purifyText($row->reason);
			$row->date      = date('Y-m-d H:i:s', time());

			// Check and store the reason
			if (!$row->check()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
			if (!$row->store()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}

		// Log the membership request
		$log = new XCourseLog($this->database);
		$log->gid = $this->course->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action = 'membership_requested';
		$log->actorid = $this->juser->get('id');
		if (!$log->store())
		{
			$this->setError($log->getError());
		}
		// Log the membership approval if the join policy is open
		if ($this->course->get('join_policy') == 0) 
		{
			$log2 = new XCourseLog($this->database);
			$log2->gid = $this->course->get('gidNumber');
			$log2->uid = $this->juser->get('id');
			$log2->timestamp = date('Y-m-d H:i:s', time());
			$log2->action = 'membership_approved';
			$log2->actorid = $this->juser->get('id');
			if (!$log2->store()) 
			{
				$this->setError($log2->getError());
			}
		}

		$jconfig =& JFactory::getConfig();

		// E-mail subject
		$subject = JText::sprintf('COURSES_SUBJECT_MEMBERSHIP', $this->course->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails', 'layout' => 'request'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->course = $course;
		$eview->row = $row;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		if ($this->course->get('join_policy') == 1) 
		{
			$url = 'index.php?option=' . $this->_option . '&gid=' . $this->course->get('cn') . '&active=members';
			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('courses_requests_membership', $subject, $message, $from, $this->course->get('managers'), $this->_option, $this->course->get('gidNumber'), $url))) 
			{
				$this->setError(JText::_('COURSES_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
			}
		}

		// Push through to the courses listing
		$app->redirect(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->course->get('cn')), true);
	}

	/**
	 * Accept membership invitation
	 * 
	 * @return     void
	 */
	public function acceptTask()
	{
		$app = JFactory::getApplication();

		$return = strtolower(trim(JRequest::getVar('return', '', 'get')));

		// Check if they're logged in	
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to accept your course invitation.');
			return;
		}

		// Ensure we have a course to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course
		$course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		//do we have permission to join course
		if ($this->course->get('type') == 2) 
		{
			JError::raiseError(404, JText::_('You do not have permission to join this course.'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the course params
		$gparams = new $paramsClass($this->course->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->addComponentMessage('Course membership is not managed in the course interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('cn'));
			return;
		}

		//get invite token
		$token = JRequest::getVar('token','','get');

		//get current members and invitees
		$invitees = $this->course->get('invitees');
		$members = $this->course->get("members");

		//are we already a member
		if (in_array($this->juser->get('id'), $members)) 
		{
			$add->redirect(JRoute::_('index.php?option=com_courses&gid=' . $this->course->get("cn")));
			exit();
		}

		// Get invite emails
		$course_inviteemails = new Hubzero_Course_Invite_Email($this->database);
		$inviteemails = $course_inviteemails->getInviteEmails($this->course->get('gidNumber'), true);
		$inviteemails_with_token = $course_inviteemails->getInviteEmails($this->course->get('gidNumber'), false);

		//course log comment
		$log_comments = '';

		//check to make sure weve been invited
		if ($token)
		{
			$sql = "SELECT * FROM #__courses_inviteemails WHERE token=" . $this->database->quote($token);
			$this->database->setQuery($sql);
			$invite = $this->database->loadAssoc();

			if ($invite) 
			{
				$this->course->add('members',array($this->juser->get('id')));
				$this->course->update(); 

				$log_comments = $invite['email'];
				$update = "UPDATE `jos_course_enrollments` SET `uid`=" . $this->juser->get('id') . " WHERE MD5(`enrollment_id`)='" . $token . "'";
				$this->database->setQuery($update);
				$this->database->query();

				$sql = "DELETE FROM #__courses_inviteemails WHERE id=" . $this->database->quote($invite['id']);
				$this->database->setQuery($sql);
				$this->database->query();
			}
		}
		elseif (in_array($this->juser->get('email'), $inviteemails))
		{
			$this->course->add('members',array($this->juser->get('id')));
			$this->course->update();
			$sql = "DELETE FROM #__courses_inviteemails WHERE email='" . $this->juser->get('email') . "' AND gidNumber='" . $this->course->get('gidNumber') . "'";
			$this->database->setQuery($sql);
			$this->database->query();
		}
		elseif (in_array($this->juser->get('id'), $invitees))
		{
			$this->course->add('members',array($this->juser->get('id')));
			$this->course->remove('invitees',array($this->juser->get('id')));
			$this->course->update();
		}
		else
		{
			JError::raiseError(404, JText::_('You do not have permission to join this course.'));
			return;
		}

		// Log the invite acceptance
		$log = new XCourseLog($this->database);
		$log->gid = $this->course->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->comments = $log_comments;
		$log->action = 'membership_invite_accepted';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}

		$jconfig =& JFactory::getConfig();

		// E-mail subject
		$subject = JText::sprintf('COURSES_SUBJECT_MEMBERSHIP', $this->course->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails', 'layout' => 'accepted'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->course = $course;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('courses_accepts_membership', $subject, $message, $from, $this->course->get('managers'), $this->_option))) 
		{
			$this->setError(JText::_('COURSES_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}

		// Action Complete. Redirect to appropriate page
		if ($return == 'browse') 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option));
		} 
		else 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option . '&gid='. $this->course->get('cn')));
		}
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

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getStyles($this->_option, $this->_controller . '.css');

		// Push some needed scripts to the template
		$this->_getScripts('assets/js/' . $this->_name);

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to edit or create courses.');
			return;
		}

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask(JText::_('You must be logged in to edit or create courses.'));
			return;
		}

		// Check authorization
		if (!$this->config->get('access-edit-course') && $this->_task != 'new') 
		{
			JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
			return;
		}

		// Instantiate an Hubzero_Course object
		$course = new Hubzero_Course();

		if ($this->_task != 'new') 
		{
			// Ensure we have a course to work with
			if (!$this->gid) 
			{
				JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
				return;
			}

			// Load the course
			$this->course->read($this->gid);

			// Ensure we found the course info
			if (!$course) 
			{
				JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
				return;
			}

			$title = "Edit Course: ".$this->course->get('description');
		} 
		else 
		{
			$this->course->set('join_policy', $this->config->get('join_policy'));
			$this->course->set('privacy', $this->config->get('privacy'));
			$this->course->set('access', $this->config->get('access'));
			$this->course->set('published', $this->config->get('auto_approve'));

			$title = 'Create New Course';
		}

		//get directory for course file uploads
		if ($this->lid != '')
		{
			$lid = $this->lid;
		}
		elseif ($this->course->get('gidNumber'))
		{
			$lid = $this->course->get('gidNumber');
		}
		else
		{
			$lid = time().rand(0,1000);
		}

		// Get the course's interests (tags)
		$gt = new CoursesTags($this->database);
		$tags = $gt->get_tag_string($this->course->get('gidNumber'));

		if ($this->course) 
		{
			$course = $this->course;
			$tags  = $this->tags;
		}

		// Output HTML
		$this->view->title  = $title;
		$this->view->course  = $course;
		$this->view->tags   = $tags;
		$this->view->juser  = $this->juser;
		$this->view->lid    = $lid;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
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

		// Incoming
		$g_cn           = strtolower(trim(JRequest::getVar('cn', '', 'post')));
		$g_description  = preg_replace('/\s+/', ' ',trim(JRequest::getVar('description', JText::_('NONE'), 'post')));
		$g_privacy      = JRequest::getInt('privacy', 0, 'post');
		$g_gidNumber    = JRequest::getInt('gidNumber', 0, 'post');
		$g_published    = JRequest::getInt('published', 0, 'post');
		$g_public_desc  = trim(JRequest::getVar('public_desc',  '', 'post', 'none', 2));
		$g_private_desc = trim(JRequest::getVar('private_desc', '', 'post', 'none', 2));
		$g_restrict_msg = trim(JRequest::getVar('restrict_msg', '', 'post', 'none', 2));
		$g_join_policy  = JRequest::getInt('join_policy', 0, 'post');
		$tags = trim(JRequest::getVar('tags', ''));
		$g_discussion_email_autosubscribe = JRequest::getInt('discussion_email_autosubscribe', 0, 'post');
		$lid = JRequest::getInt('lid', 0, 'post');

		//Check authorization
		if ($this->_authorize() != 'manager' && $g_gidNumber != 0) 
		{
			JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
			return;
		}

		// Instantiate an Hubzero_Course object
		$course = new Hubzero_Course();

		// Is this a new entry or updating?
		$isNew = false;
		if (!$g_gidNumber) 
		{
			$isNew = true;

			// Set the task - if anything fails and we re-enter edit mode 
			// we need to know if we were creating new or editing existing
			$this->_task = 'new';
		} 
		else 
		{
			$this->_task = 'edit';

			// Load the course
			$this->course->read($g_gidNumber);
		}

		// Check for any missing info
		if (!$g_cn) 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_MISSING_INFORMATION') . ': ' . JText::_('COURSES_ID'), 'error');
		}
		if (!$g_description) 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_MISSING_INFORMATION') . ': ' . JText::_('COURSES_TITLE'), 'error');
		}

		// Push back into edit mode if any errors
		if ($this->getComponentMessage()) 
		{
			$this->course->set('published', $g_published);
			$this->course->set('description', $g_description);
			//$this->course->set('access', $g_access);
			$this->course->set('privacy', $g_privacy);
			$this->course->set('public_desc', $g_public_desc);
			$this->course->set('private_desc', $g_private_desc);
			$this->course->set('restrict_msg', $g_restrict_msg);
			$this->course->set('join_policy', $g_join_policy);
			$this->course->set('cn', $g_cn);
			$this->course->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
            
			$this->lid = $lid;
			$this->course = $course;
			$this->tags = $tags;
			$this->edit();
			return;
		}

		// Ensure the data passed is valid
		if ($g_cn == 'new' || $g_cn == 'browse') 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_INVALID_ID'), 'error');
		}
		if (!$this->_validCn($g_cn)) 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_INVALID_ID'), 'error');
		}
		if ($isNew && Hubzero_Course::exists($g_cn,true)) 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_COURSE_ALREADY_EXIST'), 'error');
		}

		// Push back into edit mode if any errors
		if ($this->getComponentMessage()) 
		{
			$this->course->set('published', $g_published);
			$this->course->set('description', $g_description);
			//$this->course->set('access', $g_access);
			$this->course->set('privacy', $g_privacy);
			$this->course->set('public_desc', $g_public_desc);
			$this->course->set('private_desc', $g_private_desc);
			$this->course->set('restrict_msg', $g_restrict_msg);
			$this->course->set('join_policy', $g_join_policy);
			$this->course->set('cn', $g_cn);
			$this->course->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);

			$this->lid = $lid;
			$this->course = $course;
			$this->tags = $tags;
			$this->edit();
			return;
		}

		// Get some needed objects
		$jconfig =& JFactory::getConfig();

		// Build the e-mail message
		if ($isNew) 
		{
			$subject = JText::sprintf('COURSES_SUBJECT_COURSE_REQUESTED', $g_cn);
		} 
		else 
		{
			$subject = JText::sprintf('COURSES_SUBJECT_COURSE_UPDATED', $g_cn);
		}

		if ($isNew) 
		{
			$type = 'courses_created';
		} 
		else 
		{
			$type = 'courses_changed';
		}

		// Build the e-mail message
		// Note: this is done *before* pushing the changes to the course so we can show, in the message, what was changed
		$eview = new JView(array('name' => 'emails', 'layout' => 'saved'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->course = $course;
		$eview->isNew = $isNew;
		$eview->g_description = $g_description;
		//$eview->g_access = $g_access;
		$eview->g_privacy = $g_privacy;
		$eview->g_public_desc = $g_public_desc;
		$eview->g_private_desc = $g_private_desc;
		$eview->g_restrict_msg = $g_restrict_msg;
		$eview->g_join_policy = $g_join_policy;
		$eview->g_cn = $g_cn;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Set the course changes and save
		$this->course->set('cn', $g_cn);
		if ($isNew) 
		{
			$this->course->create();
			$this->course->set('type', 1);
			$this->course->set('published', $g_published);
			$this->course->set('created', date("Y-m-d H:i:s"));
			$this->course->set('created_by', $this->juser->get('id'));

			$this->course->add('managers', array($this->juser->get('id')));
			$this->course->add('members', array($this->juser->get('id')));
		}

		$this->course->set('description', $g_description);
		//$this->course->set('access', $g_access);
		$this->course->set('privacy', $g_privacy);
		$this->course->set('public_desc', $g_public_desc);
		$this->course->set('private_desc', $g_private_desc);
		$this->course->set('restrict_msg',$g_restrict_msg);
		$this->course->set('join_policy',$g_join_policy);
		$this->course->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
		$this->course->update();

		// Process tags
		$gt = new CoursesTags($this->database);
		$gt->tag_object($this->juser->get('id'), $this->course->get('gidNumber'), $tags, 1, 1);

		// Log the course save
		$log = new XCourseLog($this->database);
		$log->gid = $this->course->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->actorid = $this->juser->get('id');

		// Rename the temporary upload directory if it exist
		if ($isNew) 
		{
			if ($lid != $this->course->get('gidNumber')) 
			{
				$config = $this->config;
				$bp = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/courses'), DS);
				if (is_dir($bp . DS . $lid)) 
				{
					rename($bp . DS . $lid, $bp . DS . $this->course->get('gidNumber'));
				}
			}

			$log->action = 'course_created';

			// Get plugins
			JPluginHelper::importPlugin('courses');
			$dispatcher =& JDispatcher::getInstance();

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger('onCourseNew', array($course));
			if (count($logs) > 0) 
			{
				$log->comments .= implode('', $logs);
			}
		} 
		else 
		{
			$log->action = 'course_edited';
		}

		if (!$log->store()) 
		{
			$this->addComponentMessage($log->getError(), 'error');
		}

		// Get the administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Get the "from" info
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Get plugins
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array($type, $subject, $message, $from, $this->course->get('managers'), $this->_option))) 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_EMAIL_MANAGERS_FAILED'), 'error');
		}

		if ($this->getComponentMessage()) 
		{
			$view = new JView(array('name' => 'error'));
			$this->view->title = $title;
			$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
			$this->view->display();
			return;
		}

		// Show success message to user
		if ($isNew) 
		{
			$this->addComponentMessage("You have successfully created the \"{$this->course->get('description')}\" course" , 'passed');
		} 
		else 
		{
			$this->addComponentMessage("You have successfully updated the \"{$this->course->get('description')}\" course" , 'passed');
		}

		// Redirect back to the course page
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $g_cn);
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
		// Build title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to delete a course.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
			return;
		}

		// Ensure we have a course to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course page
		$course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the course params
		$gparams = new $paramsClass($this->course->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->addComponentMessage('Course membership is not managed in the course interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('cn'));
			return;
		}

		// Push some needed styles to the template
		$this->_getStyles();

		// Push some needed scripts to the template
		$this->_getScripts();

		// Get number of course members
		$members  = $this->course->get('members');
		$managers = $this->course->get('managers');

		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher =& JDispatcher::getInstance();

		// Incoming
		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');
		$msg = trim(JRequest::getVar('msg', '', 'post'));

		// Did they confirm delete?
		if (!$process || !$confirmdel) 
		{
			if ($process && !$confirmdel) 
			{
				$this->addComponentMessage(JText::_('COURSES_ERROR_CONFIRM_DELETION'), 'error');
			}

			$log = JText::sprintf('COURSES_MEMBERS_LOG',count($members));

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger('onCourseDeleteCount', array($course));
			if (count($logs) > 0) 
			{
				$log .= '<br />' . implode('<br />', $logs);
			}

			// Output HTML
			$view = new JView(array('name' => 'delete'));
			$this->view->option = $this->_option;
			$this->view->title  = 'Delete Course: ' . $this->course->get('description');
			$this->view->course  = $course;
			$this->view->log    = $log;
			$this->view->msg    = $msg;
			$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
			$this->view->display();
			return;
		}

		// Start log
		$log  = JText::sprintf('COURSES_SUBJECT_COURSE_DELETED', $this->course->get('cn'));
		$log .= JText::_('COURSES_TITLE') . ': ' . $this->course->get('description') . "\n";
		$log .= JText::_('COURSES_ID') . ': ' . $this->course->get('cn') . "\n";
		$log .= JText::_('COURSES_PRIVACY') . ': ' . $this->course->get('access') . "\n";
		$log .= JText::_('COURSES_PUBLIC_TEXT') . ': ' . stripslashes($this->course->get('public_desc'))  . "\n";
		$log .= JText::_('COURSES_PRIVATE_TEXT') . ': ' . stripslashes($this->course->get('private_desc'))  . "\n";
		$log .= JText::_('COURSES_RESTRICTED_MESSAGE') . ': ' . stripslashes($this->course->get('restrict_msg')) . "\n";

		// Log ids of course members
		if ($members) 
		{
			$log .= JText::_('COURSES_MEMBERS') . ': ';
			foreach ($members as $gu)
			{
				$log .= $gu . ' ';
			}
			$log .= '' . "\n";
		}
		$log .= JText::_('COURSES_MANAGERS') . ': ';
		foreach ($managers as $gm)
		{
			$log .= $gm . ' ';
		}
		$log .= '' . "\n";

		// Trigger the functions that delete associated content
		// Should return logs of what was deleted
		$logs = $dispatcher->trigger('onCourseDelete', array($course));
		if (count($logs) > 0) 
		{
			$log .= implode('',$logs);
		}

		// Build the file path
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $this->course->get('gidNumber');
		$config = $this->config;

		if (is_dir($path)) 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($path)) 
			{
				$this->addComponentMessage(JText::_('UNABLE_TO_DELETE_DIRECTORY'), 'error');
			}
		}

		$gidNumber = $this->course->get('gidNumber');
		$gcn = $this->course->get('cn');

		$deletedcourse = clone($course);

		// Delete course
		if (!$this->course->delete()) 
		{
			$view = new JView(array('name' => 'error'));
			$this->view->title = $title;
			if ($this->course->error) 
			{
				$this->addComponentMessage($this->course->error, 'error');
			}
			$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
			$this->view->display();
			return;
		}

		// Get and set some vars
		$date = date('Y-m-d H:i:s', time());

		$jconfig =& JFactory::getConfig();

		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail subject
		$subject = JText::sprintf('COURSES_SUBJECT_COURSE_DELETED', $gcn);

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails','layout' => 'deleted'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->gcn = $gcn;
		$eview->msg = $msg;
		$eview->course = $deletedcourse;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Send the message
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('courses_deleted', $subject, $message, $from, $members, $this->_option))) 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_EMAIL_MEMBERS_FAILED'), 'error');
		}

		// Log the deletion
		$xlog = new XCourseLog($this->database);
		$xlog->gid = $gidNumber;
		$xlog->uid = $this->juser->get('id');
		$xlog->timestamp = date('Y-m-d H:i:s', time());
		$xlog->action = 'course_deleted';
		$xlog->comments = $log;
		$xlog->actorid = $this->juser->get('id');
		if (!$xlog->store()) 
		{
			$this->addComponentMessage($xlog->getError(), 'error');
		}

		// Redirect back to the courses page
		$this->addComponentMessage("You successfully deleted the \"{$deletedcourse->get('description')}\" course", 'passed');
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
	}

	/**
	 * Invite users to a course
	 * This method initially displays a form for sending invites
	 * then processes those invites upon POST
	 * 
	 * @return     void
	 */
	public function inviteTask()
	{
		$app = JFactory::getApplication();
		
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to invite members to a course.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
			return;
		}

		// Ensure we have a course to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course page
		$course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get course params
		$gparams = new $paramsClass($this->course->get('params'));
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->addComponentMessage('Course membership is not managed in the course interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('cn'));
			return;
		}

		// Incoming
		$process = JRequest::getVar('process', '');
		$logins  = trim(JRequest::getVar('logins', '', 'post'));
		$msg     = trim(JRequest::getVar('msg', '', 'post'));
		$return  = trim(JRequest::getVar('return', '', 'get'));

		// Did they confirm delete?
		if (!$process || !$logins) 
		{
			if ($process && !$logins) 
			{
				$this->addComponentMessage(JText::_('Please provide a list of names or emails to invite.'), 'error');
			}

			// Build the page title
			$this->_buildTitle();

			// Build the pathway
			$this->_buildPathway();

			// Push some needed styles to the template
			$this->_getStyles();

			// Push some needed scripts to the template
			$this->_getScripts();

			// Output HTML
			$view = new JView(array('name' => 'invite'));
			$this->view->option = $this->_option;
			$this->view->title = $this->_title;
			$this->view->course = $course;
			$this->view->return = $return;
			$this->view->msg = $msg;
			$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
			$this->view->display();
			return;
		}

		$return = trim(JRequest::getVar('return', '', 'post'));
		$invitees = array();
		$inviteemails = array();
		$badentries = array();
		$apps = array();
		$mems = array();

		// Get all the course's members
		$members = $this->course->get('members');
		$applicants = $this->course->get('applicants');
		$current_invitees = $this->course->get('invitees');

		// Get invite emails
		$course_inviteemails = new Hubzero_Course_Invite_Email($this->database);
		$current_inviteemails = $course_inviteemails->getInviteEmails($this->course->get('gidNumber'), true);

		// Explode the string of logins/e-mails into an array
		if (strstr($logins, ',')) 
		{
			$la = explode(',', $logins);
		}
		else 
		{
			$la = array($logins);
		}

		foreach($la as $l)
		{
			// Trim up content
			$l = trim($l);

			// If it was a user id
			if (is_numeric($l)) 
			{
				$user = JUser::getInstance($l);
				$uid = $user->get('id');

				// Ensure we found an account
				if ($uid != '') 
				{
					// If not a member
					if (!in_array($uid, $members)) 
					{
						// If an applicant
						// Make applicant a member
						if (in_array($uid, $applicants)) 
						{
							$apps[] = $uid;
							$mems[] = $uid;
						} 
						else 
						{
							$invitees[] = $uid;
						}
					} 
					else 
					{
						$badentries[] = array($uid, 'User is already a member.');
					}
				}
			} 
			else 
			{
				// If not a userid check if proper email
				if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $l)) 
				{
					// Try to find an account that might match this e-mail
					$this->database->setQuery("SELECT u.id FROM #__users AS u WHERE u.email='" . $l . "' OR u.email LIKE '" . $l . "\'%' LIMIT 1;");
					$uid = $this->database->loadResult();
					if (!$this->database->query()) 
					{
						$this->addComponentMessage($this->database->getErrorMsg(), 'error');
					}

					// If we found an ID, add it to the invitees list
					if ($uid) 
					{
						// Check if user is already member or invitee
						// Check if applicant remove from applicants and add as member
						// Check if in current email invitee if not add a new email invite
						if (in_array($uid, $members) || in_array($uid, $current_invitees)) 
						{
							$badentries[] = array($uid, 'User is already a member or invitee.');
						} 
						elseif (in_array($uid, $applicants)) 
						{
							$apps[] = $uid;
							$mems[] = $uid;
						} 
						else 
						{
							$invitees[] = $uid;
						}
					} 
					else 
					{
						if (!in_array($l, $current_inviteemails)) 
						{
							$inviteemails[] = array('email' => $l, 'gidNumber' => $this->course->get('gidNumber'), 'token' => $this->randomString(32));
						} 
						else 
						{
							$badentries[] = array($l, 'Email address has already been invited.');
						}
					}
				} 
				else 
				{
					$badentries[] = array($l, 'Entry is not a valid email address or user.');
				}
			}
		}

		// Add the users to the invitee list and save
		$this->course->remove('applicants', $apps);
		$this->course->add('members', $mems);
		$this->course->add('invitees', $invitees);
		$this->course->update();

		// Add the inviteemails
		foreach ($inviteemails as $ie)
		{
			$course_inviteemails = new Hubzero_Course_Invite_Email($this->database);
			$course_inviteemails->save($ie);
		}

		// Log the sending of invites
		foreach ($invitees as $invite)
		{
			if (!in_array($invite,$current_invitees)) 
			{
				$log = new XCourseLog($this->database);
				$log->gid = $this->course->get('gidNumber');
				$log->uid = $invite;
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action = 'membership_invites_sent';
				$log->actorid = $this->juser->get('id');
				if (!$log->store()) 
				{
					$this->addComponentMessage($log->getError(), 'error');
				}
			}
		}

		// Sending of invites to emails
		foreach ($inviteemails as $invite)
		{
			if (!in_array($invite,$current_inviteemails)) 
			{
				$log = new XCourseLog($this->database);
				$log->gid = $this->course->get('gidNumber');
				$log->uid = $invite;
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action = 'membership_email_sent';
				$log->actorid = $this->juser->get('id');
				if (!$log->store()) 
				{
					$this->addComponentMessage($log->getError(), 'error');
				}
			}
		}

		// Get and set some vars
		$jconfig =& JFactory::getConfig();

		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Message subject
		$subject = JText::sprintf('COURSES_SUBJECT_INVITE', $this->course->get('cn'));

		// Message body for HUB user
		$eview = new JView(array('name' => 'emails', 'layout' => 'invite'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->course = $course;
		$eview->msg = $msg;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		$juri = JURI::getInstance();

		foreach ($inviteemails as $mbr)
		{
			// Message body for HUB user
			$eview2 = new JView(array('name' => 'emails', 'layout' => 'inviteemail'));
			$eview2->option = $this->_option;
			$eview2->sitename = $jconfig->getValue('config.sitename');
			$eview2->juser = $this->juser;
			$eview2->course = $course;
			$eview2->msg = $msg;
			$eview2->token = $mbr['token'];
			$message2 = $eview2->loadTemplate();
			$message2 = str_replace("\n", "\r\n", $message2);

			// Send the e-mail
			if (!$this->email($mbr['email'], $jconfig->getValue('config.sitename') . ' ' . $subject, $message2, $from)) 
			{
				$this->addComponentMessage(JText::_('COURSES_ERROR_EMAIL_INVITEE_FAILED') . ' ' . $mbr['email'], 'error');
			}
		}

		// Send the message
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('courses_invite', $subject, $message, $from, $invitees, $this->_option))) 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_EMAIL_INVITEE_FAILED'), 'error');
		}

		// Do we need to redirect?
		if ($return == 'members') 
		{
			$app->redirect(JRoute::_('index.php?option=' . $this->_option . '&gid='. $this->course->get('cn') . '&active=members'), true);
		}

		// Push all invitees together
		$all_invites = array_merge($invitees,$inviteemails);

		// Declare success/error message vars
		$success_message = '';
		$error_message = '';

		if (count($all_invites) > 0) 
		{
			$success_message = 'Course invites were successfully sent to the following users/email addresses: <br />';
			foreach ($all_invites as $invite)
			{
				if (is_numeric($invite)) 
				{
					$user = JUser::getInstance($invite);
					$success_message .= ' - ' . $user->get('name') . '<br />';
				} 
				else 
				{
					$success_message .= ' - ' . $invite['email'] . '<br />';
				}
			}
		}

		if (count($badentries) > 0) 
		{
			$error_message = 'We were unable to send invites to the following entries: <br />';
			foreach ($badentries as $entry)
			{
				if (is_numeric($entry[0])) 
				{
					$user = JUser::getInstance($entry[0]);
					if ($user->get('name') != '') 
					{
						$error_message .= ' - ' . $user->get('name') . ' &rarr; ' . $entry[1] . '<br />';
					} 
					else 
					{
						$error_message .= ' - ' . $entry[0] . ' &rarr; ' . $entry[1] . '<br />';
					}
				} 
				else 
				{
					$error_message .= ' - ' . $entry[0] . ' &rarr; ' . $entry[1] . '<br />';
				}
			}
		}

		// Push some notifications to the view
		$this->addComponentMessage($success_message, 'passed');
		$this->addComponentMessage($error_message, 'error');

		// Redirect back to view course
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->course->get('cn'));
	}

	/**
	 * Show a form for setting course customizations
	 * 
	 * @return     void
	 */
	public function customizeTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to customize a course.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
			return;
		}

		// Ensure we have a course to work with
		if (!$this->gid)
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course page
		$course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$course || !$this->course->get('gidNumber'))
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Path to course assets
		$asset_path = JPATH_ROOT . DS . 'site' . DS . 'courses' . DS . $this->course->get('gidNumber');

		// Declare an empty array to hold logo paths
		$logo_fullpaths = array();

		// If path is a directory then load images
		$logos = array();
		if (is_dir($asset_path)) 
		{
			// Get all images that are in course asset folder and could be a possible course logo
			$logos = JFolder::files($asset_path, '.jpg|.jpeg|.png|.gif|.PNG|.JPG|.JPEG|.GIF', false, true);
		}

		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		// then add overview to array
		$hub_course_plugins = $dispatcher->trigger('onCourseAreas', array());
		array_unshift($hub_course_plugins, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone', 
			'display_menu_tab' => true
		));

		// Get plugin access
		$course_plugin_access = Hubzero_Course_Helper::getPluginAccess($course);

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getCourseStyles();

		// Push some needed scripts to the template
		$this->_getCourseScripts();

		// Output HTML
		$view = new JView(array('name' => 'customize'));
		$this->view->option = $this->_option;
		$this->view->task   = $this->_task;
		$this->view->title  = $this->_title;
		$this->view->course  = $course;

		if (is_dir($asset_path)) 
		{
			$this->view->logos = $logos;
		}
		
		$this->view->hub_course_plugins   = $hub_course_plugins;
		$this->view->course_plugin_access = $course_plugin_access;

		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Save course customizations
	 * Redirects to course view
	 * 
	 * @return     void
	 */
	public function saveCustomizationTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to customize the course.');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
			return;
		}

		// Get the course 
		$gid = JRequest::getVar('gidNumber', '', 'POST');

		// Ensure we have a course to work with
		if (!$gid) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course page
		$course = Hubzero_Course::getInstance($gid);

		// Ensure we found the course info
		if (!$course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		$customization = JRequest::getVar('course', '', 'POST', 'none', 2);
		$plugins = JRequest::getVar('course_plugin', '', 'POST');

		// Get the logo
		$logo_parts = explode("/",$customization['logo']);
		$logo = array_pop($logo_parts);

		// Overview type and content
		$overview_type = (!is_numeric($customization['overview_type'])) ? 0 : $customization['overview_type'];
		$overview_content = $customization['overview_content'];

		// Plugin settings
		$plugin_access = '';
		foreach ($plugins as $plugin)
		{
			$plugin_access .= $plugin['name'] . '=' . $plugin['access'] . ',' . "\n";
		}

		$this->course->set('logo', $logo);
		$this->course->set('overview_type', $overview_type);
		$this->course->set('overview_content', $overview_content);
		$this->course->set('plugins',$plugin_access);
		$this->course->update();

		if ($this->course->error) 
		{
			$this->addComponentMessage($this->course->error, 'error');
		}

		// Log the course save
		$log = new XCourseLog($this->database);
		$log->gid = $this->course->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->actorid = $this->juser->get('id');
		$log->action = 'course_customized';

		if (!$log->store()) 
		{
			$this->addComponentMessage($log->getError(), 'error');
		}

		// Push a success message
		$this->addComponentMessage("You have successfully customized the \"{$this->course->get('description')}\" course.", 'passed');

		// Redirect back to the course page
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->course->get('cn'));
	}

	/**
	 * Show an interface for editing the course outline
	 * 
	 * @return     void
	 */
	public function editOutlineTask()
	{
		// Make sure the user is logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to edit the course outline');
			return;
		}

		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
			return;
		}

		// Ensure we have a course to work with
		if (!$this->gid) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}

		// Load the course page
		$course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getCourseStyles();

		// Push some needed scripts to the template
		$this->_getCourseScripts();

		// Import the wiki parser
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $this->course->get('cn'),
			'pageid'   => $this->course->get('gidNumber'),
			'filepath' => $this->config->get('uploadpath'),
			'domain'   => $this->course->get('cn')
		);

		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		// Set the grou for changing state
		$this->_course = $course;

		// Output HTML
		$view = new JView(array('name' => 'customize', 'layout' => 'editoutline'));
		$this->view->option = $this->_option;
		$this->view->task   = $this->_task;
		$this->view->title  = 'Manage Custom Content: ' . $this->course->get('description');
		$this->view->course  = $course;
		$this->view->database = $this->database;

		$this->view->parser = $p;
		$this->view->wikiconfig = $wikiconfig;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Change the status of an item
	 * 
	 * @param      string $type   Item being changed
	 * @param      string $status Status to set
	 * @param      string $id     Item ID
	 * @return     void
	 */
	public function change_state($type, $status, $id)
	{
		// Based on passed in status either activate or deactivate
		if ($status == 'deactivate') 
		{
			$active = 0;
		} 
		else 
		{
			$active = 1;
		}

		// Create and set query
		$sql = "UPDATE #__courses_" . $type . "s SET active='" . $active . "' WHERE id='" . $id . "'";
		$this->database->setQuery($sql);

		// Run query and set message
		if (!$this->database->Query()) 
		{
			$this->addComponentMessage('An error occurred while trying to ' . $status . ' the ' . $type . '. Please try again', 'error');
		} 
		else 
		{
			$this->addComponentMessage('The ' . $type . ' was successfully ' . $status . 'd.', 'passed');
		}

		// Redirect back to manage pages area
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->_course->get('cn') . '&task=managepages')
		);
	}

	/**
	 * Reorder items in a list
	 * 
	 * @param      string  $type       Items being reordered
	 * @param      string  $direction  Direction to move item
	 * @param      string  $id         Item ID
	 * @param      integer $high_order Highest order
	 * @return     void
	 */
	public function reorder($type, $direction, $id, $high_order)
	{
		$order_field = substr($type, 0, 1) . 'order';

		// Get the current order of the object trying to reorder
		$sql = "SELECT $order_field FROM #__courses_" . $type . "s WHERE id='" . $id . "'";
		$this->database->setQuery($sql);
		$order = $this->database->loadAssoc();

		// Set the high and low that the order can be
		$lowest_order = 1;
		$highest_order = $high_order;

		// Set the old order
		$old_order = $order[$order_field];

		// Get the new order depending on the direction of reordering
		// Make sure we are with our high and low limits
		if ($direction == 'down') 
		{
			$new_order = $old_order + 1;
			if ($new_order > $highest_order) 
			{
				$new_order = $highest_order;
			}
		} 
		else 
		{
			$new_order = $old_order - 1;
			if ($new_order < $lowest_order) 
			{
				$new_order = $lowest_order;
			}
		}

		// Check to see if another object holds the order we are trying to move to
		$sql = "SELECT *  FROM #__courses_" . $type . "s WHERE $order_field='" . $new_order . "' AND gid='" . $this->_course->get('gidNumber') . "'";
		$this->database->setQuery($sql);
		$new = $this->database->loadAssoc();

		// If there isnt an object there then just update
		if ($new['id'] == '') 
		{
			$sql = "UPDATE #__courses_" . $type . "s SET $order_field='" . $new_order . "' WHERE id='" . $id . "'";
			$this->database->setQuery($sql);

			if (!$this->database->Query()) 
			{
				$this->addComponentMessage('An error occurred while trying to reorder the ' . $type . '. Please try again', 'error');
			} 
			else 
			{
				$this->addComponentMessage('The ' . $type . ' was successfully reordered.', 'passed');
			}
		} 
		else 
		{
			// Otherwise basically switch the two objects orders
			$sql = "UPDATE #__courses_" . $type . "s SET $order_field='" . $new_order . "' WHERE id='" . $id . "'";
			$this->database->setQuery($sql);
			$this->database->Query();

			$sql = "UPDATE #__courses_" . $type . "s SET $order_field='" . $old_order . "' WHERE id='" . $new['id'] . "'";
			$this->database->setQuery($sql);

			if (!$this->database->Query()) 
			{
				$this->addComponentMessage('An error occurred while trying to reorder the ' . $type . '. Please try again','error');
			} 
			else 
			{
				$this->addComponentMessage('The ' . $type . ' was successfully reordered.', 'passed');
			}
		}

		// Redirect back to manage pages area
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->_course->get('cn') . '&task=managepages')
		);
	}

	/**
	 * Check user access
	 * 
	 * @param      boolean $checkOnlyMembership Flag for checking only membership (not admin access)
	 * @return     mixed False if no access, string if has access
	 */
	/*public function _authorize($checkOnlyMembership = true)
	{
		//load the course
		$course = Hubzero_Course::getInstance($this->gid);
		if (!is_object($course))
		{
			return false;
		}

		//check to see if they are a site admin
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			if (!$checkOnlyMembership && $this->juser->authorise('core.admin', $this->_option))
			{
				return 'admin';
			}
		}
		else 
		{
			if (!$checkOnlyMembership && $this->juser->get('usertype') == 'Super Administrator')
			{
				return 'admin';
			}
		}

		//check to see if they are a course manager
		if (in_array($this->juser->get('id'), $this->course->get('managers')))
		{
			return 'manager';
		}

		//check to see if they are a course member
		if (in_array($this->juser->get('id'), $this->course->get('members')))
		{
			return 'member';
		}

		//return false if they are none of the above
		return false;
		
		/*
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			return false;
		}

		if (!$checkonlymembership) {
			// Check if they're a site admin (from Joomla)
			if ($this->juser->authorize($this->_option, 'manage')) {
				return 'admin';
			}
		}

		// Get the user's courses
		$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));
		
		$invitees = $profile->getCourses('invitees');
		$members = $profile->getCourses('members');
		$managers = $profile->getCourses('managers');

		$courses = array();
		$managerids = array();
		if ($managers && count($managers) > 0) {
			foreach ($managers as $manager)
			{
				$courses[] = $manager;
				$managerids[] = $manager->cn;
			}
		}
		if ($members && count($members) > 0) {
			foreach ($members as $mem)
			{
				if (!in_array($mem->cn,$managerids)) {
					$courses[] = $mem;
				}
			}
		}

		// Check if they're a member of this course
		if ($courses && count($courses) > 0) {
			foreach ($courses as $ug)
			{
				if ($ug->cn == $this->gid) {
					// Check if they're a manager of this course
					if ($ug->manager) {
						return 'manager';
					}
					// Are they a confirmed member?
					if ($ug->regconfirmed) {
						return 'member';
					}
				}
			}
		}

		// Check if they're invited to this course
		if ($invitees && count($invitees) > 0) {
			foreach ($invitees as $ug)
			{
				if ($ug->cn == $this->gid) {
					return 'invitee';
				}
			}
		}

		return false;
	}*/

	/**
	 * Set access permissions for a user
	 * 
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, false);
		if (!$this->juser->get('guest')) 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$asset  = $this->_option;
				if ($assetId)
				{
					$asset .= ($assetType != 'component') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$at = '';
				if ($assetType != 'component')
				{
					$at .= '.' . $assetType;
				}

				// Admin
				$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				// Permissions
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if (in_array($this->juser->get('id'), $this->course->get('managers')))
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
				if (in_array($this->juser->get('id'), $this->course->get('members')))
				{
					$this->config->set('access-view-' . $assetType, true);
				}
			}
		}
	}

	/**
	 * Short description for 'getCourses'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $courses Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	/*private function getCourses($courses)
	{
		if (!$this->juser->get('guest')) 
		{
			$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));

			$ugs = $profile->getCourses('all');

			for ($i = 0; $i < count($courses); $i++)
			{
				if (!isset($courses[$i]->cn)) 
				{
					$courses[$i]->cn = '';
				}
				$courses[$i]->registered   = 0;
				$courses[$i]->regconfirmed = 0;
				$courses[$i]->manager      = 0;

				if ($ugs && count($ugs) > 0) 
				{
					foreach ($ugs as $ug)
					{
						if (is_object($ug) && $ug->cn == $courses[$i]->cn) 
						{
							$courses[$i]->registered   = $ug->registered;
							$courses[$i]->regconfirmed = $ug->regconfirmed;
							$courses[$i]->manager      = $ug->manager;
						}
					}
				}
			}
		}

		return $courses;
	}*/

	/**
	 * Send an email
	 * 
	 * @param      string $email   Address to send message to
	 * @param      string $subject Message subject
	 * @param      string $message Message to send
	 * @param      array  $from    Who the email is from (name and address)
	 * @return     boolean Return description (if any) ...
	 */
	public function email($email, $subject, $message, $from)
	{
		if ($from) 
		{
			$args = "-f '" . $from['email'] . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $from['name'] . ' <' . $from['email'] . ">\n";
			$headers .= 'Reply-To: ' . $from['name'] .' <' . $from['email'] . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: ' . $from['name'] . "\n";
			if (mail($email, $subject, $message, $headers, $args)) 
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Get a list of members
	 * 
	 * @return     void
	 */
	public function memberslist()
	{
		// Fetch results
		$filters = array();
		$filters['cn'] = trim(JRequest::getString('course', ''));

		if ($filters['cn']) 
		{
			$query = "SELECT u.username, u.name 
						FROM #__users AS u, #__courses_members AS m, #__courses AS g
						WHERE g.cn='" . $filters['cn'] . "' AND g.gidNumber=m.gidNumber AND m.uidNumber=u.id
						ORDER BY u.name ASC";
		} 
		else 
		{
			$query = "SELECT a.username, a.name"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
				. "\n INNER JOIN #__core_acl_courses_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to course
				. "\n INNER JOIN #__core_acl_aro_courses AS g ON g.id = gm.course_id"
				. "\n WHERE a.block = '0' AND g.id=25"
				. "\n ORDER BY a.name";
		}

		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();

		// Output search results in JSON format
		$json = array();
		if ($filters['cn'] == '') 
		{
			$json[] = '{"username":"","name":"No User"}';
		}
		if (count($rows) > 0) 
		{
			foreach ($rows as $row)
			{
				$json[] = '{"username":"' . $row->username . '","name":"' . htmlentities(stripslashes($row->name), ENT_COMPAT, 'UTF-8') . '"}';
			}
		}

		echo '{"members":[' . implode(',', $json) . ']}';
	}

	/**
	 * Check if a course alias is valid
	 * 
	 * @param      integer $gid Course alias
	 * @return     boolean True if valid, false if not
	 */
    private function _validCn($gid)
	{
		if (preg_match("/^[0-9a-zA-Z]+[_0-9a-zA-Z]*$/i", $gid))
		{
			if (is_numeric($gid) && intval($gid) == $gid && $gid >= 0) 
			{
				return false;
			} 
			else 
			{
				return true;
			}
		} 
		else 
		{
			return false;
		}
	}

	/**
	 * Generate a random string
	 * 
	 * @param      integer $length Length of string
	 * @return     string
	 */
	private function randomString($length)
	{
		$str = '';

		for ($i=0; $i<$length; $i++)
		{
		    $d = rand(1, 30)%2;
		    $str .= $d ? chr(rand(65, 90)) : chr(rand(48, 57));
		}

		return strtoupper($str);
	}

	/**
	 * Get a course's availability
	 * 
	 * @param      object $course Hubzero_Course
	 * @return     string
	 */
	private function courseAvailability($course = NULL)
	{
		//get the course
		$course = (!is_null($course)) ? $course : JRequest::getVar('course', '');
		$course = strtolower(trim($course));

		if ($course == '')
		{
			return;
		}

		// Ensure the data passed is valid
		if (($course == 'new' || $course == 'browse') || (!$this->_validCn($course)) || (Hubzero_Course::exists($course, true))) 
		{
			$availability = false;
		}
		else
		{
			$availability = true;
		}

		if (JRequest::getVar('no_html', 0) == 1)
		{
			echo json_encode(array('available' => $availability));
            return;
		}
		else
		{
			return $availability;
		}
	}
}

