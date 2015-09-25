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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedback\Site\Controllers;

use Components\Feedback\Models\Quote;
use Hubzero\Component\SiteController;
use Hubzero\Utility\String;
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
	 * @return     void
	 */
	public function uploadTask()
	{
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
			$this->displayTask('', 0);
			return;
		}

		// Incoming
		if (!($id = Request::getInt('id', 0)))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_ID'));
			$this->displayTask('', $id);
			return;
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_FILE'));
			$this->displayTask('', $id);
			return;
		}

		// Build upload path
		$path = $this->path . DS . $id;

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_FEEDBACK_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask('', $id);
				return;
			}
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_ERROR_UPLOADING'));
			$file = $curfile;
		}
		else
		{
			// Do we have an old file we're replacing?
			$curfile = Request::getVar('currentfile', '');

			if ($curfile != '' && file_exists($path . DS . $curfile))
			{
				if (!Filesystem::delete($path . DS . $curfile))
				{
					$this->setError(Lang::txt('COM_FEEDBACK_UNABLE_TO_DELETE_FILE'));
					$this->displayTask($file['name'], $id);
					return;
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
	 * @return     void
	 */
	public function deleteTask()
	{
		if (User::isGuest())
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
			$this->displayTask('', 0);
			return;
		}

		// Incoming member ID
		if (!($id = Request::getInt('id', 0)))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_ID'));
			$this->displayTask('', $id);
			return;
		}

		if (User::get('id') != $id)
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
			$this->displayTask('', User::get('id'));
			return;
		}

		// Incoming file
		if (!($file = Request::getVar('file', '')))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_FILE'));
			$this->displayTask($file, $id);
			return;
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
				$this->displayTask($file, $id);
				return;
			}

			$file = '';
		}

		// Push through to the image view
		$this->displayTask($file, $id);
	}

	/**
	 * Display a form for uploading an image and any data for current uploaded image
	 *
	 * @param      string  $file Image name
	 * @param      integer $id   User ID
	 * @return     void
	 */
	public function displayTask($file='', $id=0)
	{
		// Do have an ID or do we need to get one?
		if (!$id)
		{
			$id = Request::getInt('id', 0);
		}
		$dir = String::pad($id);

		// Do we have a file or do we need to get one?
		$file = ($file)
			  ? $file
			  : Request::getVar('file', '');

		// Build the directory path
		$path = $this->path . DS . $dir;

		// Output form with error messages
		$this->view->title     = $this->_title;
		$this->view->webpath   = $this->config->get('uploadpath', '/site/quotes');
		$this->view->default_picture = $this->config->get('defaultpic', '/core/components/com_feedback/site/assets/img/contributor.gif');
		$this->view->path      = $dir;
		$this->view->file      = $file;
		$this->view->file_path = $path;
		$this->view->id        = $id;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('display')
			->display();
	}
}

