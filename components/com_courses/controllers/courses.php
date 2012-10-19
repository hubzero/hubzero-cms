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
class CoursesControllerCourses extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();
		$this->_authorize('course');

		$this->registerTask('__default', 'intro');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 * 
	 * @param      array $course_pages List of roup pages
	 * @return     void
	 */
	public function _buildPathway($course_pages = array())
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);

			if ($this->_task == 'new') 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&task=' . $this->_task
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
		$this->_title = JText::_(strtoupper($this->_name));

		if ($this->_task && $this->_task != 'intro') 
		{
			$this->_title = JText::_(strtoupper($this->_name . '_' . $this->_task));
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
		$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . 'task=' . $this->_task));
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . $return),
			$message,
			'warning'
		);
		return;
	}

	/**
	 * Display component main page
	 * 
	 * @return     void
	 */
	public function introTask()
	{
		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getStyles($this->_option, 'intro.css');

		// Push some needed scripts to the template
		//$this->_getScripts();

		//vars
		$mytags = '';
		$mycourses = array();
		$popularcourses = array();
		$interestingcourses = array();

		//get the users profile
		$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));

		if (is_object($profile))
		{
			//get users tags
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'tags.php');
			$mt = new MembersTags($this->database);
			$mytags = $mt->get_tag_string($profile->get("uidNumber"));

			//get users courses
			$mycourses['members'] = Hubzero_User_Helper::getCourses($profile->get("uidNumber"), 'members', 1);
			$mycourses['invitees'] = Hubzero_User_Helper::getCourses($profile->get("uidNumber"), 'invitees', 1);
			$mycourses['applicants'] = Hubzero_User_Helper::getCourses($profile->get("uidNumber"), 'applicants', 1);
			$mycourses = array_filter($mycourses);

			//get courses user may be interested in
			$interestingcourses = Hubzero_Course_Helper::getCoursesMatchingTagString($mytags, Hubzero_User_Helper::getCourses($profile->get("uidNumber")));
		}

		//get the popular courses
		$popularcourses = Hubzero_Course_Helper::getPopularCourses(3);

		// Output HTML
		//$this->view->option = $this->_option;
		$this->view->config = $this->config;
		$this->view->database = $this->database;
		$this->view->user = $this->juser;
		$this->view->title = $this->_title;
		$this->view->mycourses = $mycourses;
		$this->view->popularcourses = $popularcourses;
		$this->view->interestingcourses = $interestingcourses;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Display a list of courses on the site and options for filtering/browsing them
	 * 
	 * @return     void
	 */
	public function browseTask()
	{
		// Push some styles to the template
		$this->_getStyles($this->_option, $this->_task . '.css');

		// Incoming
		$this->view->filters = array();
		$this->view->filters['type']   = array(1,3);
		$this->view->filters['authorized'] = "";

		// Filters for getting a result count
		$this->view->filters['limit']  = 'all';
		$this->view->filters['fields'] = array('COUNT(*)');
		$this->view->filters['search'] = JRequest::getVar('search', '');
		$this->view->filters['sortby'] = strtolower(JRequest::getWord('sortby', 'title'));
		if (!in_array($this->view->filters['sortby'], array('alias', 'title')))
		{
			$this->view->filters['sortby'] = 'title';
		}
		$this->view->filters['policy'] = strtolower(JRequest::getWord('policy', ''));
		if (!in_array($this->view->filters['policy'], array('open', 'restricted', 'invite', 'closed')))
		{
			$this->view->filters['policy'] = '';
		}
		$this->view->filters['index']  = htmlentities(JRequest::getVar('index', ''));

		// Get a record count
		$this->view->total = Hubzero_Course::find($this->view->filters);

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Filters for returning results
		$this->view->filters['limit']  = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$this->view->filters['limit']  = ($this->view->filters['limit']) ? $this->view->filters['limit'] : 'all';
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['fields'] = array('cn', 'description', 'published', 'gidNumber', 'type', 'public_desc', 'join_policy');

		// Get a list of all courses
		$this->view->courses = Hubzero_Course::find($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Run through the master list of courses and mark the user's status in that course
		//$this->view->authorized = $this->_authorize();
		if (!$this->juser->get('guest') && $this->view->courses) 
		{
			$this->view->courses = $this->_getCourses($this->view->courses);
		}

		//build the title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->title = $this->_title;
		$this->view->config = $this->config;
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Short description for 'getCourses'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $courses Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function _getCourses($courses)
	{
		if (!$this->juser->get('guest')) 
		{
			$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));

			$ugs = $profile->getGroups('all');

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
			$course->read($this->gid);

			// Ensure we found the course info
			if (!$course) 
			{
				JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
				return;
			}

			$title = "Edit Course: " . $course->get('description');
		} 
		else 
		{
			$course->set('join_policy', $this->config->get('join_policy'));
			$course->set('privacy', $this->config->get('privacy'));
			$course->set('access', $this->config->get('access'));
			$course->set('published', $this->config->get('auto_approve'));

			$title = 'Create New Course';
		}

		//get directory for course file uploads
		if ($this->lid != '')
		{
			$lid = $this->lid;
		}
		elseif ($course->get('gidNumber'))
		{
			$lid = $course->get('gidNumber');
		}
		else
		{
			$lid = time() . rand(0, 1000);
		}

		// Get the course's interests (tags)
		$gt = new CoursesTags($this->database);
		$tags = $gt->get_tag_string($course->get('gidNumber'));

		if ($this->course) 
		{
			$course = $this->course;
			$tags  = $this->tags;
		}

		// Output HTML
		$this->view->title  = $title;
		$this->view->course = $course;
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
			$this->loginTask(JText::_('You must be logged in to save course settings.'));
			return;
		}

		// Incoming
		$c = JRequest::getVar('course', array(), 'post');
		
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
		//if ($this->_authorize() != 'manager' && $g_gidNumber != 0) 
		if (!$this->config->get('access-edit-course') && $c['gidNumber']) 
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
			$course->read($g_gidNumber);
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
			$course->set('published', $g_published);
			$course->set('description', $g_description);
			//$course->set('access', $g_access);
			$course->set('privacy', $g_privacy);
			$course->set('public_desc', $g_public_desc);
			$course->set('private_desc', $g_private_desc);
			$course->set('restrict_msg', $g_restrict_msg);
			$course->set('join_policy', $g_join_policy);
			$course->set('cn', $g_cn);
			$course->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
            
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
			$course->set('published', $g_published);
			$course->set('description', $g_description);
			//$course->set('access', $g_access);
			$course->set('privacy', $g_privacy);
			$course->set('public_desc', $g_public_desc);
			$course->set('private_desc', $g_private_desc);
			$course->set('restrict_msg', $g_restrict_msg);
			$course->set('join_policy', $g_join_policy);
			$course->set('cn', $g_cn);
			$course->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);

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
		$eview = new JView(array(
			'name'   => 'emails', 
			'layout' => 'saved'
		));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->course = $course;
		$eview->isNew = $isNew;
		$eview->g_description = $g_description;
		$eview->g_privacy = $g_privacy;
		$eview->g_public_desc = $g_public_desc;
		$eview->g_private_desc = $g_private_desc;
		$eview->g_restrict_msg = $g_restrict_msg;
		$eview->g_join_policy = $g_join_policy;
		$eview->g_cn = $g_cn;

		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Set the course changes and save
		$course->set('cn', $g_cn);
		if ($isNew) 
		{
			$course->create();
			$course->set('type', 1);
			$course->set('published', $g_published);
			$course->set('created', date("Y-m-d H:i:s"));
			$course->set('created_by', $this->juser->get('id'));

			$course->add('managers', array($this->juser->get('id')));
			$course->add('members', array($this->juser->get('id')));
		}

		$course->set('description', $g_description);
		//$course->set('access', $g_access);
		$course->set('privacy', $g_privacy);
		$course->set('public_desc', $g_public_desc);
		$course->set('private_desc', $g_private_desc);
		$course->set('restrict_msg',$g_restrict_msg);
		$course->set('join_policy',$g_join_policy);
		$course->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
		$course->update();

		// Process tags
		$gt = new CoursesTags($this->database);
		$gt->tag_object($this->juser->get('id'), $course->get('gidNumber'), $tags, 1, 1);

		// Log the course save
		$log = new XCourseLog($this->database);
		$log->gid = $course->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->actorid = $this->juser->get('id');

		// Rename the temporary upload directory if it exist
		if ($isNew) 
		{
			if ($lid != $course->get('gidNumber')) 
			{
				$config = $this->config;
				$bp = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/courses'), DS);
				if (is_dir($bp . DS . $lid)) 
				{
					rename($bp . DS . $lid, $bp . DS . $course->get('gidNumber'));
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
		if (!$dispatcher->trigger('onSendMessage', array($type, $subject, $message, $from, $course->get('managers'), $this->_option))) 
		{
			$this->addComponentMessage(JText::_('COURSES_ERROR_EMAIL_MANAGERS_FAILED'), 'error');
		}

		if ($this->getComponentMessage()) 
		{
			$this->view = new JView(array(
				'name' => 'error'
			));
			$this->view->title = $title;
			$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
			$this->view->display();
			return;
		}

		// Show success message to user
		if ($isNew) 
		{
			$this->addComponentMessage("You have successfully created the \"{$course->get('description')}\" course" , 'passed');
		} 
		else 
		{
			$this->addComponentMessage("You have successfully updated the \"{$course->get('description')}\" course" , 'passed');
		}

		// Redirect back to the course page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $g_cn)
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
		if (!$course || !$course->get('gidNumber')) 
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
		$gparams = new $paramsClass($course->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->addComponentMessage('Course membership is not managed in the course interface.', 'error');
			$this->_redirect = JRoute::_('index.php?option=com_courses&gid=' . $course->get('cn'));
			return;
		}

		// Push some needed styles to the template
		$this->_getStyles();

		// Push some needed scripts to the template
		$this->_getScripts();

		// Get number of course members
		$members  = $course->get('members');
		$managers = $course->get('managers');

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
			$this->view->title  = 'Delete Course: ' . $course->get('description');
			$this->view->course  = $course;
			$this->view->log    = $log;
			$this->view->msg    = $msg;
			$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
			$this->view->display();
			return;
		}

		// Start log
		$log  = JText::sprintf('COURSES_SUBJECT_COURSE_DELETED', $course->get('cn'));
		$log .= JText::_('COURSES_TITLE') . ': ' . $course->get('description') . "\n";
		$log .= JText::_('COURSES_ID') . ': ' . $course->get('cn') . "\n";
		$log .= JText::_('COURSES_PRIVACY') . ': ' . $course->get('access') . "\n";
		$log .= JText::_('COURSES_PUBLIC_TEXT') . ': ' . stripslashes($course->get('public_desc'))  . "\n";
		$log .= JText::_('COURSES_PRIVATE_TEXT') . ': ' . stripslashes($course->get('private_desc'))  . "\n";
		$log .= JText::_('COURSES_RESTRICTED_MESSAGE') . ': ' . stripslashes($course->get('restrict_msg')) . "\n";

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
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $course->get('gidNumber');
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

		$gidNumber = $course->get('gidNumber');
		$gcn = $course->get('cn');

		$deletedcourse = clone($course);

		// Delete course
		if (!$course->delete()) 
		{
			$view = new JView(array('name' => 'error'));
			$this->view->title = $title;
			if ($course->error) 
			{
				$this->addComponentMessage($course->error, 'error');
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
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option)
		);
	}

	/**
	 * Set access permissions for a user
	 * 
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		$this->config->set('access-create-' . $assetType, false);
		$this->config->set('access-manage-' . $assetType, false);
		$this->config->set('access-admin-' . $assetType, false);

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
				$this->config->set('access-create-' . $assetType, true);
				if ($this->juser->authorize($this->_option, 'manage')) 
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
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
	private function getCourses($courses)
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
	}

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
		    $d = rand(1,30)%2;
		    $str .= $d ? chr(rand(65,90)) : chr(rand(48,57));
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

