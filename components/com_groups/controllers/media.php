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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Groups controller class
 */
class GroupsControllerMedia extends GroupsControllerAbstract
{
	/**
	 * Show File Browser method
	 * 
	 * @return     array
	 */
	public function filebrowserTask()
	{
		// set the neeced layout
		$this->view->setLayout('filebrowser');
		
		//get request vars
		$this->view->listdir = JRequest::getVar('listdir', '');
		
		// push styles
		$this->_getStyles();
		
		// push scripts
		$this->_getScripts();
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 * List all group files
	 * 
	 * @return     array
	 */
	public function listfilesTask()
	{
		// set the neeced layout
		$this->view->setLayout('filelist');
		
		//get request vars
		$this->view->listdir = JRequest::getVar('listdir', '');
		
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $this->view->listdir;

		// Get the directory we'll be reading out of
		$d = @dir($path);

		$this->view->images  = array();
		$this->view->folders = array();
		$this->view->docs    = array();

		if ($d) 
		{
			// Loop through all files and separate them into arrays of images, folders, and other
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file($path . DS . $img_file) 
				 && substr($entry, 0, 1) != '.' 
				 && strtolower($entry) !== 'index.html') 
				{
					if (preg_match("#bmp|gif|jpg|jpeg|jpe|tif|tiff|png#i", $img_file)) 
					{
						$this->view->images[$entry] = $img_file;
					} 
					else 
					{
						$this->view->docs[$entry] = $img_file;
					}
				} 
				else if (is_dir($path . DS . $img_file) 
				 && substr($entry, 0, 1) != '.' 
				 && strtolower($entry) !== 'cvs' 
				 && strtolower($entry) !== 'template' 
				 && strtolower($entry) !== 'blog') 
				{
					$this->view->folders[$entry] = $img_file;
				}
			}
			
			$d->close();

			ksort($this->view->images);
			ksort($this->view->folders);
			ksort($this->view->docs);
		}
		
		// push styles
		$this->_getStyles();
		
		// push scripts
		$this->_getScripts();
		
		$this->view->config = $this->config;
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 * File upload - older browser support
	 * 
	 * @return     array
	 */
	public function uploadTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->filebrowserTask();
			return;
		}

		// Incoming
		$listdir = JRequest::getInt('listdir', 0, 'post');

		// Ensure we have an ID to work with
		if (!$listdir) 
		{
			$this->setNotification(JText::_('GROUPS_NO_ID'), 'error');
			$this->filebrowserTask();
			return;
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			$this->setNotification(JText::_('GROUPS_NO_FILE'), 'error');
			$this->filebrowserTask();
			return;
		}

		// Build the upload path if it doesn't exist
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . trim($listdir, DS);

		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setNotification(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'), 'error');
				$this->filebrowserTask();
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = urldecode($file['name']);
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
		{
			$this->setNotification(JText::_('ERROR_UPLOADING'), 'error');
		}

		//push a success message
		$this->setNotification('You successfully uploaded the file.', 'passed');

		// Push through to the media view
		$this->filebrowserTask();
	}
	
	
	/**
	 * Streaming file upload
	 * This is used by AJAX
	 * 
	 * @return     void
	 */
	public function ajaxuploadTask()
	{
		if ($this->juser->get('guest')) 
		{
			echo json_encode(array('error' => "Server error. Must be logged in to upload files."));
			return;
		}
		
		//get config
		$config =& JComponentHelper::getParams('com_media');

		// Incoming
		$listdir = JRequest::getInt('listdir', 0);

		//allowed extensions for uplaod
		$allowedExtensions = array_values(array_filter(explode(',', $config->get('upload_extensions'))));

		//max upload size
		$sizeLimit = $config->get('upload_maxsize');

		//get the file
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
			return;
		}

		// Build the upload path if it doesn't exist
		$uploadDirectory = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $listdir . DS;

		//make sure upload directory is writable
		if (!is_dir($uploadDirectory))
		{
			if (!JFolder::create($uploadDirectory))
			{
				echo json_encode(array('error' => "Server error. Unable to create upload directory."));
				return;
			}
		}
		if (!is_writable($uploadDirectory))
		{
			echo json_encode(array('error' => "Server error. Upload directory isn't writable."));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0) 
		{
			echo json_encode(array('error' => 'File is empty'));
			return;
		}
		if ($size > $sizeLimit) 
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Hubzero_View_Helper_Html::formatSize($sizeLimit));
			echo json_encode(array('error' => 'File is too large. Max file upload size is ' . $max));
			return;
		}

		//check to make sure we have an allowable extension
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];
		$ext = $pathinfo['extension'];
		if ($allowedExtensions && !in_array(strtolower($ext), $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			echo json_encode(array('error' => 'File has an invalid extension, it should be one of ' . $these . '.'));
			return;
		} 

		//final file
		$file = $uploadDirectory . $filename . '.' . $ext;

		//save file
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

		//return success
		echo json_encode(array('success'=>true));
		return;
	}
	
	
	/**
	 * Delete group file
	 * 
	 * @return     array
	 */
	public function deletefileTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->filebrowserTask();
			return;
		}

		// Incoming group ID
		$listdir = JRequest::getInt('listdir', 0, 'get');
		if (!$listdir) 
		{
			$this->setNotification(JText::_('GROUPS_NO_ID'), 'error');
			$this->filebrowserTask();
			return;
		}

		// Incoming file
		$file = trim(JRequest::getVar('file', '', 'get'));
		if (!$file) 
		{
			$this->setNotification(JText::_('GROUPS_NO_FILE'), 'error');
			$this->filebrowserTask();
			return;
		}

		// Build the file path
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/groups'), DS) . DS . $listdir;
		if (!file_exists($path . DS . $file) or !$file) 
		{
			$this->setNotification(JText::_('FILE_NOT_FOUND'), 'error');
			$this->filebrowserTask();
			return;
		} 
		else 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file)) 
			{
				$this->setNotification(JText::_('UNABLE_TO_DELETE_FILE'), 'error');
			}
		}

		//push a success message
		$this->setNotification('The file was successfully deleted.', 'passed');

		// Push through to the media view
		$this->filebrowserTask();
	}
}