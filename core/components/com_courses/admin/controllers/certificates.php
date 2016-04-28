<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Admin\Controllers;

use Components\Courses\Models\Certificate;
use Hubzero\Component\AdminController;
use Filesystem;
use Exception;
use Request;
use Route;
use Lang;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'certificate.php');

/**
 * Courses controller class for managing membership and course info
 */
class Certificates extends AdminController
{
	/**
	 * Displays a list of courses
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->cert_id   = Request::getInt('certificate', 0);
		$this->view->course_id = Request::getInt('course', 0);

		$this->view->certificate = Certificate::getInstance($this->view->cert_id, $this->view->course_id);

		if (!$this->view->certificate->exists())
		{
			return $this->addTask($this->view->certificate);
		}

		if (!$this->view->certificate->hasFile())
		{
			return $this->editTask($this->view->certificate);
		}

		Request::setVar('hidemainmenu', 1);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('display')
			->display();
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
		Request::checkToken();

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');

		// Instantiate a Course object
		$model = Certificate::getInstance($fields['id'], $fields['course_id']);

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
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false), //'&controller=' . $this->_controller . '&course=' . $model->get('course_id') . '&certificate=' . $model->get('id'),
				Lang::txt('COM_COURSES_SETTINGS_SAVED')
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
		$certificate = Certificate::getInstance(Request::getInt('certificate', 0));
		if (!$certificate->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=courses', false),
				Lang::txt('COM_COURSES_ERROR_MISSING_CERTIFICATE'),
				'error'
			);
			return;
		}

		$certificate->render(User::getInstance());
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
		Request::setVar('hidemainmenu', 1);

		if (!is_object($model))
		{
			// Incoming
			$id = Request::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$model = new Certificate($id);
		}

		$this->view->row = $model;

		if (!$this->view->row->get('course_id'))
		{
			$this->view->row->set('course_id', Request::getInt('course', 0));
		}

		if (!$this->view->row->exists())
		{
			$this->view->row->store();
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Upload a file or create a new folder
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$cert_id   = Request::getInt('certificate', 0, 'post');
		$course_id = Request::getInt('course', 0, 'post');
		if (!$course_id)
		{
			$this->setError(Lang::txt('COURSES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		$model = Certificate::getInstance($cert_id, $course_id);
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
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask();
				return;
			}
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_NO_FILE_FOUND'));
			$this->displayTask();
			return;
		}

		// Make the filename safe
		$ext = Filesystem::extension($file['name']);
		if (strtolower($ext) != 'pdf')
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_INVALID_FILE_TYPE'));
			$this->displayTask();
			return;
		}

		$file['name'] = $model->get('name');

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_UPLOADING') . $path . DS . $file['name']);
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
		Request::checkToken();

		$cert_id   = Request::getInt('certificate', 0, 'post');
		$course_id = Request::getInt('course', 0, 'post');
		if (!$course_id)
		{
			$this->setError(Lang::txt('COURSES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		$model = Certificate::getInstance($cert_id, $course_id);
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
			if (!Filesystem::emptyDirectory($path))
			{
				$this->setError(Lang::txt('COM_COURSES_UNABLE_TO_DELETE_FILE'));
			}
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=courses', false),
			Lang::txt('COM_COURSES_ITEM_REMOVED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=courses', false)
		);
	}
}
