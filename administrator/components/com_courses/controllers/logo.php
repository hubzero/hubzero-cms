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

/**
 * Manage logo for a course
 */
class CoursesControllerLogo extends \Hubzero\Component\AdminController
{
	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return     string
	 */
	public function ajaxUploadTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Ensure we have an ID to work with
		$id = JRequest::getInt('id', 0);
		if (!$id)
		{
			echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_NO_ID')));
			return;
		}

		// Build the path
		$type = strtolower(JRequest::getWord('type', ''));
		$path = $this->_path($type, $id);

		if (!$path)
		{
			echo json_encode(array('error' => $this->getError()));
			return;
		}

		// allowed extensions for uplaod
		$allowedExtensions = array('png','jpeg','jpg','gif');

		// max upload size
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
			echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_NO_FILE_FOUND')));
			return;
		}

		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_UPLOAD_DIRECTORY_IS_NOT_WRITABLE')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_EMPTY_FILE')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => JText::sprintf('COM_COURSES_ERROR_FILE_TOO_LARGE', $max)));
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
		if (!in_array(strtolower($ext), $allowedExtensions))
		{
			echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_UNKNOWN_FILE_TYPE')));
			return;
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

		// Do we have an old file we're replacing?
		if (($curfile = JRequest::getVar('currentfile', '')))
		{
			// Remove old image
			if (file_exists($path . DS . $curfile))
			{
				if (!JFile::delete($path . DS . $curfile))
				{
					echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_UNABLE_TO_DELETE_FILE')));
					return;
				}
			}
		}

		switch ($type)
		{
			case 'section':
				// Instantiate a model, change some info and save
				$model = CoursesModelSection::getInstance($id);
				$model->params()->set('logo', $filename . '.' . $ext);
				$model->set('params', $model->params()->toString());
			break;

			case 'offering':
				// Instantiate a model, change some info and save
				$model = CoursesModelOffering::getInstance($id);
				$model->params()->set('logo', $filename . '.' . $ext);
				$model->set('params', $model->params()->toString());
			break;

			case 'course':
				// Instantiate a model, change some info and save
				$model = CoursesModelCourse::getInstance($id);
				$model->set('logo', $filename . '.' . $ext);
			break;

			default:
				echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_INVALID_TYPE')));
				return;
			break;
		}
		if (!$model->store())
		{
			echo json_encode(array('error' => $model->getError()));
			return;
		}

		$this_size = filesize($file);
		list($width, $height, $type, $attr) = getimagesize($file);

		//echo result
		echo json_encode(array(
			'success'   => true,
			'file'      => $filename . '.' . $ext,
			'directory' => str_replace(JPATH_ROOT, '', $path),
			'id'        => $id,
			'size'      => \Hubzero\Utility\Number::formatBytes($this_size),
			'width'     => $width,
			'height'    => $height
		));
	}

	/**
	 * Upload a file
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

		// Incoming
		$id = JRequest::getInt('id', 0);
		if (!$id)
		{
			$this->setError(JText::_('COM_COURSES_ERROR_NO_ID'));
			$this->displayTask('', $id);
			return;
		}

		// Build the path
		$type = strtolower(JRequest::getWord('type', ''));
		$path = $this->_path($type, $id);

		if (!$path)
		{
			$this->displayTask('', $id);
			return;
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('COM_COURSES_NO_FILE'));
			$this->displayTask('', $id);
			return;
		}
		$curfile = JRequest::getVar('curfile', '');

		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				$this->setError(JText::_('COM_COURSES_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask('', $id);
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(JText::_('COM_COURSES_ERROR_UPLOADING'));
			$file = $curfile;
		}
		else
		{
			// Do we have an old file we're replacing?
			if (($curfile = JRequest::getVar('currentfile', '')))
			{
				// Remove old image
				if (file_exists($path . DS . $curfile))
				{
					if (!JFile::delete($path . DS . $curfile))
					{
						$this->setError(JText::_('COM_COURSES_ERROR_UNABLE_TO_DELETE_FILE'));
						$this->displayTask($file['name'], $id);
						return;
					}
				}
			}

			switch ($type)
			{
				case 'section':
					$model = CoursesModelSection::getInstance($id);
					$model->params()->set('logo', $file['name']);
					$model->set('params', $model->params()->toString());
				break;

				case 'offering':
					$model = CoursesModelOffering::getInstance($id);
					$model->params()->set('logo', $file['name']);
					$model->set('params', $model->params()->toString());
				break;

				case 'course':
					$model = CoursesModelCourse::getInstance($id);
					$model->set('logo', $file['name']);
				break;

				default:
					$this->setError(JText::_('COM_COURSES_ERROR_INVALID_TYPE'));
					$this->displayTask($file['name'], $id);
					return;
				break;
			}
			if (!$model->store())
			{
				$this->setError($model->getError());
			}

			$file = $file['name'];
		}

		// Push through to the image view
		$this->displayTask($file, $id);
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return     string
	 */
	public function ajaxRemoveTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Ensure we have an ID to work with
		$id = JRequest::getInt('id', 0);
		if (!$id)
		{
			echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_NO_ID')));
			return;
		}

		// Build the path
		$type = strtolower(JRequest::getWord('type', ''));
		$path = $this->_path($type, $id);

		if (!$path)
		{
			echo json_encode(array('error' => $this->getError()));
			return;
		}

		// Instantiate a model, change some info and save
		switch ($type)
		{
			case 'section':
				$model = CoursesModelSection::getInstance($id);
				$file = $model->params('logo');
				$model->params()->set('logo', '');
				$model->set('params', $model->params()->toString());
			break;

			case 'offering':
				$model = CoursesModelOffering::getInstance($id);
				$file = $model->params('logo');
				$model->params()->set('logo', '');
				$model->set('params', $model->params()->toString());
			break;

			case 'course':
				$model = CoursesModelCourse::getInstance($id);
				$file = $model->get('logo');
				$model->set('logo', '');
			break;

			default:
				echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_INVALID_TYPE')));
				return;
			break;
		}

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
				echo json_encode(array('error' => JText::_('COM_COURSES_ERROR_UNABLE_TO_DELETE_FILE')));
				return;
			}
		}

		if (!$model->store())
		{
			echo json_encode(array('error' => $model->getError()));
			return;
		}

		//echo result
		echo json_encode(array(
			'success'   => true,
			'file'      => '',
			'directory' => str_replace(JPATH_ROOT, '', $path),
			'id'        => $id,
			'size'      => 0,
			'width'     => 0,
			'height'    => 0
		));
	}

	/**
	 * Delete a file
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		if (JRequest::getVar('no_html', 0))
		{
			return $this->ajaxRemoveTask();
		}

		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming member ID
		$id = JRequest::getInt('id', 0);
		if (!$id)
		{
			$this->setError(JText::_('COM_COURSES_ERROR_NO_ID'));
			$this->displayTask('', $id);
			return;
		}

		// Incoming file
		$file = JRequest::getVar('file', '');
		if (!$file)
		{
			$this->setError(JText::_('COM_COURSES_ERROR_NO_FILE_FOUND'));
			$this->displayTask('', $id);
			return;
		}

		// Build the file path
		$type = strtolower(JRequest::getWord('type', ''));
		$path = $this->_path($type, $id);

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(JText::_('COM_COURSES_ERROR_NO_FILE_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file))
			{
				$this->setError(JText::_('COM_COURSES_ERROR_UNABLE_TO_DELETE_FILE'));
				$this->displayTask($file, $id);
				return;
			}

			switch ($type)
			{
				case 'section':
					$model = CoursesModelSection::getInstance($id);
					$model->params()->set('logo', '');
					$model->set('params', $model->params()->toString());
				break;

				case 'offering':
					$model = CoursesModelOffering::getInstance($id);
					$model->params()->set('logo', '');
					$model->set('params', $model->params()->toString());
				break;

				case 'course':
					$model = CoursesModelCourse::getInstance($id);
					$model->set('logo', '');
				break;

				default:
					$this->setError(JText::_('COM_COURSES_ERROR_INVALID_TYPE'));
					$this->displayTask($file, $id);
					return;
				break;
			}
			if (!$model->store())
			{
				$this->setError($model->getError());
			}

			$file = '';
		}

		$this->displayTask($file, $id);
	}

	/**
	 * Display a file and its info
	 *
	 * @param      integer $id ID
	 * @return     string
	 */
	protected function _path($type, $id)
	{
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS;

		switch ($type)
		{
			case 'section':
				$model = CoursesModelSection::getInstance($id);
				$offering = CoursesModelOffering::getInstance($model->get('offering_id'));
				$path .= $offering->get('course_id') . DS . 'sections' . DS . $id;
			break;

			case 'offering':
				$model = CoursesModelOffering::getInstance($id);
				$path .= $model->get('course_id') . DS . 'offerings' . DS . $id;
			break;

			case 'course':
				$model = CoursesModelCourse::getInstance($id);
				$path .= $id;
			break;

			default:
				$this->setError(JText::_('COM_COURSES_ERROR_INVALID_TYPE'));
				return '';
			break;
		}

		return $path;
	}

	/**
	 * Display a file and its info
	 *
	 * @param      string  $file File name
	 * @param      integer $id   User ID
	 * @return     void
	 */
	public function displayTask($file='', $id=0)
	{
		$this->view->setLayout('display');

		// Load the component config
		$this->view->config = $this->config;

		// Incoming
		if (!$id)
		{
			$id = JRequest::getInt('id', 0);
		}
		$this->view->id = $id;

		$this->view->type = strtolower(JRequest::getWord('type', ''));

		$this->view->file = $course->get('logo');

		// Build the file path
		$this->view->dir  = $id;
		$this->view->path = $this->_path($this->view->type, $id);

		switch ($this->view->type)
		{
			case 'section':
				$model = CoursesModelSection::getInstance($id);
				$this->view->file = $model->params('logo');
			break;

			case 'offering':
				$model = CoursesModelOffering::getInstance($id);
				$this->view->file = $model->params('logo');
			break;

			case 'course':
				$model = CoursesModelCourse::getInstance($id);
				$this->view->file = $model->get('logo');
			break;

			default:
				$this->setError(JText::_('COM_COURSES_ERROR_INVALID_TYPE'));
				return;
			break;
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
}

