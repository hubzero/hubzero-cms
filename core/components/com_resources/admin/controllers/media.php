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

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Helpers\Utilities;
use Hubzero\Component\AdminController;
use Filesystem;
use Request;
use Lang;

/**
 * Methods for listing and managing files and folders
 */
class Media extends AdminController
{
	/**
	 * Upload a file or create a new folder
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = Request::getVar('listdir', '', 'post');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_NO_LISTDIR'));
			return $this->displayTask();
		}

		// Incoming sub-directory
		$subdir = Request::getVar('dirPath', '', 'post');

		// Build the path
		$path = Utilities::buildUploadPath($listdir, $subdir);

		// Are we creating a new folder?
		$foldername = Request::getVar('foldername', '', 'post');
		if ($foldername != '')
		{
			// Make sure the name is valid
			if (preg_match("/[^0-9a-zA-Z_]/i", $foldername))
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_DIR_INVALID_CHARACTERS'));
			}
			else
			{
				if (!is_dir($path . DS . $foldername))
				{
					if (!Filesystem::makeDirectory($path . DS . $foldername))
					{
						$this->setError(Lang::txt('COM_RESOURCES_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
					}
				}
				else
				{
					$this->setError(Lang::txt('COM_RESOURCES_ERROR_DIR_EXISTS'));
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
					$this->setError(Lang::txt('COM_RESOURCES_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
					return $this->displayTask();
				}
			}

			// Incoming file
			$file = Request::getVar('upload', '', 'files', 'array');
			if (!$file['name'])
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_NO_FILE'));
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

			// Perform the upload
			if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_UPLOADING'));
			}
			else
			{
				// File was uploaded

				// Was the file an archive that needs unzipping?
				$batch = Request::getInt('batch', 0, 'post');
				if ($batch)
				{
					//build path
					$path = rtrim($path, DS) . DS;
					$escaped_file = escapeshellarg($path . $file['name']);

					//determine command to uncompress
					switch ($ext)
					{
						case 'gz':     $cmd = "tar zxvf {$escaped_file} -C {$path}";    break;
						case 'tar':    $cmd = "tar xvf {$escaped_file} -C {$path}";     break;
						case 'zip':
						default:       $cmd = "unzip -o {$escaped_file} -d {$path}";
					}

					//unzip file
					if ($result = shell_exec($cmd))
					{
						// Remove original archive
						Filesystem::delete( $path . $file['name'] );

						// Remove MACOSX dirs if there
						if (Filesystem::exists($path . '__MACOSX'))
						{
							Filesystem::deleteDirectory($path . '__MACOSX');
						}

						//remove ._ files
						$dotFiles = Filesystem::files($path, '._[^\s]*', true, true);
						foreach ($dotFiles as $dotFile)
						{
							Filesystem::delete($dotFile);
						}
					}
				}
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a folder and contents
	 *
	 * @return  void
	 */
	public function deletefolderTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = Request::getVar('listdir', '');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_NO_LISTDIR'));
			return $this->displayTask();
		}

		// Make sure the listdir follows YYYY/MM/#
		$parts = explode('/', $listdir);
		if (count($parts) < 3)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DIRECTORY_NOT_FOUND'));
			return $this->displayTask();
		}

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '');

		// Build the path
		$path = Utilities::buildUploadPath($listdir, $subdir);

		// Incoming directory to delete
		$folder = Request::getVar('delFolder', '');
		if (!$folder)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_NO_DIRECTORY'));
			return $this->displayTask();
		}

		$folder = Utilities::normalizePath($folder);

		// Check if the folder even exists
		if (!is_dir($path . $folder) or !$folder)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DIRECTORY_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::deleteDirectory($path . $folder))
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_UNABLE_TO_DELETE_DIRECTORY'));
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Deletes a file
	 *
	 * @return  void
	 */
	public function deletefileTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = Request::getVar('listdir', '');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_NO_LISTDIR'));
			return $this->displayTask();
		}

		// Make sure the listdir follows YYYY/MM/#
		$parts = explode('/', $listdir);
		if (count($parts) < 3)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DIRECTORY_NOT_FOUND'));
			return $this->displayTask();
		}

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '');

		// Build the path
		$path = Utilities::buildUploadPath($listdir, $subdir);

		// Incoming file to delete
		$file = Request::getVar('delFile', '');
		if (!$file)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_NO_FILE'));
			return $this->displayTask();
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_UNABLE_TO_DELETE_FILE'));
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Display an upload form and file listing
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = Request::getVar('listdir', '');
		if (!$listdir)
		{
			echo '<p class="error">' . Lang::txt('COM_RESOURCES_ERROR_NO_LISTDIR') . '</p>';
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '');
		if (!$subdir)
		{
			$subdir = Request::getVar('dirPath', '', 'post');
		}

		// Build the path
		$path = Utilities::buildUploadPath($listdir, $subdir);

		// Get list of directories
		$dirs = $this->_recursiveListDir($path);

		$folders   = array();
		$folders[] = \Html::select('option', '/');
		if ($dirs)
		{
			foreach ($dirs as $dir)
			{
				$folders[] = \Html::select('option', substr($dir, strlen($path)));
			}
		}
		sort($folders);

		// Create folder <select> list
		$dirPath = \Html::select(
			'genericlist',
			$folders,
			'dirPath',
			'onchange="goUpDir()" ',
			'value',
			'text',
			$subdir
		);

		// Output the HTML
		$this->view
			->set('listdir', $listdir)
			->set('subdir', $subdir)
			->set('path', $path)
			->set('dirPath', $dirPath)
			->set('config', $this->config)
			->setErrors($this->getErrors())
			->setLayout('display')
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
		$listdir = Request::getVar('listdir', '');
		if (!$listdir)
		{
			echo '<p class="error">' . Lang::txt('COM_RESOURCES_ERROR_NO_LISTDIR') . '</p>';
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '');

		// Build the path
		$path = Utilities::buildUploadPath($listdir, $subdir);

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

				$name = $file->getFilename();

				if ($file->isDir())
				{
					$folders[$path . DS . $name] = $name;
					continue;
				}

				if ($file->isFile())
				{
					if (in_array(strtolower($name), array('cvs', '.svn', '.git', '.ds_store')))
					{
						continue;
					}

					$docs[$path . DS . $name] = $name;
				}
			}

			ksort($folders);
			ksort($docs);
		}

		$this->view
			->set('listdir', $listdir)
			->set('subdir', $subdir)
			->set('docs', $docs)
			->set('folders', $folders)
			->set('config', $this->config)
			->setErrors($this->getErrors())
			->setLayout('list')
			->display();
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
}
