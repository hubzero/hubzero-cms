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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'certificate.php');

/**
 * Courses controller class for managing membership and course info
 */
class CoursesControllerCertificates extends \Hubzero\Component\AdminController
{
	/**
	 * Displays a list of courses
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		$this->view->cert_id   = JRequest::getInt('certificate', 0);
		$this->view->course_id = JRequest::getInt('course', 0);

		$this->view->certificate = CoursesModelCertificate::getInstance($this->view->cert_id, $this->view->course_id);

		if (!$this->view->certificate->exists())
		{
			return $this->addTask($this->view->certificate);
		}

		if (!$this->view->certificate->hasFile())
		{
			return $this->editTask($this->view->certificate);
		}

		JRequest::setVar('hidemainmenu', 1);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->setLayout('display')->display();
	}

	/**
	 * Saves changes
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Saves changes
	 *
	 * @return void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		// Instantiate a Course object
		$model = CoursesModelCertificate::getInstance($fields['id'], $fields['course_id']);

		if (!$model->bind($fields))
		{
			$this->setError($model->getError());
			$this->displayTask();
			return;
		}

		if (!$model->store(true))
		{
			$this->setError($model->getError());
			$this->displayTask();
			return;
		}

		if ($redirect)
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option, //'&controller=' . $this->_controller . '&course=' . $model->get('course_id') . '&certificate=' . $model->get('id'),
				JText::_('COM_COURSES_SETTINGS_SAVED')
			);
			return;
		}

		$this->displayTask();
	}

	/**
	 * Displays a list of courses
	 *
	 * @return	void
	 */
	public function previewTask()
	{
		// Load certificate record
		$certificate = CoursesModelCertificate::getInstance(JRequest::getInt('certificate', 0));
		if (!$certificate->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses',
				JText::_('COM_COURSES_ERROR_MISSING_CERTIFICATE'),
				'error'
			);
			return;
		}

		$certificate->render($this->juser);
	}

	/**
	 * Create a new course
	 *
	 * @return	void
	 */
	public function addTask($model=null)
	{
		$this->editTask($model);
	}

	/**
	 * Displays an edit form
	 *
	 * @return	void
	 */
	public function editTask($model=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		if (is_object($model))
		{
			$this->view->row = $model;
		}
		else
		{
			// Incoming
			$id = JRequest::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$this->view->row = new CoursesModelCertificate($id);
		}

		if (!$this->view->row->get('course_id'))
		{
			$this->view->row->set('course_id', JRequest::getInt('course', 0));
		}

		if (!$this->view->row->exists())
		{
			$this->view->row->store();
		}

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Upload a file or create a new folder
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$cert_id   = JRequest::getInt('certificate', 0, 'post');
		$course_id = JRequest::getInt('course', 0, 'post');
		if (!$course_id)
		{
			$this->setError(JText::_('COURSES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		$model = CoursesModelCertificate::getInstance($cert_id, $course_id);
		$model->set('name', 'certificate.pdf');
		if (!$model->exists())
		{
			$model->store();
		}

		// Build the path
		$path = $model->path('system');

		// Make sure the upload path exist
		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				$this->setError(JText::_('COM_COURSES_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask();
				return;
			}
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('COM_COURSES_ERROR_NO_FILE_FOUND'));
			$this->displayTask();
			return;
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$ext = JFile::getExt($file['name']);
		if (strtolower($ext) != 'pdf')
		{
			$this->setError(JText::_('COM_COURSES_ERROR_INVALID_FILE_TYPE'));
			$this->displayTask();
			return;
		}

		$file['name'] = $model->get('name');

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(JText::_('COM_COURSES_ERROR_UPLOADING') . $path . DS . $file['name']);
		}

		$model->renderPageImages();

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Removes a course certificate
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$cert_id   = JRequest::getInt('certificate', 0, 'post');
		$course_id = JRequest::getInt('course', 0, 'post');
		if (!$course_id)
		{
			$this->setError(JText::_('COURSES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		$model = CoursesModelCertificate::getInstance($cert_id, $course_id);
		if ($model->exists())
		{
			$model->set('properties', '');
			$model->store();
		}

		// Build the path
		$path = $model->path('system');

		// Make sure the upload path exist
		if (is_dir($path))
		{
			// Delete all the files in the directory
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');

			$dirIterator = new DirectoryIterator($path);
			foreach ($dirIterator as $file)
			{
				if ($file->isDot())
				{
					continue;
				}

				if ($file->isDir())
				{
					$name = $file->getFilename();
					if (!JFolder::delete($path . DS . $file->getFilename()))
					{
						$this->setError(JText::_('COM_COURSES_UNABLE_TO_DELETE_FILE'));
					}
					continue;
				}

				if ($file->isFile())
				{
					if (!JFile::delete($path . DS . $file->getFilename()))
					{
						$this->setError(JText::_('COM_COURSES_UNABLE_TO_DELETE_FILE'));
					}
				}
			}
		}

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=courses',
			JText::_('COM_COURSES_ITEM_REMOVED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=courses'
		);
	}
}
