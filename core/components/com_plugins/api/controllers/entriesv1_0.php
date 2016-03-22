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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 *
 */

namespace Components\Plugins\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Plugins\Models\Plugin;
use Exception;
use stdClass;
use Request;
use Event;
use Lang;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'plugin.php');

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
			'folder'   => Request::getVar('folder', ''),
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
			'name'           => Request::getVar('name', null, 'post'),
			'element'        => Request::getVar('element', null, 'post'),
			'folder'         => Request::getVar('folder', null, 'post'),
			'client_id'      => Request::getInt('client_id', 0, 'post'),
			'enabled'        => Request::getInt('enabled', 0, 'post'),
			'access'         => Request::getInt('access', 1, 'post'),
			'protected'      => 0,
			'params'         => Request::getVar('params', null, 'post', 'none')
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

		$fields = array(
			'extension_id' => Request::getInt('extension_id', 0, 'post'),
			'type'         => 'plugin',
			'name'         => Request::getVar('name', null, 'post'),
			'element'      => Request::getVar('element', null, 'post'),
			'folder'       => Request::getVar('folder', null, 'post'),
			'client_id'    => Request::getInt('client_id', 0, 'post'),
			'enabled'      => Request::getInt('enabled', 0, 'post'),
			'access'       => Request::getInt('access', 1, 'post'),
			'protected'    => 0,
			'params'       => Request::getVar('params', '', 'post', 'none')
		);

		$row = Plugin::oneOrFail($fields['extension_id']);

		if ($row->isNew())
		{
			App::abort(404, Lang::txt('COM_PLUGINS_ERROR_MISSING_RECORD'));
		}

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

		$ids = Request::getVar('extension_id', array());
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

		$event  = Request::getVar('event', '');
		$folder = Request::getVar('folder', '');

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
