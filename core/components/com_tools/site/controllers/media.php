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
use Components\Resources\Models\Entry;
use Filesystem;
use Component;
use Request;
use Route;
use Lang;
use User;
use App;

require_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';
require_once Component::path('com_resources') . DS . 'helpers' . DS . 'utilities.php';
require_once Component::path('com_resources') . DS . 'helpers' . DS . 'html.php';

/**
 * Methods for listing and managing files and folders
 */
class Media extends SiteController
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
		$resource = Request::getInt('resource', 0, 'post');
		if (!$resource)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return $this->displayTask();
		}

		// Incoming sub-directory
		$subdir = Request::getVar('dirPath', '', 'post');

		$row = Entry::oneOrFail($resource);

		if (!$row->get('created') || $row->get('created') == '0000-00-00 00:00:00')
		{
			$row->set('created', Date::format('Y-m-d 00:00:00'));
		}

		$path = $row->filespace() . DS . 'media';

		// Make sure the upload path exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask();
			}
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_FILE'));
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

		$path .= DS . $file['name'];

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path))
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_UPLOADING'));
		}

		if (!Filesystem::isSafe($path))
		{
			Filesystem::delete($path);

			$this->setError(Lang::txt('COM_TOOLS_ERROR_FAILED_VIRUS_SCAN'));
			return $this->displayTask();
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Deletes a file
	 *
	 * @return  void
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
			return $this->displayTask();
		}

		// Incoming sub-directory
		$row = Entry::oneOrFail($resource);

		// Allow for temp resource uploads
		if (!$row->get('created') || $row->get('created') == '0000-00-00 00:00:00')
		{
			$row->set('created', Date::format('Y-m-d 00:00:00'));
		}

		$path = $row->filespace() . DS . 'media';

		// Make sure the listdir follows YYYY/MM/##/media
		$parts = explode(DS, $path);
		if (count($parts) < 4)
		{
			$this->setError(Lang::txt('DIRECTORY_NOT_FOUND'));
			return $this->displayTask();
		}

		// Incoming file to delete
		$file = Request::getVar('file', '');

		if (!$file)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_FILE'));
			return $this->displayTask();
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
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$resource = Request::getInt('resource', 0);

		if (!$resource)
		{
			echo '<p class="error">' . Lang::txt('No resource ID provided.') . '</p>';
			return;
		}

		$row = Entry::oneOrFail($resource);

		// Incoming sub-directory
		$subdir = Request::getVar('subdir', '');

		// Allow for temp resource uploads
		if (!$row->get('created') || $row->get('created') == '0000-00-00 00:00:00')
		{
			$row->set('created', Date::format('Y-m-d 00:00:00'));
		}

		$path = $row->filespace() . DS . 'media';

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
			->set('resource', $resource)
			->set('row', $row)
			->set('subdir', $subdir)
			->set('path', $path)
			->set('docs', $docs)
			->set('folders', $folders)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
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
