<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Feedback\Site\Controllers;

use Components\Feedback\Tables\Quote;
use Hubzero\Component\SiteController;
use Hubzero\Utility\String;

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
		$row = new Quote($this->database);
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
		if ($this->juser->get('guest'))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
			$this->displayTask('', 0);
			return;
		}

		// Incoming
		if (!($id = \JRequest::getInt('id', 0)))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_ID'));
			$this->displayTask('', $id);
			return;
		}

		// Incoming file
		$file = \JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_FILE'));
			$this->displayTask('', $id);
			return;
		}

		// Build upload path
		$path = $this->path . DS . String::pad($id);

		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!\JFolder::create($path))
			{
				$this->setError(Lang::txt('COM_FEEDBACK_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask('', $id);
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = \JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Perform the upload
		if (!\JFile::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_ERROR_UPLOADING'));
			$file = $curfile;
		}
		else
		{
			// Do we have an old file we're replacing?
			$curfile = \JRequest::getVar('currentfile', '');

			if ($curfile != '' && file_exists($path . DS . $curfile))
			{
				if (!\JFile::delete($path . DS . $curfile))
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
		if ($this->juser->get('guest'))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
			$this->displayTask('', 0);
			return;
		}

		// Incoming member ID
		if (!($id = \JRequest::getInt('id', 0)))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_ID'));
			$this->displayTask('', $id);
			return;
		}

		if ($this->juser->get('id') != $id)
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
			$this->displayTask('', $this->juser->get('id'));
			return;
		}

		// Incoming file
		if (!($file = \JRequest::getVar('file', '')))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_NO_FILE'));
			$this->displayTask($file, $id);
			return;
		}

		$file = basename($file);

		// Build the file path
		$path = $this->path . DS . String::pad($id);

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_FEEDBACK_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!\JFile::delete($path . DS . $file))
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
			$id = \JRequest::getInt('id', 0);
		}
		$dir = String::pad($id);

		// Do we have a file or do we need to get one?
		$file = ($file)
			  ? $file
			  : \JRequest::getVar('file', '');

		// Build the directory path
		$path = $this->path . DS . $dir;

		// Output form with error messages
		$this->view->title     = $this->_title;
		$this->view->webpath   = $this->config->get('uploadpath', '/site/quotes');
		$this->view->default_picture = $this->config->get('defaultpic', '/components/com_feedback/assets/img/contributor.gif');
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

