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

require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'assoc.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'utilities.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

/**
 * Methods for listing and managing files and folders
 */
class ToolsControllerMedia extends \Hubzero\Component\SiteController
{
	/**
	 * Upload a file or create a new folder
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$resource = Request::getInt('resource', 0, 'post');
		if (!$resource)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getVar('dirPath', '', 'post');

		$row = new \Components\Resources\Tables\Resource($this->database);
		$row->load($resource);

		$path = \Components\Resources\Helpers\Html::dateToPath($row->created) . DS . \Components\Resources\Helpers\Html::niceidformat($resource);
		$path = \Components\Resources\Helpers\Utilities::buildUploadPath($path, $subdir) . DS . 'media';

		// Make sure the upload path exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask();
				return;
			}
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_FILE'));
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
			$this->setError(Lang::txt('COM_TOOLS_ERROR_UPLOADING'));
		}

		$fpath = $path . DS . $file['name'];

		if (!Filesystem::isSafe($fpath))
		{
			Filesystem::delete($fpath);

			$this->setError(Lang::txt('COM_TOOLS_ERROR_FAILED_VIRUS_SCAN'));
			$this->displayTask();
			return;
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Deletes a file
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or exit('Invalid Token');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$resource = Request::getInt('resource', 0);
		if (!$resource)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		//$subdir = Request::getVar('dirPath', '', 'post');
		$row = new \Components\Resources\Tables\Resource($this->database);
		$row->load($resource);

		$path = \Components\Resources\Helpers\Html::dateToPath($row->created) . DS . \Components\Resources\Helpers\Html::niceidformat($resource);

		// Make sure the listdir follows YYYY/MM/#
		$parts = explode('/', $path);
		if (count($parts) < 3)
		{
			$this->setError(Lang::txt('COM_TOOLS_DIRECTORY_NOT_FOUND'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '');

		// Build the path
		$path = \Components\Resources\Helpers\Utilities::buildUploadPath($path, $subdir) . DS . 'media';

		// Incoming file to delete
		$file = Request::getVar('file', '');
		if (!$file)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_FILE'));
			$this->displayTask();
			return;
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_TOOLS_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_DELETE_FILE'));
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
		$this->view->resource = Request::getInt('resource', 0);
		if (!$this->view->resource)
		{
			echo '<p class="error">' . Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID') . '</p>';
			return;
		}

		// Incoming sub-directory
		$this->view->subdir = Request::getVar('subdir', '');

		// Build the path
		//$this->view->path = \Components\Resources\Helpers\Utilities::buildUploadPath($this->view->listdir, $this->view->subdir);
		$row = new \Components\Resources\Tables\Resource($this->database);
		$row->load($this->view->resource);

		$path = \Components\Resources\Helpers\Html::dateToPath($row->created) . DS . \Components\Resources\Helpers\Html::niceidformat($this->view->resource);
		$this->view->path =\Components\Resources\Helpers\Utilities::buildUploadPath($path, $this->view->subdir) . DS . 'media';

		// Get list of directories
		/*$dirs = $this->_recursiveListDir($this->view->path);

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
			'dirPath',
			'onchange="goUpDir()" ',
			'value',
			'text',
			$this->view->subdir
		);*/
		$folders = array();
		$docs    = array();

		if (is_dir($this->view->path))
		{
			// Loop through all files and separate them into arrays of images, folders, and other
			$dirIterator = new DirectoryIterator($this->view->path);
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

		$this->view->row = $row;
		$this->view->docs = $docs;
		$this->view->folders = $folders;

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
		$this->view->resource = Request::getInt('resource', 0);
		if (!$this->view->resource)
		{
			echo '<p class="error">' . Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID') . '</p>';
			return;
		}

		/*$this->view->version = Request::getInt('version', 0);
		if (!$this->view->version)
		{
			echo '<p class="error">' . Lang::txt('No tool version ID provided.') . '</p>';
			return;
		}*/

		// Incoming sub-directory
		$this->view->subdir = Request::getVar('subdir', '');

		// Build the path
		$row = new \Components\Resources\Tables\Resource($this->database);
		$row->load($this->view->resource);

		$path = \Components\Resources\Helpers\Html::dateToPath($row->created) . DS . \Components\Resources\Helpers\Html::niceidformat($this->view->resource);
		$path = \Components\Resources\Helpers\Utilities::buildUploadPath($path, $this->view->subdir) . DS . 'media';

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

		$this->view->docs = $docs;
		$this->view->folders = $folders;
		$this->view->config = $this->config;

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

