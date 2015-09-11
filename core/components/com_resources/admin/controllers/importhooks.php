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

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models;
use Components\Resources\Import\Importer;
use Hubzero\Component\AdminController;
use Request;
use Route;
use User;
use Date;
use Lang;
use App;

/**
 * Resource importer
 */
class ImportHooks extends AdminController
{
	/**
	 * Display imports
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// get all imports from archive
		$hooksArchive = Models\Import\Hook\Archive::getInstance();
		$this->view->hooks = $hooksArchive->hooks('list', array(
			'state' => array(1)
		));

		// Output the HTML
		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Add an Import
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an Import
	 *
	 * @return  void
	 */
	public function editTask()
	{
		Request::setVar('hidemainmenu', 1);

		// get request vars
		$id = Request::getVar('id', array(0));
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : null;
		}

		// get the import object
		$this->view->hook = new Models\Import\Hook($id);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an Import
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// check token
		\Session::checkToken();

		// get request vars
		$hook = Request::getVar('hook', array());
		$file = Request::getVar('file', array(), 'FILES');

		// create hook model object
		$this->hook = new Models\Import\Hook();

		// bind input to model
		if (!$this->hook->bind( $hook ))
		{
			$this->setError($this->hook->getError());
			return $this->editTask();
		}

		// is this a new import
		$isNew = false;
		if (!$this->hook->get('id'))
		{
			$isNew = true;

			// set the created by/at
			$this->hook->set('created_by', User::get('id'));
			$this->hook->set('created', Date::toSql());
		}

		// attempt to save
		if (!$this->hook->store(true))
		{
			$this->setError($this->hook->getError());
			return $this->editTask();
		}

		// is this a new import
		if ($isNew)
		{
			// create folder for files
			$this->_createImportFilespace($this->hook);
		}

		// if we have a file
		if ($file['size'] > 0 && $file['error'] == 0)
		{
			move_uploaded_file($file['tmp_name'], $this->hook->fileSpacePath() . DS . $file['name']);

			$this->hook->set('file', $file['name']);
			$this->hook->store();
		}

		//inform user & redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display', false),
			Lang::txt('COM_RESOURCES_IMPORTHOOK_CREATED'),
			'passed'
		);
	}

	/**
	 * Show Raw immport hook file
	 *
	 * @return  void
	 */
	public function rawTask()
	{
		// get request vars
		$id = Request::getVar('id', array());
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : null;
		}

		// create hook model object
		$this->hook = new Models\Import\Hook($id);

		// get path to file
		$file = $this->hook->fileSpacePath() . DS . $this->hook->get('file');

		// default contents
		$contents = '';

		// if we have a file
		if (file_exists($file))
		{
			// get contents of file
			$contents = file_get_contents($file);
		}

		// output contents of hook file
		highlight_string($contents);
		exit();
	}

	/**
	 * Delete Import
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// check token
		\Session::checkToken();

		// get request vars
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// loop through all ids posted
		foreach ($ids as $id)
		{
			// make sure we have an object
			if (!$hook = new Models\Import\Hook($id))
			{
				continue;
			}

			// attempt to delete hook
			$hook->set('state', 2);
			if (!$hook->store(true))
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display', false),
					$hook->getError(),
					'error'
				);
				return;
			}
		}

		//inform user & redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display', false),
			Lang::txt('COM_RESOURCES_IMPORTHOOK_REMOVED'),
			'passed'
		);
	}

	/**
	 * Method to create import filespace if needed
	 *
	 * @param   object   $hook  Models\Import\Hook
	 * @return  boolean
	 */
	private function _createImportFilespace(Models\Import\Hook $hook)
	{
		// upload path
		$uploadPath = $hook->fileSpacePath();

		// if we dont have a filespace, create it
		if (!is_dir($uploadPath))
		{
			\Filesystem::makeDirectory($uploadPath, 0775);
		}

		// all set
		return true;
	}
}
