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
use Components\Plugins\Models\Plugin;
use Request;
use Notify;
use Route;
use Cache;
use Event;
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
		$filters = array(
			'folder' => Request::getState(
				$this->_option . '.' . $this->_controller . '.folder',
				'filter_folder',
				''
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'filter_state',
				''
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'filter_access',
				0,
				'int'
			),
			'enabled' => Request::getState(
				$this->_option . '.' . $this->_controller . '.enabled',
				'filter_state',
				'',
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'folder'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$query = Plugin::all()
			->where('state', '>=', 0);

		$p = $query->getTableName();
		$u = '#__users';
		$a = '#__viewlevels';

		$query->select($p . '.*');

		// Join over the users for the checked out user.
		$query
			->select($u . '.name', 'editor')
			->join($u, $u . '.id', $p . '.checked_out', 'left');

		// Join over the access groups.
		$query
			->select($a . '.title', 'access_level')
			->join($a, $a . '.id', $p . '.access', 'left');

		// Filter by access level.
		if ($filters['access'])
		{
			$query->whereEquals($p . '.access', (int) $filters['access']);
		}

		// Filter by published state
		if (is_numeric($filters['state']))
		{
			$query->whereEquals($p . '.enabled', (int) $filters['state']);
		}
		elseif ($filters['state'] === '')
		{
			$query->whereIn($p . '.enabled', array(0, 1));
		}

		// Filter by folder.
		if ($filters['folder'])
		{
			$query->whereEquals($p . '.folder', $filters['folder']);
		}

		// Filter by search in id
		if (!empty($filters['search']) && stripos($filters['search'], 'id:') === 0)
		{
			$query->whereEquals($p . '.extension_id', (int) substr($filters['search'], 3));
		}

		if ($filters['sort'] == 'name')
		{
			$query->order('name', $filters['sort_Dir']);
			$query->order('ordering', 'asc');
		}
		else if ($filters['sort'] == 'ordering')
		{
			$query->order('folder', 'asc');
			$query->order('ordering', $filters['sort_Dir']);
			$query->order('name', 'asc');
		}
		else
		{
			$query->order($filters['sort'], $filters['sort_Dir']);
			$query->order('name', 'asc');
			$query->order('ordering', 'asc');
		}

		$items = $query
			->paginated('limitstart', 'limit')
			->rows();

		// Check if there are no matching items
		if (!count($items))
		{
			Notify::warning(Lang::txt('COM_PLUGINS_MSG_MANAGE_NO_PLUGINS'));
		}

		$this->view
			->set('filters', $filters)
			->set('items', $items)
			->display();
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   object  $model
	 * @return  void
	 */
	public function editTask($model = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($model))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the record
			$model = Plugin::oneOrNew($id);
		}

		// Fail if checked out not by 'me'
		if ($model->get('checked_out') && $model->get('checked_out') != User::get('id'))
		{
			Notify::warning(Lang::txt('COM_PLUGINS_CHECKED_OUT'));
			return $this->cancelTask();
		}

		if (!$model->isNew())
		{
			// Checkout the record
			$model->checkout();
			// Check-out failed, display a notice but allow the user to see the record.
			//Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			//return $this->cancelTask();
		}

		// Load the language file
		$model->loadLanguage();

		$this->view
			->set('item', $model)
			->set('form', $model->getForm())
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
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// Initiate extended database class
		$model = Plugin::oneOrNew($fields['extension_id'])->set($fields);

		// Get parameters
		$params = Request::getVar('params', array(), 'post');

		$p = $model->params;

		if (is_array($params))
		{
			$txt = array();
			foreach ($params as $k => $v)
			{
				$p->set($k, $v);
			}
			$model->set('params', $p->toString());
		}

		// Trigger before save event
		$result = Event::trigger('onExtensionBeforeSave', array($this->_option . '.plugin', &$model, $model->isNew()));

		if (in_array(false, $result, true))
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		// Store content
		if (!$model->save())
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		// Trigger after save event
		Event::trigger('onExtensionAfterSave', array($this->_option . '.plugin', &$model, $model->isNew()));

		// Clean the cache.
		$this->cleanCache();

		// Success message
		Notify::success(Lang::txt('COM_PLUGINS_SAVE_SUCCESS'));

		// Display the edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($model);
		}

		// Check the record back in
		$model->checkin();

		/*if (!$model->checkin())
		{
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
		}*/

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$cid = Request::getInt('cid', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$success = 0;

		foreach ($ids as $id)
		{
			// Load the record
			$model = Plugin::oneOrFail(intval($id));

			// Trigger before delete event
			Event::trigger('onExtensionBeforeDelete', array('com_plugins.plugin', $model->getTableName()));

			// Attempt to delete the record
			if (!$model->destroy())
			{
				Notify::error($model->getError());
				continue;
			}

			// Trigger after delete event
			Event::trigger('onExtensionAfterDelete', array('com_plugins.plugin', $model->getTableName()));

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			Notify::success(Lang::txt('COM_PLUGINS_N_ITEMS_DELETED', $success));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

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

		$success = 0;

		foreach ($cid as $id)
		{
			// Load the record
			$model = Plugin::oneOrFail(intval($id));

			// Set state
			$model->set('enabled', $value);

			if (!$model->save())
			{
				Notify::error($model->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			if ($value == 1)
			{
				$ntext = 'COM_PLUGINS_N_ITEMS_PUBLISHED';
			}
			elseif ($value == 0)
			{
				$ntext = 'COM_PLUGINS_N_ITEMS_UNPUBLISHED';
			}
			elseif ($value == 2)
			{
				$ntext = 'COM_PLUGINS_N_ITEMS_ARCHIVED';
			}
			else
			{
				$ntext = 'COM_PLUGINS_N_ITEMS_TRASHED';
			}

			Notify::success(Lang::txts($ntext, $success));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return  boolean  True on success
	 */
	public function reorderTask()
	{
		// Check for request forgeries.
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Initialise variables.
		$ids = Request::getVar('cid', null, 'post', 'array');
		$inc = ($this->getTask() == 'orderup') ? -1 : +1;

		$success = 0;

		foreach ($ids as $id)
		{
			// Load the record and reorder it
			$model = Plugin::oneOrFail(intval($id));

			if (!$model->move($inc))
			{
				Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError()));
				continue;
			}

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED'));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 */
	public function saveorderTask()
	{
		// Check for request forgeries.
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Get the input
		$pks   = Request::getVar('cid', null, 'post', 'array');
		$order = Request::getVar('order', null, 'post', 'array');

		// Sanitize the input
		Arr::toInteger($pks);
		Arr::toInteger($order);

		// Save the ordering
		$return = Plugin::saveorder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_REORDER_FAILED'));
		}
		else
		{
			// Clean the cache.
			$this->cleanCache();

			// Reorder succeeded.
			Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Check in of one or more records.
	 *
	 * @return  void
	 */
	public function checkinTask()
	{
		// Check for request forgeries.
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('cid', null, 'post', 'array');

		foreach ($ids as $id)
		{
			$model = Plugin::oneOrFail(intval($id));
			$model->checkin();

			/*if (!$model->checkin())
			{
				Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				continue;
			}*/
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string   $key  The name of the primary key of the URL variable.
	 * @return  boolean  True if access level checks pass, false otherwise.
	 */
	public function cancelTask()
	{
		// Attempt to check-in the current record.
		if ($id = Request::getInt('id', 0))
		{
			$model = Plugin::oneOrNew($id);

			if ($model->get('checked_out') && $model->get('checked_out') == User::get('id'))
			{
				$model->checkin();
				// Check-in failed, go back to the record and display a notice.
				/*if (!$model->checkin())
				{
					Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				}*/
			}
		}

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
	 * Clean cached data
	 *
	 * @return  void
	 */
	public function cleanCache()
	{
		Cache::clean($this->_option);
	}
}
