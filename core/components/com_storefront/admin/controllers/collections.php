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

namespace Components\Storefront\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Archive;
use Components\Storefront\Models\Collection;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Collection.php');

/**
 * Controller class for storefront collections
 */
class Collections extends AdminController
{
	/**
	 * Display a list of all collections
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			// Get sorting variables
				'sort' => Request::getState(
						$this->_option . '.' . $this->_controller . '.sort',
						'filter_order',
						'title'
				),
				'sort_Dir' => Request::getState(
						$this->_option . '.' . $this->_controller . '.sortdir',
						'filter_order_Dir',
						'ASC'
				),
			// Get paging variables
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
				)
		);
		//print_r($this->view->filters);

		$obj = new Archive();

		// Get record count
		$this->view->total = $obj->collections('count', $this->view->filters);

		// Get records
		$this->view->rows  = $obj->collections('list', $this->view->filters);
		//print_r($this->view->rows); die;


		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		//print_r($this->view); die;
		$this->view->display();
	}

	/**
	 * Create a new category
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a category
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		$obj = new Archive();

		if (is_object($row))
		{
			$this->view->row = $row;
			$this->view->task = 'edit';
		}
		else
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load collection
			$this->view->row = new Collection($id);
		}

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
	 * Save a category and come back to the edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save a collection
	 *
	 * @param   boolean  $redirect  Redirect the page after saving
	 * @return  void
	 */
	public function saveTask($redirect = true)
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');

		// Get the collection
		$collection = new Collection($fields['cId']);

		try
		{
			if (isset($fields['cName']))
			{
				$collection->setName($fields['cName']);
			}
			if (isset($fields['state']))
			{
				$collection->setActiveStatus($fields['state']);
			}
			if (isset($fields['alias']))
			{
				$collection->setAlias($fields['alias']);
			}
			$collection->setType('category');

			// Make sure there are no integrity issues TODO: move the integrity check to the collection verify method (same as SKU)
			$collection->save();
		}
		catch (\Exception $e)
		{
			\Notify::error($e->getMessage());
			$this->editTask($collection);
			return;
		}

		if ($redirect)
		{
			// Redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_STOREFRONT_COLLECTION_SAVED')
			);
			return;
		}

		$this->editTask($collection);
	}

	/**
	 * Remove an entry
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Incoming
		$step = Request::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		// What step are we on?
		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				// Incoming
				$id = Request::getVar('id', array(0));
				if (!is_array($id) && !empty($id))
				{
					$id = array($id);
				}

				$this->view->cId = $id;

				// Set any errors
				if ($this->getError())
				{
					$this->view->setError($this->getError());
				}

				// Output the HTML
				$this->view->display();
			break;

			case 2:
				// Check for request forgeries
				Request::checkToken() or jexit('Invalid Token');

				// Incoming
				$cIds = Request::getVar('cId', array(0));

				// Make sure we have IDs to work with
				if (empty($cIds))
				{
					App::redirect(
							Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
							Lang::txt('COM_STOREFRONT_NO_ID'),
							'error'
					);
					return;
				}

				$delete = Request::getVar('delete', 0);

				$msg = "Delete canceled";
				$type = 'error';
				if ($delete)
				{
					// Do the delete
					foreach ($cIds as $cId)
					{
						// Delete option group
						try
						{
							$collection = new Collection($cId);
							$collection->delete();
						}
						catch (\Exception $e)
						{
							App::redirect(
									Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly', false),
									$e->getMessage(),
									$type
							);
							return;
						}
					}

					$msg = "Collection(s) deleted";
					$type = 'message';
				}

				// Set the redirect
				App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly', false),
						$msg,
						$type
				);
				break;
		}
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of an entry
	 *
	 * @param      integer $state State to set
	 * @return     void
	 */
	public function stateTask($state = 0)
	{
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
					Route::url('index.php?option='. $this->_option . '&controller=' . $this->_controller, false),
					($state == 1 ? Lang::txt('COM_STOREFRONT_SELECT_PUBLISH') : Lang::txt('COM_STOREFRONT_SELECT_UNPUBLISH')),
					'error'
			);
			return;
		}

		foreach ($ids as $cId)
		{
			// Save category
			try {
				$collection = new Collection($cId);
				$collection->setActiveStatus($state);

				// Make sure there are no integrity issues
				$this->checkIntegrity($collection);
				$collection->save();
			}
			catch (\Exception $e)
			{
				$error = true;
			}
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = Lang::txt('COM_STOREFRONT_ARCHIVED', count($ids));
			break;
			case '1':
				$message = Lang::txt('COM_STOREFRONT_PUBLISHED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_STOREFRONT_UNPUBLISHED', count($ids));
			break;
		}

		$type = 'message';

		if (isset($error) && $error)
		{
			switch ($state)
			{
				case '1':
					$action = 'published';
					break;
				case '0':
					$action = 'unpublished';
					break;
			}

			$message = 'Collection could not be ' . $action;
			if (sizeof($ids) > 1)
			{
				$message = 'Some collection(s) could not be ' . $action;
			}
			$type = 'error';
		}

		// Redirect
		App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$message,
				$type
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	private function checkIntegrity($collection)
	{
		// If the collection is not being published, no need to check for integrity
		if (!$collection->getActiveStatus())
		{
			return true;
		}

		require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Integrity.php');
		$integrityCheck = \Integrity::collectionIntegrityCheck($collection);

		if ($integrityCheck->status != 'ok')
		{
			$errorMessage = "Integrity check error:";
			foreach ($integrityCheck->errors as $error)
			{
				$errorMessage .= '<br>' . $error;
			}

			throw new \Exception($errorMessage);
		}
		return true;
	}
}

