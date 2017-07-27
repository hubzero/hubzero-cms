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

namespace Components\Blog\Site\Controllers;

use Components\Blog\Models\Archive;
use Components\Blog\Models\Entry;
use Hubzero\Component\SiteController;
use Hubzero\Content\Server;
use InvalidArgumentException;
use RuntimeException;
use DirectoryIterator;
use Filesystem;
use Request;
use User;
use Lang;

/**
 * Blog controller class for media
 */
class Media extends SiteController
{
	/**
	 * Download a file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		$archive = new Archive('site', 0);

		$entry = Entry::oneByScope(
			Request::getVar('alias', ''),
			'site',
			0
		);

		if (!$entry->get('id') || !$entry->access('view'))
		{
			throw new Exception(Lang::txt('Access denied.'), 403);
		}

		if (!($file = Request::getVar('file', '')))
		{
			$filename = array_pop(explode('/', $_SERVER['REQUEST_URI']));

			// Get the file name
			if (substr(strtolower($filename), 0, strlen('image:')) == 'image:')
			{
				$file = substr($filename, strlen('image:'));
			}
			elseif (substr(strtolower($filename), 0, strlen('file:')) == 'file:')
			{
				$file = substr($filename, strlen('file:'));
			}
		}

		// Decode file name
		$file = urldecode($file);

		// Build file path
		$file_path = $archive->filespace() . DS . $file;

		// Ensure the file exist
		if (!file_exists($file_path))
		{
			throw new InvalidArgumentException(Lang::txt('The requested file could not be found: %s', $file), 404);
		}

		// Serve up the image
		$server = new Server();
		$server->filename($file_path);
		$server->disposition('inline');
		$server->acceptranges(false); // @TODO fix byte range support

		// Serve up file
		if (!$server->serve())
		{
			// Should only get here on error
			throw new RuntimeException(Lang::txt('An error occurred while trying to output the file'), 500);
		}

		exit;
	}

	/**
	 * Upload a file or create a new folder
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->displayTask();
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_BLOG_NO_FILE'));
			return $this->displayTask();
		}

		// Incoming
		$archive = new Archive(
			Request::getWord('scope', 'site'),
			Request::getInt('id', 0)
		);

		// Build the file path
		$path = $archive->filespace();

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_BLOG_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask();
			}
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);

		// Ensure file names fit.
		$ext = Filesystem::extension($file['name']);

		$file['name'] = str_replace(' ', '_', $file['name']);
		if (strlen($file['name']) > 230)
		{
			$file['name']  = substr($file['name'], 0, 230);
			$file['name'] .= '.' . $ext;
		}

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_BLOG_ERROR_UPLOADING'));
		}

		if (!Filesystem::isSafe($path . DS . $file['name']))
		{
			Filesystem::delete($path . DS . $file['name']);

			$this->setError(Lang::txt('COM_BLOG_ERROR_UPLOADING'));
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Deletes a folder
	 *
	 * @return  void
	 */
	public function deletefolderTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->displayTask();
		}

		// Incoming file
		$file = trim(Request::getVar('folder', '', 'get'));
		if (!$file)
		{
			$this->setError(Lang::txt('COM_BLOG_NO_DIRECTORY'));
			return $this->displayTask();
		}

		// Incoming
		$archive = new Archive(
			Request::getWord('scope', 'site'),
			Request::getInt('id', 0)
		);

		// Build the file path
		$folder = $path . DS . $archive->filespace();

		// Delete the folder
		if (is_dir($folder))
		{
			// Attempt to delete the file
			if (!Filesystem::deleteDirectory($folder))
			{
				$this->setError(Lang::txt('COM_BLOG_UNABLE_TO_DELETE_DIRECTORY'));
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

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->displayTask();
		}

		// Incoming file
		$file = trim(Request::getVar('file', '', 'get'));
		if (!$file)
		{
			$this->setError(Lang::txt('COM_BLOG_NO_FILE'));
			return $this->displayTask();
		}

		// Incoming
		$archive = new Archive(
			Request::getWord('scope', 'site'),
			Request::getInt('id', 0)
		);

		// Build the file path
		$path = $archive->filespace();

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_BLOG_FILE_NOT_FOUND'));
			return $this->displayTask();
		}

		// Attempt to delete the file
		if (!Filesystem::delete($path . DS . $file))
		{
			$this->setError(Lang::txt('COM_BLOG_UNABLE_TO_DELETE_FILE'));
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
		// Output HTML
		$archive = new Archive(
			Request::getWord('scope', 'site'),
			Request::getInt('id', 0)
		);

		$this->view
			->set('archive', $archive)
			->setLayout('display')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Lists all files and folders for a given directory
	 *
	 * @return  void
	 */
	public function listTask()
	{
		// Incoming
		$archive = new Archive(
			Request::getWord('scope', 'site'),
			Request::getInt('id', 0)
		);

		// Build the file path
		$path = $archive->filespace();

		$folders = array();
		$files   = array();

		if (!$this->getError() && is_dir($path))
		{
			$files   = Filesystem::files($path);
			$folders = Filesystem::directories($path);
		}

		$this->view
			->set('archive', $archive)
			->set('docs', $files)
			->set('folders', $folders)
			->setErrors($this->getErrors())
			->display();
	}
}
