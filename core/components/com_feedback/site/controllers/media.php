<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedback\Site\Controllers;

use Components\Feedback\Models\Quote;
use Hubzero\Component\SiteController;
use Hubzero\Utility\Str;
use Filesystem;
use Request;
use Route;
use Lang;
use User;

/**
 * Feedback controller class for media management
 */
class Media extends SiteController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$row = Quote::oneOrNew(0);
		$this->path = $row->filespace();

		parent::execute();
	}

	/**
	 * Upload an image
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
			return $this->displayTask('', 0);
		}

		// Incoming
		if (!($id = Request::getInt('id', 0)))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_ID'));
			return $this->displayTask('', $id);
		}

		// Incoming file
		$file = Request::getArray('upload', '', 'files');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_FILE'));
			return $this->displayTask('', $id);
		}

		// Build upload path
		$path = $this->path . DS . $id;

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_FEEDBACK_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask('', $id);
			}
		}

		// Get media config
		$mediaConfig = \Component::params('com_media');

		// Size limit is in MB, so we need to turn it into just B
		$sizeLimit = $mediaConfig->get('upload_maxsize', 10);
		$sizeLimit = $sizeLimit * 1024 * 1024;

		if ($file['size'] > $sizeLimit)
		{
			$this->setError(Lang::txt('COM_FEEDBACKI_ERROR_UPLOADING_FILE_TOO_BIG', \Hubzero\Utility\Number::formatBytes($sizeLimit)));
			return $this->displayTask();
		}

		// Make the filename safe
		$file['name'] = urldecode($file['name']);
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = Filesystem::extension($file['name']);

		// Check that the file type is allowed
		$allowed = array_values(array_filter(explode(',', $mediaConfig->get('upload_extensions'))));

		if (!empty($allowed) && !in_array(strtolower($ext), $allowed))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_ERROR_UPLOADING_INVALID_FILE', implode(', ', $allowed)));
			return $this->displayTask();
		}

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_ERROR_UPLOADING'));
			$file = $curfile;
		}
		else
		{
			// Do we have an old file we're replacing?
			$curfile = Request::getString('currentfile', '');

			if ($curfile != '' && file_exists($path . DS . $curfile))
			{
				if (!Filesystem::delete($path . DS . $curfile))
				{
					$this->setError(Lang::txt('COM_FEEDBACK_UNABLE_TO_DELETE_FILE'));
					return $this->displayTask($file['name'], $id);
				}
			}

			$file = $file['name'];
		}

		// Push through to the image view
		$this->displayTask($file, $id);
	}

	/**
	 * Delete an image
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
			return $this->displayTask('', 0);
		}

		// Incoming member ID
		if (!($id = Request::getInt('id', 0)))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_ID'));
			return $this->displayTask('', $id);
		}

		if (User::get('id') != $id)
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
			return $this->displayTask('', User::get('id'));
		}

		// Incoming file
		if (!($file = Request::getString('file', '')))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_FILE'));
			return $this->displayTask($file, $id);
		}

		$file = basename($file);

		// Build the file path
		$path = $this->path . DS . $id;

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_FEEDBACK_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_FEEDBACK_UNABLE_TO_DELETE_FILE'));
				return $this->displayTask($file, $id);
			}

			$file = '';
		}

		// Push through to the image view
		$this->displayTask($file, $id);
	}

	/**
	 * Display a form for uploading an image and any data for current uploaded image
	 *
	 * @param   string   $file  Image name
	 * @param   integer  $id    User ID
	 * @return  void
	 */
	public function displayTask($file='', $id=0)
	{
		// Do have an ID or do we need to get one?
		if (!$id)
		{
			$id = Request::getInt('id', 0);
		}
		$dir = Str::pad($id);

		// Do we have a file or do we need to get one?
		$file = $file ?: Request::getString('file', '');

		// Build the directory path
		$path = $this->path . DS . $dir;

		// Output view
		$this->view
			->set('title', $this->_title)
			->set('webpath', $this->config->get('uploadpath', '/site/quotes'))
			->set('default_picture', $this->config->get('defaultpic', '/core/components/com_feedback/site/assets/img/contributor.gif'))
			->set('path', $dir)
			->set('file', $file)
			->set('file_path', $path)
			->set('id', $id)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}
}
