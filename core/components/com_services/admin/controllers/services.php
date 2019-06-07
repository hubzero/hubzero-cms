<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Admin\Controllers;

use Components\Services\Models\Service;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use Lang;
use Date;
use App;

/**
 * Controller class for services
 */
class Services extends AdminController
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
	 * Services List
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'category'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// get all available services
		$query = Service::all();

		// Get records
		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Initial setup of default jobs services
	 *
	 * @return  boolean
	 */
	protected function setupServices()
	{
		$now = Date::toSql();

		$defaults = array(
			array(
				'title' => Lang::txt('COM_SERVICES_BASIC_SERVICE_TITLE'),
				'category' => strtolower(Lang::txt('COM_SERVICES_JOBS')),
				'alias' => 'employer_basic',
				'status' => 1,
				'description' => Lang::txt('COM_SERVICES_BASIC_SERVICE_DESC'),
				'unitprice' => '0.00',
				'pointprice' => 0,
				'currency' => '$',
				'maxunits' => 6,
				'minunits' => 1,
				'unitsize' => 1,
				'unitmeasure' => strtolower(Lang::txt('month')),
				'changed' => $now,
				'params' => "promo=" . Lang::txt('COM_SERVICES_BASIC_SERVICE_PROMO') . "\npromomaxunits=3\nmaxads=1"
			),
			array(
				'title' => Lang::txt('COM_SERVICES_PREMIUM_SERVICE_TITLE'),
				'category' => strtolower(Lang::txt('COM_SERVICES_JOBS')),
				'alias' => 'employer_premium',
				'status' => 0,
				'description' => Lang::txt('COM_SERVICES_PREMIUM_SERVICE_DESC'),
				'unitprice' => '500.00',
				'pointprice' => 0,
				'currency' => '$',
				'maxunits' => 6,
				'minunits' => 1,
				'unitsize' => 1,
				'unitmeasure' => strtolower(Lang::txt('month')),
				'changed' => $now,
				'params' => "promo=\npromomaxunits=\nmaxads=3"
			)
		);

		foreach ($defaults as $data)
		{
			$row = Service::blank()->set($data);

			if (!$row->save())
			{
				$this->setError($row->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Edit an entry
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = Request::getArray('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			// load infor from database
			$row = Service::oneOrNew($id);
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Saves an entry and redirects to listing
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
		$fields = Request::getArray('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = Service::oneOrNew($fields['id'])->set($fields);

		// Store content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Notify of success
		Notify::success(Lang::txt('COM_SERVICES_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Delete one or more entries
	 *
	 * @return 	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// get the request vars
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;

		foreach ($ids as $id)
		{
			$row = Service::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				Notify::error(Lang::txt('COM_SERVICES_DELETE_FAILED', $id));
				continue;
			}

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_SERVICES_DELETE_SUCCESS'));
		}

		// Redirect back to list
		$this->cancelTask();
	}
}
