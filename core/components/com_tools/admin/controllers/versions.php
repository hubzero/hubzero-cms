<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$filters       = array();
		$filters['id'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.id',
			'id',
			0,
			'int'
		);
		$filters['sort']     = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.' . $filters['id'] . 'sort',
			'filter_order',
			'toolname'
		);
		$filters['sort_Dir'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.' . $filters['id'] . 'sortdir',
			'filter_order_Dir',
			'ASC'
		);
		$filters['limit']    = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.' . $filters['id'] . 'limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$filters['start']    = Request::getState(
			$this->_option . '.' . $this->_controller . '.versions.' . $filters['id'] . 'limitstart',
			'limitstart',
			0,
			'int'
		);

		$filters['sortby'] = $filters['sort'] . ' ' . $filters['sort_Dir'];

		// In case limit has been changed, adjust limitstart accordingly
		$filters['start'] = ($filters['limit'] != 0 ? (floor($filters['start'] / $filters['limit']) * $filters['limit']) : 0);

		if (!$filters['id'])
		{
			App::abort(404, Lang::txt('COM_TOOLS_ERROR_MISSING_ID'));
		}

		$tool = Tool::getInstance($filters['id']);

		if (!$tool)
		{
			App::abort(404, Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
		}

		$total = count($tool->version);
		$rows  = $tool->getToolVersionSummaries($filters, true);

		if ($this->config->get('new_doi')
		 && file_exists(\Component::path('com_resources') . '/models/doi.php'))
		{
			require_once \Component::path('com_resources') . '/models/doi.php';

			$dois = \Components\Resources\Models\Doi::all()
				->whereEquals('alias', $tool->toolname)
				->rows()
				->toArray();

			foreach ($rows as $k => $row)
			{
				$rows[$k]['doi'] = '';

				if (substr($row['instance'], -4) == '_dev')
				{
					continue;
				}

				foreach ($dois as $doi)
				{
					if ($doi['versionid'] == $row['id'] || $doi['local_revision'] == $row['revision'])
					{
						$rows[$k]['doi'] = $doi['doi_shoulder'] . '/' . $doi['doi'];
						break;
					}
				}
			}
		}

		// Display results
		$this->view
			->set('filters', $filters)
			->set('tool', $tool)
			->set('total', $total)
			->set('rows', $rows)
			->set('config', $this->config)
			->display();
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

		$parent = Tool::getInstance($id);

		if (!is_object($row))
		{
			$row = Version::getInstance($version);
		}

		$doi = null;

		// If the DOI service is enabled
		// and the DOI model exists
		// and the tool version is not a dev version
		if ($this->config->get('new_doi')
		 && file_exists(\Component::path('com_resources') . '/models/doi.php'))
		{
			require_once \Component::path('com_resources') . '/models/doi.php';

			$doi = \Components\Resources\Models\Doi::all()
				->whereEquals('alias', $row->toolname)
				->whereEquals('local_revision', $row->revision)
				->row();

			if (substr($row->instance, -4) == '_dev')
			{
				$doi->addError(Lang::txt('COM_TOOLS_WARNING_DOI_DEV'));
			}
		}

		// Display results
		$this->view
			->set('parent', $parent)
			->set('row', $row)
			->set('doi', $doi)
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
		$fields = Request::getArray('fields', array(), 'post');

		// Do we have an ID?
		if (!$fields['version'])
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_MISSING_ID'));

			return $this->cancelTask();
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
		$row->vnc_timeout = ($fields['vnc_timeout'] != 0) ? intval(trim($fields['vnc_timeout'])) : null;
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

		// If the DOI service is enabled
		// and the DOI model exists
		// and the tool version is not a dev version
		if ($this->config->get('new_doi')
		 && file_exists(\Component::path('com_resources') . '/models/doi.php')
		 && substr($row->instance, -4) != '_dev')
		{
			require_once \Component::path('com_resources') . '/models/doi.php';

			// Save DOI data
			$dois = Request::getArray('doi', array(), 'post');

			if ($dois['doi'])
			{
				if (!$dois['rid'])
				{
					if (file_exists(\Component::path('com_resources') . '/models/entry.php'))
					{
						require_once \Component::path('com_resources') . '/models/entry.php';

						$dois['rid'] = \Components\Resources\Models\Entry::oneByAlias($version->toolname)->get('id');
					}
				}

				$doi = \Components\Resources\Models\Doi::oneOrNew($dois['id'])->set($dois);
				if (!$doi->save())
				{
					Notify::error($doi->getError());
				}
			}
		}

		if ($this->getTask() == 'apply')
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $fields['id'] . '&version=' . $fields['version'], false)
			);
			return;
		}

		Notify::success(Lang::txt('COM_TOOLS_ITEM_SAVED'));

		$this->editTask();
	}

	/**
	 * Display a list of version zones
	 *
	 * @return  void
	 */
	public function displayZonesTask()
	{
		// Get the version number
		$version = Request::getInt('version', 0);

		// Do we have an ID?
		if (!$version)
		{
			return $this->cancelTask();
		}

		$rows = Zone::whereEquals('tool_version_id', $version);

		// Display results
		$this->view
			->set('rows', $rows)
			->set('version', $version)
			->setLayout('component')
			->display();
	}

	/**
	 * Add a new zone
	 *
	 * @return  void
	 */
	public function addZoneTask()
	{
		$this->editZoneTask();
	}

	/**
	 * Edit a zone
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editZoneTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$row = Zone::oneOrNew(Request::getInt('id', 0));
		}

		if ($row->isNew())
		{
			$row->set('tool_version_id', Request::getInt('version', 0));
		}

		// Display results
		$this->view
			->set('row', $row)
			->setLayout('editZone')
			->display();
	}

	/**
	 * Save changes to version zone
	 *
	 * @return  void
	 */
	public function saveZoneTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getArray('fields', [], 'post');
		$row    = Zone::oneOrNew($fields['id'])->set($fields);

		if (empty($fields['publish_up']))
		{
			$row->set('publish_up', null);
		}
		if (empty($fields['publish_down']))
		{
			$row->set('publish_down', null);
		}

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
	 * @return  void
	 */
	public function removeZoneTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$row = Zone::oneOrFail(Request::getInt('id', false));

		if (!$row->destroy())
		{
			App::abort(500, $row->getError());
		}

		Notify::success(Lang::txt('COM_TOOLS_ITEM_DELETED'));

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=component&task=displayZones&version=' . Request::getInt('version', 0), false)
		);
	}
}
