<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Api\Controllers;

use Components\Collections\Models\Archive;
use Components\Collections\Models\Collection;
use Components\Collections\Models\Post;
use Components\Collections\Models\Item;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'archive.php';

/**
 * API controller class for collection posts
 */
class Postsv1_0 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('posts', 'list');

		parent::execute();
	}

	/**
	 * Display posts
	 *
	 * @apiMethod GET
	 * @apiUri    /collections/posts
	 * @apiParameter {
	 * 		"name":          "collection_id",
	 * 		"description":   "Collection identifier",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
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
		$model = new Collection();

		$filters = array(
			'limit'         => Request::getInt('limit', 25),
			'start'         => Request::getInt('limitstart', 0),
			'search'        => Request::getString('search', ''),
			'state'         => 1,
			'sort'          => Request::getString('sort', 'created'),
			'sort_Dir'      => strtoupper(Request::getWord('sortDir', 'DESC')),
			'is_default'    => 0,
			'access'        => 0,
			'count'         => true
		);
		if ($collection_id = Request::getInt('collection_id'))
		{
			$filters['collection_id'] = $collection_id;
		}
		$filters['sort'] = 'p.' . $filters['sort'];

		$response = new stdClass;
		$response->posts = array();
		$response->total = $model->posts($filters);

		if ($response->total)
		{
			$href = 'index.php?option=com_collections&controller=media&post=';
			$base = rtrim(Request::base(), '/');
			$base = str_replace('/api', '', $base) . '/';

			$filters['count'] = false;

			foreach ($model->posts($filters) as $i => $entry)
			{
				$item = $entry->item();

				$obj = new stdClass;
				$obj->id        = $entry->get('id');
				$obj->collection_id = $entry->get('collection_id');
				$obj->item_id   = $entry->get('item_id');
				$obj->original  = $entry->get('original');
				$obj->ordering  = $entry->get('ordering');
				$obj->title     = $entry->get('title', $item->get('title'));
				$obj->type      = $item->get('type');
				$obj->created   = $entry->get('created');
				$obj->created_by = new stdClass;
				$obj->created_by->id   = $entry->get('created_by');
				$obj->created_by->name = $entry->creator()->get('name');
				$obj->url       = $base . ltrim(Route::url($entry->link()), '/');

				$obj->tags      = $item->tags('string');
				$obj->comments  = $item->get('comments', 0);
				$obj->likes     = $item->get('positive', 0);
				$obj->reposts   = $item->get('reposts', 0);
				$obj->assets    = array();

				$assets = $item->assets();
				if ($assets->total() > 0)
				{
					foreach ($assets as $asset)
					{
						$a = new stdClass;
						$a->title       = ltrim($asset->get('filename'), '/');
						$a->description = $asset->get('description');
						$a->url         = ($asset->get('type') == 'link' ? $asset->get('filename') : $base . ltrim(Route::url($href . $entry->get('id') . '&task=download&file=' . $a->title), '/'));

						$obj->assets[] = $a;
					}
				}

				$response->posts[] = $obj;
			}
		}

		$this->send($response);
	}

	/**
	 * Create a post
	 *
	 * @apiMethod POST
	 * @apiUri    /collections/posts
	 * @apiParameter {
	 * 		"name":        "collection_id",
	 * 		"description": "Collection identifier",
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
	 * 		"name":        "description",
	 * 		"description": "Entry description",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "url",
	 * 		"description": "Entry URL; Requires 'type'='link'",
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
	 * 		"name":        "type",
	 * 		"description": "Item type",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "file"
	 * }
	 * @apiParameter {
	 * 		"name":        "object_id",
	 * 		"description": "Object ID",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'title'          => Request::getString('title', null, 'post', 'none', 2),
			'description'    => Request::getString('description', null, 'post', 'none', 2),
			'url'            => Request::getString('url', null, 'post'),
			'created'        => Request::getString('created', with(new Date('now'))->toSql(), 'post'),
			'created_by'     => Request::getInt('created_by', 0, 'post'),
			'state'          => Request::getInt('state', 1, 'post'),
			'access'         => Request::getInt('access', 0, 'post'),
			'type'           => Request::getString('type', 'file', 'post'),
			'object_id'      => Request::getInt('object_id', 0, 'post'),
			'collection_id'  => Request::getInt('collection_id', 0, 'post')
		);

		if (!$fields['collection_id'])
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_COLLECTION'), 422);
		}

		$row = new Item();

		if (!$row->bind($fields))
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_BINDING_DATA'), 500);
		}

		if (!$row->store(true))
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_SAVING_DATA'), 500);
		}

		$post = new Post();
		$post->set('item_id', $row->get('id'));
		$post->set('original', 1);
		$post->set('collection_id', $fields['collection_id']);

		if (!$post->store(true))
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_SAVING_DATA'), 500);
		}

		// Output the newly created post
		$href = 'index.php?option=com_collections&controller=media&post=';
		$base = rtrim(Request::base(), '/');
		$base = str_replace('/api', '', $base) . '/';

		$item = $post->item();

		$collection = new Collection($post->get('collection_id'));

		$post->set('object_type', $collection->get('object_type'));
		$post->set('object_id', $collection->get('object_id'));

		$obj = new stdClass;
		$obj->id        = $post->get('id');
		$obj->collection_id = $post->get('collection_id');
		$obj->item_id   = $post->get('item_id');
		$obj->original  = $post->get('original');
		$obj->ordering  = $post->get('ordering');
		$obj->title     = $post->get('title', $item->get('title'));
		$obj->type      = $item->get('type');
		$obj->created   = $post->get('created');
		$obj->created_by = new stdClass;
		$obj->created_by->id   = $post->get('created_by');
		$obj->created_by->name = $post->creator()->get('name');
		$obj->url       = $base . ltrim(Route::url($post->link()), '/');

		$obj->tags      = $item->tags('string');
		$obj->comments  = $item->get('comments', 0);
		$obj->likes     = $item->get('positive', 0);
		$obj->reposts   = $item->get('reposts', 0);
		$obj->assets    = array();

		$assets = $item->assets();

		if ($assets->total() > 0)
		{
			foreach ($assets as $asset)
			{
				$a = new stdClass;
				$a->title       = ltrim($asset->get('filename'), '/');
				$a->description = $asset->get('description');
				$a->url         = ($asset->get('type') == 'link' ? $asset->get('filename') : $base . ltrim(Route::url($href . $post->get('id') . '&task=download&file=' . $a->title), '/'));

				$obj->assets[] = $a;
			}
		}

		$this->send($obj);
	}

	/**
	 * Retrieve a post
	 *
	 * @apiMethod GET
	 * @apiUri    /collections/posts/{id}
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

		$entry = new Post($id);

		if (!$entry->exists())
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_RECORD'), 404);
		}

		$href = 'index.php?option=com_collections&controller=media&post=';
		$base = rtrim(Request::base(), '/');
		$base = str_replace('/api', '', $base) . '/';

		$item = $entry->item();

		$collection = new Collection($entry->get('collection_id'));

		$entry->set('object_type', $collection->get('object_type'));
		$entry->set('object_id', $collection->get('object_id'));

		$obj = new stdClass;
		$obj->id        = $entry->get('id');
		$obj->collection_id = $entry->get('collection_id');
		$obj->item_id   = $entry->get('item_id');
		$obj->original  = $entry->get('original');
		$obj->ordering  = $entry->get('ordering');
		$obj->title     = $entry->get('title', $item->get('title'));
		$obj->type      = $item->get('type');
		$obj->created   = $entry->get('created');
		$obj->created_by = new stdClass;
		$obj->created_by->id   = $entry->get('created_by');
		$obj->created_by->name = $entry->creator()->get('name');
		$obj->url       = $base . ltrim(Route::url($entry->link()), '/');

		$obj->tags      = $item->tags('string');
		$obj->comments  = $item->get('comments', 0);
		$obj->likes     = $item->get('positive', 0);
		$obj->reposts   = $item->get('reposts', 0);
		$obj->assets    = array();

		$assets = $item->assets();

		if ($assets->total() > 0)
		{
			foreach ($assets as $asset)
			{
				$a = new stdClass;
				$a->title       = ltrim($asset->get('filename'), '/');
				$a->description = $asset->get('description');
				$a->url         = ($asset->get('type') == 'link' ? $asset->get('filename') : $base . ltrim(Route::url($href . $entry->get('id') . '&task=download&file=' . $a->title), '/'));

				$obj->assets[] = $a;
			}
		}

		$this->send($obj);
	}

	/**
	 * Update a post
	 *
	 * @apiMethod PUT
	 * @apiUri    /collections/posts/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "collection_id",
	 * 		"description": "Collection identifier",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Entry title",
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
	 * 		"name":        "url",
	 * 		"description": "Entry URL; Requires 'type'='link'",
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
	 * 		"name":        "type",
	 * 		"description": "Item type",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "file"
	 * }
	 * @apiParameter {
	 * 		"name":        "object_id",
	 * 		"description": "Object ID",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'id'             => Request::getInt('id', 0, 'put'),
			'title'          => Request::getString('title', null, 'put'),
			'description'    => Request::getString('description', null, 'put'),
			'url'            => Request::getString('url', null, 'put'),
			'created'        => Request::getString('created', with(new Date('now'))->toSql(), 'put'),
			'created_by'     => Request::getInt('created_by', 0, 'put'),
			'state'          => Request::getInt('state', 1, 'put'),
			'access'         => Request::getInt('access', 0, 'put'),
			'type'           => Request::getString('type', 'file', 'put'),
			'object_id'      => Request::getInt('object_id', 0, 'put'),
			'collection_id'  => Request::getInt('collection_id', 0, 'put')
		);

		$row = new Post($fields['id']);

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

		// Output the newly created post
		$href = 'index.php?option=com_collections&controller=media&post=';
		$base = rtrim(Request::base(), '/');
		$base = str_replace('/api', '', $base) . '/';

		$item = $row->item();

		$collection = new Collection($row->get('collection_id'));

		$row->set('object_type', $collection->get('object_type'));
		$row->set('object_id', $collection->get('object_id'));

		$obj = new stdClass;
		$obj->id        = $row->get('id');
		$obj->collection_id = $row->get('collection_id');
		$obj->item_id   = $row->get('item_id');
		$obj->original  = $row->get('original');
		$obj->ordering  = $row->get('ordering');
		$obj->title     = $row->get('title', $item->get('title'));
		$obj->type      = $item->get('type');
		$obj->created   = $row->get('created');
		$obj->created_by = new stdClass;
		$obj->created_by->id   = $row->get('created_by');
		$obj->created_by->name = $row->creator()->get('name');
		$obj->url       = $base . ltrim(Route::url($row->link()), '/');

		$obj->tags      = $item->tags('string');
		$obj->comments  = $item->get('comments', 0);
		$obj->likes     = $item->get('positive', 0);
		$obj->reposts   = $item->get('reposts', 0);
		$obj->assets    = array();

		$assets = $item->assets();

		if ($assets->total() > 0)
		{
			foreach ($assets as $asset)
			{
				$a = new stdClass;
				$a->title       = ltrim($asset->get('filename'), '/');
				$a->description = $asset->get('description');
				$a->url         = ($asset->get('type') == 'link' ? $asset->get('filename') : $base . ltrim(Route::url($href . $row->get('id') . '&task=download&file=' . $a->title), '/'));

				$obj->assets[] = $a;
			}
		}

		$this->send($obj);
	}

	/**
	 * Delete a post
	 *
	 * @apiMethod DELETE
	 * @apiUri    /collections/posts/{id}
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
			$row = new Post(intval($id));

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
