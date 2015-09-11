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

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Filesystem;
use Component;
use Request;
use Route;
use Lang;
use User;
use App;

require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'assoc.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'utilities.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

/**
 * Methods for listing and managing files and folders
 */
class Media extends SiteController
{
	/**
	 * Upload a file or create a new folder
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Request::checkToken();

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
		Request::checkToken('get');

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
			$dirIterator = new \DirectoryIterator($this->view->path);
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

