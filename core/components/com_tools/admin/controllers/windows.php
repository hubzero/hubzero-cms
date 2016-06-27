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

use Hubzero\Component\AdminController;
use Components\Resources\Models\Orm\Resource;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use User;
use Date;
use App;

require_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'orm' . DS . 'resource.php');

/**
 * Controller class for Windows tools
 */
class Windows extends AdminController
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
	 * Display a list of records
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (!$this->config->get('windows_type'))
		{
			$this->view
				->setLayout('unconfigured')
				->display();
			return;
		}

		// Get filters
		$filters = array(
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
			)
		);

		// Get a list of tools
		$rows = Resource::all()
			->whereEquals('type', $this->config->get('windows_type'))
		//	->ordered('filter_order', 'filter_order_Dir')
		//	->paginated('limitstart', 'limit')
			->rows();

		// Display results
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$row = Resource::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('type', $this->config->get('windows_type'));
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming fields
		$fields = Request::getVar('fields', array(), 'post');
		$fields['standalone'] = 1;

		// Load the profile
		$row = Resource::oneOrNew($fields['id'])->set($fields);

		if ($row->isNew())
		{
			$row->set('access', 0);
			$row->set('published', 1);
			$row->set('created', Date::toSql());
			$row->set('created_by', User::get('id'));
		}

		if (!$row->get('alias'))
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_MISSING_ALIAS'));
			return $this->editTask($row);
		}

		$row->set('alias', preg_replace('/[^a-z0-9_\-]/i', '', strtolower($row->get('alias'))));

		if (!$row->get('title'))
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_MISSING_TITLE'));
			return $this->editTask($row);
		}

		if (!$row->get('path'))
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_MISSING_UUID'));
			return $this->editTask($row);
		}

		if (!$row->save())
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_MISSING_UUID'));
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_TOOLS_SAVE_SUCCESSFUL'));

		// Redirect
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Remove entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (!empty($ids))
		{
			$i = 0;

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$row = Resource::oneOrFail($id);

				if (!$row->destroy())
				{
					Notify::error($row->getError());
					continue;
				}

				// Remove
				$i++;
			}
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			($i ? Lang::txt('COM_TOOLS_DELETE_SUCCESSFUL') : null)
		);
	}

	/**
	 * Display sessions
	 *
	 * @return  void
	 */
	public function sessionsTask()
	{
		// Get filters
		$filters = array(
			'appname' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.appname',
				'appname',
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

		// Get the list of sessions
		$rows = array();

		// Get a count of all sessions (for pagination)
		$total = count($rows);

		// Get a list of all apps for the filters bar
		$apps =
			Resource::all()
			->whereEquals('type', $this->config->get('windows_type'))
//			->ordered('filter_order', 'filter_order_Dir')
//			->paginated('limitstart', 'limit')
			->rows();

		// Get a list of all active sessions for specified app
		$appname = Request::getVar('appname','');

		$sessions = array();

		if (!empty($appname))
		{
			exec('/usr/bin/hz-aws-appstream getappsessions --appid' . ' "' . $appname . '"', $rawsessions);

			$sessions = array();
			foreach ($rawsessions as $s)
			{
				$sessionsArray = explode("|", $s);

				if (sizeof($sessionsArray) == 4)
					$sessions[] = array("sessionid" => $sessionsArray[0], "status" => $sessionsArray[2], "opaquedata" => $sessionsArray[3]);
				//else
				//	$sessions[] = array("sessionid" => $sessionsArray[0], "status" => "cannot parse", "opaquedata" => "cannot parse");


			}
			usort($sessions, function($a, $b){return strcmp( $a['status'], $b['status']); } );
		}


		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('apps', $apps)
			->set('sessions', $sessions)
			->set('total', 0)
			->setErrors($this->getErrors())
			->setLayout('sessions')
			->display();
	}


	/**
	 * Display sessions
	 *
	 * @return  void
	 */
	public function usageTask()
	{

		// Get report startdate and enddate set
		// 'Y-m-d H:i:s'
		$startdate = Request::getVar('startdate', '');
		$enddate = Request::getVar('enddate', '');

		if (empty($startdate))
			$startdate = new \DateTime('midnight first day of this month');
		else
			$startdate = new \DateTime($startdate);

		if (empty($enddate))
			$enddate = new \DateTime('midnight first day of next month');
		else
			$enddate = new \DateTime($enddate);

		// Get the usage data
		$db = App::get('db');
		$sql =  'SELECT jr.title as appname, ' ;
		$sql .= 'count(sessnum) as sessions ';
		$sql .= ', truncate(stddev(walltime)/60/60,3) as "standarddeviationhours" ';
		$sql .= ', truncate(avg(walltime)/60/60,3) as "averagehours" ';
		$sql .= ', truncate(sum(walltime)/60/60,3) as "totalhours" ';
		$sql .= 'FROM sessionlog ';
		$sql .= 'JOIN jos_resources jr on (jr.path = appname and jr.`type` = 64) ';
		$sql .= 'WHERE start >"' . $startdate->format('Y-m-d H:i:s') . '"';
		$sql .= ' AND start <"' . $enddate->format('Y-m-d H:i:s') . '"';
		$sql .= 'GROUP BY appname;';

		$db->setQuery($sql);
		$usageFigures = $db->loadObjectList();

		// Get summary usage data
		$db = App::get('db');
		$sql = 'SELECT ifnull(truncate(sum(walltime)/60/60,3),0) as totalhours FROM sessionlog ';
		$sql .= 'JOIN jos_resources jr on (jr.path = appname and jr.`type` = 64) ';
		$sql .= 'WHERE start >"' . $startdate->format('Y-m-d H:i:s') . '"';
		$sql .= ' AND start <"' . $enddate->format('Y-m-d H:i:s') . '"';

		$db->setQuery($sql);
		$totalUsageFigure = $db->loadObjectList();

		// Output the HTML
		$this->view
			->set('startdate', $startdate)
			->set('enddate', $enddate)
			->set('usageFigures', $usageFigures)
			->set('totalUsageFigure', $totalUsageFigure)
			->setErrors($this->getErrors())
			->setLayout('usage')
			->display();
	}


	/**
	 * Delete one or more sessions
	 *
	 * @return  void
	 */
	public function terminateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) > 0)
		{
			// Loop through each ID
			foreach ($ids as $id)
			{
				// Stop the session
			}
		}

		// Redirect back to the listing
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=sessions', false),
			Lang::txt('COM_TOOLS_SESSIONS_TERMINATED')
		);
	}
}
