<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Site\Controllers;

use Components\Support\Helpers\Utilities;
use Components\Support\Models\Conditions;
use Components\Support\Models\Query;
use Components\Support\Models\QueryFolder;
use Hubzero\Component\SiteController;
use stdClass;
use Request;
use Route;
use Lang;
use User;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'ticket.php';

/**
 * Support controller class for ticket queries
 */
class Queries extends SiteController
{
	/**
	 * Displays a list of records
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=tickets&task=display', false)
		);
	}

	/**
	 * Create a new record
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @return  void
	 */
	public function editTask()
	{
		$lists = array();
		$lists['severities'] = Utilities::getSeverities($this->config->get('severities'));

		$id = Request::getInt('id', 0);

		$row = Query::oneOrNew($id);
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

		// Output the HTML
		$this->view
			->set('lists', $lists)
			->set('row', $row)
			->set('conditions', $conditions)
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

		// Incoming
		$fields  = Request::getArray('fields', array(), 'post');
		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getCmd('component', '');

		$row = Query::oneOrNew($fields['id'])->set($fields);
		$row->set('query', $row->toSql());

		if ($row->isNew())
		{
			$row->set('id', null);
		}

		// Store new content
		if (!$row->save())
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->setError($row->getError());
				$this->editTask($row);
			}
			else
			{
				echo $row->getError();
			}
			return;
		}

		if (!$no_html && $tmpl != 'component')
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=tickets&task=display&show=' . $row->get('id'), false)
			);
		}
		else
		{
			$this->listTask();
		}
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Incoming
		$id      = Request::getInt('id', 0);
		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getCmd('component', '');

		// Check for an ID
		if (!$id)
		{
			if (!$no_html && $tmpl != 'component')
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=tickets&task=display', false),
					Lang::txt('COM_SUPPORT_ERROR_SELECT_QUERY_TO_DELETE'),
					'error'
				);
			}
			return;
		}

		$row = Query::oneOrFail(intval($id));
		$row->destroy();

		if (!$no_html && $tmpl != 'component')
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=tickets&task=display', false)
			);
		}
		else
		{
			$this->listTask();
		}
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
			->set('folders', $folders)
			->set('show', 0)
			->setLayout('list')
			->display();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=tickets&task=display', false)
		);
	}

	/**
	 * Create a new folder
	 *
	 * @return  void
	 */
	public function addfolderTask()
	{
		$this->editfolderTask();
	}

	/**
	 * Display a form for adding/editing a folder
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editfolderTask($row=null)
	{
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
	public function applyfolderTask()
	{
		$this->savefolderTask(false);
	}

	/**
	 * Save a folder
	 *
	 * @param   boolean  $redirect
	 * @return  void
	 */
	public function savefolderTask($redirect=true)
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

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
			if (!$no_html && $tmpl != 'component')
			{
				$this->setError($row->getError());
				$this->editfolderTask($row);
			}
			else
			{
				$response->success = 0;
				$response->message = $row->getError();
				echo json_encode($response);
			}
			return;
		}

		if ($redirect)
		{
			if (!$no_html && $tmpl != 'component')
			{
				// Output messsage and redirect
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets', false),
					Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_SAVED')
				);
				return;
			}

			$this->listTask();
			return;
		}

		$this->editfolderTask($row);
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

		foreach ($ids as $id)
		{
			$row = QueryFolder::oneOrFail(intval($id));
			$row->destroy();
		}

		if (!$no_html)
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED')
			);
			return;
		}

		$this->listTask();
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

		if (!Request::getInt('no_html'))
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED')
			);
		}

		$response = new stdClass;
		$response->success = 1;
		$response->message = Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED');

		echo json_encode($response);
	}
}
