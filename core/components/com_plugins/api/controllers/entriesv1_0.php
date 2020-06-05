<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Plugins\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Plugins\Models\Plugin;
use Exception;
use stdClass;
use Request;
use Event;
use Lang;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'plugin.php';

/**
 * API controller class for resources
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Display a list of entries
	 *
	 * @apiMethod GET
	 * @apiUri    /plugins/list
	 * @apiParameter {
	 * 		"name":          "folder",
	 * 		"description":   "Folder (plugin group)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "enabled",
	 * 		"description":   "Enabled",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       1
	 * }
	 * @apiParameter {
	 * 		"name":          "access",
	 * 		"description":   "Access",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       1
	 * }
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "created",
	 * 		"allowedValues": "created, title, alias, id, publish_up, publish_down, state"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		if (!User::authorise('core.manage', 'com_plugins'))
		{
			App::abort(403, 'Not Authorized');
		}

		$model = Plugin::all();

		$filters = array(
			'folder'   => Request::getString('folder', ''),
			'enabled'  => Request::getInt('enabled', 0),
			'access'   => Request::getInt('access', 1)
		);

		$response = new stdClass;
		$response->plugins = array();

		$model = Plugin::all()
			->whereEquals('enabled', $filters['enabled'])
			->whereEquals('access', $filters['access']);

		if ($filters['folder'])
		{
			$model->whereEquals('folder', $filters['folder']);
		}

		$response->total = $model->total();

		$rows = $model
			->ordered('sort', 'sort_Dir')
			->paginated()
			->rows();

		foreach ($rows as $plugin)
		{
			$response->plugins[] = $plugin->toObject();
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Create an entry
	 *
	 * @apiMethod POST
	 * @apiUri    /plugins
	 * @apiParameter {
	 * 		"name":        "name",
	 * 		"description": "Name",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "element",
	 * 		"description": "Element",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "folder",
	 * 		"description": "Folder (plugin group)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "enabled",
	 * 		"description": "Enabled",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "access",
	 * 		"description": "Access",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "params",
	 * 		"description": "JSON Encoded list of params",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		if (!User::authorise('core.manage', 'com_plugins'))
		{
			App::abort(403, 'Not Authorized');
		}

		$fields = array(
			'type'           => 'plugin',
			'name'           => Request::getString('name', null, 'post'),
			'element'        => Request::getString('element', null, 'post'),
			'folder'         => Request::getString('folder', null, 'post'),
			'client_id'      => Request::getInt('client_id', 0, 'post'),
			'enabled'        => Request::getInt('enabled', 0, 'post'),
			'access'         => Request::getInt('access', 1, 'post'),
			'protected'      => 0,
			'params'         => Request::getString('params', null, 'post', 'none')
		);

		$row = Plugin::blank();

		if (!$row->set($fields))
		{
			App::abort(500, Lang::txt('COM_PLUGINS_ERROR_BINDING_DATA'));
		}

		if (!$row->save())
		{
			App::abort(500, Lang::txt('COM_PLUGINS_ERROR_SAVING_DATA'));
		}

		$this->send($row->toObject());
	}

	/**
	 * Retrieve an entry
	 *
	 * @apiMethod GET
	 * @apiUri    /plugins/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Extension identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		if (!User::authorise('core.manage', 'com_plugins'))
		{
			App::abort(403, 'Not Authorized');
		}

		$id = Request::getInt('id', 0);

		$row = Plugin::oneOrFail($id);

		if (!$row->get('id'))
		{
			App::abort(404, Lang::txt('COM_PLUGINS_ERROR_MISSING_RECORD'));
		}

		$this->send($row->toObject());
	}

	/**
	 * Update an entry
	 *
	 * @apiMethod PUT
	 * @apiUri    /plugins/{extension_id}
	 * @apiParameter {
	 * 		"name":        "extension_id",
	 * 		"description": "Extension identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "name",
	 * 		"description": "Name",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "element",
	 * 		"description": "Element",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "folder",
	 * 		"description": "Folder (plugin group)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "enabled",
	 * 		"description": "Enabled",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "access",
	 * 		"description": "Access",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "params",
	 * 		"description": "JSON Encoded list of params",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		if (!User::authorise('core.manage', 'com_plugins'))
		{
			App::abort(403, 'Not Authorized');
		}

		$row = Plugin::oneOrFail(Request::getInt('extension_id', 0));

		if ($row->isNew())
		{
			App::abort(404, Lang::txt('COM_PLUGINS_ERROR_MISSING_RECORD'));
		}

		$fields = array(
			'extension_id' => $row->get('extension_id'),
			'type'         => 'plugin',
			'name'         => Request::getString('name', $row->get('name')),
			'element'      => Request::getString('element', $row->get('element')),
			'folder'       => Request::getString('folder', $row->get('folder')),
			'client_id'    => Request::getInt('client_id', $row->get('client_id')),
			'enabled'      => Request::getInt('enabled', $row->get('enabled')),
			'access'       => Request::getInt('access', $row->get('access')),
			'protected'    => $row->get('protected'),
			'params'       => Request::getString('params', $row->get('params'))
		);

		if (!$row->set($fields))
		{
			App::abort(422, Lang::txt('COM_PLUGINS_ERROR_BINDING_DATA'));
		}

		if (!$row->save())
		{
			App::abort(500, Lang::txt('COM_PLUGINS_ERROR_SAVING_DATA'));
		}

		$this->send($row->toObject());
	}

	/**
	 * Delete an entry
	 *
	 * @apiMethod DELETE
	 * @apiUri    /plugins/{id}
	 * @apiParameter {
	 * 		"name":        "extension_id",
	 * 		"description": "Extension identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		if (!User::authorise('core.manage', 'com_plugins'))
		{
			App::abort(403, 'Not Authorized');
		}

		$ids = Request::getArray('extension_id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			App::abort(500, Lang::txt('COM_PLUGINS_ERROR_MISSING_ID'));
		}

		foreach ($ids as $id)
		{
			$row = Plugin::oneOrNew(intval($id));

			if (!$row->get('id'))
			{
				App::abort(404, Lang::txt('COM_PLUGINS_ERROR_MISSING_RECORD'));
			}

			if ($row->get('protected'))
			{
				App::abort(404, Lang::txt('COM_PLUGINS_ERROR_PROTECTED_RECORD'));
			}

			if (!$row->destroy())
			{
				App::abort(500, $row->getError());
			}
		}

		$this->send(null, 204);
	}

	/**
	 * Trigger a specific event
	 *
	 * @apiMethod GET
	 * @apiUri    /plugins/trigger
	 * @apiParameter {
	 * 		"name":        "event",
	 * 		"description": "Event name with optional plugin group in dot notation (group.event)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "folder",
	 * 		"description": "Folder (plugin group)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @return    void
	 */
	public function triggerTask()
	{
		//$this->requiresAuthentication();

		$event  = Request::getString('event', '');
		$folder = Request::getString('folder', '');

		if (!$event)
		{
			App::abort(404, Lang::txt('COM_PLUGINS_ERROR_MISSING_ARGUMENT'));
		}

		if (!strstr($event, '.'))
		{
			$event = $folder . '.' . $event;
		}

		$results = Event::trigger($event, array());

		$this->send($results);
	}
}
