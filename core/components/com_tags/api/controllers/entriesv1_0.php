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

namespace Components\Tags\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Tags\Models\Tag;
use Components\Tags\Models\Cloud;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'cloud.php');

/**
 * API controller class for tags
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('autocomplete', 'list');

		parent::execute();
	}

	/**
	 * Displays a list of tags
	 *
	 * @apiMethod GET
	 * @apiUri    /tags/list
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
	 *      "default":       "raw_tag",
	 * 		"allowedValues": "created, id, tag, raw_tag"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	  * @apiParameter {
	 * 		"name":          "scope",
	 * 		"description":   "Object scope (ex: group, resource, etc.)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	  * @apiParameter {
	 * 		"name":          "scope_id",
	 * 		"description":   "Object scope ID. Typically a Resource ID, Group ID, etc.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	  * @apiParameter {
	 * 		"name":          "taggerid",
	 * 		"description":   "ID of user that tagged items.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$cloud = new Cloud();

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'scope'      => Request::getWord('scope', ''),
			'scope_id'   => Request::getInt('scope_id', 0),
			'taggerid'   => Request::getVar('tagger', ''),
			'sort'       => Request::getWord('sort', 'raw_tag'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'ASC'))
		);
		if ($filters['scope'] == 'members' || $filters['scope'] == 'member')
		{
			$filters['scope'] = 'xprofiles';
		}

		$response = new stdClass;
		$response->tags  = array();
		$response->total = $cloud->tags('count', $filters);
		//$response->showing = ($filters['start'] + 1) . ' - ' . ($filters['start'] + $filters['limit']);
		$response->start = $filters['start'];
		$response->limit = $filters['limit'];

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($cloud->tags('list', $filters) as $i => $tag)
			{
				$obj = new stdClass;
				$obj->id      = $tag->get('id');
				$obj->raw_tag = $tag->get('raw_tag');
				$obj->tag     = $tag->get('tag');
				$obj->uri     = str_replace('/api', '', $base . '/' . ltrim(Route::url($tag->link()), '/'));

				$obj->substitutes_count = $tag->get('substitutes');
				$obj->objects_count = $tag->get('total');

				$response->tags[] = $obj;
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Create an entry
	 *
	 * @apiMethod POST
	 * @apiUri    /tags
	 * @apiParameter {
	 * 		"name":        "raw_tag",
	 * 		"description": "Tag text",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "tag",
	 * 		"description": "Normalized text (alpha-numeric, no punctuation)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "description",
	 * 		"description": "Longer description of a tag",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "admin",
	 * 		"description": "Admin state (0 = no, 1 = yes)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "substitutes",
	 * 		"description": "Comma-separated list of aliases or alternatives",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$tag   = Request::getVar('tag', null, 'post');
		$raw   = Request::getVar('raw_tag', null, 'post');
		$desc  = Request::getVar('description', null, 'post');
		$admin = Request::getInt('admin', 0, 'post');
		$subs  = Request::getVar('substitutes', null, 'post');

		if (!$tag && !$raw)
		{
			throw new Exception(Lang::txt('COM_TAGS_ERROR_MISSING_DATA'), 500);
		}

		$tag = ($tag ? $tag : $raw);

		$record = Tag::oneByTag($tag);

		if ($record->isNew())
		{
			$record->set('admin', ($admin ? 1 : 0));

			if ($raw)
			{
				$record->set('raw_tag', $raw);
			}
			if ($tag)
			{
				$record->set('tag', $tag);
			}
			if ($desc)
			{
				$record->set('description', $desc);
			}

			if (!$record->save())
			{
				throw new Exception($record->getError(), 500);
			}

			if (!$record->saveSubstitutions($subs))
			{
				throw new Exception($record->getError(), 500);
			}
		}

		$this->send($record->toObject());
	}

	/**
	 * Retrieve an entry
	 *
	 * @apiMethod GET
	 * @apiUri    /tags/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Tag entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$name = Request::getWord('tag', '');
		$id   = Request::getInt('id', 0);

		$tag = ($id ? Tag::oneOrFail($id) : Tag::oneByTag($name));

		if (!$tag->get('id'))
		{
			throw new Exception(Lang::txt('Specified tag does not exist.'), 404);
		}

		$data = $tag->toObject();
		//$data->substitutes = $tag->substitutes()->rows()->toObject();

		$this->send($data);
	}

	/**
	 * Update an entry
	 *
	 * @apiMethod PUT
	 * @apiUri    /tags/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Tag entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "raw_tag",
	 * 		"description": "Tag text",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "tag",
	 * 		"description": "Normalized text (alpha-numeric, no punctuation)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "description",
	 * 		"description": "Longer description of a tag",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "admin",
	 * 		"description": "Admin state (0 = no, 1 = yes)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "substitutes",
	 * 		"description": "Comma-separated list of aliases or alternatives",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$id = Request::getInt('id', 0);

		$record = Tag::oneOrNew($id);

		if (!$record->get('id'))
		{
			throw new Exception(Lang::txt('COM_TAGS_ERROR_MISSING_DATA'), 500);
		}

		$tag   = Request::getVar('tag', $record->get('tag'));
		$raw   = Request::getVar('raw_tag', $record->get('raw_tag'));
		$desc  = Request::getVar('description', $record->get('description'));
		$admin = Request::getInt('admin', $record->get('admin'));

		$record->set('admin', ($admin ? 1 : 0));
		$record->set('raw_tag', $raw);
		$record->set('tag', $tag);
		$record->set('description', $desc);

		if (!$record->save())
		{
			throw new Exception($record->getError(), 500);
		}

		$subs = Request::getVar('substitutes', null);

		if ($subs)
		{
			if (!$record->saveSubstitutions($subs))
			{
				throw new Exception($record->getError(), 500);
			}
		}

		$this->send($record->toObject());
	}

	/**
	 * Delete an entry
	 *
	 * @apiMethod DELETE
	 * @apiUri    /tags/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Tag entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$name = Request::getWord('tag', '');
		$id   = Request::getInt('id', 0);

		$tag = ($id ? Tag::oneOrFail($id) : Tag::oneByTag($name));

		if (!$tag->get('id'))
		{
			throw new Exception(Lang::txt('Specified tag does not exist.'), 404);
		}

		if (!$tag->destroy())
		{
			throw new Exception(Lang::txt('Failed to delete tag.'), 500);
		}

		$this->send(null, 202);
	}

	/**
	 * Remove tag from an item
	 *
	 * @apiMethod DELETE
	 * @apiUri    /tags/{id}/remove
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Tag entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Item type",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Item ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	  * @apiParameter {
	 * 		"name":        "tagger",
	 * 		"description": "ID of user who tagged the item. Supplying this will only remove tags by this user.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function removeTask()
	{
		$this->requiresAuthentication();

		$name = Request::getWord('tag', '');
		$id   = Request::getInt('id', 0);

		$tag = ($id ? Tag::oneOrFail($id) : Tag::oneByTag($name));

		if (!$tag->get('id'))
		{
			throw new Exception(Lang::txt('Specified tag does not exist.'), 404);
		}

		$scope    = Request::getWord('scope', '');
		$scope_id = Request::getInt('scope_id', 0);
		$tagger   = Request::getInt('tagger', 0);

		if (!$scope || !$scope_id)
		{
			throw new Exception(Lang::txt('Invalid scope and/or scope_id.'), 500);
		}

		if (!$tag->removeFrom($scope, $scope_id, $tagger))
		{
			throw new Exception(Lang::txt('Failed to remove tag from object.'), 500);
		}

		$this->send(null, 202);
	}

	/**
	 * Add a tag to an item
	 *
	 * @apiMethod DELETE
	 * @apiUri    /tags/{id}/add
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Tag entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Item type",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Item ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	  * @apiParameter {
	 * 		"name":        "tagger",
	 * 		"description": "ID of user who tagged the item.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function addTask()
	{
		$this->requiresAuthentication();

		$name = Request::getWord('tag', '');
		$id   = Request::getInt('id', 0);

		$tag = ($id ? Tag::oneOrFail($id) : Tag::oneByTag($name));

		if (!$tag->get('id'))
		{
			throw new Exception(Lang::txt('Specified tag does not exist.'), 404);
		}

		$scope    = Request::getWord('scope', '');
		$scope_id = Request::getInt('scope_id', 0);
		$tagger   = Request::getInt('tagger', User::get('id'));

		if (!$scope || !$scope_id)
		{
			throw new Exception(Lang::txt('Invalid scope and/or scope_id.'), 500);
		}

		if (!$tag->addTo($scope, $scope_id, $tagger))
		{
			throw new Exception(Lang::txt('Failed to add tag to object.'), 500);
		}

		$this->send(null, 202);
	}
}
