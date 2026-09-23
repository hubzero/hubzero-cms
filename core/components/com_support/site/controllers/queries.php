<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
use App;

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

		// A saved query belongs to the user who created it; user_id 0 is a
		// shared core query. saveTask already enforces this -- without it here
		// the edit form handed any caller any user's saved query, title and
		// conditions included.
		if (!$row->isNew()
		 && (int) $row->get('user_id') !== 0
		 && (int) $row->get('user_id') !== (int) User::get('id'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
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
		// Saved queries belong to logged-in users; a guest (id 0) would
		// otherwise match the shared core rows (user_id 0) below.
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields  = Request::getArray('fields', array(), 'post');
		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getCmd('component', '');

		$row = Query::oneOrNew(isset($fields['id']) ? (int) $fields['id'] : 0);

		// A saved query belongs to the user who created it; the core rows
		// (iscore) are the component's and nobody's to edit here
		if (!$row->isNew() && ($row->get('iscore') || $row->get('user_id') != User::get('id')))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// iscore is what makes a query show for every non-agent; a folder is
		// only the caller's own
		unset($fields['iscore']);
		if (!empty($fields['folder_id']))
		{
			$__folder = QueryFolder::oneOrNew((int) $fields['folder_id']);
			if ($__folder->isNew() || $__folder->get('user_id') != User::get('id'))
			{
				App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			}
		}

		$row->set($fields);
		$row->set('user_id', User::get('id'));
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
		// Saved queries belong to logged-in users; a guest (id 0) would
		// otherwise match the shared core rows (user_id 0) below.
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// The only link to this task appends Session::getFormToken() to the
		// URL (site/views/queries/tmpl/list.php), exactly as the folder delete
		// beside it does, so there is a caller to honour the check.
		Request::checkToken(['get', 'post']);

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
		if ($row->get('iscore'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Only the owner may delete their saved query
		if ($row->get('user_id') != User::get('id'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}
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

			// As editTask above: a folder belongs to its creator.
			if (!$row->isNew()
			 && (int) $row->get('user_id') !== 0
			 && (int) $row->get('user_id') !== (int) User::get('id'))
			{
				App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			}
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
		// Saved queries belong to logged-in users; a guest (id 0) would
		// otherwise match the shared core rows (user_id 0) below.
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$fields  = Request::getArray('fields', array());
		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getString('component', '');

		$response = new stdClass;
		$response->success = 1;
		$response->message = '';

		$row = QueryFolder::oneOrNew(isset($fields['id']) ? $fields['id'] : 0);
		if (!$row->isNew() && $row->get('user_id') != User::get('id'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}
		$row->set($fields);
		$row->set('user_id', User::get('id'));

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
		// Saved queries belong to logged-in users; a guest (id 0) would
		// otherwise match the shared core rows (user_id 0) below.
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

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
		// Saved queries belong to logged-in users; a guest (id 0) would
		// otherwise match the shared core rows (user_id 0) below.
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

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
				if ($row->get('user_id') != User::get('id'))
				{
					continue;
				}
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
				$id = isset($bits[1]) ? intval($bits[1]) : 0;

				if (!$id)
				{
					continue;
				}

				// The destination folder has to be the caller's too. Checking
				// only the query let an owner drop their own query into someone
				// else's sidebar. Folder 0 is "unfiled".
				if ($fd)
				{
					$dest = QueryFolder::oneOrNew($fd);

					if (!$dest->get('id') || $dest->get('user_id') != User::get('id'))
					{
						continue;
					}
				}

				if ($fd != $folder)
				{
					$folder = $fd;
					$i = 0;
				}

				$row = Query::oneOrFail($id);
				if ($row->get('user_id') != User::get('id'))
				{
					continue;
				}
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
