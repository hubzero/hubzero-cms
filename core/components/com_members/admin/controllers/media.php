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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Image\Processor;
use Hubzero\Utility\String;
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
		$curfile = Request::getVar('file', '');
		$file    = Request::getVar('upload', '', 'files', 'array');

		// Build upload path
		$dir  = String::pad($id);
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/members'), DS) . DS . $dir;

		//allowed extensions for uplaod
		$allowedExtensions = array('png', 'jpe', 'jpeg', 'jpg', 'gif');

		//max upload size
		$sizeLimit = $this->config->get('maxAllowed', '40000000');

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
		$file = Request::getVar('file', '');
		if (!$file)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_FILE'));
			return $this->displayTask($id);
		}

		// Build the file path
		$dir  = String::pad($id);
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/members'), DS) . DS . $dir;

		// if we have file
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_MEMBERS_FILE_NOT_FOUND'));
		}
		else
		{
			$ih = new \Components\Members\Helpers\ImgHandler();

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

