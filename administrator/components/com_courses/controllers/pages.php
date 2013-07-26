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

// Course model pulls in other classes we need
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for managing course pages
 */
class CoursesControllerPages extends Hubzero_Controller
{
	/**
	 * Manage course pages
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['search']  = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		)));
		// Filters for returning results
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		$this->view->filters['offering']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.offering',
			'offering',
			0
		);

		$this->view->offering = CoursesModelOffering::getInstance($this->view->filters['offering']);
		if ($this->view->offering->exists())
		{
			$this->view->filters['course']    = $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.course',
				'course',
				$this->view->offering->get('course_id')
			);
		}
		else
		{
			$using = 'course';
			$this->view->filters['course']    = $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.course',
				'course',
				0
			);
		}

		$this->view->course = CoursesModelCourse::getInstance($this->view->filters['course']);
		/*if (!$this->view->course->exists())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=courses'
			);
			return;
		}*/

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

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

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
	 * Create a course page
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a course page
	 *
	 * @return void
	 */
	public function editTask($model = null)
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
			$ids = JRequest::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($ids))
			{
				$id = (!empty($ids)) ? $ids[0] : 0;
			}
			else
			{
				$id = 0;
			}

			$this->view->row = new CoursesTablePage($this->database);
			$this->view->row->load($id);
		}

		if (!$this->view->row->get('course_id'))
		{
			$this->view->row->set('course_id', JRequest::getInt('course', 0));
		}
		if (!$this->view->row->get('offering_id'))
		{
			$this->view->row->set('offering_id', JRequest::getInt('offering', 0));
		}
		if (!$this->view->row->id)
		{
			$this->view->row->active = 1;
		}

		$this->view->course   = CoursesModelCourse::getInstance($this->view->row->get('course_id'));
		$this->view->offering = CoursesModelOffering::getInstance($this->view->row->get('offering_id'));

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
	 * Save a course page and fall through to edit view
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save a course page
	 *
	 * @return void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// load the request vars
		$fields = JRequest::getVar('fields', array(), 'post');

		// instatiate course page object for saving
		$row = new CoursesTablePage($this->database);

		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($redirect)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . $fields['course_id'] . '&offering=' . $fields['offering_id'],
				JText::_('Page successfully saved')
			);
			return;
		}

		$this->editTask($row);
	}

	/**
	 * Remove one or more types
	 * 
	 * @return     void Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = JRequest::getVar('id', array());
		$rtrn = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . JRequest::getInt('course', 0) . '&offering=' . JRequest::getInt('offering', 0);

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			$this->setRedirect(
				$rtrn,
				JText::_('No page selected'),
				'error'
			);
			return;
		}

		$tbl = new CoursesTablePage($this->database);

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
			$this->setRedirect(
				$rtrn,
				JText::sprintf('%s Page(s) successfully removed', $i)
			);
			return;
		}

		$this->setRedirect(
			$rtrn,
			$this->getError(),
			'error'
		);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&course=' . JRequest::getInt('course', 0) . '&offering=' . JRequest::getInt('offering', 0)
		);
	}

	/**
	 * Build file path
	 * 
	 * @return     void
	 */
	private function _buildUploadPath($listdir=0)
	{
		$course_id = JRequest::getInt('course', 0);

		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/courses'), DS);
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
	 * @return     string
	 */
	public function ajaxUploadTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			echo json_encode(array('error' => JText::_('Must be logged in.')));
			return;
		}

		// Ensure we have an ID to work with
		$listdir = JRequest::getVar('listdir', 0);
		/*if (!$listdir) 
		{
			echo json_encode(array('error' => JText::_('COM_COURSES_NO_ID')));
			return;
		}*/

		// Incoming sub-directory
		$subdir = JRequest::getVar('subdir', '');

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
			echo json_encode(array('error' => JText::_('File not found')));
			return;
		}


		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				echo json_encode(array('error' => JText::_('Error uploading. Unable to create path.')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array('error' => JText::_('Server error. Upload directory isn\'t writable.')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0) 
		{
			echo json_encode(array('error' => JText::_('File is empty')));
			return;
		}
		if ($size > $sizeLimit) 
		{
			ximport('Hubzero_View_Helper_Html');
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Hubzero_View_Helper_Html::formatSize($sizeLimit));
			echo json_encode(array('error' => JText::sprintf('File is too large. Max file upload size is %s', $max)));
			return;
		}

		// don't overwrite previous files that were uploaded
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$filename = urldecode($filename);
		$filename = JFile::makeSafe($filename);
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
			'directory' => str_replace(JPATH_ROOT, '', $path),
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
		if (JRequest::getVar('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar('listdir', 0, 'post');
		if (!$listdir)
		{
			$this->setError(JText::_('COURSES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = JRequest::getVar('subdir', '', 'post');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Are we creating a new folder?
		$foldername = JRequest::getVar('foldername', '', 'post');
		if ($foldername != '')
		{
			// Make sure the name is valid
			if (preg_match("#[^0-9a-zA-Z_]#i", $foldername))
			{
				$this->setError(JText::_('Directory name must only contain alphanumeric characters and no spaces please.'));
			}
			else
			{
				if (!is_dir($path . DS . $foldername))
				{
					jimport('joomla.filesystem.folder');
					if (!JFolder::create($path . DS . $foldername, 0777))
					{
						$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
					}
				}
				else
				{
					$this->setError(JText::_('Directory already exists'));
				}
			}
			// Directory created
		} 
		else 
		{
			// Make sure the upload path exist
			if (!is_dir($path))
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::create($path, 0777))
				{
					$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
					$this->displayTask();
					return;
				}
			}

			// Incoming file
			$file = JRequest::getVar('upload', '', 'files', 'array');
			if (!$file['name'])
			{
				$this->setError(JText::_('COURSES_NO_FILE'));
				$this->displayTask();
				return;
			}

			// Make the filename safe
			jimport('joomla.filesystem.file');
			$file['name'] = JFile::makeSafe($file['name']);
			// Ensure file names fit.
			$ext = JFile::getExt($file['name']);
			$file['name'] = str_replace(' ', '_', $file['name']);
			if (strlen($file['name']) > 230)
			{
				$file['name'] = substr($file['name'], 0, 230);
				$file['name'] .= '.' . $ext;
			}

			// Perform the upload
			if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
			{
				$this->setError(JText::_('ERROR_UPLOADING'));
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
		JRequest::checkToken('get') or jexit('Invalid Token');

		$this->view->setLayout('display');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar('listdir', 0);
		if (!$listdir)
		{
			$this->setError(JText::_('COURSES_NO_LISTDIR'));
			$this->filesTask();
			return;
		}

		// Incoming sub-directory
		$subdir = JRequest::getVar('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Incoming directory to delete
		$folder = trim(JRequest::getVar('delFolder', ''), DS);
		if (!$folder)
		{
			$this->setError(JText::_('COURSES_NO_DIRECTORY'));
			$this->filesTask();
			return;
		}

		//$folder = ResourcesUtilities::normalizePath($folder);

		// Check if the folder even exists
		if (!is_dir($path . DS . $folder) or !$folder) {
			$this->setError(JText::_('DIRECTORY_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.folder');
			if (!JFolder::delete($path . DS . $folder))
			{
				$this->setError(JText::_('UNABLE_TO_DELETE_DIRECTORY'));
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
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar('listdir', 0);
		if (!$listdir)
		{
			$this->setError(JText::_('COURSES_NO_LISTDIR'));
			$this->filesTask();
			return;
		}

		// Incoming sub-directory
		$subdir = JRequest::getVar('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Incoming file to delete
		$file = JRequest::getVar('delFile', '');
		if (!$file)
		{
			$this->setError(JText::_('COURSES_NO_FILE'));
			$this->filesTask();
			return;
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(JText::_('FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file))
			{
				$this->setError(JText::_('UNABLE_TO_DELETE_FILE'));
			}
		}

		// Push through to the media view
		$this->filesTask();
	}

	/**
	 * Display an upload form and file listing
	 * 
	 * @return     void
	 */
	public function filesTask()
	{
		$this->view->setLayout('files');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$this->view->course_id = JRequest::getInt('course', 0);
		$this->view->listdir   = JRequest::getVar('listdir', 0);
		/*if (!$this->view->listdir)
		{
			echo '<p class="error">' . JText::_('No list directory provided.') . '</p>';
			return;
		}*/

		// Incoming sub-directory
		$this->view->subdir = JRequest::getVar('subdir', '');

		// Build the path
		//$this->view->path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $this->view->course_id . DS . $this->view->listdir;
		$this->view->path = $this->_buildUploadPath($this->view->listdir, $this->view->subdir);

		// Get list of directories
		$dirs = $this->_recursiveListDir($this->view->path);

		$folders   = array();
		$folders[] = JHTML::_('select.option', '/');
		if ($dirs)
		{
			foreach ($dirs as $dir)
			{
				$folders[] = JHTML::_('select.option', substr($dir, strlen($this->view->path)));
			}
		}
		sort($folders);

		// Create folder <select> list
		$this->view->dirPath = JHTML::_(
			'select.genericlist',
			$folders,
			'subdir',
			'onchange="goUpDir()" ',
			'value',
			'text',
			$this->view->subdir
		);

		$this->view->config  = $this->config;

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
	 * Lists all files and folders for a given directory
	 * 
	 * @return     void
	 */
	public function listTask()
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$this->view->course_id = JRequest::getInt('course', 0);
		$this->view->listdir   = JRequest::getVar('listdir', 0);
		/*if (!$this->view->listdir)
		{
			echo '<p class="error">' . JText::_('No list directory provided.') . '</p>';
			return;
		}*/

		// Incoming sub-directory
		$this->view->subdir = JRequest::getVar('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($this->view->listdir, $this->view->subdir);

		$folders = array();
		$docs    = array();

		if (is_dir($path))
		{
			// Loop through all files and separate them into arrays of images, folders, and other
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

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Scans directory and builds multi-dimensional array of all files and sub-directories
	 * 
	 * @param      string $base Directory to scan
	 * @return     array
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
	 * Reorder a record up
	 * 
	 * @return     void
	 */
	public function orderupTask()
	{
		$this->orderTask();
	}

	/**
	 * Reorder a record up
	 * 
	 * @return     void
	 */
	public function orderdownTask()
	{
		$this->orderTask();
	}

	/**
	 * Reorder a plugin
	 * 
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function orderTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$id = JRequest::getVar('id', array(0), 'post', 'array');
		JArrayHelper::toInteger($id, array(0));

		$uid = $id[0];
		$inc = ($this->_task == 'orderup' ? -1 : 1);

		$row = new CoursesTablePage($this->database);
		$row->load($uid);
		$row->move($inc, 'course_id=' . $this->database->Quote($row->course_id) . ' AND offering_id=' . $this->database->Quote($row->offering_id));
		$row->reorder('course_id=' . $this->database->Quote($row->course_id) . ' AND offering_id=' . $this->database->Quote($row->offering_id));

		$this->cancelTask();
	}
}
