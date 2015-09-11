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

namespace Components\Store\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Store\Helpers\ImgHandler;
use Exception;
use DirectoryIterator;

/**
 * Store controller class for handling media (files)
 */
class Media extends AdminController
{
	/**
	 * Upload an image
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_STORE_FEEDBACK_NO_ID'));
			$this->displayTask($id);
			return;
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_STORE_FEEDBACK_NO_FILE'));
			$this->displayTask($id);
			return;
		}

		// Build upload path
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/store'), DS) . DS . $id;

		if (!is_dir($path))
		{
			if (!\Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_STORE_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask($id);
				return;
			}
		}

		// Make the filename safe
		$file['name'] = \Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'imghandler.php');

		// Perform the upload
		if (!\Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_STORE_ERROR_UPLOADING'));
		}
		else
		{
			$ih = new ImgHandler();

			// Do we have an old file we're replacing?
			if (($curfile = Request::getVar('currentfile', '')))
			{
				// Remove old image
				if (file_exists($path . DS . $curfile))
				{
					if (!\Filesystem::delete($path . DS . $curfile))
					{
						$this->setError(Lang::txt('COM_STORE_UNABLE_TO_DELETE_FILE'));
						$this->displayTask($id);
						return;
					}
				}

				// Get the old thumbnail name
				$curthumb = $ih->createThumbName($curfile);

				// Remove old thumbnail
				if (file_exists($path . DS . $curthumb))
				{
					if (!\Filesystem::delete($path . DS . $curthumb))
					{
						$this->setError(Lang::txt('COM_STORE_UNABLE_TO_DELETE_FILE'));
						$this->displayTask($id);
						return;
					}
				}
			}

			// Create a thumbnail image
			$ih->set('image', $file['name']);
			$ih->set('path', $path . DS);
			$ih->set('maxWidth', 80);
			$ih->set('maxHeight', 80);
			$ih->set('cropratio', '1:1');
			$ih->set('outputName', $ih->createThumbName());
			if (!$ih->process())
			{
				$this->setError($ih->getError());
			}
		}

		// Push through to the image view
		$this->displayTask($id);
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_STORE_FEEDBACK_NO_ID'));
			$this->displayTask($id);
			return;
		}

		// Incoming picture
		$picture = Request::getVar('current', '');

		// Build the file path
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/store'), DS) . DS . $id;

		// Attempt to delete the file
		if (!\Filesystem::deleteDirectory($path))
		{
			$this->setError(Lang::txt('COM_STORE_UNABLE_TO_DELETE_FILE'));
			$this->displayTask($id);
			return;
		}

		// Push through to the image view
		$this->displayTask($id);
	}

	/**
	 * Display an image
	 *
	 * @param   integer  $id  Item ID
	 * @return  void
	 */
	public function displayTask($id=0)
	{
		$this->view->type = $this->type;

		// Load the component config
		$this->view->config = $this->config;

		// Do have an ID or do we need to get one?
		$this->view->id = ($id) ? $id : Request::getInt('id', 0);

		// Do we have a file or do we need to get one?
		//$this->view->file = ($file) ? $file : Request::getVar('file', '');
		// Build the directory path
		$this->view->path = DS . trim($this->config->get('webpath', '/site/store'), DS) . DS . $this->view->id;

		$folders = array();
		$docs    = array();
		$imgs    = array();

		$path = PATH_APP . $this->view->path;

		if (is_dir($path))
		{
			// Loop through all files and separate them into arrays of images, folders, and other
			$dirIterator = new DirectoryIterator($path);
			foreach ($dirIterator as $file)
			{
				if ($file->isDot())
				{
					continue;
				}

				if ($file->isDir())
				{
					$name = $file->getFilename();
					$folders[$path . DS . $name] = $name;
					continue;
				}

				if ($file->isFile())
				{
					$name = $file->getFilename();
					if (('cvs' == strtolower($name))
					 || ('.svn' == strtolower($name)))
					{
						continue;
					}

					if (preg_match("#bmp|gif|jpg|png|swf#i", $name))
					{
						$base = \Filesystem::name($name);
						if (substr($base, -3) == '-tn')
						{
							continue;
						}

						$imgs[$path . DS . $name] = $name;
					}
					else
					{
						$docs[$path . DS . $name] = $name;
					}
				}
			}

			ksort($folders);
			ksort($docs);
		}

		$this->view->file = array_shift($imgs);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('display')
			->display();
	}
}

