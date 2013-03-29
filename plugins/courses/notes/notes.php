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
class plgCoursesNotes extends JPlugin
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

		require_once(JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . 'notes' . DS . 'models' . DS . 'note.php');
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
		$active_real = 'discussion';

		// The output array we're returning
		$arr = array(
			'html' => '',
			'name' => $active
		);

		$this_area = $this->onCourseAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!in_array($this_area['name'], $areas)) 
			{
				$return = 'metadata';
			}
		}
		else if ($areas != $arr['name'])
		{
			$return = 'metadata';
		}

		if ($return == 'html') 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('courses', $this->_name);
			Hubzero_Document::addPluginScript('courses', $this->_name);

			$this->config   = $config;
			$this->course   = $course;
			$this->offering = $offering;
			$this->database = JFactory::getDBO();

			ximport('Hubzero_Plugin_View');
			$this->view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => $this->_name,
					'name'    => 'notes'
				)
			);
			$this->view->option     = JRequest::getCmd('option', 'com_courses');
			$this->view->controller = JRequest::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->no_html    = JRequest::getInt('no_html', 0);

			$this->view->model = new CoursesPluginModelNote(0);

			$action = strtolower(JRequest::getWord('action', ''));
			if ($action)
			{
				switch ($action)
				{
					case 'add':    $result = $this->_edit();   break;
					case 'edit':   $result = $this->_edit();   break;
					case 'save':   $result = $this->_save();   break;
					case 'delete': $result = $this->_delete(); break;

					default: $result = $this->_list(); break;
				}
			}

			if ($this->view->no_html && $result)
			{
				$note = new stdClass;
				$note->id = $result;
				$note->success = true;
				if ($this->getError())
				{
					$note->success = false;
					$note->error = $this->getError();
				}

				ob_clean();
				echo json_encode($note);
				return;
			}

			$arr['html'] = $this->view->loadTemplate();
		}

		$arr['metadata']['count'] = 0;

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
	public function onCourseAfterLecture($course, $unit, $lecture)
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('courses', $this->_name);
		Hubzero_Document::addPluginScript('courses', $this->_name);

		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'lecture'
			)
		);

		$this->database = JFactory::getDBO();
		$this->juser    = JFactory::getUser();
		$this->view->course   = $this->course   = $course;
		$this->view->offering = $this->offering = $course->offering();
		$this->view->unit     = $this->unit     = $unit;
		$this->view->lecture  = $this->lecture  = $lecture;

		$this->view->model = new CoursesPluginModelNote(0);

		return $this->view->loadTemplate();
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _list()
	{
		if (!$this->view->no_html)
		{
			$this->view->setLayout('default');
		}
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _edit($model=null)
	{
		if (!$this->view->no_html)
		{
			$this->view->setLayout('edit');
		}

		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else
		{
			$note_id = JRequest::getInt('note', 0);

			$this->view->model = new CoursesPluginModelNote($note_id);
		}
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @param      object $msg  Message to send
	 * @return     void
	 */
	public function _save()
	{
		$note_id = JRequest::getInt('note', 0);

		$model = new CoursesPluginModelNote($note_id);

		if ($scope = JRequest::getWord('scope', 'lecture'))
		{
			$model->set('scope', $scope);
		}
		if ($scope_id = JRequest::getInt('scope_id', 0))
		{
			$model->set('scope_id', $scope_id);
		}
		if ($pos_x = JRequest::getInt('x', 0))
		{
			$model->set('pos_x', $pos_x);
		}
		if ($pos_y = JRequest::getInt('y', 0))
		{
			$model->set('pos_y', $pos_y);
		}
		if ($width = JRequest::getInt('w', 0))
		{
			$model->set('width', $width);
		}
		if ($height = JRequest::getInt('h', 0))
		{
			$model->set('height', $height);
		}
		if ($state = JRequest::getInt('state', 0))
		{
			$model->set('state', $state);
		}
		if ($content = JRequest::getVar('txt', ''))
		{
			$model->set('content', $content);
		}

		if (!$model->store(true))
		{
			$this->setError($model->getError());
			if (!$this->view->no_html)
			{
				return $this->_edit($model);
			}
		}

		if (!$this->view->no_html)
		{
			return $this->_list();
		}

		return $model->get('id');
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _delete()
	{
		$note_id = JRequest::getInt('note', 0);

		$model = new CoursesPluginModelNote($note_id);
		if ($model->exists())
		{
			$model->set('state', 2);
			if (!$model->store(false))
			{
				$this->setError($model->getError());
			}
		}

		if (!$this->view->no_html)
		{
			return $this->_list();
		}
		return $note_id;
	}
}

