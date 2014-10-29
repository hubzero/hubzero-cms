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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Blog controller class for media
 */
class BlogControllerMedia extends \Hubzero\Component\SiteController
{
	/**
	 * Download a file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		$archive = new BlogModelArchive('site', 0);

		$entry = $archive->entry(JRequest::getVar('alias', ''));
		if (!$entry->exists() || !$entry->access('view'))
		{
			JError::raiseError(403, JText::_('Access denied.'));
			return;
		}

		if (!($file = JRequest::getVar('file', '')))
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
			throw new InvalidArgumentException(JText::_('The requested file could not be found: %s', $file), 404);
		}

		// Serve up the image
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($file_path);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		// Serve up file
		if (!$xserver->serve())
		{
			// Should only get here on error
			throw new RuntimeException(JText::_('An error occurred while trying to output the file'), 500);
		}
		else
		{
			exit;
		}
	}

	/**
	 * Upload a file or create a new folder
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->displayTask();
			return;
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('COM_BLOG_NO_FILE'));
			$this->displayTask();
			return;
		}

		// Incoming
		$archive = new BlogModelArchive(
			JRequest::getWord('scope', 'site'),
			JRequest::getInt('id', 0)
		);

		// Build the file path
		$path = $archive->filespace();

		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				$this->setError(JText::_('COM_BLOG_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask();
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);

		// Ensure file names fit.
		$ext = JFile::getExt($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		if (strlen($file['name']) > 230)
		{
			$file['name'] = substr($file['name'], 0, 230);
			$file['name'] .= '.' . $ext;
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(JText::_('COM_BLOG_ERROR_UPLOADING'));
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
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->displayTask();
			return;
		}

		// Incoming file
		$file = trim(JRequest::getVar('folder', '', 'get'));
		if (!$file)
		{
			$this->setError(JText::_('COM_BLOG_NO_DIRECTORY'));
			$this->displayTask();
			return;
		}

		// Incoming
		$archive = new BlogModelArchive(
			JRequest::getWord('scope', 'site'),
			JRequest::getInt('id', 0)
		);

		// Build the file path
		$folder = $path . DS . $archive->filespace();

		// Delete the folder
		if (is_dir($folder))
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($folder))
			{
				$this->setError(JText::_('COM_BLOG_UNABLE_TO_DELETE_DIRECTORY'));
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
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->displayTask();
			return;
		}

		// Incoming file
		$file = trim(JRequest::getVar('file', '', 'get'));
		if (!$file)
		{
			$this->setError(JText::_('COM_BLOG_NO_FILE'));
			$this->displayTask();
			return;
		}

		// Incoming
		$archive = new BlogModelArchive(
			JRequest::getWord('scope', 'site'),
			JRequest::getInt('id', 0)
		);

		// Build the file path
		$path = $archive->filespace();

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(JText::_('COM_BLOG_FILE_NOT_FOUND'));
			$this->displayTask();
			return;
		}

		// Attempt to delete the file
		jimport('joomla.filesystem.file');
		if (!JFile::delete($path . DS . $file))
		{
			$this->setError(JText::_('COM_BLOG_UNABLE_TO_DELETE_FILE'));
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
		$this->view->archive = new BlogModelArchive(
			JRequest::getWord('scope', 'site'),
			JRequest::getInt('id', 0)
		);

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
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
		// Incoming
		$this->view->archive = new BlogModelArchive(
			JRequest::getWord('scope', 'site'),
			JRequest::getInt('id', 0)
		);

		// Build the file path
		$path = $this->view->archive->filespace();

		$folders = array();
		$files   = array();

		if (!$this->getError() && is_dir($path))
		{
			// Loop through all files and separate them into arrays of files and folders
			$dirIterator = new DirectoryIterator($path);
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

					$files[$path . DS . $name] = $name;
				}
			}

			ksort($folders);
			ksort($files);
		}

		$this->view->docs    = $files;
		$this->view->folders = $folders;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}
}
