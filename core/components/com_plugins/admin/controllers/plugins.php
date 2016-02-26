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

namespace Components\Plugins\Admin\Controllers;

use Hubzero\Utility\Arr;
use Hubzero\Component\AdminController;
use Components\Plugins\Admin\Models;
use Exception;
use Session;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Plugins controller class.
 */
class Plugins extends AdminController
{
	/**
	 * Determine task and execute it.
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Define standard task mappings.

		// Value = 0
		$this->registerTask('unpublish', 'publish');

		// Value = 2
		$this->registerTask('archive', 'publish');

		// Value = -2
		$this->registerTask('trash', 'publish');

		// Value = -3
		$this->registerTask('report', 'publish');
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');

		// Guess the message prefix. Defaults to the option.
		if (empty($this->text_prefix))
		{
			$this->text_prefix = strtoupper($this->_option);
		}

		parent::execute();
	}

	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		require_once(dirname(__DIR__) . DS . 'models' . DS . 'plugins.php');

		$model = new Models\Plugins();

		$this->view->ftp = \JClientHelper::setCredentialsFromRequest('ftp');

		$this->view->state      = $model->getState();
		$this->view->items      = $model->getItems();
		$this->view->pagination = $model->getPagination();

		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
			App::abort(500, implode("\n", $errors));
		}

			// Check if there are no matching items
		if (!count($this->view->items))
		{
			Notify::warning(Lang::txt('COM_PLUGINS_MSG_MANAGE_NO_PLUGINS'));
		}

		$this->view
			->display();
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @return  void
	 */
	public function editTask()
	{
		// Initialise variables.
		$model   = new Models\Plugin();
		$table   = $model->getTable();
		$cid     = Request::getVar('cid', array(), 'post', 'array');
		$key     = $table->getKeyName();
		$context = "$this->_option.edit.plugin";

		// Get the previous record id (if any) and the current record id.
		$recordId = (int) (count($cid) ? $cid[0] : Request::getInt($key));
		if ($recordId)
		{
			Request::setVar('extension_id', $recordId);
		}
		$checkin  = property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false),
				Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
				'error'
			);
		}

		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice but allow the user to see the record.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToItemAppend($recordId, $key), false),
				Lang::txt('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED'),
				'error'
			);
		}

		$this->holdEditId($context, $recordId);

		User::setState($context . '.data', null);

		//$id = Request::getInt('extension_id');

		/*if (!$this->checkEditId('com_plugins.edit.plugin', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('JLIB_APPLICATION_ERROR_UNHELD_ID', $id),
				'error'
			);
		}

		$result = parent::edit($key, $urlVar);

		$model = new Models\Plugin();*/

		$this->view->state = $model->getState();
		$this->view->item  = $model->getItem();
		$this->view->form  = $model->getForm();

		if (count($errors = $model->getErrors()))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$model   = new Models\Plugin();
		$table   = $model->getTable();
		$data    = Request::getVar('jform', array(), 'post', 'array');
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->_option.edit.plugin";
		$task    = $this->getTask();
		$key     = $table->getKeyName();

		$recordId = Request::getInt($key);

		if (!$this->checkEditId($context, $recordId))
		{
			// Somehow the person just went to the form and tried to save it. We don't allow that.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false),
				Lang::txt('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId),
				'error'
			);
		}

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false),
				Lang::txt('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED', $model->getError()),
				'error'
			);
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			throw new Exception($model->getError());
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					Notify::warning($errors[$i]->getMessage());
				}
				else
				{
					Notify::warning($errors[$i]);
				}
			}

			// Save the data in the session.
			User::setState($context . '.data', $data);

			// Redirect back to the edit screen.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToItemAppend($recordId, $key), false)
			);
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			User::setState($context . '.data', $validData);

			// Redirect back to the edit screen.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToItemAppend($recordId, $key), false),
				Lang::txt('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()),
				'error'
			);
		}

		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData[$key]) === false)
		{
			// Save the data in the session.
			User::setState($context . '.data', $validData);

			// Check-in failed, so go back to the record and display a notice.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . $this->getRedirectToItemAppend($recordId, $key), false),
				Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()),
				'error'
			);
		}

		Notify::success(
			Lang::txt(
				(Lang::hasKey($this->text_prefix . '_SAVE_SUCCESS') ? $this->text_prefix : 'JLIB_APPLICATION') . '_SAVE_SUCCESS'
			)
		);

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
				//$recordId = $model->getState($this->context . '.id');
				$this->holdEditId($context, $recordId);
				User::setState($context . '.data', null);
				$model->checkout($recordId);

				// Redirect back to the edit screen.
				App::redirect(
					Route::url('index.php?option=' . $this->_option . $this->getRedirectToItemAppend($recordId, $key), false)
				);
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				User::setState($context . '.data', null);

				// Redirect to the list screen.
				$url = Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false);
				if ($component = Request::getVar('component', ''))
				{
					$url = Route::url('index.php?option=com_' . $component . '&controller=plugins', false);
				}

				App::redirect(
					$url
				);
				break;
		}
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Session::checkToken() or die(Lang::txt('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = Request::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			throw new Exception(Lang::txt($this->text_prefix . '_NO_ITEM_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = new Models\Plugin();

			// Make sure the item ids are integers
			Arr::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				Notify::success(Lang::txts($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				Notify::error($model->getError());
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
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
		Session::checkToken() or die(Lang::txt('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid   = Request::getVar('cid', array(), '', 'array');
		$data  = array(
			'publish'   => 1,
			'unpublish' => 0,
			'archive'   => 2,
			'trash'     => -2,
			'report'    => -3
		);
		$task  = $this->getTask();
		$value = Arr::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			throw new Exception(Lang::txt($this->text_prefix . '_NO_ITEM_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = new Models\Plugin();

			// Make sure the item ids are integers
			Arr::toInteger($cid);

			// Publish the items.
			if (!$model->publish($cid, $value))
			{
				throw new Exception($model->getError(), 500);
			}
			else
			{
				if ($value == 1)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value == 2)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
				}
				Notify::success(Lang::txts($ntext, count($cid)));
			}
		}

		$extension    = Request::getCmd('extension');
		$extensionURL = ($extension) ? '&extension=' . Request::getCmd('extension') : '';

		App::redirect(
			Route::url('index.php?option=' . $this->_option . $extensionURL, false)
		);
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return  boolean  True on success
	 */
	public function reorderTask()
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$ids = Request::getVar('cid', null, 'post', 'array');
		$inc = ($this->getTask() == 'orderup') ? -1 : +1;

		$model  = new Models\Plugin();
		$return = $model->reorder($ids, $inc);

		if ($return === false)
		{
			// Reorder failed.
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError()));
		}
		else
		{
			// Reorder succeeded.
			Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED'));
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 */
	public function saveorderTask()
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Get the input
		$pks   = Request::getVar('cid', null, 'post', 'array');
		$order = Request::getVar('order', null, 'post', 'array');

		// Sanitize the input
		Arr::toInteger($pks);
		Arr::toInteger($order);

		// Get the model
		$model  = new Models\Plugin();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError()));
		}
		else
		{
			// Reorder succeeded.
			Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}

	/**
	 * Check in of one or more records.
	 *
	 * @return  void
	 */
	public function checkinTask()
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$ids = Request::getVar('cid', null, 'post', 'array');

		$model  = new Models\Plugin();
		$return = $model->checkin($ids);

		if ($return === false)
		{
			// Checkin failed.
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
		}
		else
		{
			// Checkin succeeded.
			Notify::success(Lang::txts($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids)));
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string   $key  The name of the primary key of the URL variable.
	 * @return  boolean  True if access level checks pass, false otherwise.
	 */
	public function cancelTask()
	{
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$model   = new Models\Plugin();
		$table   = $model->getTable();
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->_option.edit.plugin";
		$key     = $table->getKeyName();

		$recordId = Request::getInt($key);

		// Attempt to check-in the current record.
		if ($recordId)
		{
			// Check we are holding the id in the edit list.
			if (!$this->checkEditId($context, $recordId))
			{
				// Somehow the person just went to the form - we don't allow that.
				App::redirect(
					Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false),
					Lang::txt('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId),
					'error'
				);
			}

			if ($checkin)
			{
				if ($model->checkin($recordId) === false)
				{
					// Check-in failed, go back to the record and display a notice.
					App::redirect(
						Route::url('index.php?option=' . $this->_option . $this->getRedirectToItemAppend($recordId, $key), false),
						Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()),
						'error'
					);
				}
			}
		}

		// Clean the session data and redirect.
		$this->releaseEditId($context, $recordId);

		User::setState($context . '.data', null);

		if ($component = Request::getVar('component', ''))
		{
			// Redirect to the list screen.
			App::redirect(
				Route::url('index.php?option=com_' . $component . '&controller=plugins', false)
			);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false)
		);
	}

	/**
	 * Method to add a record ID to the edit list.
	 *
	 * @param   string   $context  The context for the session storage.
	 * @param   integer  $id       The ID of the record to add to the edit list.
	 * @return  void
	 */
	protected function holdEditId($context, $id)
	{
		// Initialise variables.
		$values = (array) User::getState($context . '.id');

		// Add the id to the list if non-zero.
		if (!empty($id))
		{
			array_push($values, (int) $id);
			$values = array_unique($values);
			User::setState($context . '.id', $values);
		}
	}

	/**
	 * Method to check whether an ID is in the edit list.
	 *
	 * @param   string   $context  The context for the session storage.
	 * @param   integer  $id       The ID of the record to add to the edit list.
	 * @return  void
	 */
	protected function releaseEditId($context, $id)
	{
		$values = (array) User::getState($context . '.id');

		// Do a strict search of the edit list values.
		$index = array_search((int) $id, $values, true);

		if (is_int($index))
		{
			unset($values[$index]);
			User::setState($context . '.id', $values);
		}
	}

	/**
	 * Method to check whether an ID is in the edit list.
	 *
	 * @param   string   $context  The context for the session storage.
	 * @param   integer  $id       The ID of the record to add to the edit list.
	 * @return  boolean  True if the ID is in the edit list.
	 */
	protected function checkEditId($context, $id)
	{
		if ($id)
		{
			$values = (array) User::getState($context . '.id');

			return in_array((int) $id, $values);
		}

		return true;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToListAppend()
	{
		$append = '';

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
		$tmpl   = Request::getCmd('tmpl');
		$layout = Request::getCmd('layout', 'edit');
		$append = '';

		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout)
		{
			$append .= '&task=' . $layout;
		}

		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 * @return  boolean
	 */
	protected function allowAdd($data = array())
	{
		return (User::authorise('core.create', $this->_option) || count(User::getAuthorisedCategories($this->_option, 'core.create')));
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return User::authorise('core.edit', $this->_option);
	}

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 * @return  boolean
	 */
	protected function allowSave($data, $key = 'id')
	{
		$recordId = isset($data[$key]) ? $data[$key] : '0';

		if ($recordId)
		{
			return $this->allowEdit($data, $key);
		}

		return $this->allowAdd($data);
	}
}
