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

namespace Components\Courses\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Courses\Models\Course;
use Components\Resources\Tables\MediaTrackingDetailed;
use Components\Resources\Tables\MediaTracking;
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
	 * @return     void
	 */
	public function trackingTask()
	{
		// Include need media tracking library
		require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'mediatracking.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'mediatrackingdetailed.php');

		// Instantiate objects
		$database = \App::get('db');
		$session  = \App::get('session');

		// Get request vars
		$time       = Request::getVar('time', 0);
		$duration   = Request::getVar('duration', 0);
		$event      = Request::getVar('event', 'update');
		$resourceid = Request::getVar('resourceid', 0);
		$detailedId = Request::getVar('detailedTrackingId', 0);
		$ipAddress  = $_SERVER['REMOTE_ADDR'];

		// Check for asset id
		if (!$resourceid)
		{
			echo 'Unable to find resource identifier.';
			return;
		}

		// Instantiate new media tracking object
		$mediaTracking         = new MediaTracking($database);
		$mediaTrackingDetailed = new MediaTrackingDetailed($database);

		// Load tracking information for user for this resource
		$trackingInformation         = $mediaTracking->getTrackingInformationForUserAndResource(User::get('id'), $resourceid, 'course');
		$trackingInformationDetailed = $mediaTrackingDetailed->loadByDetailId($detailedId);

		// Are we creating a new tracking record
		if (!is_object($trackingInformation))
		{
			$trackingInformation                              = new stdClass;
			$trackingInformation->user_id                     = User::get('id');
			$trackingInformation->session_id                  = $session->getId();
			$trackingInformation->ip_address                  = $ipAddress;
			$trackingInformation->object_id                   = $resourceid;
			$trackingInformation->object_type                 = 'course';
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

			// If we have a positive value and its less then our ten second threshold,
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
			$trackingInformationDetailed                              = new stdClass;
			$trackingInformationDetailed->user_id                     = User::get('id');
			$trackingInformationDetailed->session_id                  = $session->getId();
			$trackingInformationDetailed->ip_address                  = $ipAddress;
			$trackingInformationDetailed->object_id                   = $resourceid;
			$trackingInformationDetailed->object_type                 = 'course';
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

	/**
	 * Upload a file
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			$this->mediaTask();
			return;
		}

		// Incoming
		$listdir = Request::getInt('listdir', 0, 'post');

		// Ensure we have an ID to work with
		if (!$listdir)
		{
			Notify::error(Lang::txt('COURSES_NO_ID'), 'courses');
			$this->mediaTask();
			return;
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			Notify::error(Lang::txt('COURSES_NO_FILE'), 'courses');
			$this->mediaTask();
			return;
		}

		// Build the upload path if it doesn't exist
		$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . trim($listdir, DS);

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				Notify::error(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'), 'courses');
				$this->mediaTask();
				return;
			}
		}

		// Make the filename safe
		$file['name'] = urldecode($file['name']);
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			Notify::error(Lang::txt('ERROR_UPLOADING'), 'courses');
		}

		if (!Filesystem::isSafe($path . DS . $file['name']))
		{
			Filesystem::delete($path . DS . $file['name']);

			Notify::error(Lang::txt('File rejected because the anti-virus scan failed.'), 'courses');
		}
		else
		{
			//push a success message
			Notify::success(Lang::txt('You successfully uploaded the file.'), 'courses');
		}

		// Push through to the media view
		$this->mediaTask();
	}

	/**
	 * Streaking file upload
	 * This is used by AJAX
	 *
	 * @return     void
	 */
	private function ajaxuploadTask()
	{
		// get config
		$config = Component::params('com_media');

		// Incoming
		$listdir = Request::getInt('listdir', 0);

		// allowed extensions for uplaod
		$allowedExtensions = array_values(array_filter(explode(',', $config->get('upload_extensions'))));

		// max upload size
		$sizeLimit = $config->get('upload_maxsize');
		$sizeLimit = $sizeLimit * 1024 * 1024;

		// get the file
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

		//make sure upload directory is writable
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

		//check to make sure we have a file and its not too big
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

		if (!Filesystem::isSafe($file))
		{
			Filesystem::delete($file);

			echo json_encode(array('error' => Lang::txt('File rejected because the anti-virus scan failed.')));
			return;
		}

		//return success
		echo json_encode(array('success'=>true));
		return;
	}

	/**
	 * Delete a folder
	 *
	 * @return     void
	 */
	public function deletefolderTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			$this->mediaTask();
			return;
		}

		// Incoming course ID
		$listdir = Request::getInt('listdir', 0, 'get');
		if (!$listdir)
		{
			Notify::error(Lang::txt('COURSES_NO_ID'), 'courses');
			$this->mediaTask();
			return;
		}

		// Incoming file
		$folder = trim(Request::getVar('folder', '', 'get'));
		if (!$folder)
		{
			Notify::error(Lang::txt('COURSES_NO_DIRECTORY'), 'courses');
			$this->mediaTask();
			return;
		}

		$del_folder = DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . trim($listdir, DS) . DS . ltrim($folder, DS);

		// Delete the folder
		if (is_dir(PATH_APP . $del_folder))
		{
			// Attempt to delete the file
			if (!Filesystem::deleteDirectory(PATH_APP . $del_folder))
			{
				Notify::error(Lang::txt('UNABLE_TO_DELETE_DIRECTORY'), 'courses');
			}
			else
			{
				//push a success message
				Notify::success('You successfully deleted the folder.', 'courses');
			}
		}

		// Push through to the media view
		$this->mediaTask();
	}

	/**
	 * Delete a file
	 *
	 * @return     void
	 */
	public function deletefileTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			$this->mediaTask();
			return;
		}

		// Incoming course ID
		$listdir = Request::getInt('listdir', 0, 'get');
		if (!$listdir)
		{
			Notify::error(Lang::txt('COURSES_NO_ID'), 'courses');
			$this->mediaTask();
			return;
		}

		// Incoming file
		$file = trim(Request::getVar('file', '', 'get'));
		if (!$file)
		{
			Notify::error(Lang::txt('COURSES_NO_FILE'), 'courses');
			$this->mediaTask();
			return;
		}

		// Build the file path
		$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $listdir;

		if (!file_exists($path . DS . $file) or !$file)
		{
			Notify::error(Lang::txt('FILE_NOT_FOUND'), 'courses');
			$this->mediaTask();
			return;
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
		Notify::success('The file was successfully deleted.', 'courses');

		// Push through to the media view
		$this->mediaTask();
	}

	/**
	 * Show a form for uploading and managing files
	 *
	 * @return     void
	 */
	public function mediaTask()
	{
		// Incoming
		$listdir = Request::getInt('listdir', 0);
		if (!$listdir)
		{
			Notify::error(Lang::txt('COURSES_NO_ID'), 'courses');
		}

		$course = Course::getInstance($listdir);

		// Output HTML
		$this->view->config = $this->config;
		if (is_object($course))
		{
			$this->view->course = $course;
		}
		$this->view->listdir = $listdir;
		$this->view->notifications = Notify::messages('courses');
		$this->view->display();
	}

	/**
	 * List files for a course
	 *
	 * @return     void
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
				 && strtolower($entry) !== 'cvs'
				 && strtolower($entry) !== 'template'
				 && strtolower($entry) !== 'blog')
				{
					$folders[$entry] = $img_file;
				}
			}

			$d->close();

			ksort($images);
			ksort($folders);
			ksort($docs);
		}

		$this->view->docs = $docs;
		$this->view->folders = $folders;
		$this->view->images = $images;
		$this->view->config = $this->config;
		$this->view->listdir = $listdir;
		$this->view->notifications = Notify::messages('courses');
		$this->view->display();
	}

	/**
	 * Download a file
	 *
	 * @param      string $filename File name
	 * @return     void
	 */
	public function downloadTask($filename)
	{
		//get the course
		$course = Course::getInstance($this->gid);

		//authorize
		$authorized = $this->_authorize();

		//get the file name
		if (substr(strtolower($filename), 0, 5) == 'image')
		{
			$file = urldecode(substr($filename, 6));
		}
		elseif (substr(strtolower($filename), 0, 4) == 'file')
		{
			$file = urldecode(substr($filename, 5));
		}

		//if were on the wiki we need to output files a specific way
		if ($this->active == 'wiki')
		{
			//check to make sure user has access to wiki section
			if (!in_array(User::get('id'), $course->get('members')) || User::isGuest())
			{
				return App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH') . ' ' . $file);
			}

			//load wiki page from db
			require_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
			$page = new \Components\Wiki\Tables\Page($this->database);
			$page->load(Request::getVar('pagename'), $course->get('cn') . DS . 'wiki');

			//check specific wiki page access
			if ($page->get('access') == 1 && !in_array(User::get('id'), $course->get('members')) && $authorized != 'admin')
			{
				return App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH') . ' ' . $file);
			}

			//get the config and build base path
			$wiki_config = Component::params('com_wiki');
			$base_path = $wiki_config->get('filepath') . DS . $page->get('id');
		}
		else
		{
			//check to make sure we can access it
			if (!in_array(User::get('id'), $course->get('members')) || User::isGuest())
			{
				return App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH') . ' ' . $file);
			}

			// Build the path
			$base_path = $this->config->get('uploadpath');
			$base_path .= DS . $course->get('gidNumber');
		}

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
		else
		{
			exit;
		}
		return;
	}
}

