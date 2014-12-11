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

/**
 * Wiki controller class for media
 */
class WikiControllerMedia extends \Hubzero\Component\SiteController
{
	/**
	 * Constructor
	 *
	 * @param      array $config Optional configurations
	 * @return     void
	 */
	public function __construct($config=array())
	{
		$this->_base_path = JPATH_ROOT . DS . 'components' . DS . 'com_wiki';
		if (isset($config['base_path']))
		{
			$this->_base_path = $config['base_path'];
		}

		$this->_sub = false;
		if (isset($config['sub']))
		{
			$this->_sub = $config['sub'];
		}

		$this->_group = false;
		if (isset($config['group']))
		{
			$this->_group = $config['group'];
		}

		$this->book = new WikiModelBook(($this->_group ? $this->_group : '__site__'));

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->page = $this->book->page();

		parent::execute();
	}

	/**
	 * Download a wiki file
	 *
	 * @return     void
	 */
	public function downloadTask()
	{
		$this->page->set('pagename', trim(JRequest::getVar('pagename', '', 'default', 'none', 2)));

		// Instantiate an attachment object
		$attachment = new WikiTableAttachment($this->database);
		if ($this->page->get('namespace') == 'image' || $this->page->get('namespace') == 'file')
		{
			$attachment->filename = $this->page->denamespaced();
		}
		$attachment->filename = urldecode($attachment->filename);

		// Get the scope of the parent page the file is attached to
		if (!$this->scope)
		{
			$this->scope = trim(JRequest::getVar('scope', ''));
		}
		$segments = explode('/', $this->scope);
		$pagename = array_pop($segments);
		$scope = implode('/', $segments);

		// Get the parent page the file is attached to
		$this->page = new WikiModelPage($pagename, $scope);

		// Load the page
		if ($this->page->exists())
		{
			// Check if the page is group restricted and the user is authorized
			if ($this->page->get('group_cn') != '' && $this->page->get('access') != 0 && !$this->page->access('view'))
			{
				JError::raiseWarning(403, JText::_('COM_WIKI_WARNING_NOT_AUTH'));
				return;
			}
		}
		else if ($this->page->get('namespace') == 'tmp')
		{
			$this->page->set('id', $this->page->denamespaced());
		}
		else
		{
			JError::raiseError(404, JText::_('COM_WIKI_PAGE_NOT_FOUND'));
			return;
		}

		// Ensure we have a path
		if (empty($attachment->filename))
		{
			JError::raiseError(404, JText::_('COM_WIKI_FILE_NOT_FOUND'));
			return;
		}

		// Get the configured upload path
		$base_path = DS . trim($this->book->config('filepath', '/site/wiki'), DS) . DS . $this->page->get('id');

		// Does the path start with a slash?
		$attachment->filename = DS . ltrim($attachment->filename, DS);

		// Does the beginning of the $attachment->path match the config path?
		if (substr($attachment->filename, 0, strlen($base_path)) == $base_path)
		{
			// Yes - this means the full path got saved at some point
		}
		else
		{
			// No - append it
			$attachment->filename = $base_path . $attachment->filename;
		}

		// Add JPATH_ROOT
		$filename = JPATH_ROOT . $attachment->filename;

		// Ensure the file exist
		if (!file_exists($filename))
		{
			JError::raiseError(404, JText::_('COM_WIKI_FILE_NOT_FOUND') . ' ' . $filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('COM_WIKI_SERVER_ERROR'));
		}
		else
		{
			exit;
		}
		return;
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return     string
	 */
	public function ajaxUploadTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			echo json_encode(array('error' => JText::_('COM_WIKI_WARNING_LOGIN')));
			return;
		}

		// Ensure we have an ID to work with
		$listdir = JRequest::getInt('listdir', 0);
		if (!$listdir)
		{
			echo json_encode(array('error' => JText::_('COM_WIKI_NO_ID')));
			return;
		}

		//allowed extensions for uplaod
		//$allowedExtensions = array("png","jpeg","jpg","gif");

		//max upload size
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
			//$files = JRequest::getVar('qqfile', '', 'files', 'array');

			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			echo json_encode(array('error' => JText::_('COM_WIKI_ERROR_NO_FILE')));
			return;
		}

		//define upload directory and make sure its writable
		$path = JPATH_ROOT . DS . trim($this->book->config('filepath', '/site/wiki'), DS) . DS . $listdir;
		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				echo json_encode(array('error' => JText::_('COM_WIKI_ERROR_UNABLE_TO_CREATE_DIRECTORY')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array('error' => JText::_('COM_WIKI_ERROR_DIRECTORY_NOT_WRITABLE')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => JText::_('COM_WIKI_ERROR_NO_FILE')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => JText::sprintf('COM_WIKI_ERROR_FILE_TOO_LARGE', $max)));
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

		// Create database entry
		$attachment = new WikiTableAttachment($this->database);
		$attachment->pageid      = $listdir;
		$attachment->filename    = $filename . '.' . $ext;
		$attachment->description = trim(JRequest::getVar('description', '', 'post'));
		$attachment->created     = JFactory::getDate()->toSql();
		$attachment->created_by  = $this->juser->get('id');

		if (!$attachment->check())
		{
			$this->setError($attachment->getError());
		}
		if (!$attachment->store())
		{
			$this->setError($attachment->getError());
		}

		//echo result
		echo json_encode(array(
			'success'   => true,
			'file'      => $filename . '.' . $ext,
			'directory' => str_replace(JPATH_ROOT, '', $path)
		));
	}

	/**
	 * Upload a file to the wiki
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->displayTask();
			return;
		}

		if (JRequest::getVar('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		// Ensure we have an ID to work with
		$listdir = JRequest::getInt('listdir', 0, 'post');
		if (!$listdir)
		{
			$this->setError(JText::_('COM_WIKI_NO_ID'));
			$this->displayTask();
			return;
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('COM_WIKI_NO_FILE'));
			$this->displayTask();
			return;
		}

		// Build the upload path if it doesn't exist
		$path = JPATH_ROOT . DS . trim($this->book->config('filepath', '/site/wiki'), DS) . DS . $listdir;

		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				$this->setError(JText::_('COM_WIKI_ERROR_UNABLE_TO_CREATE_DIRECTORY'));
				$this->displayTask();
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = urldecode($file['name']);
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Upload new files
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(JText::_('COM_WIKI_ERROR_UPLOADING'));
		}
		// File was uploaded
		else
		{
			// Create database entry
			$attachment = new WikiTableAttachment($this->database);
			$attachment->pageid      = $listdir;
			$attachment->filename    = $file['name'];
			$attachment->description = trim(JRequest::getVar('description', '', 'post'));
			$attachment->created     = JFactory::getDate()->toSql();
			$attachment->created_by  = $this->juser->get('id');

			if (!$attachment->check())
			{
				$this->setError($attachment->getError());
			}
			if (!$attachment->store())
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
	 * @return     void
	 */
	public function deletefolderTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->displayTask();
			return;
		}

		// Incoming group ID
		$listdir = JRequest::getInt('listdir', 0, 'get');
		if (!$listdir)
		{
			$this->setError(JText::_('COM_WIKI_NO_ID'));
			$this->displayTask();
			return;
		}

		// Incoming folder
		$folder = trim(JRequest::getVar('folder', '', 'get'));
		if (!$folder)
		{
			$this->setError(JText::_('COM_WIKI_NO_DIRECTORY'));
			$this->displayTask();
			return;
		}

		// Build the file path
		$path = JPATH_ROOT . DS . trim($this->book->config('filepath', '/site/wiki'), DS) . DS . $listdir . DS . $folder;

		// Delete the folder
		if (is_dir($path))
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($path))
			{
				$this->setError(JText::_('COM_WIKI_ERROR_UNABLE_TO_DELETE_DIRECTORY'));
			}
		}
		else
		{
			$this->setError(JText::_('COM_WIKI_NO_DIRECTORY'));
		}

		// Push through to the media view
		if (JRequest::getVar('no_html', 0))
		{
			return $this->listTask();
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a file in the wiki
	 *
	 * @return     void
	 */
	public function deletefileTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->displayTask();
			return;
		}

		// Incoming
		$listdir = JRequest::getInt('listdir', 0, 'get');
		if (!$listdir)
		{
			$this->setError(JText::_('COM_WIKI_NO_ID'));
			$this->displayTask();
			return;
		}

		// Incoming file
		$file = trim(JRequest::getVar('file', '', 'get'));
		if (!$file)
		{
			$this->setError(JText::_('COM_WIKI_NO_FILE'));
			$this->displayTask();
			return;
		}

		// Build the file path
		$path = JPATH_ROOT . DS . trim($this->book->config('filepath', '/site/wiki'), DS) . DS . $listdir;

		// Delete the file
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(JText::_('COM_WIKI_ERROR_NO_FILE'));
			$this->displayTask();
		}
		else
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file))
			{
				$this->setError(JText::sprintf('COM_WIKI_ERROR_UNABLE_TO_DELETE_FILE', $file));
			}
			else
			{
				// Delete the database entry for the file
				$attachment = new WikiTableAttachment($this->database);
				$attachment->deleteFile($file, $listdir);
			}
		}

		// Push through to the media view
		if (JRequest::getVar('no_html', 0))
		{
			return $this->listTask();
		}

		$this->displayTask();
	}

	/**
	 * Display a form for uploading files
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->setLayout('display');

		// Incoming
		$this->view->listdir = JRequest::getInt('listdir', 0, 'request');

		// Output HTML
		$this->view->config = $this->config;

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
	 * Display a list of files
	 *
	 * @return     void
	 */
	public function listTask()
	{
		$this->view->setLayout('list');

		// Incoming
		$listdir = JRequest::getInt('listdir', 0, 'get');

		if (!$listdir)
		{
			$this->setError(JText::_('COM_WIKI_NO_ID'));
		}

		$path = JPATH_ROOT . DS . trim($this->book->config('filepath', '/site/wiki'), DS) . DS . $listdir;

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
		$this->view->listdir = $listdir;
		$this->view->name    = $this->_name;
		$this->view->sub     = $this->_sub;

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}
}

