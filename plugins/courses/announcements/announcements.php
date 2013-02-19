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

jimport('joomla.plugin.plugin');


/**
 * Courses Plugin class for course members
 */
class plgCoursesAnnouncements extends JPlugin
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
			'default_access' => $this->params->get('plugin_access', 'members'), //$this->params->get('plugin_access', 'managers'),
			'display_menu_tab' => true
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
		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		//get this area details
		$this_area = $this->onCourseAreas();

		// Set some variables so other functions have access
		$this->action = $action;
		$this->option = JRequest::getVar('option', 'com_courses');
		$this->course = $course;
		$this->offering = $offering;

		// Get a student count
		$arr['metadata']['count'] = $offering->announcements(array('count' => true));

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!in_array($this_area['name'], $areas)) 
			{
				return $arr;
			}
		}
		else if ($areas != $this_area['name'])
		{
			return $arr;
		}

		// Only perform the following if this is the active tab/plugin
		$this->config = $config;

		//Create user object
		$juser =& JFactory::getUser();

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle($document->getTitle() . ': ' . JText::_('PLG_COURSES_ANNOUNCEMENTS'));

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'announcement.php');

		$action = JRequest::getWord('action', '');

		switch (strtolower($action))
		{
			case 'save':     $arr['html'] .= $this->_save();     break;
			case 'new':      $arr['html'] .= $this->_edit();     break;
			case 'edit':     $arr['html'] .= $this->_edit();     break;
			case 'delete':   $arr['html'] .= $this->_delete();   break;

			default: $arr['html'] .= $this->_list(); break;
		}

		// Return the output
		return $arr;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @param      object $msg  Message to send
	 * @return     void
	 */
	public function onCourseBeforeOutline($course, $offering)
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('courses', $this->_name);

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'latest'
			)
		);
		$view->course   = $course;
		$view->offering = $offering;
		$view->params   = $this->params;

		return $view->loadTemplate();
	}

	/**
	 * Display a list of all announcements
	 * 
	 * @return     string HTML
	 */
	private function _list()
	{
		// Get course members based on their status
		// Note: this needs to happen *after* any potential actions ar performed above
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'browse'
			)
		);

		$view->option   = $this->option;
		$view->course   = $this->course;
		$view->offering = $this->offering;
		$view->params   = $this->params;

		$view->filters  = array();
		$view->filters['search'] = JRequest::getVar('q', '');
		$view->filters['limit']  = JRequest::getInt('limit', $this->params->get('display_limit', 50));
		$view->filters['start']  = JRequest::getInt('limitstart', 0);
		$view->filters['start']  = ($view->filters['limit'] == 0) ? 0 : $view->filters['start'];

		$view->no_html = JRequest::getInt('no_html', 0);

		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('courses', $this->_name);
		//Hubzero_Document::addPluginScript('courses', $this->_name);

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a list of all announcements
	 * 
	 * @return     string HTML
	 */
	private function _edit($model=null)
	{
		if (!$this->offering->access('manage'))
		{
			return $this->_list();
		}

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'edit'
			)
		);

		$view->option   = $this->option;
		$view->course   = $this->course;
		$view->offering = $this->offering;
		$view->params   = $this->params;

		if (!is_object($model))
		{
			$id = JRequest::getInt('entry', 0);
			$model = CoursesModelAnnouncement::getInstance($id);
		}
		$view->model = $model;

		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('courses', $this->_name);
		//Hubzero_Document::addPluginScript('courses', $this->_name);

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		// Display edit form
		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 * 
	 * @return     string HTML
	 */
	private function _save()
	{
		if (!$this->offering->access('manage'))
		{
			return $this->_list();
		}

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Get the model and bind the data
		$model = new CoursesModelAnnouncement();
		if (!$model->bind($fields))
		{
			$this->setError($model->getError());
			return $this->_edit($model);
		}
		if (!isset($fields['priority']) || !$fields['priority'])
		{
			$model->set('priority', 0);
		}

		// Store content
		if (!$model->store(true)) 
		{
			$this->setError($model->getError());
			return $this->_edit($model);
		}

		// Incoming message and subject
		/*$subject = JText::_('PLG_COURSES_ANNOUNCEMENTS_SUBJECT');

		// Add a link to the course page to the bottom of the message
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . 'active=announcements');

		$message  = $model->get('content');
		$message .= "\r\n\r\n------------------------------------------------\r\n" . rtrim($juri->base(), DS) . DS . ltrim($sef, DS) . "\r\n";

		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = $juser->get('name') . ' (' . JText::_(strtoupper($this->_name)) . ': ' . $this->course->get('alias') . ')';
		$from['email'] = $juser->get('email');

		// Send the message
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('course_announcement', $subject, $message, $from, $mbrs, $this->_option, null, '', $this->course->get('id')))) 
		{
			$this->setError(JText::_('COURSES_ERROR_EMAIL_MEMBERS_FAILED'));
		}*/

		/*$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . 'active=announcements')
		);
		return;*/

		// Display listing
		return $this->_list();
	}

	/**
	 * Mark an entry as deleted
	 * 
	 * @return     string HTML
	 */
	private function _delete()
	{
		if (!$this->offering->access('manage'))
		{
			return $this->_list();
		}

		// Incoming
		$id = JRequest::getInt('entry', 0);

		// Get the model and set the state to "deleted"
		$model = CoursesModelAnnouncement::getInstance($id);
		$model->set('state', 2);

		// Store content
		if (!$model->store()) 
		{
			$this->setError($model->getError());
		}

		// Display listing
		return $this->_list();
	}
}

