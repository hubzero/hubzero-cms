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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Tools\Models\Version\Zone;
use Components\Tools\Models\Version;
use Components\Tools\Models\Tool;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

require_once dirname(dirname(dirname(__FILE__))) . DS . 'models' . DS . 'version' . DS . 'zone.php';
require_once dirname(dirname(dirname(__FILE__))) . DS . 'tables' . DS . 'zones.php';

/**
 * Tools controller class for tool versions
 */
class Versions extends AdminController
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

		if (!$this->view->filters['id'])
		{
			App::abort(404, Lang::txt('COM_TOOLS_ERROR_MISSING_ID'));
		}
		$this->view->tool  = Tool::getInstance($this->view->filters['id']);
		if (!$this->view->tool)
		{
			App::abort(404, Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
		}
		$this->view->total = count($this->view->tool->version);
		$this->view->rows  = $this->view->tool->getToolVersionSummaries($this->view->filters, true);

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

		$this->view->parent = Tool::getInstance($id);

		if (!is_object($row))
		{
			$row = Version::getInstance($version);
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
		Request::checkToken();

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

		$row = Version::getInstance(intval($fields['version']));
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

		$row->hostreq = (is_array($row->hostreq) && !empty($row->hostreq) ? explode(',', $row->hostreq[0]) : []);

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
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
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
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
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
		Request::checkToken();

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
		Request::checkToken(['get', 'post']);

		$row = Zone::oneOrFail(Request::getInt('id', false));

		if (!$row->destroy())
		{
			App::abort(500, $row->getError());
			return;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=component&task=displayZones&version=' . Request::getInt('version', 0), false),
			Lang::txt('COM_TOOLS_ITEM_DELETED')
		);
	}
}