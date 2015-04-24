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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once dirname(dirname(dirname(__FILE__))) . DS . 'models' . DS . 'version' . DS . 'zone.php';
require_once dirname(dirname(dirname(__FILE__))) . DS . 'tables' . DS . 'mw.zones.php';

use Components\Tools\Models\Version\Zone;

/**
 * Tools controller class for tool versions
 */
class ToolsControllerVersions extends \Hubzero\Component\AdminController
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
	 * Display all versions for a specific entry
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get Filters
		$this->view->filters       = array();
		$this->view->filters['id'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.id',
			'id',
			0,
			'int'
		);
		$this->view->filters['sort']     = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'sort',
			'filter_order',
			'toolname'
		);
		$this->view->filters['sort_Dir'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'sortdir',
			'filter_order_Dir',
			'ASC'
		);
		$this->view->filters['limit']    = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$this->view->filters['start']    = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'limitstart',
			'limitstart',
			0,
			'int'
		);

		$this->view->filters['sortby'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$this->view->tool = ToolsModelTool::getInstance($this->view->filters['id']);
		$this->view->total = count($this->view->tool->version);
		$this->view->rows = $this->view->tool->getToolVersionSummaries($this->view->filters, true);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Edit an entry version
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming instance ID
		$id = Request::getInt('id', 0);
		$version = Request::getInt('version', 0);

		// Do we have an ID?
		if (!$id || !$version)
		{
			return $this->cancelTask();
		}

		$this->view->parent = ToolsModelTool::getInstance($id);

		if (!is_object($row))
		{
			$row = ToolsModelVersion::getInstance($version);
		}
		$this->view->row = $row;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry version
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming instance ID
		$fields = Request::getVar('fields', array(), 'post');

		// Do we have an ID?
		if (!$fields['version'])
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_TOOLS_ERROR_MISSING_ID'),
				'error'
			);
			return;
		}

		$row = ToolsModelVersion::getInstance(intval($fields['version']));
		if (!$row)
		{
			Request::setVar('id', $fields['id']);
			Request::setVar('version', $fields['version']);

			Notify::error(Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
			return $this->editTask();
		}

		$row->vnc_command = trim($fields['vnc_command']);
		$row->vnc_timeout = ($fields['vnc_timeout'] != 0) ? intval(trim($fields['vnc_timeout'])) : NULL;
		$row->hostreq     = trim($fields['hostreq']);
		$row->mw          = trim($fields['mw']);
		$row->params      = trim($fields['params']);

		if (!$row->vnc_command)
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_MISSING_COMMAND'));
			return $this->editTask($row);
		}

		$row->hostreq = (is_array($row->hostreq) ? explode(',', $row->hostreq[0]) : explode(',', $row->hostreq));

		$hostreq = array();
		foreach ($row->hostreq as $req)
		{
			if (!empty($req))
			{
				$hostreq[] = trim($req);
			}
		}

		$row->hostreq = $hostreq;
		$row->update();

		if ($this->getTask() == 'apply')
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $fields['id'] . '&version=' . $fields['version'], false)
			);
			return;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_ITEM_SAVED')
		);
	}

	/**
	 * Display a list of version zones
	 *
	 * @return     void
	 */
	public function displayZonesTask()
	{
		$this->view->setLayout('component');

		// Get the version number
		$version = Request::getInt('version', 0);

		// Do we have an ID?
		if (!$version)
		{
			$this->cancelTask();
			return;
		}

		$this->view->rows    = Zone::whereEquals('tool_version_id', $version);
		$this->view->version = $version;

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
	 * Add a new zone
	 *
	 * @return     void
	 */
	public function addZoneTask()
	{
		$this->editZoneTask();
	}

	/**
	 * Edit a zone
	 *
	 * @return     void
	 */
	public function editZoneTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		$this->view->setLayout('editZone');

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			$this->view->row = Zone::oneOrNew(Request::getInt('id', 0));
		}

		if ($this->view->row->isNew())
		{
			$this->view->row->set('tool_version_id', Request::getInt('version', 0));
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
		$this->view->display();
	}

	/**
	 * Save changes to version zone
	 *
	 * @return     void
	 */
	public function saveZoneTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = Request::getVar('fields', [], 'post');
		$row    = Zone::oneOrNew($fields['id'])->set($fields);

		if (empty($fields['publish_up'])) $row->set('publish_up', null);
		if (empty($fields['publish_down'])) $row->set('publish_down', null);

		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Request::setVar('tmpl', 'component');

		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
		else
		{
			echo '<p class="message">' . Lang::txt('COM_TOOLS_ITEM_SAVED') . '</p>';
		}
	}

	/**
	 * Delete zone entry
	 *
	 * @return     void
	 */
	public function removeZoneTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or jexit('Invalid Token');

		$row = Zone::oneOrFail(Request::getInt('id', false));

		if (!$row->destroy())
		{
			App::abort(500, $row->getError());
			return;
		}

		App::redirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=component&task=displayZones&version=' . Request::getInt('version', 0),
			Lang::txt('COM_TOOLS_ITEM_DELETED')
		);
	}
}