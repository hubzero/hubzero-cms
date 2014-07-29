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
 * Courses controller class
 */
class CoursesControllerCourse extends \Hubzero\Component\SiteController
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

		$this->registerTask('edit', 'display');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param      array $course_pages List of roup pages
	 * @return     void
	 */
	protected function _buildPathway()
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
		}
		else
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return     void
	 */
	protected function _buildTitle()
	{
		//set title used in view
		$this->_title = JText::_(strtoupper($this->_option));

		if ($this->course->exists())
		{
			$this->_title .= ': ' . stripslashes($this->course->get('title'));
		}
		else
		{
			if ($this->_task && $this->_task != 'intro')
			{
				$this->_title .= JText::_(strtoupper($this->_option . '_' . $this->_task));
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
		$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->course->get('id') . '&task=' . $this->_task, false, true));
		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login&return=' . $return, false),
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
			JError::raiseError(404, JText::_('COM_COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		$this->view->active = JRequest::getVar('active', 'overview');

		JPluginHelper::importPlugin('courses');
		$dispatcher = JDispatcher::getInstance();

		$this->view->cats = $dispatcher->trigger('onCourseViewAreas', array(
				$this->course
			)
		);

		$this->view->sections = $dispatcher->trigger('onCourseView', array(
				$this->course,
				$this->view->active
			)
		);

		$this->view->isPage = false;

		if ($pages = $this->course->pages(array('active' => 1)))
		{
			foreach ($pages as $page)
			{
				$this->view->cats[] = array(
					$page->get('url') => $page->get('title')
				);

				if ($page->get('url') == $this->view->active)
				{
					$this->view->sections[] = array(
						'name' => $page->get('url'),
						'html' => $page->content('parsed'),
						'metadata' => ''
					);
					$this->view->isPage = true;
				}
			}
		}

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
	public function editTask($model=null)
	{
		$this->view->setLayout('edit');

		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->loginTask(JText::_('COM_COURSES_NOT_LOGGEDIN'));
			return;
		}

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		if (is_object($model))
		{
			$this->course = $model;
		}

		if ($this->_task != 'new')
		{
			// Ensure we found the course info
			if (!$this->course->exists())
			{
				JError::raiseError(404, JText::_('COM_COURSES_NO_COURSE_FOUND'));
				return;
			}

			// Check authorization
			if (!$this->course->access('edit'))
			{
				JError::raiseError(403, JText::_('COM_COURSES_NOT_AUTH'));
				return;
			}

			$this->view->title = JText::_('COM_COURSES_EDIT_COURSE') . ': ' . $this->course->get('title');
		}
		else
		{
			$this->course->set('state', 3);

			$this->view->title = JText::_('COM_COURSES_NEW_COURSE');
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

		// Output HTML
		$this->view->course = $this->course;
		$this->view->juser  = $this->juser;

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
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->loginTask(JText::_('COM_COURSES_NOT_LOGGEDIN'));
			return;
		}

		// Incoming
		$data = JRequest::getVar('course', array(), 'post', 'none', 2);
		$tags = trim(JRequest::getVar('tags', ''));

		$course = CoursesModelCourse::getInstance($data['id']);

		// Is this a new entry or updating?
		$isNew = false;
		if (!$course->exists())
		{
			$isNew = true;
		}

		// Check authorization
		if (!$isNew && !$course->access('edit', 'course'))
		{
			JError::raiseError(403, JText::_('COM_COURSES_NOT_AUTH'));
			return;
		}

		// Push back into edit mode if any errors
		if (!$course->bind($data))
		{
			$this->tags = $tags;
			$this->addComponentMessage($course->getError(), 'error');
			$this->newTask($course);
			return;
		}

		// Force into draft state
		if ($isNew)
		{
			$course->set('state', 3);
		}

		// Push back into edit mode if any errors
		if (!$course->store(true))
		{
			$this->tags = $tags;
			$this->addComponentMessage($course->getError(), 'error');
			$this->editTask($course);
			return;
		}

		$tagger = new CoursesTags($this->database);
		$tagger->tag_object($this->juser->get('id'), $course->get('id'), $tags, 1);

		// Rename the temporary upload directory if it exist
		if ($isNew)
		{
			// Set the creator as a manager
			$role_id = 0;
			if ($roles = $course->roles())
			{
				foreach ($roles as $role)
				{
					if ($role->alias == 'manager')
					{
						$role_id = $role->id;
						break;
					}
				}
			}
			$course->add($this->juser->get('id'), $role_id);
		}

		// Show success message to user
		if ($isNew)
		{
			$msg = JText::sprintf('COM_COURSES_COURSE_CREATED', $course->get('title'));
		}
		else
		{
			$msg = JText::sprintf('COM_COURSES_COURSE_UPDATED', $course->get('title'));
		}

		// Redirect back to the course page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&gid=' . $course->get('alias')),
			$msg
		);
	}

	/**
	 * Show a form for editing a course
	 *
	 * @return     void
	 */
	public function instructorsTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->loginTask(JText::_('COM_COURSES_NOT_LOGGEDIN'));
			return;
		}

		$this->view->no_tml = JRequest::getInt('no_html', 0);

		$this->view->course = $this->course;
		$this->view->juser  = $this->juser;

		$this->view->display();
	}

	/**
	 * Show a form for editing a course
	 *
	 * @return     void
	 */
	public function newofferingTask($offering=null)
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->loginTask(JText::_('COM_COURSES_NOT_LOGGEDIN'));
			return;
		}

		$this->view->no_html = JRequest::getInt('no_html', 0);

		if ($offering instanceof CoursesModelOffering)
		{
			$this->view->offering = $offering;
		}
		else
		{
			$this->view->offering = new CoursesModelOffering(0);
		}

		$this->view->course = $this->course;
		$this->view->juser  = $this->juser;

		$this->view->title = JText::_('COM_COURSES_NEW_OFFERING');
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

		$this->view->display();
	}

	/**
	 * Show a form for editing a course
	 *
	 * @return     void
	 */
	public function saveofferingTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->loginTask(JText::_('COM_COURSES_NOT_LOGGEDIN'));
			return;
		}

		$data = JRequest::getVar('offering', array(), 'post', 'none', 2);
		$no_html = JRequest::getInt('no_html', 0);

		$course = CoursesModelCourse::getInstance($data['course_id']);
		$offering = CoursesModelOffering::getInstance($data['id']);

		// Is this a new entry or updating?
		$isNew = false;
		if (!$offering->exists())
		{
			$isNew = true;
		}

		$response = new stdClass;
		$response->success = true;

		// Push back into edit mode if any errors
		if (!$offering->bind($data))
		{
			if ($no_html)
			{
				$response->message = $offering->getError();

				echo json_encode($response);
			}
			else
			{
				$this->addComponentMessage($offering->getError(), 'error');
				$this->newofferingTask($offering);
			}
			return;
		}

		// Push back into edit mode if any errors
		if (!$offering->store(true))
		{
			if ($no_html)
			{
				$response->message = $offering->getError();
			}
			else
			{
				$this->addComponentMessage($offering->getError(), 'error');
				$this->newTask($offering);
			}
			return;
		}

		$response->message = JText::_('COM_COURSES_OFFERING_SAVED');

		if ($no_html)
		{
			echo json_encode($response);
		}
		else
		{
			// Redirect back to the course page
			$this->setRedirect(
				JRoute::_($course->link()),
				$response->message
			);
		}
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
			$this->loginTask(JText::_('COM_COURSES_NOT_LOGGEDIN'));
			return;
		}

		// Ensure we found the course info
		if (!$this->course->exists())
		{
			JError::raiseError(404, JText::_('COM_COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Check authorization
		if (!$this->course->access('delete'))
		{
			JError::raiseError(403, JText::_('COM_COURSES_NOT_AUTH'));
			return;
		}

		// Get number of course members
		$managers = $this->course->get('managers');

		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher = JDispatcher::getInstance();

		// Incoming
		$process = JRequest::getVar('process', '');
		$confirmdel = JRequest::getVar('confirmdel', '');
		$msg = trim(JRequest::getVar('msg', '', 'post'));

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				$this->addComponentMessage(JText::_('COM_COURSES_ERROR_CONFIRM_DELETION'), 'error');
			}

			$log = JText::sprintf('COM_COURSES_MEMBERS_LOG', count($managers));

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = $dispatcher->trigger('onCourseDeleteCount', array($course));
			if (count($logs) > 0)
			{
				$log .= '<br />' . implode('<br />', $logs);
			}

			// Output HTML
			$this->view->title  = JText::_('COM_COURSES_DELETE_COURSE') . ': ' . $this->course->get('title');
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
		$date = JFactory::getDate();

		$jconfig = JFactory::getConfig();

		// Build the "from" info for e-mails
		$from = array(
			'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name)),
			'email' => $jconfig->getValue('config.mailfrom')
		);

		// E-mail subject
		$subject = JText::sprintf('COM_COURSES_SUBJECT_COURSE_DELETED', $gcn);

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array(
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
		$dispatcher = JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('courses_deleted', $subject, $message, $from, $members, $this->_option)))
		{
			$this->addComponentMessage(JText::_('COM_COURSES_ERROR_EMAIL_MEMBERS_FAILED'), 'error');
		}

		// Log the deletion
		$xlog = new CoursesTableLog($this->database);
		$xlog->gid       = $this->course->get('id');
		$xlog->uid       = $this->juser->get('id');
		$xlog->timestamp = JFactory::getDate()->toSql();
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
			JText::sprintf('COM_COURSES_COURSE_DELETED', $this->course->get('title')),
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
	public function savepageTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->loginTask(JText::_('COM_COURSES_NOT_LOGGEDIN'));
			return;
		}

		// Incoming
		$page = JRequest::getVar('page', array(), 'post', 'none', 2);

		$course = CoursesModelCourse::getInstance($page['course_id']);
		if (!$course->exists())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
		}

		$model = new CoursesModelPage($page['id']);

		if (!$model->bind($page))
		{
			// Redirect back to the course page
			$this->setRedirect(
				JRoute::_($course->link() . '&action=' . ($model->get('id') ? 'addpage' : 'editpage')),
				$model->getError(),
				'error'
			);
			return;
		}

		if (!$model->store(true))
		{
			// Redirect back to the course page
			$this->setRedirect(
				JRoute::_($course->link() . '&action=' . ($model->get('id') ? 'addpage' : 'editpage')),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect back to the course page
		$this->setRedirect(
			JRoute::_($course->link()),
			JText::_('COM_COURSES_PAGE_SAVED')
		);
	}

	/**
	 * Change the status of an item
	 *
	 * @return     void
	 */
	public function deletepageTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->loginTask('COM_COURSES_NOT_LOGGEDIN');
			return;
		}

		if (!$this->course->exists())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		if (!$this->course->access('edit', 'course'))
		{
			$this->setRedirect(
				JRoute::_($this->course->link())
			);
			return;
		}

		$model = $this->course->page(JRequest::getVar('active', ''));

		$msg = null;

		if ($model->exists())
		{
			$model->set('active', 0);

			if (!$model->store(true))
			{
				$msg = $model->getError();
			}
		}

		// Redirect back to the course page
		$this->setRedirect(
			JRoute::_($this->course->link()),
			($msg ? $msg : JText::_('COM_COURSES_PAGE_REMOVED')),
			($msg ? 'error' : null)
		);
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

		return false;
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

	/**
	 * Download a wiki file
	 *
	 * @return     void
	 */
	public function downloadTask()
	{
		// Get some needed libraries
		if (!$this->course->access('view'))
		{
			JError::raiseError(404, JText::_('COM_COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Get the scope of the parent page the file is attached to
		$filename = JRequest::getVar('file', '');
		if (substr(strtolower($filename), 0, strlen('image:')) == 'image:')
		{
			$filename = substr($filename, strlen('image:'));
		}
		else if (substr(strtolower($filename), 0, strlen('file:')) == 'file:')
		{
			$filename = substr($filename, strlen('file:'));
		}
		$filename = urldecode($filename);

		// Get the configured upload path
		$base_path = DS . trim($this->config->get('filepath', '/site/courses'), DS) . DS . $this->course->get('id') . DS . 'pagefiles';

		// Does the path start with a slash?
		$filename = DS . ltrim($filename, DS);

		// Does the beginning of the $attachment->path match the config path?
		if (substr($filename, 0, strlen($base_path)) == $base_path)
		{
			// Yes - this means the full path got saved at some point
		}
		else
		{
			// No - append it
			$filename = $base_path . $filename;
		}

		// Add JPATH_ROOT
		$filename = JPATH_ROOT . $filename;

		// Ensure the file exist
		if (!file_exists($filename))
		{
			JError::raiseError(404, JText::_('COM_COURSES_FILE_NOT_FOUND').' '.$filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('COM_COURSES_SERVER_ERROR'));
		}
		else
		{
			exit;
		}
		return;
	}
}

