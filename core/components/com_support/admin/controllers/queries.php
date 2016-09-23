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

namespace Components\Support\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Support\Models\Conditions;
use Components\Support\Helpers\Utilities;
use Components\Support\Tables\Ticket;
use Components\Support\Tables\Resolution;
use Components\Support\Tables\Query;
use Components\Support\Tables\QueryFolder;
use stdClass;
use Request;
use Config;
use Route;
use Lang;
use User;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'ticket.php');
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'query.php');
include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'queryfolder.php');

/**
 * Support controller class for ticket queries
 */
class Queries extends AdminController
{
	/**
	 * Displays a list of records
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get paging variables
		$this->view->filters = array(
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

		$obj = new Query($this->database);

		// Record count
		$this->view->total = $obj->find('count', $this->view->filters);

		// Fetch results
		$this->view->rows  = $obj->find('list', $this->view->filters);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new record
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @return	void
	 */
	public function editTask()
	{
		Request::setVar('hidemainmenu', 1);

		$this->view->lists = array();

		// Get resolutions
		$sr = new Resolution($this->database);
		$this->view->lists['resolutions'] = $sr->getResolutions();

		$this->view->lists['severities'] = Utilities::getSeverities($this->config->get('severities'));

		$id = Request::getVar('id', array(0));
		if (is_array($id))
		{
			$id = (!empty($id) ? $id[0] : 0);
		}

		$this->view->row = new Query($this->database);
		$this->view->row->load($id);
		if (!$this->view->row->sort)
		{
			$this->view->row->sort = 'created';
		}
		if (!$this->view->row->sort_dir)
		{
			$this->view->row->sort_dir = 'desc';
		}

		include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'conditions.php');
		$con = new Conditions();
		$this->view->conditions = $con->getConditions();

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
	 * Create a new record
	 *
	 * @return	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields  = Request::getVar('fields', array(), 'post');
		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getVar('component', '');

		$row = new Query($this->database);
		if (!$row->bind($fields))
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

		// Check content
		if (!$row->check())
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

		// Store new content
		if (!$row->store())
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

		$row->reorder('folder_id=' . $this->database->Quote($row->folder_id));

		if (!$no_html && $tmpl != 'component')
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SUPPORT_QUERY_SUCCESSFULLY_SAVED')
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
		$obj = new Ticket($this->database);

		// Get query list
		$sf = new QueryFolder($this->database);
		$this->view->folders = $sf->find('list', array(
			'user_id'  => User::get('id'),
			'sort'     => 'ordering',
			'sort_Dir' => 'asc'
		));

		$sq = new Query($this->database);
		$queries = $sq->find('list', array(
			'user_id'  => User::get('id'),
			'sort'     => 'ordering',
			'sort_Dir' => 'asc'
		));

		foreach ($queries as $query)
		{
			$query->query = $sq->getQuery($query->conditions);
			$query->count = $obj->getCount($query->query);

			foreach ($this->view->folders as $k => $v)
			{
				if (!isset($this->view->folders[$k]->queries))
				{
					$this->view->folders[$k]->queries = array();
				}
				if ($query->folder_id == $v->id)
				{
					$this->view->folders[$k]->queries[] = $query;
				}
			}
		}

		$this->view->show = 0;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
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

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getVar('component', '');

		// Check for an ID
		if (count($ids) < 1)
		{
			if (!$no_html && $tmpl != 'component')
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_SUPPORT_ERROR_SELECT_QUERY_TO_DELETE'),
					'error'
				);
			}
			return;
		}

		$row = new Query($this->database);
		foreach ($ids as $id)
		{
			// Delete message
			$row->delete(intval($id));
		}

		if (!$no_html && $tmpl != 'component')
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SUPPORT_QUERY_SUCCESSFULLY_DELETED', count($ids))
			);
		}
		else
		{
			$this->listTask();
		}
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
	 * @return  void
	 */
	public function editfolderTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			$row = new QueryFolder($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
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
	 * @return  void
	 */
	public function savefolderTask($redirect=true)
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$fields  = Request::getVar('fields', array());
		$no_html = Request::getInt('no_html', 0);
		$tmpl    = Request::getVar('component', '');

		$response = new stdClass;
		$response->success = 1;
		$response->message = '';

		$row = new QueryFolder($this->database);
		if (!$row->bind($fields))
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

		// Check content
		if (!$row->check())
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

		// Store new content
		if (!$row->store())
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
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_SAVED')
				);
			}
			else
			{
				$this->listTask();
			}
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
		$ids = Request::getVar('id', array());
		$ids = (is_array($ids) ?: array($ids));

		$no_html = Request::getInt('no_html', 0);

		foreach ($ids as $id)
		{
			$row = new Query($this->database);
			$row->deleteByFolder(intval($id));

			$row = new QueryFolder($this->database);
			$row->delete(intval($id));
		}

		if (!$no_html)
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED')
			);
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
		$folders = Request::getVar('folder', array());
		$queries = Request::getVar('queries', array());

		if (is_array($folders))
		{
			foreach ($folders as $key => $folder)
			{
				$row = new QueryFolder($this->database);
				$row->load(intval($folder));
				$row->ordering = $key + 1;
				$row->store();
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

				$row = new Query($this->database);
				$row->load($id);
				$row->folder_id = $fd;
				$row->ordering  = $i + 1;
				$row->store();

				$i++;
			}
		}

		if (!$no_html)
		{
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED')
			);
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

		$db = App::get('db');

		$query  = new Query($db);
		$folder = new QueryFolder($db);

		$db->setQuery("DELETE FROM " . $db->quoteName($query->getTableName()));
		$db->query();

		$db->setQuery("DELETE FROM " . $db->quoteName($folder->getTableName()));
		$db->query();

		// Get all the default folders
		$folders = $folder->find('list', array(
			'user_id'  => 0,
			'sort'     => 'ordering',
			'sort_Dir' => 'asc',
			'iscore'   => 1
		));

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
					$f = new QueryFolder($db);
					$f->iscore = $iscore;
					$f->title = $fldr;
					$f->check();
					$f->ordering = $i;
					$f->user_id = 0;
					$f->store();

					switch ($f->alias)
					{
						case 'common':
							$j = ($iscore == 1 ? $query->populateDefaults('common', $f->id) : $query->populateDefaults('commonnotacl', $f->id));
						break;

						case 'mine':
							$query->populateDefaults('mine', $f->id);
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
