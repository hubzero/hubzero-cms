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

require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'job.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'session.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'view.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'viewperm.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'sessionclass.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'sessionclassgroup.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'preferences.php');

/**
 * Controller class for tool sessions
 */
class Sessions extends AdminController
{
	/**
	 * Display a list of hosts
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
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
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		$model = new Tables\Session($mwdb);

		$this->view->total = $model->getAllCount($this->view->filters);

		$this->view->rows = $model->getAllRecords($this->view->filters);

		$this->view->appnames = $model->getAppnames();

		$this->view->exechosts = $model->getExechosts();

		$this->view->usernames = $model->getUsernames();

		// Display results
		$this->view->display();
	}

	/**
	 * Delete one or more hostname records
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$mwdb = Utils::getMWDBO();

		if (count($ids) > 0)
		{
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
				}

				// Trigger any events that need to be called after session stop
				Event::trigger('mw.onAfterSessionStop', array($row->appname));
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_SESSIONS_TERMINATED'),
			'message'
		);
	}

	/**
	 * Invoke the Python script to do real work.
	 *
	 * @param      string  $comm Parameter description (if any) ...
	 * @param      array   &$output Parameter description (if any) ...
	 * @return     integer Session ID
	 */
	public function middleware($comm, &$output)
	{
		$retval = true; // Assume success.
		$output = new \stdClass();
		$cmd = "/bin/sh " . dirname(dirname(__DIR__)) . "/scripts/mw $comm 2>&1 </dev/null";

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
		$this->view->filters = array(
			'limit' => Request::getState($this->_option . '.classes.limit', 'limit', Config::get('list_limit'), 'int'),
			'start' => Request::getState($this->_option . '.classes.limitstart', 'limitstart', 0, 'int')
		);

		$obj = new Tables\SessionClass($this->database);

		// Get a record count
		$this->view->total = $obj->find('count', $this->view->filters);
		$this->view->rows  = $obj->find('list', $this->view->filters);

		if (!$this->view->total)
		{
			$obj->createDefault();

			$this->view->total = $obj->find('count', $this->view->filters);
			$this->view->rows  = $obj->find('list', $this->view->filters);
		}

		$this->view->config = $this->config;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('classes')
			->display();
	}

	/**
	 * Create a new quota class
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Output the HTML
		$this->editTask();
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
			$id = Request::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}
		}

		// Initiate database class and load info
		$this->view->row = new Tables\SessionClass($this->database);
		$this->view->row->load($id);

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
	 * Apply changes to a quota class item
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		// Save without redirect
		$this->saveTask();
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
		$fields = Request::getVar('fields', array(), 'post');

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
		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=classes', false)
		);
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
		$ids = Request::getVar('id', array());
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
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=classes', false),
						Lang::txt('COM_TOOLS_SESSION_CLASS_DONT_DELETE_DEFAULT'),
						'warning'
					);

					return;
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
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=classes', false),
				Lang::txt('COM_TOOLS_SESSION_CLASS_DELETE_NO_ROWS'),
				'warning'
			);
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=classes', false),
			Lang::txt('COM_TOOLS_SESSION_CLASS_DELETE_SUCCESSFUL')
		);
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
