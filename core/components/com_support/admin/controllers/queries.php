<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Support\Models\Conditions;
use Components\Support\Models\Query;
use Components\Support\Models\QueryFolder;
use Components\Support\Helpers\Utilities;
use stdClass;
use Request;
use Config;
use Route;
use Lang;
use User;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'ticket.php';
include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'queryfolder.php';

/**
 * Support controller class for ticket queries
 */
class Queries extends AdminController
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
		$this->registerTask('addfolder', 'editfolder');
		$this->registerTask('applyfolder', 'savefolder');

		parent::execute();
	}

	/**
	 * Displays a list of records
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get paging variables
		$filters = array(
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
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'iscore' => array(4, 2, 1)
		);

		$query = Query::all();

		$rows = $query
			->whereIn('iscore', $filters['iscore'])
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
	 * Display a form for adding/editing a record
	 *
	 * @param   object  $row
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
				$id = (!empty($id) ? $id[0] : 0);
			}

			$row = Query::oneOrNew($id);
		}

		if (!$row->get('sort'))
		{
			$row->set('sort', 'created');
		}
		if (!$row->get('sort_dir'))
		{
			$row->set('sort_dir', 'desc');
		}

		include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'conditions.php';
		$con = new Conditions();
		$conditions = $con->getConditions();

		$severities = Utilities::getSeverities($this->config->get('severities'));

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('conditions', $conditions)
			->set('severities', $severities)
			->setLayout('edit')
			->display();
	}

	/**
	 * Create a new record
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
		$fields  = Request::getArray('fields', array(), 'post');
		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getString('component', '');

		$row = Query::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			if ($no_html || $tmpl == 'component')
			{
				echo $row->getError();
				return;
			}

			Notify::error($row->getError());
			return $this->editTask($row);
		}

		if ($no_html && $tmpl == 'component')
		{
			return $this->listTask();
		}

		Notify::success(Lang::txt('COM_SUPPORT_QUERY_SUCCESSFULLY_SAVED'));

		$this->cancelTask();
	}

	/**
	 * Build the query list
	 *
	 * @return  void
	 */
	public function listTask()
	{
		// Get query list
		$folders = QueryFolder::all()
			->whereEquals('user_id', User::get('id'))
			->order('ordering', 'asc')
			->rows();

		// Output the HTML
		$this->view
			->set('show', 0)
			->set('folders', $folders)
			->setLayout('list')
			->display();
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['post', 'get']);

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getString('component', '');

		// Check for an ID
		if (count($ids) < 1)
		{
			if ($no_html || $tmpl == 'component')
			{
				return;
			}

			Notify::warning(Lang::txt('COM_SUPPORT_ERROR_SELECT_QUERY_TO_DELETE'));
			return $this->cancelTask();
		}

		$removed = 0;

		foreach ($ids as $id)
		{
			// Delete entry
			$row = Query::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$removed++;
		}

		if ($no_html || $tmpl == 'component')
		{
			return $this->listTask();
		}

		// Output messsage and redirect
		if ($removed)
		{
			Notify::success(Lang::txt('COM_SUPPORT_QUERY_SUCCESSFULLY_DELETED', $removed));
		}

		$this->cancelTask();
	}

	/**
	 * Display a form for adding/editing a folder
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editfolderTask($row=null)
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

			$row = QueryFolder::oneOrNew($id);
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('editfolder')
			->display();
	}

	/**
	 * Save a folder
	 *
	 * @return  void
	 */
	public function savefolderTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields  = Request::getArray('fields', array());
		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getString('component', '');

		$response = new stdClass;
		$response->success = 1;
		$response->message = '';

		$row = QueryFolder::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			if ($no_html || $tmpl == 'component')
			{
				$response->success = 0;
				$response->message = $row->getError();
				echo json_encode($response);
			}

			Notify::error($row->getError());
			return $this->editfolderTask($row);
		}

		if (!$no_html && $tmpl != 'component')
		{
			Notify::success(Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_SAVED'));
		}

		if ($this->getTask() == 'applyfolder')
		{
			if ($no_html || $tmpl == 'component')
			{
				return $this->listTask();
			}

			return $this->editfolderTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Remove a folder
	 *
	 * @return  void
	 */
	public function removefolderTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (is_array($ids) ?: array($ids));

		$no_html = Request::getInt('no_html', 0);
		$removed = 0;

		foreach ($ids as $id)
		{
			$row = QueryFolder::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$removed++;
		}

		if ($no_html)
		{
			return $this->listTask();
		}

		// Output messsage and redirect
		if ($removed)
		{
			Notify::success(Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED'));
		}

		$this->cancelTask();
	}

	/**
	 * Remove a folder
	 *
	 * @return  void
	 */
	public function saveorderingTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$folders = Request::getArray('folder', array());
		$queries = Request::getArray('queries', array());

		if (is_array($folders))
		{
			foreach ($folders as $key => $folder)
			{
				$row = QueryFolder::oneOrFail(intval($folder));
				$row->set('ordering', $key + 1);
				$row->save();
			}
		}

		if (is_array($queries))
		{
			$folder = null;
			$i = 0;

			foreach ($queries as $query)
			{
				$bits = explode('_', $query);

				$fd = intval($bits[0]);
				$id = intval($bits[1]);

				if ($fd != $folder)
				{
					$folder = $fd;
					$i = 0;
				}

				$row = Query::oneOrFail($id);
				$row->set('folder_id', $fd);
				$row->set('ordering', $i + 1);
				$row->save();

				$i++;
			}
		}

		if (!$no_html)
		{
			// Output messsage and redirect
			Notify::success(Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED'));
			return $this->cancelTask();
		}

		$response = new stdClass;
		$response->success = 1;
		$response->message = Lang::txt('COM_SUPPORT_QUERY_FOLDER_ORDERING_UPDATED');

		echo json_encode($response);
	}

	/**
	 * Reset default queries and folders.
	 * System will repopulate them.
	 *
	 * @return  void
	 */
	public function resetTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.admin', $this->_option))
		{
			return $this->cancelTask();
		}

		foreach (QueryFolder::all()->rows() as $folder)
		{
			$folder->destroy();
		}

		foreach (Query::all()->rows() as $query)
		{
			$query->destroy();
		}

		// Get all the default folders
		$folders = QueryFolder::all()
			->whereEquals('user_id', 0)
			->whereEquals('iscore', 1)
			->order('ordering', 'asc')
			->rows();

		if (count($folders) <= 0)
		{
			$defaults = array(
				1 => array('Common', 'Mine', 'Custom'),
				2 => array('Common', 'Mine'),
			);

			foreach ($defaults as $iscore => $fldrs)
			{
				$i = 1;

				foreach ($fldrs as $fldr)
				{
					$f = QueryFolder::blank()
						->set(array(
							'iscore'   => $iscore,
							'title'    => $fldr,
							'ordering' => $i,
							'user_id'  => 0
						));

					$f->save();

					switch ($f->get('alias'))
					{
						case 'common':
							$j = ($iscore == 1 ? Query::populateDefaults('common', $f->get('id')) : Query::populateDefaults('commonnotacl', $f->get('id')));
						break;

						case 'mine':
							Query::populateDefaults('mine', $f->get('id'));
						break;

						default:
							// Nothing for custom folder
						break;
					}

					$i++;
				}
			}
		}

		$this->cancelTask();
	}
}
