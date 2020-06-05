<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Site\Controllers;

use Components\Resources\Models\Entry;
use Components\Resources\Models\MediaTracking;
use Components\Resources\Models\MediaTracking\Detailed;
use Hubzero\Component\SiteController;
use Filesystem;
use stdClass;
use Request;
use Date;
use User;
use Lang;
use App;

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
			$row = Entry::blank();
		}
		else
		{
			$row = Entry::oneOrFail($resource);
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
		$file = Request::getArray('upload', '', 'files');
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
			$row = Entry::blank();
		}
		else
		{
			$row = Entry::oneOrFail($resource);
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
		$file = Request::getString('file', '');

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
			$row = Entry::blank();
		}
		else
		{
			$row = Entry::oneOrFail($resource);
		}
		$row->set('id', $resource);

		// Incoming sub-directory
		$subdir = Request::getString('subdir', '');

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
		require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'mediatracking.php';
		require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'mediatracking' . DS . 'detailed.php';

		// Instantiate objects
		$database = App::get('db');
		$session  = App::get('session');

		// Get request vars
		$time       = Request::getInt('time', 0);
		$duration   = Request::getInt('duration', 0);
		$event      = Request::getWord('event', 'update');
		$resourceid = Request::getInt('resourceid', 0);
		$detailedId = Request::getInt('detailedTrackingId', 0);
		$ipAddress  = Request::ip();

		// Check for resource id
		if (!$resourceid)
		{
			echo 'Unable to find resource identifier.';
			return;
		}

		// Load tracking information for user for this resource
		$trackingInformation         = MediaTracking::oneByUserAndResource(User::get('id'), $resourceid);
		$trackingInformationDetailed = Detailed::oneOrNew($detailedId);

		// Are we creating a new tracking record?
		if ($trackingInformation->isNew())
		{
			$trackingInformation->set(array(
				'user_id'                     => User::get('id'),
				'session_id'                  => $session->getId(),
				'ip_address'                  => $ipAddress,
				'object_id'                   => $resourceid,
				'object_type'                 => 'resource',
				'object_duration'             => $duration,
				'current_position'            => $time,
				'farthest_position'           => $time,
				'current_position_timestamp'  => Date::toSql(),
				'farthest_position_timestamp' => Date::toSql(),
				'completed'                   => 0,
				'total_views'                 => 1,
				'total_viewing_time'          => 0
			));
		}
		else
		{
			// Get the amount of video watched from last tracking event
			$time_viewed = (int)$time - (int)$trackingInformation->get('current_position');

			// If we have a positive value and its less then our ten second threshold
			// add viewing time to total watched time
			if ($time_viewed < 10 && $time_viewed > 0)
			{
				$trackingInformation->set('total_viewing_time', $trackingInformation->get('total_viewing_time') + $time_viewed);
			}

			// Set the new current position
			$trackingInformation->set('current_position', $time);
			$trackingInformation->set('current_position_timestamp', Date::toSql());

			// Set the object duration
			if ($duration > 0)
			{
				$trackingInformation->set('object_duration', $duration);
			}

			// Check to see if we need to set a new farthest position
			if ($trackingInformation->get('current_position') > $trackingInformation->get('farthest_position'))
			{
				$trackingInformation->set('farthest_position', $time);
				$trackingInformation->set('farthest_position_timestamp', Date::toSql());
			}

			// If event type is start, means we need to increment view count
			if ($event == 'start' || $event == 'replay')
			{
				$trackingInformation->set('total_views', $trackingInformation->get('total_views') + 1);
			}

			// If event type is end, we need to increment completed count
			if ($event == 'ended')
			{
				$trackingInformation->set('completed', $trackingInformation->get('completed') + 1);
			}
		}

		// Save detailed tracking info
		if ($event == 'start' || $trackingInformationDetailed->isNew())
		{
			$trackingInformationDetailed->set(array(
				'user_id'                     => User::get('id'),
				'session_id'                  => $session->getId(),
				'ip_address'                  => $ipAddress,
				'object_id'                   => $resourceid,
				'object_type'                 => 'resource',
				'object_duration'             => $duration,
				'current_position'            => $time,
				'farthest_position'           => $time,
				'current_position_timestamp'  => Date::toSql(),
				'farthest_position_timestamp' => Date::toSql(),
				'completed'                   => 0
			));
		}
		else
		{
			// Set the new current position
			$trackingInformationDetailed->set('current_position', $time);
			$trackingInformationDetailed->set('current_position_timestamp', Date::toSql());

			// Set the object duration
			if ($duration > 0)
			{
				$trackingInformationDetailed->set('object_duration', $duration);
			}

			// Check to see if we need to set a new farthest position
			if ($trackingInformationDetailed->get('current_position') > $trackingInformationDetailed->get('farthest_position'))
			{
				$trackingInformationDetailed->set('farthest_position', $time);
				$trackingInformationDetailed->set('farthest_position_timestamp', Date::toSql());
			}

			// If event type is end, we need to increment completed count
			if ($event == 'ended')
			{
				$trackingInformationDetailed->set('completed', $trackingInformationDetailed->get('completed') + 1);
			}
		}

		// Save detailed
		$trackingInformationDetailed->save();

		// Save tracking information
		if ($trackingInformation->save())
		{
			$trackingInformation = $trackingInformation->toObject();
			$trackingInformation->detailedId = $trackingInformationDetailed->get('id');

			echo json_encode($trackingInformation);
		}
	}
}
