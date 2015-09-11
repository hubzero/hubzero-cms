<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Redirect\Admin\Controllers;

use Components\Redirect\Helpers\Redirect as Helper;
use Components\Redirect\Models\Links as Records;
use Components\Redirect\Models\Link as Record;
use Components\Redirect\Tables\Link;
use Hubzero\Component\AdminController;
use Exception;
use Request;
use Route;
use User;
use Lang;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'redirect.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'links.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'link.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'link.php');

/**
 * Redirect link list controller class.
 */
class Links extends AdminController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Value = 0
		$this->registerTask('unpublish', 'publish');

		// Value = 2
		$this->registerTask('archive', 'publish');

		// Value = -2
		$this->registerTask('trash', 'publish');

		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');

		parent::execute();
	}

	/**
	 * Display
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$model = new Records;

		$this->view->enabled     = Helper::isEnabled();
		$this->view->items       = $model->getItems();
		$this->view->pagination  = $model->getPagination();
		$this->view->state       = $model->getState();

		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->view
			->set('model', $model)
			->setLayout('default')
			->display();
	}

	/**
	 * Method to add a new record.
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Access check.
		if (!(User::authorise('core.create', $this->_option))) // || count(User::getAuthorisedCategories($this->_option, 'core.create'))))
		{
			// Set the internal error and also the redirect error.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false),
				Lang::txt('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				'error'
			);
			return;
		}

		$this->editTask();
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToListAppend()
	{
		$append = '';

		// Setup redirect info.
		if ($tmpl = Request::getCmd('tmpl'))
		{
			$append .= '&tmpl=' . $tmpl;
		}

		return $append;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 * @return  string   The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = '';

		// Setup redirect info.
		if ($tmpl = Request::getCmd('tmpl'))
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout = Request::getCmd('layout', 'edit'))
		{
			$append .= '&layout=' . $layout;
		}

		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	/**
	 * Display edit form
	 *
	 * @return  void
	 */
	public function editTask()
	{
		$cid = Request::getVar('cid', array(), 'post', 'array');
		$recordId = (int) (count($cid) ? $cid[0] : Request::getInt('id'));

		// Access check.
		if (!User::authorise('core.edit', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false),
				Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
				'error'
			);
			return;
		}

		$link = new Link($this->database);
		$link->load($recordId);

		if ($data = User::getState($this->_option . '.edit.link.data'))
		{
			$link->bind($data);
		}

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->set('row', $link)
			->setLayout('edit')
			->display();
	}

	/**
	 * Method to save a record.
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries.
		Request::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$data    = Request::getVar('fields', array(), 'post', 'array');
		$context = "$this->_option.edit.link";
		$model   = new Link($this->database);

		// Determine the name of the primary key for the data.
		$urlVar = $model->getKeyName();
		$recordId = Request::getInt($urlVar);

		// Populate the row id from the session.
		$data[$urlVar] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($this->_task == 'save2copy')
		{
			// Reset the ID and then treat the request as for Apply.
			$data[$urlVar] = 0;
			$this->_task = 'apply';
		}

		// Access check.
		//if (!$this->allowSave($data, $key))
		if (!User::authorise('core.edit', $this->_option) && !User::authorise('core.create', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false),
				Lang::txt('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'),
				'error'
			);
			return;
		}

		$model->bind($data);

		// Check for validation errors.
		if (!$model->check())
		{
			// Push up to three validation messages out to the user.
			foreach ($model->getErrors() as $error)
			{
				Notify::warning(($error instanceof Exception ? $error->getMessage() : $error));
			}

			// Save the data in the session.
			User::setState($context . '.data', $data);

			// Redirect back to the edit screen.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToItemAppend($recordId, $urlVar), false)
			);
			return;
		}

		// Attempt to save the data.
		if (!$model->store())
		{
			// Save the data in the session.
			User::setState($context . '.data', $data);

			// Redirect back to the edit screen.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToItemAppend($recordId, $urlVar), false),
				Lang::txt('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()),
				'error'
			);
			return;
		}

		$msg = Lang::txt('COM_REDIRECT_SAVE_SUCCESS');

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->_task)
		{
			case 'apply':
				// Redirect back to the edit screen.
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&task=edit' . $this->getRedirectToItemAppend($recordId, $urlVar), false),
					$msg
				);
			break;

			case 'save2new':
				// Clear the record id and data from the session.
				User::setState($context . '.data', null);

				// Redirect back to the edit screen.
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&task=edit' . $this->getRedirectToItemAppend(null, $urlVar), false),
					$msg
				);
			break;

			default:
				// Clear the record id and data from the session.
				User::setState($context . '.data', null);

				// Redirect to the list screen.
				App::redirect(
					Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false),
					$msg
				);
			break;
		}
	}

	/**
	 * Method to update a record.
	 *
	 * @return  void
	 */
	public function activateTask()
	{
		// Check for request forgeries.
		Request::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$ids     = Request::getVar('cid', array(), '', 'array');
		$newUrl  = Request::getString('new_url');
		$comment = Request::getString('comment');

		if (empty($ids))
		{
			throw new Exception(Lang::txt('COM_REDIRECT_NO_ITEM_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = new Record(); //$this->getModel();

			\Hubzero\Utility\Arr::toInteger($ids);

			// Remove the items.
			if (!$model->activate($ids, $newUrl, $comment))
			{
				throw new Exception($model->getError(), 500);
			}
			else
			{
				Notify::success(Lang::txts('COM_REDIRECT_N_LINKS_UPDATED', count($ids)));
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option)
		);
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		// Check for request forgeries
		Request::checkToken() or die(Lang::txt('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid = Request::getVar('cid', array(), '', 'array');
		$data = array(
			'publish'   => 1,
			'unpublish' => 0,
			'archive'   => 2,
			'trash'     => -2,
			'report'    => -3
		);

		$value = \Hubzero\Utility\Arr::getValue($data, $this->_task, 0, 'int');

		if (empty($cid))
		{
			throw new Exception(Lang::txt('COM_REDIRECT_NO_ITEM_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = new Record();

			// Make sure the item ids are integers
			\Hubzero\Utility\Arr::toInteger($cid);

			// Publish the items.
			if (!$model->publish($cid, $value))
			{
				throw new Exception($model->getError(), 500);
			}
			else
			{
				if ($value == 1)
				{
					$ntext = 'COM_REDIRECT_N_ITEMS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = 'COM_REDIRECT_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value == 2)
				{
					$ntext = 'COM_REDIRECT_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = 'COM_REDIRECT_N_ITEMS_TRASHED';
				}
				Notify::success(Lang::txts($ntext, count($cid)));
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}

	/**
	 * Cancel a task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		User::setState($this->_option . '.edit.link.data', null);

		parent::cancelTask();
	}
}
