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

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Filesystem;
use Request;
use Config;
use Route;
use User;
use Date;
use Lang;
use App;

/**
 * Members controlelr class for import hooks
 */
class ImportHooks extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display records
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'state'    => array(1),
			'sort'     => 'name',
			'sort_Dir' => 'ASC',
			'type'     => 'members'
		);

		// get all imports from archive
		$archive = \Hubzero\Content\Import\Model\Hook\Archive::getInstance();

		$this->view->total = $archive->hooks('count', $this->view->filters);
		$this->view->hooks = $archive->hooks('list', $this->view->filters);

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

	/**
	 * Edit a record
	 *
	 * @param   object  $row  \Hubzero\Content\Import\Model\Hook
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		// get the import object
		if (!($row instanceof \Hubzero\Content\Import\Model\Hook))
		{
			// get request vars
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (isset($id[0]) ? $id[0] : 0);
			}

			$row = new \Hubzero\Content\Import\Model\Hook($id);
		}

		$this->view->hook = $row;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			Notify::error($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a record
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// check token
		Request::checkToken();

		// get request vars
		$hook = Request::getVar('hook', array(), 'post');
		$file = Request::getVar('file', array(), 'FILES');

		// Xreate hook model object
		$this->hook = new \Hubzero\Content\Import\Model\Hook();

		// Bind input to model
		if (!$this->hook->bind($hook))
		{
			$this->setError($this->hook->getError());
			return $this->editTask();
		}

		$this->hook->set('type', 'members');

		// Is this a new import?
		$isNew = false;
		if (!$this->hook->get('id'))
		{
			$isNew = true;

			// set the created by/at
			$this->hook->set('created_by', User::get('id'));
			$this->hook->set('created', Date::toSql());
		}

		// Attempt to save
		if (!$this->hook->store(true))
		{
			$this->setError($this->hook->getError());
			return $this->editTask();
		}

		// Is this a new record?
		if ($isNew)
		{
			// Create folder for files
			$this->_createImportFilespace($this->hook);
		}

		// If we have a file
		if ($file['size'] > 0 && $file['error'] == 0)
		{
			move_uploaded_file($file['tmp_name'], $this->hook->fileSpacePath() . DS . $file['name']);

			$this->hook->set('file', $file['name']);
			$this->hook->store();
		}

		\Notify::success(Lang::txt('COM_MEMBERS_IMPORTHOOK_CREATED'));

		// Inform user & redirect
		if ($this->_task == 'apply')
		{
			return $this->editTask($this->import);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display', false)
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
		$this->hook = new \Hubzero\Content\Import\Model\Hook($id);

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
	 * Delete record
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// check token
		Request::checkToken();

		// get request vars
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// loop through all ids posted
		foreach ($ids as $id)
		{
			// make sure we have an object
			$hook = new \Hubzero\Content\Import\Model\Hook($id);
			if (!$hook->exists())
			{
				continue;
			}

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
			Lang::txt('COM_MEMBERS_IMPORTHOOK_REMOVED'),
			'passed'
		);
	}

	/**
	 * Method to create import filespace if needed
	 *
	 * @param   object   $hook  \Hubzero\Content\Import\Model\Hook
	 * @return  boolean
	 */
	private function _createImportFilespace(\Hubzero\Content\Import\Model\Hook $hook)
	{
		// upload path
		$uploadPath = $hook->fileSpacePath();

		// if we dont have a filespace, create it
		if (!is_dir($uploadPath))
		{
			Filesystem::makeDirectory($uploadPath, 0775);
		}

		// all set
		return true;
	}
}
