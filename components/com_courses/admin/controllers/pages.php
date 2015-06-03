<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Courses\Admin\Controllers;

use Components\Courses\Tables;
use Hubzero\Component\AdminController;
use Filesystem;
use Exception;
use Request;
use Route;
use Lang;
use Html;
use App;

// Course model pulls in other classes we need
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for managing course pages
 */
class Pages extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('orderup', 'order');
		$this->registerTask('orderdown', 'order');

		parent::execute();
	}

	/**
	 * Manage course pages
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'active' => Request::getState(
				$this->_option . '.' . $this->_controller . '.active',
				'active',
				'-1'
			),
			// Filters for returning results
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'offering' => Request::getState(
				$this->_option . '.' . $this->_controller . '.offering',
				'offering',
				0
			)
		);

		$this->view->offering = \Components\Courses\Models\Offering::getInstance($this->view->filters['offering']);
		if ($this->view->offering->exists())
		{
			$this->view->filters['course']    = Request::getState(
				$this->_option . '.' . $this->_controller . '.course',
				'course',
				$this->view->offering->get('course_id')
			);
		}
		else
		{
			$using = 'course';
			$this->view->filters['course']    = Request::getState(
				$this->_option . '.' . $this->_controller . '.course',
				'course',
				0
			);
		}

		$this->view->course = \Components\Courses\Models\Course::getInstance($this->view->filters['course']);

		if ($this->view->offering->exists())
		{
			$list = $this->view->offering->pages(array('active' => array(0, 1)));
		}
		else
		{

			$list = $this->view->course->pages(array('active' => array(0, 1)));
		}

		$this->view->total = count($list);

		$this->view->rows = array_slice($list, $this->view->filters['start'], $this->view->filters['limit']);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a course page
	 *
	 * @return void
	 */
	public function editTask($model = null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($model))
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$model = new \Components\Courses\Models\Page($id);
		}

		$this->view->row = $model;

		if (!$this->view->row->get('course_id'))
		{
			$this->view->row->set('course_id', Request::getInt('course', 0));
		}
		if (!$this->view->row->get('offering_id'))
		{
			$this->view->row->set('offering_id', Request::getInt('offering', 0));
		}
		if (!$this->view->row->exists())
		{
			$this->view->row->set('active', 1);
		}

		$this->view->course   = \Components\Courses\Models\Course::getInstance($this->view->row->get('course_id'));
		$this->view->offering = \Components\Courses\Models\Offering::getInstance($this->view->row->get('offering_id'));

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			\Notify::error($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a course page
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// load the request vars
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// instatiate course page object for saving
		$row = new \Components\Courses\Models\Page($fields['id']);

		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		if (!$row->store(true))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . $fields['course_id'] . '&offering=' . $fields['offering_id'], false),
			Lang::txt('COM_COURSES_ITEM_SAVED')
		);
	}

	/**
	 * Remove one or more types
	 *
	 * @return  void  Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming (expecting an array)
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$rtrn = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . Request::getInt('course', 0) . '&offering=' . Request::getInt('offering', 0), false);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			App::redirect(
				$rtrn,
				Lang::txt('COM_COURSES_ERROR_NO_ENTRIES_SELECTED'),
				'error'
			);
			return;
		}

		$tbl = new Tables\Page($this->database);

		$i = 0;
		foreach ($ids as $id)
		{
			// Delete the type
			if (!$tbl->delete($id))
			{
				$this->setError($tbl->getError());
			}
			else
			{
				$i++;
			}
		}

		// Redirect
		if ($i)
		{
			App::redirect(
				$rtrn,
				Lang::txt('COM_COURSES_ITEMS_REMOVED', $i)
			);
			return;
		}

		App::redirect(
			$rtrn,
			$this->getError(),
			'error'
		);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . Request::getInt('course', 0) . '&offering=' . Request::getInt('offering', 0), false)
		);
	}

	/**
	 * Build file path
	 *
	 * @return  void
	 */
	private function _buildUploadPath($listdir=0)
	{
		$course_id = Request::getInt('course', 0);

		$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS);
		$path .= ($course_id) ? DS . $course_id : '';
		$path .= DS . 'pagefiles';
		if ($listdir)
		{
			$path .= DS . $listdir;
		}
		return $path;
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return  string
	 */
	public function ajaxUploadTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or exit('Invalid Token');

		// Ensure we have an ID to work with
		$listdir = Request::getVar('listdir', 0);

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		//allowed extensions for uplaod
		//$allowedExtensions = array("png","jpeg","jpg","gif");

		//max upload size
		$sizeLimit = $this->config->get('maxAllowed', 40000000);

		// get the file
		if (isset($_GET['qqfile']))
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_NO_FILE_FOUND')));
			return;
		}

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_UPLOAD_DIRECTORY_IS_NOT_WRITABLE')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_EMPTY_FILE')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_FILE_TOO_LARGE', $max)));
			return;
		}

		// don't overwrite previous files that were uploaded
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];

		// Make the filename safe
		$filename = urldecode($filename);
		$filename = Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);

		$ext = $pathinfo['extension'];
		while (file_exists($path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		$file = $path . DS . $filename . '.' . $ext;

		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			//move from temp location to target location which is user folder
			$target = fopen($file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		//echo result
		echo json_encode(array(
			'success'   => true,
			'file'      => $filename . '.' . $ext,
			'directory' => str_replace(PATH_APP, '', $path),
			'id'        => $listdir
		));
	}

	/**
	 * Upload a file or create a new folder
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		if (Request::getVar('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = Request::getVar('listdir', 0, 'post');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COURSES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '', 'post');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Are we creating a new folder?
		$foldername = Request::getVar('foldername', '', 'post');
		if ($foldername != '')
		{
			// Make sure the name is valid
			if (preg_match("#[^0-9a-zA-Z_]#i", $foldername))
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_INVALID_DIRECTORY'));
			}
			else
			{
				if (!is_dir($path . DS . $foldername))
				{
					if (!Filesystem::makeDirectory($path . DS . $foldername))
					{
						$this->setError(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
					}
				}
				else
				{
					$this->setError(Lang::txt('COM_COURSES_ERROR_DIRECTORY_EXISTS'));
				}
			}
			// Directory created
		}
		else
		{
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
				$this->setError(Lang::txt('COM_COURSES_ERROR_NO_FILE'));
				$this->displayTask();
				return;
			}

			// Make the filename safe
			$file['name'] = Filesystem::clean($file['name']);
			// Ensure file names fit.
			$ext = Filesystem::extension($file['name']);
			$file['name'] = str_replace(' ', '_', $file['name']);
			if (strlen($file['name']) > 230)
			{
				$file['name'] = substr($file['name'], 0, 230);
				$file['name'] .= '.' . $ext;
			}

			// Perform the upload
			if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_UPLOADING'));
			}
		}

		// Push through to the media view
		$this->filesTask();
	}

	/**
	 * Delete a folder and contents
	 *
	 * @return     void
	 */
	public function deletefolderTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or exit('Invalid Token');

		$this->view->setLayout('display');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = Request::getVar('listdir', 0);
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_NO_ID'));
			$this->filesTask();
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Incoming directory to delete
		$folder = trim(Request::getVar('delFolder', ''), DS);
		if (!$folder)
		{
			$this->setError(Lang::txt('COURSES_NO_DIRECTORY'));
			$this->filesTask();
			return;
		}

		// Check if the folder even exists
		if (!is_dir($path . DS . $folder) or !$folder)
		{
			$this->setError(Lang::txt('DIRECTORY_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::deleteDirectory($path . DS . $folder))
			{
				$this->setError(Lang::txt('UNABLE_TO_DELETE_DIRECTORY'));
			}
		}

		// Push through to the media view
		$this->filesTask();
	}

	/**
	 * Deletes a file
	 *
	 * @return     void
	 */
	public function deletefileTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or exit('Invalid Token');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$course_id = Request::getInt('course', 0);
		$listdir = Request::getVar('listdir', 0);

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Incoming file to delete
		$file = Request::getVar('delFile', '');
		if (!$file)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_NO_FILE'));
			$this->filesTask();
			return;
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_DELETE_FILE'));
			}
		}

		// Push through to the media view
		$this->filesTask();
	}

	/**
	 * Display an upload form and file listing
	 *
	 * @return  void
	 */
	public function filesTask()
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$this->view->course_id = Request::getInt('course', 0);
		$this->view->listdir   = Request::getVar('listdir', 0);

		// Incoming sub-directory
		$this->view->subdir = Request::getVar('subdir', '');

		// Build the path
		$this->view->path = $this->_buildUploadPath($this->view->listdir, $this->view->subdir);

		// Get list of directories
		$dirs = $this->_recursiveListDir($this->view->path);

		$folders   = array();
		$folders[] = Html::select('option', '/');
		if ($dirs)
		{
			foreach ($dirs as $dir)
			{
				$folders[] = Html::select('option', substr($dir, strlen($this->view->path)));
			}
		}
		sort($folders);

		// Create folder <select> list
		$this->view->dirPath = Html::select(
			'genericlist',
			$folders,
			'subdir',
			'onchange="goUpDir()" ',
			'value',
			'text',
			$this->view->subdir
		);

		$this->view->config  = $this->config;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('files')
			->display();
	}

	/**
	 * Lists all files and folders for a given directory
	 *
	 * @return  void
	 */
	public function listTask()
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$this->view->course_id = Request::getInt('course', 0);
		$this->view->listdir   = Request::getVar('listdir', 0);

		// Incoming sub-directory
		$this->view->subdir = Request::getVar('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($this->view->listdir, $this->view->subdir);

		$folders = array();
		$docs    = array();

		if (is_dir($path))
		{
			// Loop through all files and separate them into arrays of images, folders, and other
			$dirIterator = new \DirectoryIterator($path);
			foreach ($dirIterator as $file)
			{
				if ($file->isDot())
				{
					continue;
				}

				if ($file->isDir())
				{
					$name = $file->getFilename();
					$folders[$path . DS . $name] = $name;
					continue;
				}

				if ($file->isFile())
				{
					$name = $file->getFilename();
					if (('cvs' == strtolower($name))
					 || ('.svn' == strtolower($name)))
					{
						continue;
					}

					$docs[$path . DS . $name] = $name;
				}
			}

			ksort($folders);
			ksort($docs);
		}

		$this->view->docs    = $docs;
		$this->view->folders = $folders;
		$this->view->config  = $this->config;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Scans directory and builds multi-dimensional array of all files and sub-directories
	 *
	 * @param   string  $base  Directory to scan
	 * @return  array
	 */
	private function _recursiveListDir($base)
	{
		static $filelist = array();
		static $dirlist  = array();

		if (is_dir($base))
		{
			$dh = opendir($base);
			while (false !== ($dir = readdir($dh)))
			{
				if (is_dir($base . DS . $dir) && $dir !== '.' && $dir !== '..' && strtolower($dir) !== 'cvs')
				{
					$subbase    = $base . DS . $dir;
					$dirlist[]  = $subbase;
					$subdirlist = $this->_recursiveListDir($subbase);
				}
			}
			closedir($dh);
		}
		return $dirlist;
	}

	/**
	 * Reorder a plugin
	 *
	 * @return  void
	 */
	public function orderTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		$id = Request::getVar('id', array(0), 'post', 'array');
		\Hubzero\Utility\Arr::toInteger($id, array(0));

		$uid = $id[0];
		$inc = ($this->_task == 'orderup' ? -1 : 1);

		$row = new Tables\Page($this->database);
		$row->load($uid);
		$row->move($inc, 'course_id=' . $this->database->Quote($row->course_id) . ' AND offering_id=' . $this->database->Quote($row->offering_id));
		$row->reorder('course_id=' . $this->database->Quote($row->course_id) . ' AND offering_id=' . $this->database->Quote($row->offering_id));

		$this->cancelTask();
	}

	/**
	 * Set the state of a course
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or exit('Invalid Token');

		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		$num = 0;
		if (!empty($ids))
		{
			//foreach course id passed in
			foreach ($ids as $id)
			{
				// Load the course page
				$model = new \Components\Courses\Models\Page($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				//set the course to be published and update
				$model->set('active', $state);
				if (!$model->store())
				{
					$this->setError(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_SET_STATE', $id));
					continue;
				}

				// Log the course approval
				$model->log($model->get('id'), 'page', ($state ? 'published' : 'unpublished'));

				$num++;
			}
		}

		if ($this->getErrors())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				implode('<br />', $this->getErrors()),
				'error'
			);
		}
		else
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				($state ? Lang::txt('COM_COURSES_ITEMS_PUBLISHED', $num) : Lang::txt('COM_COURSES_ITEMS_UNPUBLISHED', $num))
			);
		}
	}
}
