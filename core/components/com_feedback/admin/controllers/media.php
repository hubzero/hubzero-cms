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

namespace Components\Feedback\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\String;
use Filesystem;
use Request;
use Lang;

/**
 * Feedback controller class for handling media (files)
 */
class Media extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->type = Request::getVar('type', '', 'post');

		if (!$this->type)
		{
			$this->type = Request::getVar('type', 'regular', 'get');
		}
		$this->type = ($this->type == 'regular') ? $this->type : 'selected';

		parent::execute();
	}

	/**
	 * Upload an image
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('FEEDBACK_NO_ID'));
			$this->displayTask('', $id);
			return;
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('FEEDBACK_NO_FILE'));
			$this->displayTask('', $id);
			return;
		}

		$row = new Quote($this->database);

		// Build upload path
		$path = $row->filespace() . DS . String::pad($id);

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask('', $id);
				return;
			}
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		$qid = Request::getInt('qid', 0);

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('ERROR_UPLOADING'));
			$file = $curfile;
		}
		else
		{
			$row = new Quote($this->database);
			$row->load($qid);

			// Do we have an old file we're replacing?
			$curfile = $row->picture;

			if ($curfile != '' && $curfile != $file['name'])
			{
				// Yes - remove it
				if (file_exists($path . DS . $curfile))
				{
					if (!Filesystem::delete($path . DS . $curfile))
					{
						$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
						$this->displayTask($file['name'], $id);
						return;
					}
				}
			}

			$file = $file['name'];

			$row->picture = $file;
			if (!$row->store())
			{
				$this->setError($row->getError());
			}
		}

		// Push through to the image view
		$this->displayTask($file, $id, $qid);
	}

	/**
	 * Delete a file
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('FEEDBACK_NO_ID'));
			$this->displayTask('', $id);
			return;
		}

		$qid = Request::getInt('qid', 0);

		$row = new Quote($this->database);
		$row->load($qid);

		// Incoming file
		if (!$row->picture)
		{
			$this->setError(Lang::txt('FEEDBACK_NO_FILE'));
			$this->displayTask('', $id);
			return;
		}

		// Build the file path
		$path = $row->filespace() . DS . String::pad($id);

		if (!file_exists($path . DS . $row->picture) or !$row->picture)
		{
			$this->setError(Lang::txt('FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $row->picture))
			{
				$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
				$this->displayTask($file, $id);
				return;
			}

			$row->picture = '';
			if (!$row->store())
			{
				$this->setError($row->getError());
			}
		}

		// Push through to the image view
		$this->displayTask($row->picture, $id, $qid);
	}

	/**
	 * Display an image
	 *
	 * @param      string  $file File name
	 * @param      integer $id   User ID
	 * @param      integer $qid  Quote ID
	 * @return     void
	 */
	public function displayTask($file='', $id=0, $qid=0)
	{
		$this->view->type = $this->type;

		// Load the component config
		$this->view->config = $this->config;

		// Do have an ID or do we need to get one?
		$this->view->id = ($id) ? $id : Request::getInt('id', 0);

		$this->view->dir = String::pad($this->view->id);

		// Do we have a file or do we need to get one?
		$this->view->file = ($file) ? $file : Request::getVar('file', '');

		$row = new Quote($this->database);

		// Build the directory path
		$this->view->path = $row->filespace(false) . DS . $this->view->dir;

		$this->view->qid = ($qid) ? $qid : Request::getInt('qid', 0);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->setLayout('display')->display();
	}
}

