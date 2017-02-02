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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Site\Controllers;

use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Attachment;
use Hubzero\Component\SiteController;
use Hubzero\Content\Server;
use Hubzero\Utility\Number;
use Filesystem;
use Request;
use User;
use Lang;
use Date;
use App;

/**
 * Wiki controller class for media
 */
class Media extends SiteController
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  Optional configurations
	 * @return  void
	 */
	public function __construct($config=array())
	{
		if (!isset($config['scope']))
		{
			$config['scope'] = 'site';
		}

		if (!isset($config['scope_id']))
		{
			$config['scope_id'] = 0;
		}

		$this->book = new Book($config['scope'], $config['scope_id']);

		if ($config['scope'] != 'site')
		{
			Request::setVar('task', Request::getWord('action'));
		}

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->page = $this->book->page();

		parent::execute();
	}

	/**
	 * Download a wiki file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		$pagename = urldecode(Request::getVar('pagename', '', 'default', 'none', 2));
		$pagename = explode('/', $pagename);
		$filename = array_pop($pagename);
		$pagename = implode('/', $pagename);

		// Get the parent page the file is attached to
		$this->page = Page::oneByPath($pagename, $this->page->get('scope'), $this->page->get('scope_id'));

		// Load the page
		if ($this->page->exists())
		{
			// Check if the page is group restricted and the user is not authorized
			if ($this->page->get('scope') != 'site'
			 && $this->page->get('access') != 0
			 && !$this->page->access('view'))
			{
				App::abort(403, Lang::txt('COM_WIKI_WARNING_NOT_AUTH'));
			}
		}
		else if ($this->page->getNamespace() == 'tmp')
		{
			$this->page->set('id', $this->page->stripNamespace());
		}
		else
		{
			App::abort(404, Lang::txt('COM_WIKI_PAGE_NOT_FOUND'));
		}

		$filename = $this->page->stripNamespace($filename);

		// Instantiate an attachment object
		$attachment = $this->page
			->attachments()
			->whereEquals('filename', $filename)
			->row();

		// Ensure we have a path
		if (!$attachment->get('filename'))
		{
			$attachment->set('filename', $filename);

			if (!$attachment->get('id'))
			{
				$attachment->set('page_id', $this->page->get('id'));
				$attachment->set('created', Date::toSql());
				$attachment->set('created_by', User::get('id'));
				$attachment->save();
			}
		}

		// Add root
		$filename = $attachment->filespace() . DS . $this->page->get('id') . DS . ltrim($attachment->get('filename'), DS);

		// Ensure the file exist
		if (!file_exists($filename))
		{
			App::abort(404, Lang::txt('COM_WIKI_FILE_NOT_FOUND') . ' ' . $attachment->get('filename'));
		}

		// Initiate a new content server and serve up the file
		$xserver = new Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(500, Lang::txt('COM_WIKI_SERVER_ERROR'));
		}

		exit;
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return  void
	 */
	public function ajaxUploadTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			echo json_encode(array('error' => Lang::txt('COM_WIKI_WARNING_LOGIN')));
			return;
		}

		// Ensure we have an ID to work with
		$listdir = Request::getInt('listdir', 0);
		if (!$listdir)
		{
			echo json_encode(array('error' => Lang::txt('COM_WIKI_NO_ID')));
			return;
		}

		// max upload size
		$sizeLimit = $this->book->config('maxAllowed', 40000000);

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
			echo json_encode(array('error' => Lang::txt('COM_WIKI_ERROR_NO_FILE')));
			return;
		}

		$attachment = Attachment::blank();

		// define upload directory and make sure its writable
		$path = $attachment->filespace() . DS . $listdir;

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				echo json_encode(array('error' => Lang::txt('COM_WIKI_ERROR_UNABLE_TO_CREATE_DIRECTORY')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array('error' => Lang::txt('COM_WIKI_ERROR_DIRECTORY_NOT_WRITABLE')));
			return;
		}

		// check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => Lang::txt('COM_WIKI_ERROR_NO_FILE')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => Lang::txt('COM_WIKI_ERROR_FILE_TOO_LARGE', $max)));
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
			// read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			// move from temp location to target location which is user folder
			$target = fopen($file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		// Create database entry
		$attachment->set('page_id', $listdir);
		$attachment->set('filename', $filename . '.' . $ext);
		$attachment->set('description', trim(Request::getVar('description', '', 'post')));
		$attachment->set('created', Date::toSql());
		$attachment->set('created_by', User::get('id'));

		if (!$attachment->save())
		{
			$this->setError($attachment->getError());
		}

		//echo result
		echo json_encode(array(
			'success'   => true,
			'file'      => $filename . '.' . $ext,
			'directory' => str_replace(PATH_APP, '', $path)
		));
	}

	/**
	 * Upload a file to the wiki
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

		if (Request::getVar('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		// Ensure we have an ID to work with
		$listdir = Request::getInt('listdir', 0, 'post');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_WIKI_NO_ID'));
			return $this->displayTask();
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_WIKI_NO_FILE'));
			return $this->displayTask();
		}

		$attachment = Attachment::blank();

		// Build the upload path if it doesn't exist
		$path = $attachment->filespace() . DS . $listdir;

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_WIKI_ERROR_UNABLE_TO_CREATE_DIRECTORY'));
				return $this->displayTask();
			}
		}

		// Make the filename safe
		$file['name'] = urldecode($file['name']);
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Upload new files
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_WIKI_ERROR_UPLOADING'));
		}
		// File was uploaded
		else
		{
			// Create database entry
			$attachment->set('page_id', $listdir);
			$attachment->set('filename', $file['name']);
			$attachment->set('description', trim(Request::getVar('description', '', 'post')));
			$attachment->set('created', Date::toSql());
			$attachment->set('created_by', User::get('id'));

			if (!$attachment->save())
			{
				$this->setError($attachment->getError());
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a folder in the wiki
	 *
	 * @return  void
	 */
	public function deletefolderTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->displayTask();
		}

		// Incoming group ID
		$listdir = Request::getInt('listdir', 0, 'get');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_WIKI_NO_ID'));
			return $this->displayTask();
		}

		// Incoming folder
		$folder = trim(Request::getVar('folder', '', 'get'));
		if (!$folder)
		{
			$this->setError(Lang::txt('COM_WIKI_NO_DIRECTORY'));
			return $this->displayTask();
		}

		$attachment = Attachment::blank();

		// Build the file path
		$path = $attachment->filespace() . DS . $listdir . DS . $folder;

		// Delete the folder
		if (is_dir($path))
		{
			// Attempt to delete the file
			if (!Filesystem::deleteDirectory($path))
			{
				$this->setError(Lang::txt('COM_WIKI_ERROR_UNABLE_TO_DELETE_DIRECTORY'));
			}
		}
		else
		{
			$this->setError(Lang::txt('COM_WIKI_NO_DIRECTORY'));
		}

		// Push through to the media view
		if (Request::getVar('no_html', 0))
		{
			return $this->listTask();
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a file in the wiki
	 *
	 * @return  void
	 */
	public function deletefileTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->displayTask();
		}

		// Incoming
		$listdir = Request::getInt('listdir', 0, 'get');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_WIKI_NO_ID'));
			return $this->displayTask();
		}

		// Incoming file
		$file = trim(Request::getVar('file', '', 'get'));
		if (!$file)
		{
			$this->setError(Lang::txt('COM_WIKI_NO_FILE'));
			return $this->displayTask();
		}

		$attachment = Attachment::oneByFilename($file, $listdir);

		// Delete the file
		if (!$attachment || !$attachment->get('id'))
		{
			// No database record for some reason
			// Set some data so the model can still remove the file
			$attachment->set('page_id', $listdir);
			$attachment->set('filename', $file);
		}

		// Attempt to delete the file
		// Delete the database entry for the file
		if (!$attachment->destroy())
		{
			$this->setError($attachment->getError());
		}

		// Push through to the media view
		if (Request::getVar('no_html', 0))
		{
			return $this->listTask();
		}

		$this->displayTask();
	}

	/**
	 * Display a form for uploading files
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view
			->set('config', $this->config)
			->set('listdir', Request::getInt('listdir', 0, 'request'))
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}

	/**
	 * Display a list of files
	 *
	 * @return  void
	 */
	public function listTask()
	{
		// Incoming
		$listdir = Request::getInt('listdir', 0, 'get');

		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_WIKI_NO_ID'));
		}

		$attachment = Attachment::blank();

		$path = $attachment->filespace() . DS . $listdir;

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
			->set('config', $this->config)
			->set('listdir', $listdir)
			->set('sub', $this->page->get('scope') != 'site')
			->setErrors($this->getErrors())
			->setLayout('list')
			->display();
	}
}
