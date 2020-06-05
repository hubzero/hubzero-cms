<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Courses\Models\Course;
use Components\Resources\Models\MediaTracking\Detailed;
use Components\Resources\Models\MediaTracking;
use stdClass;
use Filesystem;
use Request;
use Notify;
use Date;
use User;
use Lang;

/**
 * Courses controller class for media
 */
class Media extends SiteController
{
	/**
	 * Track video viewing progress
	 *
	 * @return  void
	 */
	public function trackingTask()
	{
		if (!file_exists(\Component::path('com_resources') . DS . 'models' . DS . 'mediatracking.php'))
		{
			return;
		}

		// Include need media tracking library
		require_once \Component::path('com_resources') . DS . 'models' . DS . 'mediatracking.php';
		require_once \Component::path('com_resources') . DS . 'models' . DS . 'mediatracking' . DS . 'detailed.php';

		// Instantiate objects
		$database = \App::get('db');
		$session  = \App::get('session');

		// Get request vars
		$time       = Request::getInt('time', 0);
		$duration   = Request::getInt('duration', 0);
		$event      = Request::getString('event', 'update');
		$resourceid = Request::getInt('resourceid', 0);
		$detailedId = Request::getInt('detailedTrackingId', 0);
		$ipAddress  = $_SERVER['REMOTE_ADDR'];

		// Check for asset id
		if (!$resourceid)
		{
			echo 'Unable to find resource identifier.';
			return;
		}

		// Load tracking information for user for this resource
		$trackingInformation         = MediaTracking::oneByUserAndResource(User::get('id'), $resourceid, 'course');
		$trackingInformationDetailed = Detailed::oneOrNew($detailedId);

		// Are we creating a new tracking record
		if ($trackingInformation->isNew())
		{
			$trackingInformation->set(array(
				'user_id'                     => User::get('id'),
				'session_id'                  => $session->getId(),
				'ip_address'                  => $ipAddress,
				'object_id'                   => $resourceid,
				'object_type'                 => 'course',
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

			// If we have a positive value and its less then our ten second threshold,
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
				'object_type'                 => 'course',
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

	/**
	 * Upload a file
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		Request::checkToken(['get', 'post']);

		if (Request::getInt('no_html'))
		{
			return $this->ajaxuploadTask();
		}

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->displayTask();
		}

		// Incoming
		$listdir = Request::getInt('listdir', 0, 'post');

		// Ensure we have an ID to work with
		if (!$listdir)
		{
			Notify::error(Lang::txt('COURSES_NO_ID'), 'courses');
			return $this->displayTask();
		}

		// Incoming file
		$file = Request::getArray('upload', array(), 'files');
		if (empty($file) || !$file['name'])
		{
			Notify::error(Lang::txt('COURSES_NO_FILE'), 'courses');
			return $this->displayTask();
		}

		// Get media config
		$mediaConfig = \Component::params('com_media');

		// Size limit is in MB, so we need to turn it into just B
		$sizeLimit = $mediaConfig->get('upload_maxsize', 10);
		$sizeLimit = $sizeLimit * 1024 * 1024;

		if ($file['size'] > $sizeLimit)
		{
			Notify::error(Lang::txt('COM_COURSES_ERROR_UPLOADING_FILE_TOO_BIG', \Hubsero\Utility\Number::formatBytes($sizeLimit)));
			return $this->displayTask();
		}

		// Build the upload path if it doesn't exist
		$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . trim($listdir, DS);

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				Notify::error(Lang::txt('COM_COURSES_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask();
			}
		}

		// Make the filename safe
		$file['name'] = urldecode($file['name']);
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = Filesystem::extension($file['name']);

		// Check that the file type is allowed
		$allowed = array('png', 'gif', 'jpg', 'jpeg', 'jpe', 'jp2', 'jpx');

		if (!empty($allowed) && !in_array(strtolower($ext), $allowed))
		{
			Notify::error(Lang::txt('COM_COURSES_ERROR_UPLOADING_INVALID_FILE', implode(', ', $allowed)));
			return $this->displayTask();
		}

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			Notify::error(Lang::txt('COM_COURSES_ERROR_UPLOADING'));
		}
		else
		{
			if (!Filesystem::isSafe($path . DS . $file['name']))
			{
				Filesystem::delete($path . DS . $file['name']);

				Notify::error(Lang::txt('File rejected because the anti-virus scan failed.'));
			}
			else
			{
				// Instantiate a model, change some info and save
				$model = Course::getInstance($listdir);

				// Do we have an old file we're replacing?
				if ($curfile = $model->get('logo'))
				{
					// Remove old image
					if (file_exists($path . DS . $curfile))
					{
						if (!Filesystem::delete($path . DS . $curfile))
						{
							Notify::error(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_DELETE_FILE'));
							return $this->displayTask();
						}
					}
				}

				$model->set('logo', $filename . '.' . $ext);
				if (!$model->store())
				{
					Notify::error($model->getError());
					return $this->displayTask();
				}

				//push a success message
				Notify::success(Lang::txt('You successfully uploaded the file.'));
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Streaming file upload
	 * This is used by AJAX
	 *
	 * @return  void
	 */
	private function ajaxuploadTask()
	{
		// Incoming
		$listdir = Request::getInt('listdir', 0);
		if (!$listdir)
		{
			echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_NO_ID')));
			return;
		}

		// Allowed extensions for uplaod
		$allowedExtensions = array('png', 'jpe', 'jpeg', 'jpg', 'gif', 'jp2', 'jpx');

		// Get media config
		$mediaConfig = \Component::params('com_media');

		// Size limit is in MB, so we need to turn it into just B
		$sizeLimit = $mediaConfig->get('upload_maxsize', 10);
		$sizeLimit = $sizeLimit * 1024 * 1024;

		// Get the file
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
		$uploadDirectory = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $listdir . DS;

		// make sure upload directory is writable
		if (!is_dir($uploadDirectory))
		{
			if (!Filesystem::makeDirectory($uploadDirectory))
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

		// check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => 'File is empty'));
			return;
		}

		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => 'File is too large. Max file upload size is ' . $max));
			return;
		}

		//check to make sure we have an allowable extension
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];
		$ext      = $pathinfo['extension'];
		if ($allowedExtensions && !in_array(strtolower($ext), $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			echo json_encode(array('error' => 'File has an invalid extension, it should be one of ' . $these . '.'));
			return;
		}

		// Make the filename safe
		$filename = urldecode($filename);
		$filename = Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);

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
			$target = fopen($file, "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		if (!Filesystem::isSafe($file))
		{
			Filesystem::delete($file);

			echo json_encode(array('error' => Lang::txt('File rejected because the anti-virus scan failed.')));
			return;
		}

		// Instantiate a model, change some info and save
		$model = Course::getInstance($listdir);

		// Do we have an old file we're replacing?
		if ($curfile = $model->get('logo'))
		{
			// Remove old image
			if (file_exists($uploadDirectory . $curfile))
			{
				if (!Filesystem::delete($uploadDirectory . $curfile))
				{
					echo json_encode(array('error' => Lang::txt('COM_COURSES_ERROR_UNABLE_TO_DELETE_FILE')));
					return;
				}
			}
		}

		$model->set('logo', $filename . '.' . $ext);
		if (!$model->store())
		{
			echo json_encode(array('error' => $model->getError()));
			return;
		}

		$this_size = filesize($file);
		list($width, $height, $type, $attr) = getimagesize($file);

		echo json_encode(array(
			'success'   => true,
			'file'      => rtrim(Request::root(true), DS) . str_replace(PATH_ROOT, '', $file),
			'id'        => $listdir,
			'size'      => \Hubzero\Utility\Number::formatBytes($this_size),
			'width'     => $width,
			'height'    => $height
		));
	}

	/**
	 * Delete a folder
	 *
	 * @return  void
	 */
	public function deletefolderTask()
	{
		Request::checkToken(['get', 'post']);

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->displayTask();
		}

		// Incoming course ID
		$listdir = Request::getInt('listdir', 0, 'get');
		if (!$listdir)
		{
			Notify::error(Lang::txt('COURSES_NO_ID'));
			return $this->displayTask();
		}

		// Incoming file
		$folder = trim(Request::getString('folder', '', 'get'));
		if (!$folder)
		{
			Notify::error(Lang::txt('COURSES_NO_DIRECTORY'));
			return $this->displayTask();
		}

		$del_folder = DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . trim($listdir, DS) . DS . ltrim($folder, DS);

		// Delete the folder
		if (is_dir(PATH_APP . $del_folder))
		{
			// Attempt to delete the file
			if (!Filesystem::deleteDirectory(PATH_APP . $del_folder))
			{
				Notify::error(Lang::txt('UNABLE_TO_DELETE_DIRECTORY'));
			}
			else
			{
				//push a success message
				Notify::success(Lang::txt('You successfully deleted the folder.'));
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function deletefileTask()
	{
		Request::checkToken(['get', 'post']);

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->displayTask();
		}

		// Incoming course ID
		$listdir = Request::getInt('listdir', 0, 'get');
		if (!$listdir)
		{
			Notify::error(Lang::txt('COURSES_NO_ID'));
			return $this->displayTask();
		}

		// Incoming file
		$file = trim(Request::getString('file', '', 'get'));
		if (!$file)
		{
			Notify::error(Lang::txt('COURSES_NO_FILE'));
			return $this->displayTask();
		}

		// Build the file path
		$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $listdir;

		if (!file_exists($path . DS . $file) or !$file)
		{
			Notify::error(Lang::txt('FILE_NOT_FOUND'));
			return $this->displayTask();
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				Notify::error(Lang::txt('UNABLE_TO_DELETE_FILE'), 'courses');
			}
		}

		//push a success message
		Notify::success(Lang::txt('The file was successfully deleted.'));

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Show a form for uploading and managing files
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$listdir = Request::getInt('listdir', 0);

		if (!$listdir)
		{
			Notify::error(Lang::txt('COURSES_NO_ID'), 'courses');
		}

		$course = Course::getInstance($listdir);

		// Output HTML
		$this->view
			->set('config', $config)
			->set('course', $course)
			->set('listdir', $listdir)
			->setLayout('media')
			->display();
	}

	/**
	 * List files for a course
	 *
	 * @return  void
	 */
	public function listfilesTask()
	{
		// Incoming
		$listdir = Request::getInt('listdir', 0, 'get');

		// Check if coming from another function
		if ($listdir == '')
		{
			$listdir = $this->listdir;
		}

		if (!$listdir)
		{
			Notify::error(Lang::txt('COURSES_NO_ID'), 'courses');
		}

		$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $listdir;

		// Get the directory we'll be reading out of
		$d = @dir($path);

		$images  = array();
		$folders = array();
		$docs    = array();

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
						$images[$entry] = $img_file;
					}
					else
					{
						$docs[$entry] = $img_file;
					}
				}
				else if (is_dir($path . DS . $img_file)
				 && substr($entry, 0, 1) != '.'
				 && strtolower($entry) !== '..'
				 && strtolower($entry) !== '.git'
				 && strtolower($entry) !== 'cvs')
				{
					$folders[$entry] = $img_file;
				}
			}

			$d->close();

			ksort($images);
			ksort($folders);
			ksort($docs);
		}

		$this->view
			->set('docs', $docs)
			->set('folders', $folders)
			->set('images', $images)
			->set('config', $config)
			->set('listdir', $listdir)
			->display();
	}

	/**
	 * Download a file
	 *
	 * @param   string  $filename  File name
	 * @return  void
	 */
	public function downloadTask($filename)
	{
		//get the course
		$course = Course::getInstance(Request::getInt('id'));

		//get the file name
		if (substr(strtolower($filename), 0, 5) == 'image')
		{
			$file = urldecode(substr($filename, 6));
		}
		elseif (substr(strtolower($filename), 0, 4) == 'file')
		{
			$file = urldecode(substr($filename, 5));
		}

		//check to make sure we can access it
		if (!in_array(User::get('id'), $course->get('members')) || User::isGuest())
		{
			return App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH') . ' ' . $file);
		}

		// Build the path
		$base_path = $this->config->get('uploadpath') . DS . $course->get('id');

		// Final path of file
		$file_path = $base_path . DS . $file;

		// Ensure the file exist
		if (!file_exists(PATH_APP . DS . $file_path))
		{
			return App::abort(404, Lang::txt('COM_COURSES_FILE_NOT_FOUND') . ' ' . $file);
		}

		// Serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename(PATH_APP . DS . $file_path);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			return App::abort(404, Lang::txt('COM_COURSES_SERVER_ERROR'));
		}

		exit;
	}
}
