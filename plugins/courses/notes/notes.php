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

require_once(__DIR__ . DS . 'models' . DS . 'note.php');

/**
 * Courses Plugin class for user notes
 */
class plgCoursesNotes extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
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
			->set('icon', '270D');

		if ($describe)
		{
			return $response;
		}

		if (!($active = JRequest::getVar('active')))
		{
			JRequest::setVar('active', ($active = $this->_name));
		}

		if ($response->get('name') == $active)
		{
			$this->course   = $course;
			$this->offering = $offering;
			$this->database = JFactory::getDBO();

			$this->view = with($this->view('default', 'notes'))
				->set('option', JRequest::getCmd('option', 'com_courses'))
				->set('controller', JRequest::getWord('controller', 'course'))
				->set('course', $course)
				->set('offering', $offering)
				->set('no_html', JRequest::getInt('no_html', 0));

			$this->view->filters = array(
				'section_id' => $offering->section()->get('id'),
				'search'     => JRequest::getVar('search', '')
			);

			$this->view->model = new CoursesPluginModelNote(0);

			if ($action = strtolower(JRequest::getWord('action', '')))
			{
				switch ($action)
				{
					case 'add':      $result = $this->_edit();   break;
					case 'edit':     $result = $this->_edit();   break;
					case 'save':     $result = $this->_save();   break;
					case 'delete':   $result = $this->_delete(); break;
					case 'download': $result = $this->_download(); break;

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

			$response->set('html', $this->view->loadTemplate());
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
	public function onCourseAfterLecture($course, $unit, $lecture)
	{
		if (!$course->offering()->section()->access('view'))
		{
			return;
		}

		$this->view = $this->view('default', 'lecture');

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
	 * Set layout to the listing
	 *
	 * @return  void
	 */
	public function _list()
	{
		if (!$this->view->no_html)
		{
			$this->view->setLayout('default');
		}
	}

	/**
	 * Download
	 *
	 * @return  void
	 */
	public function _download()
	{
		$format = strtolower(JRequest::getWord('frmt', 'txt'));
		if (!in_array($format, array('txt', 'csv')))
		{
			$format = 'txt';
		}
		$this->view->setLayout('download_' . $format);

		@ob_end_clean();

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");

		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:attachment; filename="notes.' . $format . '";'); //RFC2183
		header("Content-Type: text/plain"); // MIME type

		echo $this->view->loadTemplate();
		die();
	}

	/**
	 * Set layout to the edit view
	 *
	 * @param   mixed  $model
	 * @return  void
	 */
	public function _edit($model=null)
	{
		if (!$this->view->no_html)
		{
			$this->view->setLayout('edit');
		}

		if ($model instanceof CoursesPluginModelNote)
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
	 * Save a record
	 *
	 * @return  mixed
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
		if ($timestamp = JRequest::getVar('time', ''))
		{
			$model->set('timestamp', $timestamp);
		}
		if ($content = JRequest::getVar('txt', ''))
		{
			$model->set('content', $content);
		}
		$model->set('access', JRequest::getInt('access', 0));
		$model->set('section_id', $this->view->offering->section()->get('id'));

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
	 * Delete a record
	 *
	 * @return  mixed
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

