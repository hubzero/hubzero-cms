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
 * Courses Plugin class for course members
 */
class plgCoursesAnnouncements extends \Hubzero\Plugin\Plugin
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
	public function &onCourseAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => JText::_('PLG_COURSES_' . strtoupper($this->_name)),
			'default_access' => $this->params->get('plugin_access', 'members'), //$this->params->get('plugin_access', 'managers'),
			'display_menu_tab' => true,
			'icon' => 'f095'
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
		$juser = JFactory::getUser();

		// Set the page title
		$document = JFactory::getDocument();
		$document->setTitle($document->getTitle() . ': ' . JText::_('PLG_COURSES_ANNOUNCEMENTS'));

		$pathway = JFactory::getApplication()->getPathway();
		$pathway->addItem(
			JText::_('PLG_COURSES_' . strtoupper($this->_name)),
			$this->offering->link() . '&active=' . $this->_name
		);

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
		$view = new \Hubzero\Plugin\View(
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
	 * Administrative dashboard info
	 *
	 * @param      object $course   CoursesModelCourse
	 * @param      object $offering CoursesModelOffering
	 * @return     string
	 */
	public function onCourseDashboard($course, $offering)
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'browse',
				'layout'  => 'dashboard'
			)
		);

		$view->course   = $course;
		$view->offering = $offering;
		$view->option   = 'com_courses';
		$view->config   = $course->config();
		$view->params   = $this->params;

		// Set any errors
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
	 * Display a list of all entries
	 *
	 * @return  string HTML
	 */
	private function _list()
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'browse'
			)
		);

		$jconfig = JFactory::getConfig();

		$view->option   = $this->option;
		$view->course   = $this->course;
		$view->offering = $this->offering;
		$view->params   = $this->params;

		// Get filters for the entries list
		$view->filters  = array(
			'search' => JRequest::getVar('q', ''),
			'limit'  => JRequest::getInt('limit', $jconfig->getValue('config.list_limit')),
			'start'  => JRequest::getInt('limitstart', 0)
		);
		$view->filters['start'] = ($view->filters['limit'] == 0 ? 0 : $view->filters['start']);

		$view->no_html = JRequest::getInt('no_html', 0);

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
	 * Display a form for editing or creating an entry
	 *
	 * @return  string HTML
	 */
	private function _edit($model=null)
	{
		// Permissions check
		if (!$this->offering->access('manage', 'section'))
		{
			return $this->_list();
		}

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
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
	 * @return  string HTML
	 */
	private function _save()
	{
		// Permissions check
		if (!$this->offering->access('manage', 'section'))
		{
			return $this->_list();
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$no_html = JRequest::getInt('no_html', 0);

		$response = new stdClass;
		$response->code = 0;

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		// Get the model and bind the data
		$model = new CoursesModelAnnouncement(0);
		if (!$model->bind($fields))
		{
			$this->setError($model->getError());
			return $this->_edit($model);
		}

		// Incoming dates are in local time. We need to convert to UTC
		if ($model->get('publish_up') && $model->get('publish_up') != '0000-00-00 00:00:00')
		{
			$dt = new JDate($model->get('publish_up'), JFactory::getConfig()->getValue('config.offset'));
			$model->set('publish_up', $dt->format(JFactory::getDBO()->getDateFormat()));
		}

		// Incoming dates are in local time. We need to convert to UTC
		if ($model->get('publish_down') && $model->get('publish_down') != '0000-00-00 00:00:00')
		{
			$dt = new JDate($model->get('publish_down'), JFactory::getConfig()->getValue('config.offset'));
			$model->set('publish_down', $dt->format(JFactory::getDBO()->getDateFormat()));
		}

		if (!isset($fields['priority']) || !$fields['priority'])
		{
			$model->set('priority', 0);
		}

		// Store content
		if (!$model->store(true))
		{
			$this->setError($model->getError());
			if (!$no_html)
			{
				return $this->_edit($model);
			}
		}

		if ($no_html)
		{
			if ($this->getError())
			{
				$response->code = 1;
				$response->errors = $this->getErrors();
				$response->data = $fields;
			}
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode($response);
			exit();
		}

		// Display listing
		return $this->_list();
	}

	/**
	 * Mark an entry as deleted
	 *
	 * @return  string HTML
	 */
	private function _delete()
	{
		// Permissions check
		if (!$this->offering->access('manage', 'section'))
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

