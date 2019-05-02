<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Admin\Controllers;

use Components\Tools\Helpers\Utils;
use Components\Tools\Tables\Host;
use Components\Tools\Tables\Hosttype;
use Components\Tools\Tables\Zones;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'zones.php';
include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'host.php';
include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'hosttype.php';

/**
 * Tools controller class for hosts
 */
class Hosts extends AdminController
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
			'hostname' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.hostname',
				'hostname',
				''
			)),
			'hosttype' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.hosttype',
				'hosttype',
				''
			)),
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'hostname'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
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

		$model = new Host($mwdb);

		$total = $model->getCount($filters);

		$rows = $model->getRecords($filters);

		$ht = new Hosttype($mwdb);

		$hosttypes = $ht->getRecords();

		// Display results
		$this->view
			->set('filters', $filters)
			->set('total', $total)
			->set('rows', $rows)
			->set('hosttypes', $hosttypes)
			->display();
	}

	/**
	 * Check the status of a host
	 *
	 * @return  void
	 */
	public function statusTask()
	{
		// Incoming
		$hostname = Request::getString('hostname', '', 'get');

		// $hostname is eventually used in a string passed to an exec call, we gotta
		// clean at least some of it. See RFC 1034 for valid character set info
		$hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $hostname);

		$status = $this->_middleware("check " . $hostname . " yes", $output);

		// Display results
		$this->view
			->set('hostname', $hostname)
			->set('output', $output)
			->set('status', $status)
			->display();
	}

	/**
	 * Execute a middleware command
	 *
	 * @param   string   $comm       Command to execute
	 * @param   array    &$fnoutput  Command output
	 * @return  integer  1 = success, 0 = failure
	 */
	protected function _middleware($comm, &$fnoutput)
	{
		$retval = 1; // Assume success.
		$fnoutput = array();

		$dbname = \App::get('config')->get('database.db');
		$cmd = "/bin/sh " . dirname(__DIR__) . "/../scripts/mw $comm dbname=$dbname 2>&1 </dev/null";
		exec($cmd, $output, $status);

		$outln = 0;
		if ($status != 0)
		{
			$retval = 0;
		}

		// Print out the applet tags or the error message, as the case may be.
		foreach ($output as $line)
		{
			// If it's a new session, catch the session number...
			if (($retval == 1) && preg_match("/^Session is ([0-9]+)/", $line, $sess))
			{
				$retval = $sess[1];
			}
			else
			{
				if ($status != 0)
				{
					$fnoutput[$outln] = $line;
				}
				else
				{
					$fnoutput[$outln] = $line;
				}
				$outln++;
			}
		}

		return $retval;
	}

	/**
	 * Edit a record
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		if (!is_object($row))
		{
			// Incoming
			$hostname = Request::getString('hostname', '', 'get');

			// $hostname is eventually used in a string passed to an exec call, we gotta
			// clean at least some of it. See RFC 1034 for valid character set info
			$hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $hostname);

			$row = new Host($mwdb);
			$row->load($hostname);
		}

		$ht = new Hosttype($mwdb);
		$hosttypes = $ht->getRecords();

		$v = new Zones($mwdb);
		$zones = $v->find('list');

		// make sure we have a hostname
		$toolCounts = array();
		$statusCounts = array();
		if ($row->hostname != '')
		{
			//get tool instance counts
			$sql = "SELECT appname, count(*) as count from session where exechost=" . $mwdb->quote($row->hostname) . " group by appname";
			$mwdb->setQuery($sql);
			$toolCounts = $mwdb->loadObjectList();

			//get status counts
			$sql = "SELECT status, count(*) as count from display where hostname=" . $mwdb->quote($row->hostname) . " group by status";
			$mwdb->setQuery($sql);
			$statusCounts = $mwdb->loadObjectList();
		}

		// Display results
		$this->view
			->set('row', $row)
			->set('hosttypes', $hosttypes)
			->set('zones', $zones)
			->set('toolCounts', $toolCounts)
			->set('statusCounts', $statusCounts)
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

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');

		$row = new Host($mwdb);

		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// $hostname is eventually used in a string passed to an exec call, we gotta
		// clean at least some of it. See RFC 1034 for valid character set info
		$row->hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $row->hostname);
		$fields['id'] = preg_replace("/[^A-Za-z0-9-.]/", '', $fields['id']);

		if (!$row->hostname)
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_INVALID_HOSTNAME'));
			return $this->editTask($row);
		}

		// Figure out the hosttype stuff.
		$hosttype = Request::getArray('hosttype', array(), 'post');
		$harr = array();
		foreach ($hosttype as $name => $value)
		{
			$harr[$value] = 1;
		}
		$row->provisions = 0;

		// Get the middleware database
		$ht = new Hosttype($mwdb);
		if ($rows = $ht->getRecords())
		{
			for ($i=0; $i < count($rows); $i++)
			{
				$arow = $rows[$i];
				if (isset($harr[$arow->name]))
				{
					$row->provisions += $arow->value;
				}
			}
		}

		$insert = false;
		if (!$fields['id'])
		{
			$insert = true;
		}

		// Check content
		if (!$row->check())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store($insert, $fields['id']))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_TOOLS_ITEM_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Toggle a hostname provision
	 *
	 * @return  void
	 */
	public function toggleTask()
	{
		// Incoming
		$hostname = Request::getString('hostname', '', 'get');
		$item = Request::getString('item', '', 'get');
		// $hostname is eventually used in a string passed to an exec call, we gotta
		// clean at least some of it. See RFC 1034 for valid character set info
		$hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $hostname);

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		$query1 = "SELECT @value:=value FROM hosttype WHERE name=" . $mwdb->Quote($item) . ";";
		$query2 = "UPDATE host SET provisions = provisions ^ @value WHERE hostname = " . $mwdb->Quote($hostname) . ";";

		$mwdb->setQuery($query1);
		if (!$mwdb->query())
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_PROVISION_UPDATE_FAILED'));
		}

		$mwdb->setQuery($query2);
		if (!$mwdb->query())
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_PROVISION_UPDATE_FAILED'));
		}

		$this->cancelTask();
	}

	/**
	 * Delete one or more hostname records
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getArray('id', array());

		$removed = 0;

		if (count($ids) > 0)
		{
			$mwdb = Utils::getMWDBO();
			$row = new Host($mwdb);

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = preg_replace("/[^A-Za-z0-9-.]/", '', $id);

				if (!$row->delete($id))
				{
					Notify::error($row->getError());
					continue;
				}

				$removed++;
			}
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_TOOLS_ITEM_DELETED'));
		}

		$this->cancelTask();
	}
}
