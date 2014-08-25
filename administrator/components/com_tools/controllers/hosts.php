<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.zones.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'host.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'hosttype.php');

/**
 * Short description for 'ToolsController'
 *
 * Long description (if any) ...
 */
class ToolsControllerHosts extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of hosts
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters = array();
		$this->view->filters['hostname']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.hostname',
			'hostname',
			''
		));
		$this->view->filters['hosttype']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.hosttype',
			'hosttype',
			''
		));
		// Sorting
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'hostname'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		// Get paging variables
		$this->view->filters['limit']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['limit']        = ($this->view->filters['limit'] == 'all') ? 0 : $this->view->filters['limit'];
		$this->view->filters['start']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		$model = new MwHost($mwdb);

		$this->view->total = $model->getCount($this->view->filters);

		$this->view->rows = $model->getRecords($this->view->filters);

		$ht = new MwHosttype($mwdb);

		$this->view->hosttypes = $ht->getRecords();

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Check the status of a host
	 *
	 * @return     void
	 */
	public function statusTask()
	{
		// Incoming
		$this->view->hostname = JRequest::getVar('hostname', '', 'get');

		// $hostname is eventually used in a string passed to an exec call, we gotta
		// clean at least some of it. See RFC 1034 for valid character set info
		$this->view->hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $this->view->hostname);

		$this->view->status = $this->_middleware("check " . $this->view->hostname . " yes", $output);
		$this->view->output = $output;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Execute a middleware command
	 *
	 * @param      string $comm      Command to execute
	 * @param      array  &$fnoutput Command output
	 * @return     integer 1 = success, 0 = failure
	 */
	protected function _middleware($comm, &$fnoutput)
	{
		$retval = 1; // Assume success.
		$fnoutput = array();

		exec("/bin/sh ../components/" . $this->_option . "/scripts/mw $comm 2>&1 </dev/null", $output, $status);

		$outln = 0;
		if ($status != 0)
		{
			$retval = 0;
		}

		// Print out the applet tags or the error message, as the case may be.
		foreach ($output as $line)
		{
			// If it's a new session, catch the session number...
			if (($retval == 1) && preg_match("/^Session is ([0-9]+)/",$line,$sess))
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
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a record
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$hostname = JRequest::getVar('hostname', '', 'get');

			// $hostname is eventually used in a string passed to an exec call, we gotta
			// clean at least some of it. See RFC 1034 for valid character set info
			$hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $hostname);

			$this->view->row = new MwHost($mwdb);
			$this->view->row->load($hostname);
		}

		$ht = new MwHosttype($mwdb);
		$this->view->hosttypes = $ht->getRecords();

		$v = new MwZones($mwdb);
		$this->view->zones = $v->find('list');

		//make sure we have a hostname
		if ($this->view->row->hostname != '')
		{
			//get tool instance counts
			$sql = "SELECT appname, count(*) as count from session where exechost=" . $this->database->quote($this->view->row->hostname) . " group by appname";
			$this->database->setQuery($sql);
			$this->view->toolCounts = $this->database->loadObjectList();

			//get status counts
			$sql = "SELECT status, count(*) as count from display where hostname=" . $this->database->quote($this->view->row->hostname) . " group by status";
			$this->database->setQuery($sql);
			$this->view->statusCounts = $this->database->loadObjectList();
		}

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display results
		$this->view->setLayout('edit')->display();
	}

	/**
	 * Save changes to a record and return to edit form
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save changes to a record
	 *
	 * @return     void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		$row = new MwHost($mwdb);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// $hostname is eventually used in a string passed to an exec call, we gotta
		// clean at least some of it. See RFC 1034 for valid character set info
		$row->hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $row->hostname);
		$fields['id'] = preg_replace("/[^A-Za-z0-9-.]/", '', $fields['id']);

		if (!$row->hostname)
		{
			$this->addComponentMessage(Jtext::_('COM_TOOLS_ERROR_INVALID_HOSTNAME'), 'error');
			$this->editTask($row);
			return;
		}

		// Figure out the hosttype stuff.
		$hosttype = JRequest::getVar('hosttype', array(), 'post');
		$harr = array();
		foreach ($hosttype as $name => $value)
		{
			$harr[$value] = 1;
		}
		$row->provisions = 0;

		// Get the middleware database
		$ht = new MwHosttype($mwdb);
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
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store($insert, $fields['id']))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($redirect)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				Jtext::_('COM_TOOLS_ITEM_SAVED'),
				'message'
			);
			return;
		}

		$this->editTask($row);
	}

	/**
	 * Toggle a hostname provision
	 *
	 * @return     void
	 */
	public function toggleTask()
	{
		// Incoming
		$hostname = JRequest::getVar('hostname', '', 'get');
		$item = JRequest::getVar('item', '', 'get');
		// $hostname is eventually used in a string passed to an exec call, we gotta
		// clean at least some of it. See RFC 1034 for valid character set info
		$hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $hostname);

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		$query = "SELECT @value:=value FROM hosttype WHERE name=" . $mwdb->Quote($item) . ";" .
				" UPDATE host SET provisions = provisions ^ @value WHERE hostname = " . $mwdb->Quote($hostname) . ";";
		$mwdb->setQuery($query);
		if (!$mwdb->queryBatch())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_TOOLS_ERROR_PROVISION_UPDATE_FAILED'),
				'error'
			);
			return;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Delete one or more hostname records
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		$mwdb = MwUtils::getMWDBO();

		if (count($ids) > 0)
		{
			$row = new MwHost($mwdb);

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = preg_replace("/[^A-Za-z0-9-.]/", '', $id);
				if (!$row->delete($id))
				{
					JError::raiseError(500, $row->getError());
					return;
				}
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_TOOLS_ITEM_DELETED'),
			'message'
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
