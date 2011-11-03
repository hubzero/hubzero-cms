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

ximport('Hubzero_Controller');

/**
 * Methods for listing and managing files and folders
 */
class ResourcesControllerMedia extends Hubzero_Controller
{
	/**
	 * Upload a file or create a new folder
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$this->view->setLayout('display');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar('listdir', '', 'post');
		if (!$listdir) 
		{
			$this->setError(JText::_('RESOURCES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = JRequest::getVar('dirPath', '', 'post');

		// Build the path
		$path = ResourcesUtilities::buildUploadPath($listdir, $subdir);

		// Are we creating a new folder?
		$foldername = JRequest::getVar('foldername', '', 'post');
		if ($foldername != '') 
		{
			// Make sure the name is valid
			if (eregi("[^0-9a-zA-Z_]", $foldername)) 
			{
				$this->setError(JText::_('Directory name must only contain alphanumeric characters and no spaces please.'));
			} 
			else 
			{
				if (!is_dir($path . DS . $foldername)) 
				{
					jimport('joomla.filesystem.folder');
					if (!JFolder::create($path . DS . $foldername, 0777)) 
					{
						$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
					}
				} 
				else 
				{
					$this->setError(JText::_('Directory already exists'));
				}
			}
			// Directory created
		} else {
			// Make sure the upload path exist
			if (!is_dir($path)) 
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::create($path, 0777)) 
				{
					$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
					$this->displayTask();
					return;
				}
			}

			// Incoming file
			$file = JRequest::getVar('upload', '', 'files', 'array');
			if (!$file['name']) 
			{
				$this->setError(JText::_('RESOURCES_NO_FILE'));
				$this->displayTask();
				return;
			}

			// Make the filename safe
			jimport('joomla.filesystem.file');
			$file['name'] = JFile::makeSafe($file['name']);
			$file['name'] = str_replace(' ', '_', $file['name']);

			// Perform the upload
			if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
			{
				$this->setError(JText::_('ERROR_UPLOADING'));
			} 
			else 
			{
				// File was uploaded

				// Was the file an archive that needs unzipping?
				$batch = JRequest::getInt('batch', 0, 'post');
				if ($batch) 
				{
					//$file_to_unzip = preg_replace('/(.+)\..*$/', '$1', $file['name']);

					/*jimport('joomla.filesystem.archive');

					// Extract the files
					$ret = JArchive::extract($file['name'], $path);
					if (!$ret) {
						$this->setError(JText::_('Could not extract package.'));
					}*/
					require_once(JPATH_ROOT.DS.'administrator'.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php');

					if (!extension_loaded('zlib')) 
					{
						$this->setError(JText::_('ZLIB_PACKAGE_REQUIRED'));
					} 
					else 
					{
						if (substr($path, -1, 1) == DS) 
						{
							$path = substr($path, 0, -1);
						}
						$zip = new PclZip($path . DS . $file['name']);

						// unzip the file
						$do = $zip->extract($path);
						if (!$do) 
						{
							$this->setError(JText::_('UNABLE_TO_EXTRACT_PACKAGE'));
						} 
						else 
						{
							@unlink($path . DS . $file['name']);
						}
					}
				} // if ($batch) {
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a folder and contents
	 * 
	 * @return     void
	 */
	public function deletefolderTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		$this->view->setLayout('display');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar('listdir', '');
		if (!$listdir) 
		{
			$this->setError(JText::_('RESOURCES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = JRequest::getVar('subdir', '');

		// Build the path
		$path = ResourcesUtilities::buildUploadPath($listdir, $subdir);

		// Incoming directory to delete
		$folder = JRequest::getVar('delFolder', '');
		if (!$folder) 
		{
			$this->setError(JText::_('RESOURCES_NO_DIRECTORY'));
			$this->displayTask();
			return;
		}

		$folder = ResourcesUtilities::normalizePath($folder);

		// Check if the folder even exists
		if (!is_dir($path . $folder) or !$folder) {
			$this->setError(JText::_('DIRECTORY_NOT_FOUND'));
		} 
		else 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.folder');
			if (!JFolder::delete($path . $folder)) 
			{
				$this->setError(JText::_('UNABLE_TO_DELETE_DIRECTORY'));
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
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		$this->view->setLayout('display');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar('listdir', '');
		if (!$listdir) 
		{
			$this->setError(JText::_('RESOURCES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		$subdir = JRequest::getVar('subdir', '');

		// Build the path
		$path = ResourcesUtilities::buildUploadPath($listdir, $subdir);

		// Incoming file to delete
		$file = JRequest::getVar('delFile', '');
		if (!$file) 
		{
			$this->setError(JText::_('RESOURCES_NO_FILE'));
			$this->displayTask();
			return;
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file) or !$file) 
		{
			$this->setError(JText::_('FILE_NOT_FOUND'));
		} 
		else 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file)) 
			{
				$this->setError(JText::_('UNABLE_TO_DELETE_FILE'));
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
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$this->view->listdir = JRequest::getVar('listdir', '');
		if (!$this->view->listdir) 
		{
			echo ResourcesHtml::error(JText::_('No list directory provided.'));
			return;
		}

		// Incoming sub-directory
		$this->view->subdir = JRequest::getVar('subdir', '');
		if (!$this->view->subdir) 
		{
			$this->view->subdir = JRequest::getVar('dirPath', '', 'post');
		}

		// Build the path
		$this->view->path = ResourcesUtilities::buildUploadPath($this->view->listdir, $this->view->subdir);

		// Get list of directories
		$dirs = $this->_recursiveListDir($this->view->path);

		$folders   = array();
		$folders[] = JHTML::_('select.option', '/');
		if ($dirs) 
		{
			foreach ($dirs as $dir)
			{
				$folders[] = JHTML::_('select.option', substr($dir, strlen($this->view->path)));
			}
		}
		sort($folders);

		// Create folder <select> list
		$this->view->dirPath = JHTML::_(
			'select.genericlist', 
			$folders, 
			'dirPath', 
			'onchange="goUpDir()" ',
			'value', 
			'text', 
			$this->view->subdir
		);

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
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
		$this->view->listdir = JRequest::getVar('listdir', '');
		if (!$this->view->listdir) 
		{
			echo ResourcesHtml::error(JText::_('No list directory provided.'));
			return;
		}

		// Incoming sub-directory
		$this->view->subdir = JRequest::getVar('subdir', '');

		// Build the path
		$path = ResourcesUtilities::buildUploadPath($this->view->listdir, $this->view->subdir);

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
		
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
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

