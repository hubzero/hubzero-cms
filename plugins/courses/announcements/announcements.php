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
 * Plugin class for course announcements
 */
class plgCoursesAnnouncements extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param   object   $course    Current course
	 * @param   object   $offering  Name of the component
	 * @param   boolean  $describe  Return plugin description only?
	 * @return  object
	 */
	public function onCourse($course, $offering, $describe=false)
	{
		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', JText::_('PLG_COURSES_' . strtoupper($this->_name)))
			->set('default_access', $this->params->get('plugin_access', 'members'))
			->set('display_menu_tab', true)
			->set('icon', 'f095');

		if ($describe)
		{
			return $response;
		}

		if (!($active = JRequest::getVar('active')))
		{
			JRequest::setVar('active', ($active = $this->_name));
		}

		// Get a student count
		$response->set('meta_count', $offering->announcements(array('count' => true)));

		// Check if our area is in the array of areas we want to return results for
		if ($response->get('name') == $active)
		{
			// Set some variables so other functions have access
			$this->option   = JRequest::getCmd('option', 'com_courses');
			$this->course   = $course;
			$this->offering = $offering;

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
				case 'save':   $response->set('html', $this->_save());   break;
				case 'new':    $response->set('html', $this->_edit());   break;
				case 'edit':   $response->set('html', $this->_edit());   break;
				case 'delete': $response->set('html', $this->_delete()); break;
				default:       $response->set('html', $this->_list());   break;
			}
		}

		// Return the output
		return $response;
	}

	/**
	 * Set redirect and message
	 *
	 * @param   object  $url  URL to redirect to
	 * @param   object  $msg  Message to send
	 * @return  void
	 */
	public function onCourseBeforeOutline($course, $offering)
	{
		return $this->view('default', 'latest')
					->set('course', $course)
					->set('offering', $offering)
					->set('params', $this->params)
					->loadTemplate();
	}

	/**
	 * Administrative dashboard info
	 *
	 * @param   object  $course    CoursesModelCourse
	 * @param   object  $offering  CoursesModelOffering
	 * @return  string
	 */
	public function onCourseDashboard($course, $offering)
	{
		$view = with($this->view('dashboard', 'browse'))
			->set('course', $course)
			->set('offering', $offering)
			->set('option', JRequest::getCmd('option', 'com_courses'))
			->set('config', $course->config())
			->set('params', $this->params);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a list of all entries
	 *
	 * @return  string  HTML
	 */
	private function _list()
	{
		$view = with($this->view('default', 'browse'))
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('option', $this->option)
			->set('config', $this->course->config())
			->set('params', $this->params)
			->set('no_html', JRequest::getInt('no_html', 0));

		// Get filters for the entries list
		$filters = array(
			'search' => JRequest::getVar('q', ''),
			'limit'  => JRequest::getInt('limit', JFactory::getConfig()->get('list_limit', 25)),
			'start'  => JRequest::getInt('limitstart', 0)
		);
		$filters['start'] = ($filters['limit'] == 0 ? 0 : $filters['start']);

		$view->set('filters', $filters);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display a form for editing or creating an entry
	 *
	 * @return  string  HTML
	 */
	private function _edit($model=null)
	{
		// Permissions check
		if (!$this->offering->access('manage', 'section'))
		{
			return $this->_list();
		}

		$view = with($this->view('default', 'edit'))
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('option', $this->option)
			->set('params', $this->params);

		if (!($model instanceof CoursesModelAnnouncement))
		{
			$model = CoursesModelAnnouncement::getInstance(JRequest::getInt('entry', 0));
		}

		$view->set('model', $model);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		// Display edit form
		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 *
	 * @return  string  HTML
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
				$response->code   = 1;
				$response->errors = $this->getErrors();
				$response->data   = $fields;
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
	 * @return  string  HTML
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

