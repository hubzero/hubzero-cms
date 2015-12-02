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
use Components\Storefront\Models\OptionGroup;
use Components\Storefront\Models\Archive;
use Components\Storefront\Models\Warehouse;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'OptionGroup.php');

/**
 * Controller class for knowledge base categories
 */
class Options extends AdminController
{
	/**
	 * Display a list of all categories
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

		// Get option group ID
		$ogId = Request::getVar('ogId', array(0));
		$this->view->ogId = $ogId;

		// Get option group
		$optionGroup = new OptionGroup($ogId);
		$this->view->optionGroup = $optionGroup;

		$obj = new Archive();

		// Get record count
		$this->view->total = $obj->options('count', $ogId, $this->view->filters);

		// Get records
		$this->view->rows = $obj->options('list', $ogId, $this->view->filters);
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
	public function editTask($row = null)
	{
		Request::setVar('hidemainmenu', 1);

		$obj = new Archive();

		if (is_object($row))
		{
			$id = $row->getId();
			// If this is a new option, set option group ID
			if (!$id)
			{
				$ogId = Request::getVar('ogId');
				$row->setOptionGroupId($ogId);
			}
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

			// Get option
			$row = $obj->option($id);
			$this->view->row = $row;

			// If this is a new option, set option group ID
			if (!$id)
			{
				$ogId = Request::getVar('ogId');
				$row->setOptionGroupId($ogId);
			}
		}

		//print_r($row); die;

		// Get option group's info
		$ogId = $row->getOptionGroupId();
		$warehouse = new Warehouse();
		$ogInfo = $warehouse->getOptionGroups('list', $filters = array('ids' => $ogId));
		$this->view->ogInfo = $ogInfo[0];
		//print_r($ogInfo); die;

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
	 * Save a product
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

		$obj = new Archive();
		// Save option
		try {
			$option = $obj->updateOption($fields['oId'], $fields);
			//print_r($option); die;
		}
		catch (\Exception $e)
		{
			//echo $e->getMessage(); die;
			\Notify::error($e->getMessage());
			// Get the sku
			$option = $obj->option($fields['oId']);
			//print_r($sku); die;
			$this->editTask($option);
			return;
		}

		if ($redirect)
		{
			// Redirect
			App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display&ogId=' . $fields['ogId'], false),
					Lang::txt('COM_STOREFRONT_OPTION_SAVED')
			);
			return;
		}

		$this->editTask($option);
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

		$ogId = Request::getVar('ogId');

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
				$this->view->oId = $id;
				$this->view->ogId = $ogId;

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
				$oIds = Request::getVar('oId', 0);

				// Make sure we have ID(s) to work with
				if (empty($oIds))
				{
					App::redirect(
							Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&id=' . $ogId, false),
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
					$obj = new Archive();
					$warnings = array();

					foreach ($oIds as $oId)
					{
						// Delete option
						try
						{
							$option = $obj->option($oId);
							$option->delete();

							// see if there are any warnings to display
							if ($optionWarnings = $option->getMessages())
							{
								foreach ($optionWarnings as $optionWarning)
								{
									if (!in_array($optionWarning, $warnings))
									{
										$warnings[] = $optionWarning;
									}
								}
							}
						}
						catch (\Exception $e)
						{
							App::redirect(
									Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&id=' . $ogId, false),
									$e->getMessage(),
									$type
							);
							return;
						}
					}

					$msg = "Option(s) deleted";
					$type = 'message';
				}

				// Set the redirect
				App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&ogId=' . $ogId, false),
						$msg,
						$type
				);

				if (!empty($warnings))
				{
					foreach ($warnings as $warning)
					{
						\Notify::warning($warning);
					}
				}
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
	public function stateTask($state=0)
	{
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$ogId = Request::getVar('ogId', 0);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					($state == 1 ? Lang::txt('COM_STOREFRONT_SELECT_PUBLISH') : Lang::txt('COM_STOREFRONT_SELECT_UNPUBLISH')),
					'error'
			);
			return;
		}

		// Update record(s)
		$obj = new Archive();

		foreach ($ids as $oId)
		{
			// Save option
			try
			{
				$obj->updateOption($oId, array('state' => $state));
			}
			catch (\Exception $e)
			{
				\Notify::error($e->getMessage());
				return;
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

		// Redirect
		App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display&ogId=' . $ogId, false),
				$message
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$fields = Request::getVar('fields', array(), 'post');
		$ogId = $fields['ogId'];

		// Set the redirect
		App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display&ogId=' . $ogId, false)
		);
	}
}

