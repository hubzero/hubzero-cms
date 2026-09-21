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
use Component;

/**
 * Resources controller class for media
 */
class Media extends SiteController
{
	/**
	 * Does this id have the shape of an in-progress contribution id?
	 *
	 * The bypass used to be a "9999" string prefix, which also matched real
	 * resource 9999 and every id in 99990-99999, 999900-999999 and so on, and a
	 * negative id slipped through the "< 1" half. This narrows it to the shape
	 * create.php actually generates, which is what closes those collisions.
	 *
	 * It is a shape test and nothing more. Binding it to the temp id the
	 * contribute flow stores in the session (resources_temp_id, set in
	 * create.php) would be stronger -- the generator is rand(1000, 10000), so
	 * the whole 9 001-value namespace is enumerable and any logged-in user can
	 * still list, overwrite or delete another user's in-progress contribution
	 * media by guessing one. That was tried and reverted: the session value is
	 * absent on the paths the media iframe is reached from, so comparing
	 * against it refused genuine contributors. The session binding is the right
	 * fix and needs the contribute flow driven end to end to land safely.
	 *
	 * @param   mixed  $resource  Requested resource id
	 * @return  boolean
	 */
	protected function _isTempId($resource)
	{
		// create.php issues these as '9999' . rand(1000, 10000), so they are
		// "9999" followed by four or five digits. The old test was a bare
		// substr() prefix match, which also admitted real resource 9999 and
		// every id in 99990-99999, 999900-999999 and beyond -- those resources
		// were writable and deletable by any logged-in user. Matching the
		// generator's actual shape keeps the contribution flow working while
		// putting the collision far above any plausible resource id.
		return (bool) preg_match('/^9999[0-9]{4,5}$/', (string) $resource);
	}

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

		if ($this->_isTempId($resource))
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

		// Uploads and deletes require a contributor who may edit this resource.
		// An in-progress contribution is recognised by the shape of its temp id
		// -- see _isTempId(), which records what that does and does not buy.
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			return;
		}
		if (!($this->_isTempId($resource))
			&& !$row->access('edit') && !$row->access('edit-own')
			&& !User::authorise('core.manage', 'com_resources'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			return;
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

		// Refuse server-executable extensions and the markup types the download
		// handler serves inline -- the same rule as resource attachments.
		//
		// This is a deny-list, so .svg, .js and double extensions such as
		// foo.php.jpg all pass; it closes code execution and inline HTML in the
		// hub origin, not upload typing in general. An allow-list would be
		// better and is worth doing, but com_media's list is not the one to
		// borrow: it is a media-manager list, not a resource list.
		$blockedExtensions = array('php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'pht', 'phar', 'phps', 'cgi', 'pl', 'asp', 'aspx', 'jsp', 'shtml', 'htaccess', 'htpasswd', 'html', 'htm', 'xhtml', 'xml');
		if (in_array(strtolower((string) $ext), $blockedExtensions))
		{
			$this->setError(Lang::txt('File type not allowed: %s', $ext));
			return $this->displayTask();
		}

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

		if ($this->_isTempId($resource))
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

		// Uploads and deletes require a contributor who may edit this resource.
		// An in-progress contribution is recognised by the shape of its temp id
		// -- see _isTempId(), which records what that does and does not buy.
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			return;
		}
		if (!($this->_isTempId($resource))
			&& !$row->access('edit') && !$row->access('edit-own')
			&& !User::authorise('core.manage', 'com_resources'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			return;
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

		// Keep the target within the resource media directory
		if (strpos($file, '..') !== false)
		{
			$this->setError(Lang::txt('FILE_NOT_FOUND'));
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

		if ($this->_isTempId($resource))
		{
			$row = Entry::blank();
		}
		else
		{
			$row = Entry::oneOrFail($resource);
		}
		$row->set('id', $resource);

		// Same rule as the upload and delete tasks above, which reach this view
		// after their own check: a contributor who may edit the resource, or any
		// logged-in user while the contribution is still a temp id.
		if (User::isGuest())
		{
			echo '<p class="error">' . Lang::txt('JERROR_ALERTNOAUTHOR') . '</p>';
			return;
		}
		if (!($this->_isTempId($resource))
			&& !$row->access('edit') && !$row->access('edit-own')
			&& !User::authorise('core.manage', 'com_resources'))
		{
			echo '<p class="error">' . Lang::txt('JERROR_ALERTNOAUTHOR') . '</p>';
			return;
		}

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
