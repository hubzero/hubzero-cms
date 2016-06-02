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

namespace Components\Resources\Site\Controllers;

use Components\Resources\Models\Orm\Resource;
use Components\Resources\Tables\MediaTracking;
use Components\Resources\Tables\MediaTrackingDetailed;
use Hubzero\Component\SiteController;
use Filesystem;
use stdClass;
use Request;
use Date;
use User;
use Lang;
use App;

// Include need media tracking library
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'resource.php';

/**
 * Resources controller class for media
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
			$this->setError(Lang::txt('RESOURCES_NO_LISTDIR'));
			return $this->displayTask();
		}

		if ($resource < 1 || substr($resource, 0, 4) == '9999')
		{
			$row = Resource::blank();
		}
		else
		{
			$row = Resource::oneOrFail($resource);
		}
		$row->set('id', $resource);

		// Allow for temp resource uploads
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
				$this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask();
			}
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('RESOURCES_NO_FILE'));
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
			$this->setError(Lang::txt('ERROR_UPLOADING'));
		}

		// Virus check
		if (!Filesystem::isSafe($path))
		{
			Filesystem::delete($path);

			$this->setError(Lang::txt('File rejected because the anti-virus scan failed.'));
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
			$this->setError(Lang::txt('RESOURCES_NO_LISTDIR'));
			return $this->displayTask();
		}

		if ($resource < 1 || substr($resource, 0, 4) == '9999')
		{
			$row = Resource::blank();
		}
		else
		{
			$row = Resource::oneOrFail($resource);
		}
		$row->set('id', $resource);

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
			$this->setError(Lang::txt('RESOURCES_NO_FILE'));
			return $this->displayTask();
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file))
		{
			$this->setError(Lang::txt('FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
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

		if ($resource < 1 || substr($resource, 0, 4) == '9999')
		{
			$row = Resource::blank();
		}
		else
		{
			$row = Resource::oneOrFail($resource);
		}
		$row->set('id', $resource);

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

		// Output the HTML
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
				if (is_dir($base . DS . $dir)
				 && $dir !== '.'
				 && $dir !== '..'
				 && strtolower($dir) !== 'cvs')
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

	/**
	 * Record information for video tracking
	 *
	 * @return  void
	 */
	public function trackingTask()
	{
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'mediatracking.php';
		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'mediatrackingdetailed.php';

		// Instantiate objects
		$database = App::get('db');
		$session  = App::get('session');

		// Get request vars
		$time       = Request::getVar('time', 0);
		$duration   = Request::getVar('duration', 0);
		$event      = Request::getVar('event', 'update');
		$resourceid = Request::getVar('resourceid', 0);
		$detailedId = Request::getVar('detailedTrackingId', 0);
		$ipAddress  = Request::ip();

		// Check for resource id
		if (!$resourceid)
		{
			echo 'Unable to find resource identifier.';
			return;
		}

		// Instantiate new media tracking object
		$mediaTracking         = new MediaTracking($database);
		$mediaTrackingDetailed = new MediaTrackingDetailed($database);

		// Load tracking information for user for this resource
		$trackingInformation         = $mediaTracking->getTrackingInformationForUserAndResource(User::get('id'), $resourceid);
		$trackingInformationDetailed = $mediaTrackingDetailed->loadByDetailId($detailedId);

		// Are we creating a new tracking record?
		if (!is_object($trackingInformation))
		{
			$trackingInformation = new stdClass;
			$trackingInformation->user_id                     = User::get('id');
			$trackingInformation->session_id                  = $session->getId();
			$trackingInformation->ip_address                  = $ipAddress;
			$trackingInformation->object_id                   = $resourceid;
			$trackingInformation->object_type                 = 'resource';
			$trackingInformation->object_duration             = $duration;
			$trackingInformation->current_position            = $time;
			$trackingInformation->farthest_position           = $time;
			$trackingInformation->current_position_timestamp  = Date::toSql();
			$trackingInformation->farthest_position_timestamp = Date::toSql();
			$trackingInformation->completed                   = 0;
			$trackingInformation->total_views                 = 1;
			$trackingInformation->total_viewing_time          = 0;
		}
		else
		{
			// Get the amount of video watched from last tracking event
			$time_viewed = (int)$time - (int)$trackingInformation->current_position;

			// If we have a positive value and its less then our ten second threshold
			// add viewing time to total watched time
			if ($time_viewed < 10 && $time_viewed > 0)
			{
				$trackingInformation->total_viewing_time += $time_viewed;
			}

			// Set the new current position
			$trackingInformation->current_position           = $time;
			$trackingInformation->current_position_timestamp = Date::toSql();

			// Set the object duration
			if ($duration > 0)
			{
				$trackingInformation->object_duration = $duration;
			}

			// Check to see if we need to set a new farthest position
			if ($trackingInformation->current_position > $trackingInformation->farthest_position)
			{
				$trackingInformation->farthest_position           = $time;
				$trackingInformation->farthest_position_timestamp = Date::toSql();
			}

			// If event type is start, means we need to increment view count
			if ($event == 'start' || $event == 'replay')
			{
				$trackingInformation->total_views++;
			}

			// If event type is end, we need to increment completed count
			if ($event == 'ended')
			{
				$trackingInformation->completed++;
			}
		}

		// Save detailed tracking info
		if ($event == 'start' || !$trackingInformationDetailed)
		{
			$trackingInformationDetailed = new stdClass;
			$trackingInformationDetailed->user_id                     = User::get('id');
			$trackingInformationDetailed->session_id                  = $session->getId();
			$trackingInformationDetailed->ip_address                  = $ipAddress;
			$trackingInformationDetailed->object_id                   = $resourceid;
			$trackingInformationDetailed->object_type                 = 'resource';
			$trackingInformationDetailed->object_duration             = $duration;
			$trackingInformationDetailed->current_position            = $time;
			$trackingInformationDetailed->farthest_position           = $time;
			$trackingInformationDetailed->current_position_timestamp  = Date::toSql();
			$trackingInformationDetailed->farthest_position_timestamp = Date::toSql();
			$trackingInformationDetailed->completed                   = 0;
		}
		else
		{
			// Set the new current position
			$trackingInformationDetailed->current_position           = $time;
			$trackingInformationDetailed->current_position_timestamp = Date::toSql();

			// Set the object duration
			if ($duration > 0)
			{
				$trackingInformationDetailed->object_duration = $duration;
			}

			// Check to see if we need to set a new farthest position
			if (isset($trackingInformationDetailed->farthest_position) && $trackingInformationDetailed->current_position > $trackingInformationDetailed->farthest_position)
			{
				$trackingInformationDetailed->farthest_position           = $time;
				$trackingInformationDetailed->farthest_position_timestamp = Date::toSql();
			}

			// If event type is end, we need to increment completed count
			if ($event == 'ended')
			{
				$trackingInformationDetailed->completed++;
			}
		}

		// Save detailed
		$mediaTrackingDetailed->save($trackingInformationDetailed);

		// Save tracking information
		if ($mediaTracking->save($trackingInformation))
		{
			if (!isset($trackingInformation->id))
			{
				$trackingInformation->id = $mediaTracking->id;
			}
			$trackingInformation->detailedId = $mediaTrackingDetailed->id;

			echo json_encode($trackingInformation);
		}
	}
}