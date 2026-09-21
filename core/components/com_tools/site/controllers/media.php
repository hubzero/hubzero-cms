<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Resources\Models\Entry;
use Filesystem;
use Component;
use Request;
use Route;
use Lang;
use User;
use App;
use Date;

require_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';
require_once Component::path('com_resources') . DS . 'helpers' . DS . 'utilities.php';
require_once Component::path('com_resources') . DS . 'helpers' . DS . 'html.php';

/**
 * Methods for listing and managing files and folders
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
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return $this->displayTask();
		}

		// Incoming sub-directory
		$subdir = Request::getString('dirPath', '', 'post');

		$row = Entry::oneOrFail($resource);

		if (!$row->get('created') || $row->get('created') == '0000-00-00 00:00:00')
		{
			$row->set('created', Date::format('Y-m-d 00:00:00'));
		}

		// Only a contributor who may edit this resource can manage its media.
		// The compose page this iframe sits in admits the tool's dev team and a
		// com_tools manager, so admit the manager here too rather than 403 inside
		// a page the same user is allowed to edit.
		if (User::isGuest()
		 || (!$row->access('edit')
		  && !$row->access('edit-own')
		  && !User::authorise('core.manage', 'com_tools')))
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
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask();
			}
		}

		// Incoming file
		$file = Request::getArray('upload', '', 'files');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_FILE'));
			return $this->displayTask();
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);

		// Ensure file names fit.
		$ext = Filesystem::extension($file['name']);

		// Refuse server-executable extensions and the markup types a download
		// handler would serve inline -- the same rule as resource media.
		//
		// This is a deny-list, so .svg, .js and double extensions such as
		// foo.php.jpg all pass; it closes code execution and inline HTML in the
		// hub origin, not upload typing in general. An allow-list would be
		// better and is worth doing, but com_media's list is not the one to
		// borrow: it is a media-manager list, not a tool-media list.
		$blockedExtensions = array('php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'pht', 'phar', 'phps', 'cgi', 'pl', 'asp', 'aspx', 'jsp', 'shtml', 'htaccess', 'htpasswd', 'html', 'htm', 'xhtml', 'xml');
		if (in_array(strtolower((string) $ext), $blockedExtensions))
		{
			$this->setError(Lang::txt('COM_TOOLS_INCORRECT_FILE_TYPE'));
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
			$this->setError(Lang::txt('COM_TOOLS_ERROR_UPLOADING'));
		}

		if (!Filesystem::isSafe($path))
		{
			Filesystem::delete($path);

			$this->setError(Lang::txt('COM_TOOLS_ERROR_FAILED_VIRUS_SCAN'));
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
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			return $this->displayTask();
		}

		// Incoming sub-directory
		$row = Entry::oneOrFail($resource);

		// Allow for temp resource uploads
		if (!$row->get('created') || $row->get('created') == '0000-00-00 00:00:00')
		{
			$row->set('created', Date::format('Y-m-d 00:00:00'));
		}

		// Only a contributor who may edit this resource can manage its media.
		// The compose page this iframe sits in admits the tool's dev team and a
		// com_tools manager, so admit the manager here too rather than 403 inside
		// a page the same user is allowed to edit.
		if (User::isGuest()
		 || (!$row->access('edit')
		  && !$row->access('edit-own')
		  && !User::authorise('core.manage', 'com_tools')))
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
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_FILE'));
			return $this->displayTask();
		}

		// Keep the target within the resource media directory
		if (strpos($file, '..') !== false)
		{
			$this->setError(Lang::txt('COM_TOOLS_FILE_NOT_FOUND'));
			return $this->displayTask();
		}

		// Check if the file even exists
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_TOOLS_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_TOOLS_UNABLE_TO_DELETE_FILE'));
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

		$row = Entry::oneOrFail($resource);

		// Listing the media of a resource is the same right as changing it: this
		// view is the compose page's iframe, and nothing else links to it. The
		// upload and delete tasks below reach it after their own check, so the
		// same predicate passes for them.
		if (User::isGuest()
		 || (!$row->access('edit')
		  && !$row->access('edit-own')
		  && !User::authorise('core.manage', 'com_tools')))
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
