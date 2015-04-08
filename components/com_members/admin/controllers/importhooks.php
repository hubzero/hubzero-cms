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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Members controlelr class for import hooks
 */
class MembersControllerImportHooks extends \Hubzero\Component\AdminController
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
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters = array(
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
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
			$this->view->setError($error);
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
		Request::checkToken() or die('Invalid Token');

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

		// Inform user & redirect
		if ($this->_task == 'apply')
		{
			return $this->editTask($this->import);
		}

		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display', false),
			Lang::txt('COM_MEMBERS_IMPORTHOOK_CREATED'),
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
		Request::checkToken() or die( 'Invalid Token' );

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
				$this->setRedirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display', false),
					$hook->getError(),
					'error'
				);
				return;
			}
		}

		//inform user & redirect
		$this->setRedirect(
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
			\JFolder::create($uploadPath, 0775);
		}

		// all set
		return true;
	}
}
