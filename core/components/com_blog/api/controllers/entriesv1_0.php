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

namespace Components\Blog\Api\Controllers;

use Components\Blog\Models\Entry;
use Components\Blog\Models\Archive;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Route;
use User;
use Lang;

require_once (dirname(dirname(__DIR__)) . DS . 'models' . DS . 'archive.php');

/**
 * API controller class for blog entries
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Display a list of entries
	 *
	 * @apiMethod GET
	 * @apiUri    /blog/list
	 * @apiParameter {
	 * 		"name":          "scope",
	 * 		"description":   "Scope type (group, member, etc.)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "site"
	 * }
	 * @apiParameter {
	 * 		"name":          "scope_id",
	 * 		"description":   "Scope object ID",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
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
		$model = new Archive('site');

		$filters = array(
			'scope'      => Request::getVar('scope', 'site'),
			'scope_id'   => Request::getInt('scope_id', 0),
			'search'     => Request::getVar('search', ''),
			'authorized' => false,
			'state'      => 1,
			'access'     => User::getAuthorisedViewLevels()
		);

		$response = new stdClass;
		$response->posts = array();
		$response->total = $model->entries($filters)->count();

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			$rows = $model->entries($filters)
				->ordered('sort', 'sort_order')
				->paginated()
				->rows();

			foreach ($rows as $i => $entry)
			{
				$obj = new stdClass;
				$obj->id        = $entry->get('id');
				$obj->title     = $entry->get('title');
				$obj->alias     = $entry->get('alias');
				$obj->state     = $entry->get('state');
				$obj->published = with(new Date($entry->get('publish_up')))->format('Y-m-d\TH:i:s\Z');
				$obj->scope     = $entry->get('scope');
				$obj->author    = $entry->creator->get('name');
				$obj->url       = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link()), DS));
				$obj->comments  = $entry->comments()->whereIn('state', array(1, 3))->count();

				$response->posts[] = $obj;
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Create an entry
	 *
	 * @apiMethod POST
	 * @apiUri    /blog
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Scope type (group, member, etc.)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Scope object ID",
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
	 * 		"name":        "content",
	 * 		"description": "Entry content",
	 * 		"type":        "string",
	 * 		"required":    true,
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
	 * 		"name":        "crated_by",
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
	 * 		"name":        "allow_comments",
	 * 		"description": "Allow comments on the entry?",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "publish_up",
	 * 		"description": "Publish start timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "now"
	 * }
	 * @apiParameter {
	 * 		"name":        "publish_down",
	 * 		"description": "Publish end timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "tags",
	 * 		"description": "Comma-separated list of tags",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'scope'          => Request::getVar('scope', '', 'post'),
			'scope_id'       => Request::getInt('scope_id', 0, 'post'),
			'title'          => Request::getVar('title', null, 'post', 'none', 2),
			'alias'          => Request::getVar('alias', 0, 'post'),
			'content'        => Request::getVar('content', null, 'post', 'none', 2),
			'created'        => Request::getVar('created', new Date('now'), 'post'),
			'created_by'     => Request::getInt('created_by', 0, 'post'),
			'state'          => Request::getInt('state', 0, 'post'),
			'access'         => Request::getInt('access', 0, 'post'),
			'allow_comments' => Request::getInt('allow_comments', 0, 'post'),
			'publish_up'     => Request::getVar('publish_up', new Date('now'), 'post'),
			'publish_down'   => Request::getVar('publish_down', null, 'post'),
			'hits'           => Request::getInt('hits', 0, 'post'),
			'tags'           => Request::getVar('tags', null, 'post')
		);

		$row = new Entry();

		if (!$row->set($fields))
		{
			throw new Exception(Lang::txt('COM_BLOG_ERROR_BINDING_DATA'), 500);
		}

		$row->set('email', (isset($fields['email']) ? 1 : 0));
		$row->set('anonymous', (isset($fields['anonymous']) ? 1 : 0));

		if (!$row->save())
		{
			throw new Exception(Lang::txt('COM_BLOG_ERROR_SAVING_DATA'), 500);
		}

		if (isset($fields['tags']))
		{
			if (!$row->tag($fields['tags'], User::get('id')))
			{
				throw new Exception(Lang::txt('COM_BLOG_ERROR_SAVING_TAGS'), 500);
			}
		}

		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));
		$row->set('publish_up', with(new Date($row->get('publish_up')))->format('Y-m-d\TH:i:s\Z'));
		if ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00')
		{
			$row->set('publish_down', with(new Date($row->get('publish_down')))->format('Y-m-d\TH:i:s\Z'));
		}

		// Log activity
		$base = rtrim(Request::base(), '/');
		$url  = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link()), '/'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'created',
				'scope'       => 'blog.entry',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('COM_BLOG_ACTIVITY_ENTRY_CREATED', '<a href="' . $url . '">' . $row->get('title') . '</a>'),
				'details'     => array(
					'title' => $row->get('title'),
					'url'   => $url
				)
			],
			'recipients' => [
				$row->get('created_by')
			]
		]);

		$this->send($row->toObject());
	}

	/**
	 * Retrieve an entry
	 *
	 * @apiMethod GET
	 * @apiUri    /blog/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Blog entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$id = Request::getInt('id', 0);

		$row = Entry::oneOrFail($id);

		if (!$row->get('id'))
		{
			throw new Exception(Lang::txt('COM_BLOG_ERROR_MISSING_RECORD'), 404);
		}

		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));
		$row->set('publish_up', with(new Date($row->get('publish_up')))->format('Y-m-d\TH:i:s\Z'));
		if ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00')
		{
			$row->set('publish_down', with(new Date($row->get('publish_down')))->format('Y-m-d\TH:i:s\Z'));
		}

		$this->send($row->toObject());
	}

	/**
	 * Update an entry
	 *
	 * @apiMethod PUT
	 * @apiUri    /blog/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Blog entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Scope type (group, member, etc.)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Scope object ID",
	 * 		"type":        "integer",
	 * 		"required":    false,
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
	 * 		"name":        "content",
	 * 		"description": "Entry content",
	 * 		"type":        "string",
	 * 		"required":    true,
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
	 * 		"name":        "allow_comments",
	 * 		"description": "Allow comments on the entry?",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "publish_up",
	 * 		"description": "Publish start timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "now"
	 * }
	 * @apiParameter {
	 * 		"name":        "publish_down",
	 * 		"description": "Publish end timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "hits",
	 * 		"description": "Record hits",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "tags",
	 * 		"description": "Comma-separated list of tags",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'id'             => Request::getInt('id', 0, 'post'),
			'scope'          => Request::getVar('scope', '', 'post'),
			'scope_id'       => Request::getInt('scope_id', 0, 'post'),
			'title'          => Request::getVar('title', null, 'post', 'none', 2),
			'alias'          => Request::getVar('alias', 0, 'post'),
			'content'        => Request::getVar('content', null, 'post', 'none', 2),
			'created'        => Request::getVar('created', new Date('now'), 'post'),
			'created_by'     => Request::getInt('created_by', 0, 'post'),
			'state'          => Request::getInt('state', 0, 'post'),
			'access'         => Request::getInt('access', 0, 'post'),
			'allow_comments' => Request::getInt('allow_comments', 0, 'post'),
			'publish_up'     => Request::getVar('publish_up', null, 'post'),
			'publish_down'   => Request::getVar('publish_down', null, 'post'),
			'hits'           => Request::getInt('hits', 0, 'post'),
			'tags'           => Request::getVar('tags', null, 'post')
		);

		$row = Entry::oneOrFail($fields['id']);

		if ($row->isNew())
		{
			throw new Exception(Lang::txt('COM_BLOG_ERROR_MISSING_RECORD'), 404);
		}

		if (!$row->set($fields))
		{
			throw new Exception(Lang::txt('COM_BLOG_ERROR_BINDING_DATA'), 422);
		}

		$row->set('email', (isset($fields['email']) ? 1 : 0));
		$row->set('anonymous', (isset($fields['anonymous']) ? 1 : 0));

		if (!$row->save())
		{
			throw new Exception(Lang::txt('COM_BLOG_ERROR_SAVING_DATA'), 500);
		}

		if (isset($fields['tags']))
		{
			if (!$row->tag($fields['tags'], User::get('id')))
			{
				throw new Exception(Lang::txt('COM_BLOG_ERROR_SAVING_TAGS'), 500);
			}
		}

		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));
		$row->set('publish_up', with(new Date($row->get('publish_up')))->format('Y-m-d\TH:i:s\Z'));
		if ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00')
		{
			$row->set('publish_down', with(new Date($row->get('publish_down')))->format('Y-m-d\TH:i:s\Z'));
		}

		// Log activity
		$base = rtrim(Request::base(), '/');
		$url  = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link()), '/'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'updated',
				'scope'       => 'blog.entry',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('COM_BLOG_ACTIVITY_ENTRY_UPDATED', '<a href="' . $url . '">' . $row->get('title') . '</a>'),
				'details'     => array(
					'title' => $row->get('title'),
					'url'   => $url
				)
			],
			'recipients' => [
				$row->get('created_by')
			]
		]);

		$this->send($row->toObject());
	}

	/**
	 * Delete an entry
	 *
	 * @apiMethod DELETE
	 * @apiUri    /blog/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Blog entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			throw new Exception(Lang::txt('COM_BLOG_ERROR_MISSING_ID'), 500);
		}

		foreach ($ids as $id)
		{
			$row = Entry::oneOrNew(intval($id));

			if (!$row->get('id'))
			{
				throw new Exception(Lang::txt('COM_BLOG_ERROR_MISSING_RECORD'), 404);
			}

			if (!$row->destroy())
			{
				throw new Exception($row->getError(), 500);
			}

			// Log activity
			$base = rtrim(Request::base(), '/');
			$url  = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link()), '/'));

			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'deleted',
					'scope'       => 'blog.entry',
					'scope_id'    => $id,
					'description' => Lang::txt('COM_BLOG_ACTIVITY_ENTRY_DELETED', '<a href="' . $url . '">' . $row->get('title') . '</a>'),
					'details'     => array(
						'title' => $row->get('title'),
						'url'   => $url
					)
				],
				'recipients' => [
					$entry->get('created_by')
				]
			]);
		}

		$this->send(null, 204);
	}
}
