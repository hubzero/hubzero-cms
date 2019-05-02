<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Admin\Controllers;

use Components\Tools\Helpers\Utils;
use Components\Tools\Tables;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Notify;
use Route;
use Event;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'job.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'session.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'view.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'viewperm.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'sessionclass.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'sessionclassgroup.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'preferences.php';

/**
 * Controller class for tool sessions
 */
class Sessions extends AdminController
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
		$filters = array(
			'username' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.username',
				'username',
				''
			)),
			'appname' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.appname',
				'appname',
				''
			)),
			'exechost' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.exechost',
				'exechost',
				''
			)),
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'start'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
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

		// In case limit has been changed, adjust limitstart accordingly
		$filters['limit'] = ($filters['limit'] == 'all') ? 0 : $filters['limit'];
		$filters['start'] = ($filters['limit'] != 0 ? (floor($filters['start'] / $filters['limit']) * $filters['limit']) : 0);

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		$model = new Tables\Session($mwdb);

		$total = $model->getAllCount($filters);

		$rows = $model->getAllRecords($filters);

		$appnames = $model->getAppnames();

		$exechosts = $model->getExechosts();

		$usernames = $model->getUsernames();

		// Display results
		$this->view
			->set('filters', $filters)
			->set('total', $total)
			->set('rows', $rows)
			->set('appnames', $appnames)
			->set('exechosts', $exechosts)
			->set('usernames', $usernames)
			->display();
	}

	/**
	 * Delete one or more hostname records
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$stopped = 0;

		if (count($ids) > 0)
		{
			$mwdb = Utils::getMWDBO();
			$row = new Tables\Session($mwdb);

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = intval($id);
				if (!$row->load($id))
				{
					Notify::error(Lang::txt('COM_TOOLS_ERROR_FAILED_TO_LOAD_SESSION', $id));
					continue;
				}

				// Trigger any events that need to be called before session stop
				Event::trigger('mw.onBeforeSessionStop', array($row->appname));

				// Stop the session
				$status = $this->middleware("stop $id", $output);

				if ($status)
				{
					$msg = 'Stopping ' . $id . '<br />';
					foreach ($output as $line)
					{
						$msg .= $line . "\n";
					}
					Notify::error($msg);
					continue;
				}

				// Trigger any events that need to be called after session stop
				Event::trigger('mw.onAfterSessionStop', array($row->appname));

				$stopped++;
			}
		}

		if ($stopped)
		{
			Notify::success(Lang::txt('COM_TOOLS_SESSIONS_TERMINATED'));
		}

		$this->cancelTask();
	}

	/**
	 * Invoke the Python script to do work elsewhere.
	 *
	 * @param   string   $comm
	 * @param   array    &$output
	 * @return  integer  Session ID
	 */
	public function middleware($comm, &$output)
	{
		$retval = true; // Assume success.
		$output = new \stdClass();
		$hubname = \App::get('config')->get('database.db');
		$cmd = "/bin/sh " . dirname(dirname(__DIR__)) . "/scripts/mw $comm dbname=$hubname 2>&1 </dev/null";

		exec($cmd, $results, $status);

		// Check exec status
		if ($status != 0)
		{
			// Uh-oh. Something went wrong...
			$retval = false;
			$this->setError($results[0]);
		}

		if (is_array($results))
		{
			// HTML
			// Print out the applet tags or the error message, as the case may be.
			foreach ($results as $line)
			{
				$line = trim($line);

				// If it's a new session, catch the session number...
				if ($retval && preg_match("/^Session is ([0-9]+)/", $line, $sess))
				{
					$retval = $sess[1];
					$output->session = $sess[1];
				}
				else
				{
					if (preg_match("/width=\"(\d+)\"/i", $line, $param))
					{
						$output->width = trim($param[1], '"');
					}
					if (preg_match("/height=\"(\d+)\"/i", $line, $param))
					{
						$output->height = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"PORT\" value=\"?(\d+)\"?>/i", $line, $param))
					{
						$output->port = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"ENCPASSWORD\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->password = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"CONNECT\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->connect = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"ENCODING\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->encoding = trim($param[1], '"');
					}
				}
			}
		}
		else
		{
			// JSON
			$output = json_decode($results);
			if ($output == null)
			{
				$retval = false;
			}
		}

		return $retval;
	}

	/**
	 * Display quota classes
	 *
	 * @return  void
	 */
	public function classesTask()
	{
		// Incoming
		$filters = array(
			'limit' => Request::getState($this->_option . '.classes.limit', 'limit', Config::get('list_limit'), 'int'),
			'start' => Request::getState($this->_option . '.classes.limitstart', 'limitstart', 0, 'int')
		);

		$obj = new Tables\SessionClass($this->database);

		// Get a record count
		$total = $obj->find('count', $filters);
		$rows  = $obj->find('list', $filters);

		if (!$total)
		{
			$obj->createDefault();

			$total = $obj->find('count', $filters);
			$rows  = $obj->find('list', $filters);
		}

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('total', $total)
			->set('rows', $rows)
			->set('config', $this->config)
			->setLayout('classes')
			->display();
	}

	/**
	 * Edit a quota class
	 *
	 * @param   integer  $id  ID of class to edit
	 * @return  void
	 */
	public function editTask($id=0)
	{
		Request::setVar('hidemainmenu', 1);

		if (!$id)
		{
			// Incoming
			$id = Request::getArray('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}
		}

		// Initiate database class and load info
		$row = new Tables\SessionClass($this->database);
		$row->load($id);

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save quota class
	 *
	 * @param   integer  $redirect  Whether or not to redirect after save
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming fields
		$fields = Request::getArray('fields', array(), 'post');

		// Load the profile
		$row = new Tables\SessionClass($this->database);
		$row->load($fields['id']);

		$old = $row->jobs;

		// Try to save
		if (!$row->save($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Save class/access-group association
		if (isset($fields['groups']))
		{
			if (!$row->setGroupIds($fields['groups']))
			{
				Notify::error($row->getError());
				return $this->editTask($row);
			}
		}

		// If changing, update members referencing this class
		if ($old != $row->jobs)
		{
			$prefs = new Tables\Preferences($this->database);
			$prefs->updateUsersByClassId($row->id);
		}

		Notify::success(Lang::txt('COM_TOOLS_SESSION_CLASS_SAVE_SUCCESSFUL'));

		// Redirect
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelclassTask();
	}

	/**
	 * Removes class(es)
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = new Tables\SessionClass($this->database);
				$row->load($id);

				if ($row->alias == 'default')
				{
					// Output message and redirect
					Notify::warning(Lang::txt('COM_TOOLS_SESSION_CLASS_DONT_DELETE_DEFAULT'));
					return $this->cancelclassTask();
				}

				// Remove the record
				$row->delete($id);

				$prefs = new Tables\Preferences($this->database);
				$prefs->restoreDefaultClass($id);
			}
		}
		else // no rows were selected
		{
			// Output message and redirect
			Notify::warning(Lang::txt('COM_TOOLS_SESSION_CLASS_DELETE_NO_ROWS'));
			return $this->cancelclassTask();
		}

		// Output messsage and redirect
		Notify::success(Lang::txt('COM_TOOLS_SESSION_CLASS_DELETE_SUCCESSFUL'));
		$this->cancelclassTask();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelclassTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=classes', false)
		);
	}
}
