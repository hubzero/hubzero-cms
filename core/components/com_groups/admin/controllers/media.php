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

namespace Components\Groups\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Filesystem\Util;
use Hubzero\User\Group;
use Filesystem;
use Request;
use Route;
use Lang;
use App;

/**
 * Manage files for a group
 */
class Media extends AdminController
{
	/**
	 * Override Execute Method
	 *
	 * @return  void
	 */
	public function execute()
	{
		$id = Request::getInt('gidNumber');

		// Load the group page
		$this->group = Group::getInstance($id);

		// Ensure we found the group info
		if (!$this->group || !$this->group->get('gidNumber'))
		{
			App::abort(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		//build path to the group folder
		$this->path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $this->group->get('gidNumber');

		//continue with parent execute method
		parent::execute();
	}

	/**
	 * Upload a file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$file = urldecode(Request::getVar('file', '', 'get', 'none', 2));

		if (!file_exists(PATH_ROOT . DS . $file))
		{
			App::abort(404, Lang::txt('COM_GROUPS_ERROR_FILE_NOT_FOUND') . ' ' . PATH_ROOT . DS . $file);
		}

		$extension = Filesystem::extension($file);

		// new content server
		$contentServer = new \Hubzero\Content\Server();
		$contentServer->filename(PATH_ROOT . DS . $file);
		$contentServer->disposition('attachment');
		$contentServer->acceptranges(false);

		// do we need to manually set mime type
		if ($extension == 'css')
		{
			$contentServer->setContentType('text/css');
		}

		if ($extension == 'php')
		{
			$contentServer->setContentType('text/plain');
		}

		// Serve up the file
		if (!$contentServer->serve())
		{
			App::abort(500, Lang::txt('COM_GROUPS_SERVER_ERROR'));
		}

		exit();
	}

	/**
	 * Upload a file
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$dir  = urldecode(Request::getVar('dir', ''));

		// Build upload path
		$path = $this->path . ($dir ? DS . trim($dir, DS) : '');
		$path = Util::normalizePath($path);

		$foldername = Request::getVar('foldername', '', 'post');

		if ($foldername)
		{
			// Make sure the name is valid
			if (preg_match("/[^0-9a-zA-Z_]/i", $foldername))
			{
				$this->setError(Lang::txt('COM_GROUPS_ERROR_DIR_INVALID_CHARACTERS'));
			}
			else
			{
				if (!is_dir($path . DS . $foldername))
				{
					if (!Filesystem::makeDirectory($path . DS . $foldername))
					{
						$this->setError(Lang::txt('COM_GROUPS_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
					}
				}
				else
				{
					$this->setError(Lang::txt('COM_GROUPS_ERROR_DIR_EXISTS'));
				}
			}
			// Directory created
		}
		else
		{
			$file = Request::getVar('upload', '', 'files', 'array');

			// max upload size
			$sizeLimit = $this->config->get('maxAllowed', '40000000');

			// make sure we have a file
			if (!$file['name'])
			{
				$this->setError(Lang::txt('COM_GROUPS_NO_FILE'));

				return $this->displayTask();
			}

			// make sure we have an upload path
			if (!is_dir($path))
			{
				if (!Filesystem::makeDirectory($path))
				{
					$this->setError(Lang::txt('COM_GROUPS_UNABLE_TO_CREATE_UPLOAD_PATH'));

					return $this->displayTask();
				}
			}

			// make sure file is not empty
			if ($file['size'] == 0)
			{
				$this->setError(Lang::txt('COM_GROUPS_FILE_HAS_NO_SIZE'));

				return $this->displayTask();
			}

			// make sure file is not empty
			if ($file['size'] > $sizeLimit)
			{
				$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));

				$this->setError(Lang::txt('FILE_SIZE_TOO_BIG', $max));

				return $this->displayTask();
			}

			// build needed paths
			$filePath = $path . DS . $file['name'];

			// upload image
			if (!Filesystem::upload($file['tmp_name'], $filePath))
			{
				$this->setError(Lang::txt('COM_GROUPS_ERROR_UPLOADING'));

				return $this->displayTask();
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

		// Incoming directory to delete
		$dir    = urldecode(Request::getVar('dir', ''));
		$folder = urldecode(Request::getVar('folder', ''));

		if (!$folder)
		{
			$this->setError(Lang::txt('COM_GROUPS_ERROR_NO_DIRECTORY'));

			return $this->displayTask();
		}

		$path = $this->path . ($dir ? DS . trim($dir, DS) : '');
		$path = $path . DS . trim($folder, DS);
		$path = Util::normalizePath($path);

		// Check if the folder even exists
		if (!is_dir($path))
		{
			$this->setError(Lang::txt('COM_GROUPS_ERROR_DIRECTORY_NOT_FOUND'));

			return $this->displayTask();
		}

		// Attempt to delete the file
		if (!Filesystem::deleteDirectory($path))
		{
			$this->setError(Lang::txt('COM_GROUPS_ERROR_UNABLE_TO_DELETE_DIRECTORY'));

			return $this->displayTask();
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function deletefileTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		$dir  = urldecode(Request::getVar('dir', ''));

		// Build upload path
		$path = $this->path . ($dir ? DS . trim($dir, DS) : '');
		$path = Util::normalizePath($path);

		// Incoming file to delete
		$file = urldecode(Request::getVar('file', ''));

		if (!$file)
		{
			$this->setError(Lang::txt('COM_GROUPS_ERROR_NO_FILE'));

			return $this->displayTask();
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file))
		{
			$this->setError(Lang::txt('COM_GROUPS_ERROR_FILE_NOT_FOUND'));

			return $this->displayTask();
		}

		// Attempt to delete the file
		if (!Filesystem::delete($path . DS . $file))
		{
			$this->setError(Lang::txt('COM_GROUPS_ERROR_UNABLE_TO_DELETE_FILE'));
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Display a file and its info
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$dir  = urldecode(Request::getVar('dir', ''));

		$path = $this->path . ($dir ? DS . trim($dir, DS) : '');
		$path = Util::normalizePath($path);

		$dirs = $this->_recursiveListDir($this->path);

		$folders   = array();
		$folders[] = \Html::select('option', '/');
		if ($dirs)
		{
			foreach ($dirs as $d)
			{
				$folders[] = \Html::select('option', substr($d, strlen($this->path)));
			}
		}
		sort($folders);

		// Create folder <select> list
		$dirPath = \Html::select(
			'genericlist',
			$folders,
			'dir',
			'onchange="goUpDir()" ',
			'value',
			'text',
			$dir
		);

		// Output the HTML
		$this->view
			->set('path', $this->path)
			->set('group', $this->group)
			->set('dirPath', $dirPath)
			->set('dir', $dir)
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
		$dir  = Request::getVar('dir', '');

		// Build upload path
		$path = $this->path . ($dir ? DS . trim($dir, DS) : '');
		$path = Util::normalizePath($path);

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

		$this->view
			->set('docs', $docs)
			->set('folders', $folders)
			->set('group', $this->group)
			->set('path', $path)
			->set('dir', $dir)
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
