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
 * Resources controller class
 */
class ResourcesControllerMedia extends \Hubzero\Component\SiteController
{
	/**
	 * Upload a file or create a new folder
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$resource = JRequest::getInt('resource', 0, 'post');
		if (!$resource)
		{
			$this->setError(JText::_('RESOURCES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		//$subdir = JRequest::getVar('dirPath', '', 'post');

		$row = new ResourcesResource($this->database);
		$row->load($resource);

		// allow for temp resource uploads
		if (!$row->created || $row->created == '0000-00-00 00:00:00')
		{
			$row->created = JFactory::getDate()->format('Y-m-d 00:00:00');
		}

		$path =  JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/resources'), DS) . ResourcesHtml::build_path($row->created, $resource, '') . DS . 'media';

		// Make sure the upload path exist
		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
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
			$this->setError(JText::_('ERROR_UPLOADING'));
		}

		$fpath = $path . DS . $file['name'];
		exec("clamscan -i --no-summary --block-encrypted $fpath", $output, $status);
		if ($status == 1)
		{
			JFile::delete($fpath);

			$this->setError(JText::_('File rejected because the anti-virus scan failed.'));
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
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$resource = JRequest::getInt('resource', 0);
		if (!$resource)
		{
			$this->setError(JText::_('RESOURCES_NO_LISTDIR'));
			$this->displayTask();
			return;
		}

		// Incoming sub-directory
		//$subdir = JRequest::getVar('dirPath', '', 'post');
		$row = new ResourcesResource($this->database);
		$row->load($resource);

		// Incoming sub-directory
		//$subdir = JRequest::getVar('subdir', '');

		// allow for temp resource uploads
		if (!$row->created || $row->created == '0000-00-00 00:00:00')
		{
			$row->created = JFactory::getDate()->format('Y-m-d 00:00:00');
		}

		$path =  JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/resources'), DS) . ResourcesHtml::build_path($row->created, $resource, '') . DS . 'media';

		// Make sure the listdir follows YYYY/MM/#
		$parts = explode('/', $path);
		if (count($parts) < 4)
		{
			$this->setError(JText::_('DIRECTORY_NOT_FOUND'));
			$this->displayTask();
			return;
		}

		// Incoming file to delete
		$file = JRequest::getVar('file', '');
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
		$this->view->setLayout('display');

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$this->view->resource = JRequest::getInt('resource', 0);
		if (!$this->view->resource)
		{
			echo '<p class="error">' . JText::_('No resource ID provided.') . '</p>';
			return;
		}

		// Incoming sub-directory
		$this->view->subdir = JRequest::getVar('subdir', '');

		// Build the path
		//$this->view->path = ResourcesUtilities::buildUploadPath($this->view->listdir, $this->view->subdir);
		$row = new ResourcesResource($this->database);
		$row->load($this->view->resource);

		// allow for temp resource uploads
		if (!$row->created || $row->created == '0000-00-00 00:00:00')
		{
			$row->created = JFactory::getDate()->format('Y-m-d 00:00:00');
		}

		$this->view->path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/resources'), DS) . ResourcesHtml::build_path($row->created, $this->view->resource, '') . DS . 'media';

		$folders = array();
		$docs    = array();

		if (is_dir($this->view->path))
		{
			// Loop through all files and separate them into arrays of images, folders, and other
			$dirIterator = new DirectoryIterator($this->view->path);
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

					$docs[$this->view->path . DS . $name] = $name;
				}
			}

			ksort($folders);
			ksort($docs);
		}

		$this->view->row = $row;
		$this->view->docs = $docs;
		$this->view->folders = $folders;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
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

	public function trackingTask()
	{
		//include need media tracking library
		require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'media.tracking.php';
		require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'media.tracking.detailed.php';

		//instantiate objects
		$juser    = JFactory::getUser();
		$database = JFactory::getDBO();
		$session  = JFactory::getSession();

		//get request vars
		$time       = JRequest::getVar('time', 0);
		$duration   = JRequest::getVar('duration', 0);
		$event      = JRequest::getVar('event', 'update');
		$resourceid = JRequest::getVar('resourceid', 0);
		$detailedId = JRequest::getVar('detailedTrackingId', 0);
		$ipAddress  = $_SERVER['REMOTE_ADDR'];

		//check for resource id
		if(!$resourceid)
		{
			echo 'Unable to find resource identifier.';
			return;
		}

		//instantiate new media tracking object
		$mediaTracking         = new ResourceMediaTracking( $database );
		$mediaTrackingDetailed = new ResourceMediaTrackingDetailed( $database );

		//load tracking information for user for this resource
		$trackingInformation         = $mediaTracking->getTrackingInformationForUserAndResource( $juser->get('id'), $resourceid );
		$trackingInformationDetailed = $mediaTrackingDetailed->loadByDetailId( $detailedId );

		//are we creating a new tracking record
		if(!is_object($trackingInformation))
		{
			$trackingInformation                              = new stdClass;
			$trackingInformation->user_id                     = $juser->get('id');
			$trackingInformation->session_id                  = $session->getId();
			$trackingInformation->ip_address                  = $ipAddress;
			$trackingInformation->object_id                   = $resourceid;
			$trackingInformation->object_type                 = 'resource';
			$trackingInformation->object_duration             = $duration;
			$trackingInformation->current_position            = $time;
			$trackingInformation->farthest_position           = $time;
			$trackingInformation->current_position_timestamp  = JFactory::getDate()->toSql();
			$trackingInformation->farthest_position_timestamp = JFactory::getDate()->toSql();
			$trackingInformation->completed                   = 0;
			$trackingInformation->total_views                 = 1;
			$trackingInformation->total_viewing_time          = 0;
		}
		else
		{
			//get the amount of video watched from last tracking event
			$time_viewed = (int)$time - (int)$trackingInformation->current_position;

			//if we have a positive value and its less then our ten second threshold
			//add viewing time to total watched time
			if ($time_viewed < 10 && $time_viewed > 0)
			{
				$trackingInformation->total_viewing_time += $time_viewed;
			}

			//set the new current position
			$trackingInformation->current_position           = $time;
			$trackingInformation->current_position_timestamp = JFactory::getDate()->toSql();

			//set the object duration
			if($duration > 0)
			{
				$trackingInformation->object_duration = $duration;
			}

			//check to see if we need to set a new farthest position
			if($trackingInformation->current_position > $trackingInformation->farthest_position)
			{
				$trackingInformation->farthest_position           = $time;
				$trackingInformation->farthest_position_timestamp = JFactory::getDate()->toSql();
			}

			//if event type is start, means we need to increment view count
			if($event == 'start' || $event == 'replay')
			{
				$trackingInformation->total_views++;
			}

			//if event type is end, we need to increment completed count
			if($event == 'ended')
			{
				$trackingInformation->completed++;
			}
		}

		// save detailed tracking info
		if($event == 'start' || !$trackingInformationDetailed)
		{
			$trackingInformationDetailed                              = new stdClass;
			$trackingInformationDetailed->user_id                     = $juser->get('id');
			$trackingInformationDetailed->session_id                  = $session->getId();
			$trackingInformationDetailed->ip_address                  = $ipAddress;
			$trackingInformationDetailed->object_id                   = $resourceid;
			$trackingInformationDetailed->object_type                 = 'resource';
			$trackingInformationDetailed->object_duration             = $duration;
			$trackingInformationDetailed->current_position            = $time;
			$trackingInformationDetailed->farthest_position           = $time;
			$trackingInformationDetailed->current_position_timestamp  = JFactory::getDate()->toSql();
			$trackingInformationDetailed->farthest_position_timestamp = JFactory::getDate()->toSql();
			$trackingInformationDetailed->completed                   = 0;
		}
		else
		{
			//set the new current position
			$trackingInformationDetailed->current_position           = $time;
			$trackingInformationDetailed->current_position_timestamp = JFactory::getDate()->toSql();

			//set the object duration
			if($duration > 0)
			{
				$trackingInformationDetailed->object_duration = $duration;
			}

			//check to see if we need to set a new farthest position
			if(isset($trackingInformationDetailed->farthest_position) && $trackingInformationDetailed->current_position > $trackingInformationDetailed->farthest_position)
			{
				$trackingInformationDetailed->farthest_position           = $time;
				$trackingInformationDetailed->farthest_position_timestamp = JFactory::getDate()->toSql();
			}

			//if event type is end, we need to increment completed count
			if($event == 'ended')
			{
				$trackingInformationDetailed->completed++;
			}
		}

		//save detailed
		$mediaTrackingDetailed->save( $trackingInformationDetailed );

		//save tracking information
		if( $mediaTracking->save($trackingInformation) )
		{
			if(!isset($trackingInformation->id))
			{
				$trackingInformation->id = $mediaTracking->id;
			}
			$trackingInformation->detailedId = $mediaTrackingDetailed->id;
			echo json_encode( $trackingInformation );
		}
	}
}