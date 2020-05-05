<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Api\Controllers;

use Components\Collections\Models\Archive;
use Components\Collections\Models\Collection;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'archive.php';

/**
 * API controller class for collections
 */
class Collectionsv1_0 extends ApiController
{
	/**
	 * Display a list of collections
	 *
	 * @apiMethod GET
	 * @apiUri    /collections/list
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
	 * 		"allowedValues": "created, ordering"
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
		$model = new Archive();

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getString('search', ''),
			'state'      => 1,
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'DESC')),
			'is_default' => 0,
			'access'     => 0,
			'count'      => true
		);

		$response = new stdClass;
		$response->collections = array();
		$response->total = $model->collections($filters);

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			$filters['count'] = false;

			foreach ($model->collections($filters) as $i => $entry)
			{
				$collection = Collection::getInstance($entry->item()->get('object_id'));

				$obj = $collection->toObject();
				$obj->created_by = new stdClass;
				$obj->created_by->id   = $collection->get('created_by');
				$obj->created_by->name = $collection->creator()->get('name');
				$obj->url = str_replace('/api', '', $base . '/' . ltrim(Route::url($collection->link()), '/'));

				$response->collections[] = $obj;
			}
		}

		$this->send($response);
	}

	/**
	 * Create a collection
	 *
	 * @apiMethod POST
	 * @apiUri    /collections
	 * @apiParameter {
	 * 		"name":        "object_type",
	 * 		"description": "Object type (group, member, etc.)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "object_id",
	 * 		"description": "Object ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Entry title",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "alias",
	 * 		"description": "Entry alias",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "description",
	 * 		"description": "Entry description",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "created",
	 * 		"description": "Created timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "now"
	 * }
	 * @apiParameter {
	 * 		"name":        "created_by",
	 * 		"description": "User ID of entry creator",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "state",
	 * 		"description": "Published state (0 = unpublished, 1 = published)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "access",
	 * 		"description": "Access level (0 = public, 1 = registered users, 4 = private)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "layout",
	 * 		"description": "How to display posts",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "grid"
	 * }
	 * @apiParameter {
	 * 		"name":        "sort",
	 * 		"description": "How to sort posts (created, ordering)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "created"
	 * }
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'object_type'    => Request::getString('object_type', '', 'post'),
			'object_id'      => Request::getInt('object_id', 0, 'post'),
			'title'          => Request::getString('title', null, 'post', 'none', 2),
			'alias'          => Request::getString('alias', 0, 'post'),
			'description'    => Request::getString('description', null, 'post', 'none', 2),
			'created'        => Request::getString('created', with(new Date('now'))->toSql(), 'post'),
			'created_by'     => Request::getInt('created_by', 0, 'post'),
			'state'          => Request::getInt('state', 0, 'post'),
			'access'         => Request::getInt('access', 0, 'post'),
			'layout'         => Request::getWord('layout', 'grid', 'post'),
			'sort'           => Request::getString('sort', 'created', 'post')
		);

		$row = new Collection();

		if (!$row->bind($fields))
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_BINDING_DATA'), 500);
		}

		if (!$row->store(true))
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_SAVING_DATA'), 500);
		}

		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));

		$this->send($row->toObject());
	}

	/**
	 * Retrieve a collection
	 *
	 * @apiMethod GET
	 * @apiUri    /collections/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$id = Request::getInt('id', 0);

		$row = new Collection($id);

		if (!$row->exists())
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_RECORD'), 404);
		}

		$response = $row->toObject();
		$response->created_by = new stdClass;
		$response->created_by->id   = $row->get('created_by');
		$response->created_by->name = $row->creator()->get('name');
		$response->url = str_replace('/api', '', rtrim(Request::base(), '/') . '/' . ltrim(Route::url($row->link()), '/'));

		$this->send($response);
	}

	/**
	 * Update a collection
	 *
	 * @apiMethod PUT
	 * @apiUri    /collections/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "object_type",
	 * 		"description": "Object type (group, member, etc.)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "object_id",
	 * 		"description": "Object ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Entry title",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "alias",
	 * 		"description": "Entry alias",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "description",
	 * 		"description": "Entry description",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "created",
	 * 		"description": "Created timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "now"
	 * }
	 * @apiParameter {
	 * 		"name":        "created_by",
	 * 		"description": "User ID of entry creator",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "state",
	 * 		"description": "Published state (0 = unpublished, 1 = published)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "access",
	 * 		"description": "Access level (0 = public, 1 = registered users, 4 = private)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "layout",
	 * 		"description": "How to display posts",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "grid"
	 * }
	 * @apiParameter {
	 * 		"name":        "sort",
	 * 		"description": "How to sort posts (created, ordering)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "created"
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'id'             => Request::getInt('id', 0, 'put'),
			'object_type'    => Request::getString('object_type', '', 'put'),
			'object_id'      => Request::getInt('object_id', 0, 'put'),
			'title'          => Request::getString('title', null, 'put', 'none', 2),
			'alias'          => Request::getString('alias', 0, 'put'),
			'description'    => Request::getString('description', null, 'put', 'none', 2),
			'created'        => Request::getString('created', with(new Date('now'))->toSql(), 'put'),
			'created_by'     => Request::getInt('created_by', 0, 'put'),
			'state'          => Request::getInt('state', 0, 'put'),
			'access'         => Request::getInt('access', 0, 'put'),
			'layout'         => Request::getWord('layout', 'grid', 'put'),
			'sort'           => Request::getString('sort', 'created', 'put')
		);

		$row = new Collection($fields['id']);

		if (!$row->exists())
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_RECORD'), 404);
		}

		if (!$row->bind($fields))
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_BINDING_DATA'), 422);
		}

		if (!$row->store(true))
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_SAVING_DATA'), 500);
		}

		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));

		$this->send($row->toObject());
	}

	/**
	 * Delete a collection
	 *
	 * @apiMethod DELETE
	 * @apiUri    /collections/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_ID'), 500);
		}

		foreach ($ids as $id)
		{
			$row = new Collection(intval($id));

			if (!$row->exists())
			{
				throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_RECORD'), 404);
			}

			if (!$row->delete())
			{
				throw new Exception($row->getError(), 500);
			}
		}

		$this->send(null, 204);
	}
}
