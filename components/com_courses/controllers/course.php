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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

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
		// Load the course page
		$this->course = CoursesModelCourse::getInstance(JRequest::getVar('gid', ''));

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
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);

			if ($this->course->exists()) 
			{
				$pathway->addItem(
					stripslashes($this->course->get('title')),
					'index.php?option=' . $this->_option . '&gid=' . $this->course->get('id')
				);

				if ($this->_task && in_array($this->_task, array('display', 'edit', 'offerings'))) 
				{
					$pathway->addItem(
						JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&gid=' . $this->course->get('id') . '&task=' . $this->_task
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
						'index.php?option=' . $this->_option . '&gid=' . $this->course->get('id') . '&active=' . $this->active
					);
				}
			}
			else if ($this->_task == 'new') 
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

		if ($this->course->exists()) 
		{
			$this->_title = JText::_('COURSE') . ': ' . stripslashes($this->course->get('title'));
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
		$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->course->get('id') . '&task=' . $this->_task));
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
		if (!$this->course->access('view'))
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		$this->view->wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => $this->course->get('alias'),
			'pageid'   => $this->course->get('id'),
			'filepath' => DS . ltrim($this->config->get('uploadpath', '/site/courses'), DS),
			'domain'   => $this->course->get('alias')
		);

		ximport('Hubzero_Wiki_Parser');
		$this->view->parser = Hubzero_Wiki_Parser::getInstance();

		// Push some needed styles to the template
		// Pass in course type to include special css for paying courses
		//$this->_getCourseStyles($this->course->get('type'));
		$this->_getStyles($this->_option, $this->_controller . '.css');

		// Push some needed scripts to the template
		$this->_getScripts();

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		$this->view->course        = $this->course;
		$this->view->user          = $this->juser;
		$this->view->config        = $this->config;
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

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask(JText::_('You must be logged in to edit or create courses.'));
			return;
		}

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Push some needed styles to the template
		$this->_getStyles($this->_option, $this->_controller . '.css');

		// Push some needed scripts to the template
		$this->_getScripts('assets/js/' . $this->_name);

		if ($this->_task != 'new') 
		{
			// Ensure we found the course info
			if (!$this->course->exists()) 
			{
				JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
				return;
			}

			// Check authorization
			if (!$this->course->access('edit')) 
			{
				JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
				return;
			}

			$this->view->title = 'Edit Course: ' . $this->course->get('title');
		} 
		else 
		{
			//$this->course->set('join_policy', $this->config->get('join_policy'));
			//$this->course->set('privacy', $this->config->get('privacy'));
			//$this->course->set('access', $this->config->get('access'));
			//$this->course->set('published', $this->config->get('auto_approve'));

			$this->view->title = 'Create New Course';
		}

		//get directory for course file uploads
		if ($this->lid != '')
		{
			$this->view->lid = $this->lid;
		}
		elseif ($this->course->get('id'))
		{
			$this->view->lid = $this->course->get('id');
		}
		else
		{
			$this->view->lid = time() . rand(0, 1000);
		}

		// Get the course's interests (tags)
		$gt = new CoursesTags($this->database);
		$this->view->tags = $gt->get_tag_string($this->course->get('id'));

		/*if ($this->course) 
		{
			$course = $this->course;
			$tags  = $this->tags;
		}*/

		// Output HTML
		//$this->view->title  = $title;
		$this->view->course = $this->course;
		//$this->view->tags   = $tags;
		$this->view->juser  = $this->juser;
		//$this->view->lid    = $lid;
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

		// Instantiate an CoursesCourse object
		$course = new CoursesCourse();

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
		if ($isNew && CoursesCourse::exists($g_cn,true)) 
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
		$this->course->set('alias', $g_cn);
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
		$this->course->store();

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
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&gid=' . $g_cn)
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
			$this->loginTask(JText::_('You must be logged in to delete a course.'));
			return;
		}

		// Ensure we have a course to work with
		/*if (!$this->course->exists()) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_ID'));
			return;
		}*/

		// Ensure we found the course info
		if (!$this->course->exists()) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Check authorization
		//if (!$this->config->get('access-delete-course', $this->course->get('gidNumber'))) 
		if (!$this->course->access('delete'))
		{
			JError::raiseError(403, JText::_('COURSES_NOT_AUTH'));
			return;
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the course params
		/*$gparams = new $paramsClass($this->course->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('cn')),
				JText::_('Course membership is not managed in the course interface.'), 
				'error'
			);
			return;
		}*/

		// Push some needed styles to the template
		$this->_getStyles();

		// Push some needed scripts to the template
		$this->_getScripts();

		// Get number of course members
		//$members  = $this->course->get('members');
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

			$log = JText::sprintf('COURSES_MEMBERS_LOG', count($managers));

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger('onCourseDeleteCount', array($course));
			if (count($logs) > 0) 
			{
				$log .= '<br />' . implode('<br />', $logs);
			}

			// Output HTML
			$this->view->title  = 'Delete Course: ' . $this->course->get('title');
			$this->view->course = $course;
			$this->view->log    = $log;
			$this->view->msg    = $msg;
			$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
			$this->view->display();
			return;
		}

		$this->course->set('state', 2);

		// Delete course
		if (!$this->course->update()) 
		{
			$this->view->setLayout('error');
			$this->view->title = $title;
			if ($this->course->getError()) 
			{
				$this->addComponentMessage($this->course->getError(), 'error');
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
		$eview = new JView(array(
			'name'   => 'emails',
			'layout' => 'deleted'
		));
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
		$xlog = new CoursesTableLog($this->database);
		$xlog->gid       = $this->course->get('id');
		$xlog->uid       = $this->juser->get('id');
		$xlog->timestamp = date('Y-m-d H:i:s', time());
		$xlog->action    = 'course_deleted';
		$xlog->comments  = $log;
		$xlog->actorid   = $this->juser->get('id');
		if (!$xlog->store()) 
		{
			$this->addComponentMessage($xlog->getError(), 'error');
		}

		// Redirect back to the courses page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option),
			JText::sprintf('You successfully deleted the "%s" course', $this->course->get('title')), 
			'passed'
		);
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
	 * @param      object $course CoursesCourse
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
		$c = CoursesModelCourse::getInstance($course);
		if (($course == 'new' || $course == 'browse') || !$this->_validCn($course) || $c->exists()) 
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

