<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Admin\Controllers;

use Components\Tools\Helpers\Utils;
use Components\Tools\Models\Middleware;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'middleware.php';

/**
 * Administrative tools controller for zone locations
 */
class Locations extends AdminController
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
	 * Display a list of hosts
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'zone' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.zone',
				'zone',
				0,
				'int'
			)),
			'tmpl' => Request::getState(
				$this->_option . '.' . $this->_controller . '.tmpl',
				'tmpl',
				''
			),
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'zone'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		if ($this->view->filters['tmpl'] == 'component')
		{
			$this->view->setLayout('component');
		}
		else
		{
			// Get paging variables
			$this->view->filters['limit'] = Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			);
			$this->view->filters['start'] = Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			);

			// In case limit has been changed, adjust limitstart accordingly
			$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);
		}

		// Get the middleware database
		$this->view->zone = new Middleware\Zone($this->view->filters['zone']);

		$this->view->total = $this->view->zone->locations('count', $this->view->filters);

		$this->view->rows  = $this->view->zone->locations('list', $this->view->filters);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Edit a record
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		$mw = new Middleware($mwdb);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getInt('id', 0);

			$row = new Middleware\Location($id);
		}

		$this->view->row = $row;

		$this->view->zone = Request::getInt('zone', 0);
		if (!$this->view->row->exists())
		{
			$this->view->row->set('zone_id', $this->view->zone);
		}
		$this->view->tmpl = Request::getCmd('tmpl', '');

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save changes to a record
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');
		$tmpl   = Request::getCmd('tmpl', '');

		$row = new Middleware\Location($fields['id']);
		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store(true))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		if ($tmpl == 'component')
		{
			if ($this->getError())
			{
				echo '<p class="error">' . $this->getError() . '</p>';
			}
			else
			{
				echo '<p class="message">' . Lang::txt('COM_TOOLS_ITEM_SAVED') . '</p>';
			}
			return;
		}

		Notify::success(Lang::txt('COM_TOOLS_ITEM_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Toggle a zone's state
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming
		$id = Request::getInt('id', 0);
		$state = strtolower(Request::getWord('state', 'up'));

		if ($state != 'up' && $state != 'down')
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		$row = new Middleware\Location($id);
		if ($row->exists())
		{
			$row->set('state', $state);
			if (!$row->store())
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_TOOLS_ERROR_STATE_UPDATE_FAILED'),
					'error'
				);
				return;
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Delete one or more records
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$ids = Request::getArray('id', array());

		if (count($ids) > 0)
		{
			// Loop through each ID
			foreach ($ids as $id)
			{
				$row = new Middleware\Location(intval($id));

				if (!$row->delete())
				{
					throw new \Exception($row->getError(), 500);
				}
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_ITEM_DELETED'),
			'message'
		);
	}
}
