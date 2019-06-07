<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Image\Processor;
use Hubzero\Utility\Str;
use Filesystem;
use Request;
use Route;
use Lang;
use User;
use App;

/**
 * Manage files for a member
 */
class Media extends AdminController
{
	/**
	 * Upload a file
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id      = Request::getInt('id', 0);
		$curfile = Request::getString('file', '');
		$file    = Request::getArray('upload', '', 'files');

		// Build upload path
		$dir  = Str::pad($id);
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/members'), DS) . DS . $dir;

		// Allowed extensions for uplaod
		$allowedExtensions = array('png', 'jpe', 'jpeg', 'jpg', 'gif', 'jp2', 'jpx');

		// Get media config
		$mediaConfig = \Component::params('com_media');

		// Size limit is in MB, so we need to turn it into just B
		$sizeLimit = $mediaConfig->get('upload_maxsize', 10);
		$sizeLimit = $sizeLimit * 1024 * 1024;

		// make sure we have id
		if (!$id)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->displayTask($id);
		}

		// make sure we have a file
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_FILE'));
			return $this->displayTask($id);
		}

		// make sure we have an upload path
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_MEMBERS_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask($id);
			}
		}

		// make sure file is not empty
		if ($file['size'] == 0)
		{
			$this->setError(Lang::txt('COM_MEMBERS_FILE_HAS_NO_SIZE'));
			return $this->displayTask($id);
		}

		// make sure file is not empty
		if ($file['size'] > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			$this->setError(Lang::txt('FILE_SIZE_TOO_BIG', $max));
			return $this->displayTask($id);
		}

		// must be in allowed extensions
		$pathInfo = pathinfo($file['name']);
		$ext = $pathInfo['extension'];
		if (!in_array($ext, $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			$this->setError(Lang::txt('COM_MEMBERS_FILE_TYPE_NOT_ALLOWED', $these));
			return $this->displayTask($id);
		}

		// build needed paths
		$filePath    = $path . DS . $file['name'];
		$profilePath = $path . DS . 'profile.png';
		$thumbPath   = $path . DS . 'thumb.png';

		// upload image
		if (!Filesystem::upload($file['tmp_name'], $filePath))
		{
			$this->setError(Lang::txt('COM_MEMBERS_ERROR_UPLOADING'));
			return $this->displayTask($id);
		}

		// create profile pic
		$imageProcessor = new Processor($filePath);
		if (count($imageProcessor->getErrors()) == 0)
		{
			$imageProcessor->autoRotate();
			$imageProcessor->resize(400);
			$imageProcessor->setImageType(IMAGETYPE_PNG);
			$imageProcessor->save($profilePath);
		}

		// create thumb
		$imageProcessor = new Processor($filePath);
		if (count($imageProcessor->getErrors()) == 0)
		{
			$imageProcessor->resize(50, false, true, true);
			$imageProcessor->save($thumbPath);
		}

		// remove orig file
		unlink($filePath);

		// Push through to the image view
		$this->displayTask($id);
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->displayTask($id);
		}

		// Incoming file
		$file = Request::getString('file', '');
		if (!$file)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_FILE'));
			return $this->displayTask($id);
		}

		// Build the file path
		$dir  = Str::pad($id);
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/members'), DS) . DS . $dir;

		// if we have file
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_MEMBERS_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_MEMBERS_UNABLE_TO_DELETE_FILE'));
				return $this->displayTask($id);
			}

			// Get the file thumbnail name
			if ($file == 'profile.png')
			{
				$curthumb = 'thumb.png';
			}

			// Remove the thumbnail
			if (file_exists($path . DS . $curthumb))
			{
				if (!Filesystem::delete($path . DS . $curthumb))
				{
					$this->setError(Lang::txt('COM_MEMBERS_UNABLE_TO_DELETE_FILE'));
					return $this->displayTask($file, $id);
				}
			}
		}

		$this->displayTask($id);
	}

	/**
	 * Display a file and its info
	 *
	 * @param   string   $file  File name
	 * @param   integer  $id    User ID
	 * @return  void
	 */
	public function displayTask($file='', $id=0)
	{
		if (!$id)
		{
			$id = Request::getInt('id', 0);
		}

		$profile = User::getInstance($id);

		// Output the HTML
		$this->view
			->set('config', $this->config)
			->set('profile', $profile)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}
}
