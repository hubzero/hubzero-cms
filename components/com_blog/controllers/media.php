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
	 * Execute task
	 *
	 * @return     void
	 */
	public function execute()
	{
		/*if (JFactory::getUser()->get('guest'))
		{
			JError::raiseError(403, JText::_('Access denied.'));
			return;
		}*/

		parent::execute();
	}

	/**
	 * Download a file
	 *
	 * @return     void
	 */
	public function downloadTask()
	{
		$model = new BlogModel('site', 0);

		$entry = $model->entry(JRequest::getVar('alias', ''));
		if (!$entry->exists() || !$entry->access('view'))
		{
			JError::raiseError(403, JText::_('Access denied.'));
			return;
		}

		if (!($file = JRequest::getVar('file', '')))
		{
			$filename = array_pop(explode('/', $_SERVER['REQUEST_URI']));

			//get the file name
			if (substr(strtolower($filename), 0, strlen('image:')) == 'image:')
			{
				$file = substr($filename, strlen('image:'));
			}
			elseif (substr(strtolower($filename), 0, strlen('file:')) == 'file:')
			{
				$file = substr($filename, strlen('file:'));
			}
		}

		// decode file name
		$file = urldecode($file);

		// build file path
		$file_path = $this->_getUploadPath('site', 0) . DS . $file;

		// Ensure the file exist
		if (!file_exists($file_path))
		{
			JError::raiseError(404, JText::_('The requested file could not be found: %s', $file));
			return;
		}

		// Serve up the image
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($file_path);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		//serve up file
		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('An error occurred while trying to output the file'));
		}
		else
		{
			exit;
		}
	}

	/**
	 * Upload a file or create a new folder
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

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('COM_BLOG_NO_FILE'));
			$this->displayTask();
			return;
		}

		// Incoming
		$scope = JRequest::getVar('scope', 'site');
		$id = JRequest::getInt('id', 0);

		// Build the file path
		$path = $this->_getUploadPath($scope, $id);

		if ($this->getError())
		{
			// Push through to the media view
			$this->displayTask();
			return;
		}

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

		// Incoming file
		$file = trim(JRequest::getVar('folder', '', 'get'));
		if (!$file)
		{
			$this->setError(JText::_('COM_BLOG_NO_DIRECTORY'));
			$this->displayTask();
			return;
		}

		// Incoming
		$scope = JRequest::getVar('scope', 'site');
		$id = JRequest::getInt('id', 0);

		// Build the file path
		$path = $this->_getUploadPath($scope, $id);

		if ($this->getError())
		{
			// Push through to the media view
			$this->displayTask();
			return;
		}

		$folder = $path . DS . $file;

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

		// Incoming file
		$file = trim(JRequest::getVar('file', '', 'get'));
		if (!$file)
		{
			$this->setError(JText::_('COM_BLOG_NO_FILE'));
			$this->displayTask();
			return;
		}

		// Incoming
		$scope = JRequest::getVar('scope', 'site');
		$id = JRequest::getInt('id', 0);

		// Build the file path
		$path = $this->_getUploadPath($scope, $id);

		if ($this->getError())
		{
			// Push through to the media view
			$this->displayTask();
			return;
		}

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
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->setLayout('display');

		// Output HTML
		$this->view->config = $this->config;
		$this->view->id = JRequest::getInt('id', 0);
		$this->view->scope = JRequest::getVar('scope', 'site');

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
	 * Build an upload path
	 *
	 * @param      string  $scope
	 * @param      integer $id
	 * @return     string
	 */
	protected function _getUploadPath($scope, $id)
	{
		$path = JPATH_ROOT;
		switch ($scope)
		{
			case 'member':
				jimport('joomla.plugin.plugin');
				$plugin = JPluginHelper::getPlugin('members', 'blog');
				$params = new JRegistry($plugin->params);
				$p = $params->get('uploadpath');
				$p = str_replace('{{uid}}', \Hubzero\Utility\String::pad($id), $p);
			break;

			case 'group':
				$groupParams = JComponentHelper::getParams('com_groups');
				$uploadpath = $groupParams->get('uploadpath', '/site/groups');
				$p = rtrim($uploadpath, DS) . DS . $id . DS . 'uploads' . DS  . 'blog';
			break;

			case 'site':
				$p = $this->config->get('uploadpath', '/site/blog');
			break;

			default:
				$this->setError(JText::_('Invalid scope'));
				$p = '';
			break;
		}
		$path .= DS . trim($p, DS);

		return $path;
	}

	/**
	 * Lists all files and folders for a given directory
	 *
	 * @return     void
	 */
	public function listTask()
	{
		// Incoming
		$scope = JRequest::getWord('scope', 'site');
		$id = JRequest::getInt('id', 0);

		$path = $this->_getUploadPath($scope, $id);

		$folders = array();
		$docs    = array();

		if (!$this->getError() && is_dir($path))
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
		$this->view->id      = $id;
		$this->view->scope   = $scope;

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
