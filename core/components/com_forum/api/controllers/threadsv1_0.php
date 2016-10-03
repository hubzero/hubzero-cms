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

namespace Components\Forum\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Components\Forum\Models\Manager;
use Components\Forum\Models\Section;
use Components\Forum\Models\Category;
use Components\Forum\Models\Post;
use Component;
use Exception;
use stdClass;
use Request;
use Config;
use Route;
use Lang;
use User;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'manager.php');

/**
 * API controller class for forum posts
 */
class Threadsv1_0 extends ApiController
{
	/**
	 * Display a list of sections
	 *
	 * @apiMethod GET
	 * @apiUri    /forum/sections
	 * @apiParameter {
	 * 		"name":          "scope",
	 * 		"description":   "Scope (site, groups, members, etc.)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "site"
	 * }
	 * @apiParameter {
	 * 		"name":          "scope_id",
	 * 		"description":   "Scope ID",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @return    void
	 */
	public function sectionsTask()
	{
		$filters = array(
			'scope'      => Request::getWord('scope', 'site'),
			'scope_id'   => Request::getInt('scope_id', 0),
			'state'      => Section::STATE_PUBLISHED,
			'access'     => User::getAuthorisedViewLevels()
		);

		if ($filters['scope'] == 'group')
		{
			$group = \Hubzero\User\Group::getInstance($filters['scope_id']);

			if ($group && in_array(User::get('id'), $group->get('members')))
			{
				$filters['access'][] = 5; // Private
			}
		}

		$model = new Manager($filters['scope'], $filters['scope_id']);

		$response = new stdClass;
		$response->sections = array();

		$sections = $model->sections(array(
				'state'  => $filters['state'],
				'access' => User::getAuthorisedViewLevels()
			))
			->ordered()
			->rows();

		$response->total = $sections->count();

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($sections as $section)
			{
				$obj = new stdClass;
				$obj->id         = $section->get('id');
				$obj->title      = $section->get('title');
				$obj->alias      = $section->get('alias');
				$obj->created    = with(new Date($section->get('created')))->format('Y-m-d\TH:i:s\Z');
				$obj->scope      = $section->get('scope');
				$obj->scope_id   = $section->get('scope_id');

				$obj->categories = $section->categories()
					->whereEquals('state', $filters['state'])
					->whereIn('access', $filters['access'])
					->total();

				$obj->url        = str_replace('/api', '', $base . '/' . ltrim(Route::url($section->link('base')), '/'));

				$response->sections[] = $obj;
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Display categories for a section
	 *
	 * @apiMethod GET
	 * @apiUri    /forum/categories
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "section",
	 * 		"description":   "Section ID",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 *      "default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "scope",
	 * 		"description":   "Scope (site, groups, members, etc.)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "site"
	 * }
	 * @apiParameter {
	 * 		"name":          "scope_id",
	 * 		"description":   "Scope ID",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "closed",
	 * 		"description":   "If the category is marked as closed (1) or not (0). NULL to return all.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @return    void
	 */
	public function categoriesTask()
	{
		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'section_id' => Request::getInt('section', 0),
			'search'     => Request::getVar('search', ''),
			'scope'      => Request::getWord('scope', 'site'),
			'scope_id'   => Request::getInt('scope_id', 0),
			'state'      => Category::STATE_PUBLISHED,
			'closed'     => Request::getVar('closed', null),
			'access'     => User::getAuthorisedViewLevels()
		);

		if ($filters['scope'] == 'group')
		{
			$group = \Hubzero\User\Group::getInstance($filters['scope_id']);

			if ($group && in_array(User::get('id'), $group->get('members')))
			{
				$filters['access'][] = 5; // Private
			}
		}

		$forum = new Manager($filters['scope'], $filters['scope_id']);

		$response = new stdClass;
		$response->categories = array();

		if ($filters['section_id'])
		{
			// Make sure the section exists and is available
			$section = Section::oneOrFail($filters['section_id']);

			if (!$section->get('id'))
			{
				throw new Exception(Lang::txt('Section not found.'), 404);
			}

			if ($section->get('state') == Section::STATE_DELETED)
			{
				throw new Exception(Lang::txt('Section not found.'), 404);
			}

			$response->section = new stdClass;
			$response->section->id         = $section->get('id');
			$response->section->title      = $section->get('title');
			$response->section->alias      = $section->get('alias');
			$response->section->created    = with(new Date($section->get('created')))->format('Y-m-d\TH:i:s\Z');
			$response->section->scope      = $section->get('scope');
			$response->section->scope_id   = $section->get('scope_id');
		}
		else
		{
			$sections = Section::all()
				->whereEquals('scope', $filters['scope'])
				->whereEquals('scope_id', $filters['scope_id'])
				->whereEquals('state', $filters['state'])
				->whereIn('access', $filters['access'])
				->rows();

			$filters['section_id'] = array();

			foreach ($sections as $section)
			{
				$filters['section_id'][] = $section->get('id');
			}
		}

		$entries = Category::all()
			->whereIn('section_id', (array)$filters['section_id'])
			->whereEquals('state', $filters['state'])
			->whereIn('access', $filters['access']);

		if (is_int($filters['closed']))
		{
			$entries->whereEquals('closed', $filters['closed']);
		}

		if ($filters['search'])
		{
			$entries->whereLike('description', $filters['search'], 1)
				->orWhereLike('title', $filters['search'], 1)
				->resetDepth();
		}

		$categories = $entries
			->ordered()
			->paginated()
			->rows();

		$response->total = $categories->count();

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($categories as $category)
			{
				$obj = new stdClass;
				$obj->id          = (int)$category->get('id');
				$obj->title       = $category->get('title');
				$obj->alias       = $category->get('alias');
				$obj->description = $category->get('description');
				$obj->created     = with(new Date($category->get('created')))->format('Y-m-d\TH:i:s\Z');
				$obj->closed      = (int)$category->get('closed');
				$obj->scope       = $category->get('scope');
				$obj->scope_id    = (int)$category->get('scope_id');
				$obj->section_id  = (int)$category->get('section_id');

				$obj->threads     = $category->threads()
					->whereEquals('state', $filters['state'])
					->whereIn('access', $filters['access'])
					->total();

				$obj->posts       = $category->posts()
					->whereEquals('state', $filters['state'])
					->whereIn('access', $filters['access'])
					->total();

				$obj->url         = str_replace('/api', '', $base . '/' . ltrim(Route::url($category->link()), '/'));

				$response->categories[] = $obj;
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Display a list of threads
	 *
	 * @apiMethod GET
	 * @apiUri    /forum/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
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
	 * 		"name":          "section",
	 * 		"description":   "Section ID. Find all posts for all categories within a section.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "Category ID. Find all posts within a category.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "threads_only",
	 * 		"description":   "Return only thread starter posts (true) or any post (false).",
	 * 		"type":          "boolean",
	 * 		"required":      false,
	 *      "default":       false
	 * }
	 * @apiParameter {
	 * 		"name":          "parent",
	 * 		"description":   "Parent post ID. Find all immediate descendent (replies) posts.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "thread",
	 * 		"description":   "Thread ID. Find all posts in a specified thread.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "scope",
	 * 		"description":   "Scope (site, groups, members, etc.)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "site"
	 * }
	 * @apiParameter {
	 * 		"name":          "scope_id",
	 * 		"description":   "Scope ID",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		$filters = array(
			'limit'       => Request::getInt('limit', 25),
			'start'       => Request::getInt('limitstart', 0),
			'section_id'  => Request::getInt('section', 0),
			'category_id' => Request::getInt('category', 0),
			'parent'      => Request::getInt('parent', 0),
			'thread'      => Request::getInt('thread', 0),
			'threads'     => Request::getVar('threads_only', false),
			'search'      => Request::getVar('search', ''),
			'scope'       => Request::getWord('scope', 'site'),
			'scope_id'    => Request::getInt('scope_id', 0),
			'state'       => Post::STATE_PUBLISHED,
			'parent'      => 0,
			'access'      => User::getAuthorisedViewLevels()
		);
		$filters['threads'] = (!$filters['threads'] || $filters['threads'] == 'false') ? false : true;

		if ($filters['scope'] == 'group')
		{
			$group = \Hubzero\User\Group::getInstance($filters['scope_id']);

			if ($group && in_array(User::get('id'), $group->get('members')))
			{
				$filters['access'][] = 5; // Private
			}
		}

		$entries = Post::all()
			->whereEquals('state', $filters['state'])
			->whereIn('access', $filters['access'])
			->whereEquals('scope', $filters['scope'])
			->whereEquals('scope_id', $filters['scope_id']);

		if ($filters['thread'])
		{
			$entries->whereEquals('thread', $filters['thread']);
		}

		if ($filters['parent'])
		{
			$entries->whereEquals('parent', $filters['parent']);
		}

		if ($filters['threads'])
		{
			$entries->whereEquals('parent', 0);
		}

		if ($filters['section_id'])
		{
			// Make sure the section exists and is available
			$section = Section::oneOrFail($filters['section_id']);

			if (!$section->get('id'))
			{
				throw new Exception(Lang::txt('Section not found.'), 404);
			}

			if ($section->get('state') == Section::STATE_DELETED)
			{
				throw new Exception(Lang::txt('Section not found.'), 404);
			}

			if (!$filters['category_id'])
			{
				$categories = $section->categories()
					->whereEquals('state', $filters['state'])
					->whereIn('access', $filters['access'])
					->rows();

				$filters['category_id'] = array();

				foreach ($categories as $category)
				{
					$filters['category_id'][] = $category->get('id');
				}
			}
		}

		if ($filters['category_id'])
		{
			// If one category, make sure it exists and is available
			if (is_int($filters['category_id']))
			{
				$category = Category::oneOrFail($filters['category_id']);

				if (!$category->get('id'))
				{
					throw new Exception(Lang::txt('Category not found.'), 404);
				}

				if ($category->get('state') == Category::STATE_DELETED)
				{
					throw new Exception(Lang::txt('Category not found.'), 404);
				}
			}

			$entries->whereIn('category_id', (array)$filters['category_id']);
		}

		if ($filters['search'])
		{
			$entries->whereLike('comment', $filters['search'], 1)
				->orWhereLike('title', $filters['search'], 1)
				->resetDepth();
		}

		$threads = $entries
			->ordered()
			->paginated()
			->rows();

		$response = new stdClass;
		$response->threads = array();
		$response->total   = $threads->count();

		if ($response->total)
		{
			$base = str_replace('/api', '', rtrim(Request::base(), '/'));

			foreach ($threads as $thread)
			{
				$obj = new stdClass;
				$obj->id          = $thread->get('id');
				$obj->title       = $thread->get('title');
				$obj->created     = with(new Date($thread->get('created')))->format('Y-m-d\TH:i:s\Z');
				$obj->modified    = $thread->get('modified');
				$obj->anonymous   = $thread->get('anonymous');
				//$obj->closed      = ($thread->get('closed') ? true : false);
				$obj->scope       = $thread->get('scope');
				$obj->scope_id    = $thread->get('scope_id');
				$obj->thread      = $thread->get('thread');
				$obj->parent      = $thread->get('parent');
				$obj->category_id = $thread->get('category_id');
				$obj->state       = $thread->get('state');
				$obj->access      = $thread->get('access');

				$obj->creator = new stdClass;
				$obj->creator->id   = 0;
				$obj->creator->name = Lang::txt('Anonymous');

				if (!$thread->get('anonymous'))
				{
					$obj->creator->id   = $thread->get('created_by');
					$obj->creator->name = $thread->creator->get('name');
				}

				$obj->posts       = $thread->thread()
					->whereEquals('state', $filters['state'])
					->whereIn('access', $filters['access'])
					->total();

				$obj->url         = $base . '/' . ltrim(Route::url($thread->link()), '/');

				$response->threads[] = $obj;
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Create a thread or post in a thread
	 *
	 * @apiMethod POST
	 * @apiUri    /forum
	 * @apiParameter {
	 * 		"name":        "category_id",
	 * 		"description": "Category ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Scope type (site, group, etc.)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     "site"
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Scope object ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     "0"
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Entry title",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "comment",
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
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "access",
	 * 		"description": "Access level (1 = public, 2 = registered users, 5 = private)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "anonymous",
	 * 		"description": "Commentor is anonymous?",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "parent",
	 * 		"description": "ID of the parent post this post is in reply to.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "thread",
	 * 		"description": "ID of the forum thread the post belongs to. 0 if new thread.",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "sticky",
	 * 		"description": "If the thread is sticky or not. Only applies to thread starter posts.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "closed",
	 * 		"description": "If the thread is closed (no more new posts) or not. Only applies to thread starter posts.",
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
	public function createTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'category_id'    => Request::getInt('category_id', 0, 'post'),
			'title'          => Request::getVar('title', null, 'post', 'none', 2),
			'comment'        => Request::getVar('comment', null, 'post', 'none', 2),
			'created'        => Request::getVar('created', new Date('now'), 'post'),
			'created_by'     => Request::getInt('created_by', 0, 'post'),
			'state'          => Request::getInt('state', Post::STATE_PUBLISHED, 'post'),
			'sticky'         => Request::getInt('sticky', 0, 'post'),
			'parent'         => Request::getInt('parent', 0, 'post'),
			'scope'          => Request::getVar('scope', 'site', 'post'),
			'scope_id'       => Request::getInt('scope_id', 0, 'post'),
			'access'         => Request::getInt('access', Post::ACCESS_PUBLIC, 'post'),
			'anonymous'      => Request::getInt('anonymous', 0, 'post'),
			'thread'         => Request::getInt('thread', 0, 'post'),
			'closed'         => Request::getInt('closed', 0, 'post'),
			'hits'           => Request::getInt('hits', 0, 'post'),
		);

		if (!$fields['category_id'])
		{
			throw new Exception(Lang::txt('Category ID must be specified.'), 400);
		}

		$row = Post::blank();

		if (!$row->set($fields))
		{
			throw new Exception(Lang::txt('COM_FORUM_ERROR_BINDING_DATA'), 500);
		}

		$row->set('anonymous', ($fields['anonymous'] ? 1 : 0));

		$category = Category::all()
			->whereEquals('category_id', $row->get('category_id'))
			->whereEquals('scope', $row->get('scope'))
			->whereEquals('scope_id', $row->get('scope_id'))
			->where('state', '!=', Category::STATE_DELETED)
			->row();

		if (!$category->get('id'))
		{
			throw new Exception(Lang::txt('Specified category could not be found for the provided scope and scope_id.'), 400);
		}

		if (!$row->save())
		{
			throw new Exception(Lang::txt('COM_FORUM_ERROR_SAVING_DATA'), 500);
		}

		if ($fields['created_by'])
		{
			$row->set('created_by', (int)$fields['created_by']);
			$row->save();
		}

		if ($tags = Request::getVar('tags', null, 'post'))
		{
			if (!$row->tag($tags, User::get('id')))
			{
				throw new Exception(Lang::txt('COM_FORUM_ERROR_SAVING_TAGS'), 500);
			}
		}

		$obj = $row->toObject();

		$obj->creator = new stdClass;
		$obj->creator->id   = 0;
		$obj->creator->name = Lang::txt('Anonymous');

		if (!$row->get('anonymous'))
		{
			$obj->creator->id   = $row->get('created_by');
			$obj->creator->name = $row->creator->get('name');
		}

		$this->send($obj);
	}

	/**
	 * Retrieve a thread
	 *
	 * @apiMethod GET
	 * @apiUri    /forum/{thread}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Thread identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "section",
	 * 		"description":   "Section alias to filter by",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "Category alias to filter by",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":         "state",
	 * 		"description":   "Published state (0 = unpublished, 1 = published)",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       1
	 * }
	 * @apiParameter {
	 * 		"name":          "scope",
	 * 		"description":   "Scope (site, groups, members, etc.)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "site"
	 * }
	 * @apiParameter {
	 * 		"name":          "scope_id",
	 * 		"description":   "Scope ID",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "scope_sub_id",
	 * 		"description":   "Scope sub-ID",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "object_id",
	 * 		"description":   "Object ID",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "start_id",
	 * 		"description":   "ID of record to start with",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 *      "default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "start_at",
	 * 		"description":   "Start timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "newest",
	 * 		"allowedValues": "newest, oldest"
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$find = strtolower(Request::getWord('find', 'results'));

		$filters = array(
			'limit'        => Request::getInt('limit', Config::get('list_limit', 25)),
			'start'        => Request::getInt('limitstart', 0),
			'section'      => Request::getCmd('section', ''),
			'category'     => Request::getCmd('category', ''),
			'state'        => Request::getInt('state', Post::STATE_PUBLISHED),
			'scope'        => Request::getWord('scope', ''),
			'scope_id'     => Request::getInt('scope_id', 0),
			'scope_sub_id' => Request::getInt('scope_sub_id', 0),
			'object_id'    => Request::getInt('object_id', 0),
			'start_id'     => Request::getInt('start_id', 0),
			'start_at'     => Request::getVar('start_at', ''),
			'sticky'       => false
		);

		$forum = new Manager($filters['scope'], $filters['scope_id']);

		if ($thread = Request::getInt('thread', 0))
		{
			$filters['thread'] = $thread;
		}

		$sort = Request::getVar('sort', 'newest');
		switch ($sort)
		{
			case 'oldest':
				$filters['sort_Dir'] = 'ASC';
			break;

			case 'newest':
			default:
				$filters['sort_Dir'] = 'DESC';
			break;
		}
		$filters['sort'] = 'c.created';

		if ($filters['start_id'])
		{
			$filters['limit'] = 0;
			$filters['start'] = 0;
		}

		$data = new stdClass();
		$data->code = 0;

		if ($find == 'count')
		{
			$data->count = 0;
			$data->threads = 0;

			if (isset($filters['thread']))
			{
				$data->count = $forum->posts($filters)->total();
			}

			$post = Post::all()
				->whereEquals('object_id', $filters['object_id'])
				->whereEquals('scope_id', $filters['scope_id'])
				->whereEquals('scope', $filters['scope'])
				->row();
			if ($post->get('id'))
			{
				$filters['start_at'] = Request::getVar('threads_start', '');
				$filters['parent']   = 0;
			}
			$data->threads = $forum->posts($filters)->total();
		}
		else
		{
			$rows = $forum->posts($filters)->rows();

			if ($rows)
			{
				if ($filters['start_id'])
				{
					$filters['limit'] = Request::getInt('limit', Config::get('list_limit'));

					$children = array(
						0 => array()
					);

					$levellimit = ($filters['limit'] == 0) ? 500 : $filters['limit'];

					foreach ($rows as $v)
					{
						$v->set('created', with(new Date($v->get('created')))->format('Y-m-d\TH:i:s\Z'));

						$pt      = $v->get('parent');
						$list    = @$children[$pt] ? $children[$pt] : array();
						array_push($list, $v);
						$children[$pt] = $list;
					}

					$list = $this->treeRecurse($post->get('id'), '', array(), $children, max(0, $levellimit-1));

					$inc = false;
					$newlist = array();
					foreach ($list as $l)
					{
						if ($l->id == $filters['start_id'])
						{
							$inc = true;
						}
						else
						{
							if ($inc)
							{
								$newlist[] = $l;
							}
						}
					}

					$rows = array_slice($newlist, $filters['start'], $filters['limit']);
				}
			}
			$data->response = $rows;
		}

		$this->send($data);
	}

	/**
	 * Recursive function to build tree
	 *
	 * @param   integer  $id        Parent ID
	 * @param   string   $indent    Indent text
	 * @param   array    $list      List of records
	 * @param   array    $children  Container for parent/children mapping
	 * @param   integer  $maxlevel  Maximum levels to descend
	 * @param   integer  $level     Indention level
	 * @param   integer  $type      Indention type
	 * @return  void
	 */
	private function treeRecurse($id, $indent, $list, $children, $maxlevel=9999, $level=0, $type=1)
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->id;

				$pre    = ' treenode';
				$spacer = ' indent' . $level;

				if ($v->parent == 0)
				{
					$txt = '';
				}
				else
				{
					$txt = $pre;
				}
				$pt = $v->parent;

				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);

				$list = $this->treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type);
			}
		}
		return $list;
	}
}
