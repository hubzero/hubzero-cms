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

use Hubzero\Component\AdminController;
use Filesystem;
use Request;
use Html;
use Lang;

/**
 * Methods for listing and managing files and folders
 */
class Media extends AdminController
{
	/**
	 * Build file path
	 *
	 * @return  void
	 */
	private function _buildUploadPath($listdir, $subdir='')
	{
		$course_id = Request::getInt('course', 0);

		$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $course_id . DS . $listdir;
		if ($subdir)
		{
			$path .= DS . trim($subdir, DS);
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
		Request::checkToken(['post', 'get']);

		// Ensure we have an ID to work with
		$listdir = Request::getInt('listdir', 0);
		if (!$listdir)
		{
			echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_NO_ID')));
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getString('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Get media config
		$mediaConfig = \Component::params('com_media');

		// Size limit is in MB, so we need to turn it into just B
		$sizeLimit = $mediaConfig->get('upload_maxsize', 10);
		$sizeLimit = $sizeLimit * 1024 * 1024;

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
			echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_FILE_NOT_FOUND')));
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

		$ext = Filesystem::extension($file['name']);

		// Check that the file type is allowed
		$allowed = array_values(array_filter(explode(',', $mediaConfig->get('upload_extensions'))));

		if (!empty($allowed) && !in_array(strtolower($ext), $allowed))
		{
			echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_UPLOADING_INVALID_FILE', implode(', ', $allowed))));
			return;
		}

		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			//move from temp location to target location which is user folder
			$target = fopen($file, "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		if (!Filesystem::isSafe($file))
		{
			Filesystem::delete($file);

			echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_FILE_UNSAFE')));
			return;
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
		if (Request::getInt('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = Request::getInt('listdir', 0, 'post');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COURSES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getString('subdir', '', 'post');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Are we creating a new folder?
		$foldername = Request::getString('foldername', '', 'post');
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
						$this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
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
			$file = Request::getArray('upload', array(), 'files');
			if (empty($file) || !$file['name'])
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_NO_FILE'));
				$this->displayTask();
				return;
			}

			// Get media config
			$mediaConfig = \Component::params('com_media');

			// Size limit is in MB, so we need to turn it into just B
			$sizeLimit = $mediaConfig->get('upload_maxsize', 10);
			$sizeLimit = $sizeLimit * 1024 * 1024;

			if ($file['size'] > $sizeLimit)
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_UPLOADING_FILE_TOO_BIG', \Hubzero\Utility\Number::formatBytes($sizeLimit)));
				return $this->displayTask();
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

			$ext = Filesystem::extension($file['name']);

			// Check that the file type is allowed
			$allowed = array_values(array_filter(explode(',', $mediaConfig->get('upload_extensions'))));

			if (!empty($allowed) && !in_array(strtolower($ext), $allowed))
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_UPLOADING_INVALID_FILE', implode(', ', $allowed)));
				return $this->displayTask();
			}

			// Perform the upload
			if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_UPLOADING'));
			}
			else
			{
				if (!Filesystem::isSafe($path . DS . $file['name']))
				{
					Filesystem::delete($path . DS . $file['name']);

					$this->setError(Lang::txt('COM_COURSES_ERROR_FILE_UNSAFE'));
				}

				// File was uploaded

				// Was the file an archive that needs unzipping?
				$batch = Request::getInt('batch', 0, 'post');
				if ($batch)
				{
					/*require_once(PATH_CORE . DS . 'includes' . DS . 'pcl' . DS . 'pclzip.lib.php');

					if (!extension_loaded('zlib'))
					{
						$this->setError(Lang::txt('ZLIB_PACKAGE_REQUIRED'));
					}
					else
					{
						$zip = new PclZip($path . DS . $file['name']);

						// unzip the file
						if (!($do = $zip->extract($path)))
						{
							$this->setError(Lang::txt('UNABLE_TO_EXTRACT_PACKAGE'));
						}
						else
						{
							@unlink($path . DS . $file['name']);
						}
					}*/
					if (!$this->getError() && $ext == 'zip')
					{
						set_time_limit(60);
						$escaped_file = escapeshellarg($path . DS . $file['name']);
						// @FIXME: check for symlinks and other potential security concerns
						if ($result = shell_exec("unzip -o {$escaped_file} -d " . escapeshellarg($path . DS)))
						{
							// Remove original archive
							Filesystem::delete($path . DS . $file['name']);

							// Remove MACOSX dirs if there
							Filesystem::deleteDirectory($path . DS . '__MACOSX');
						}
					}
				} // if ($batch) {
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a folder and contents
	 *
	 * @return     void
	 */
	public function deletefolderTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		$this->view->setLayout('display');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = Request::getInt('listdir', 0);
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_NO_ID'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getString('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Incoming directory to delete
		$folder = trim(Request::getString('delFolder', ''), DS);
		if (!$folder)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_MISSING_DIRECTORY'));
			$this->displayTask();
			return;
		}

		// Check if the folder even exists
		if (!is_dir($path . DS . $folder) or !$folder)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_MISSING_DIRECTORY'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::deleteDirectory($path . DS . $folder))
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_DELETE_DIRECTORY'));
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Deletes a file
	 *
	 * @return     void
	 */
	public function deletefileTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = Request::getInt('listdir', 0);
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_NO_ID'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getString('subdir', '');

		// Build the path
		$path = $this->_buildUploadPath($listdir, $subdir);

		// Incoming file to delete
		$file = Request::getString('delFile', '');
		if (!$file)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_NO_FILE'));
			$this->displayTask();
			return;
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_NO_FILE_FOUND'));
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
		$this->displayTask();
	}

	/**
	 * Display an upload form and file listing
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->setLayout('display');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$this->view->course_id = Request::getInt('course', 0);
		$this->view->listdir   = Request::getInt('listdir', 0);
		if (!$this->view->listdir)
		{
			echo '<p class="error">' . Lang::txt('COM_COURSES_ERROR_MISSING_DIRECTORY') . '</p>';
			return;
		}

		// Incoming sub-directory
		$this->view->subdir = Request::getString('subdir', '');

		// Build the path
		//$this->view->path = PATH_CORE . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $this->view->course_id . DS . $this->view->listdir;
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
		$this->view->course_id = Request::getInt('course', 0);
		$this->view->listdir   = Request::getInt('listdir', 0);
		if (!$this->view->listdir)
		{
			echo '<p class="error">' . Lang::txt('COM_COURSES_ERROR_MISSING_DIRECTORY') . '</p>';
			return;
		}

		// Incoming sub-directory
		$this->view->subdir = Request::getString('subdir', '');

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
}
