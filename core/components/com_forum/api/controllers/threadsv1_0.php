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
	 * Execute a request
	 *
	 * @return    void
	 */
	public function execute()
	{
		$this->config   = Component::params('com_forum');
		$this->database = \App::get('db');

		parent::execute();
	}

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
			'state'      => 1
		);

		$model = new \Components\Forum\Models\Manager($filters['scope'], $filters['scope_id']);

		$response = new stdClass;
		$response->sections = array();

		$response->total = $model->sections('count', array('state' => $filters['state']));

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($model->sections('list', array('state' => $filters['state'])) as $section)
			{
				$obj = new stdClass;
				$obj->id         = $section->get('id');
				$obj->title      = $section->get('title');
				$obj->alias      = $section->get('alias');
				$obj->created    = $section->get('created');
				$obj->scope      = $section->get('scope');
				$obj->scope_id   = $section->get('scope_id');
				$obj->categories = $section->count('categories');
				$obj->threads    = $section->count('threads');
				$obj->posts      = $section->count('posts');

				$obj->url        = str_replace('/api', '', $base . '/' . ltrim(Route::url($section->link()), '/'));

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
	 * 		"description":   "Section alias",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *      "default":       ""
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
	 * @return    void
	 */
	public function categoriesTask()
	{
		$filters = array(
			'authorized' => 1,
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'section'    => Request::getVar('section', ''),
			'search'     => Request::getVar('search', ''),
			'scope'      => Request::getWord('scope', 'site'),
			'scope_id'   => Request::getInt('scope_id', 0),
			'state'      => 1,
			'parent'     => 0
		);

		$model = new \Components\Forum\Models\Manager($filters['scope'], $filters['scope_id']);

		$section = $model->section($filters['section'], $model->get('scope'), $model->get('scope_id'));
		if (!$section->exists())
		{
			throw new Exception(Lang::txt('Section not found.'), 404);
		}

		$response = new stdClass;

		$response->section = new stdClass;
		$response->section->id         = $section->get('id');
		$response->section->title      = $section->get('title');
		$response->section->alias      = $section->get('alias');
		$response->section->created    = $section->get('created');
		$response->section->scope      = $section->get('scope');
		$response->section->scope_id   = $section->get('scope_id');

		$response->categories = array();
		$response->total = $section->categories('count', array('state' => 1));

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($section->categories('list', array('state' => 1)) as $category)
			{
				$obj = new stdClass;
				$obj->id          = $category->get('id');
				$obj->title       = $category->get('title');
				$obj->alias       = $category->get('alias');
				$obj->description = $category->get('description');
				$obj->created     = $category->get('created');
				$obj->scope       = $category->get('scope');
				$obj->scope_id    = $category->get('scope_id');
				$obj->threads     = $category->count('threads');
				$obj->posts       = $category->count('posts');

				$category->set('section_alias', $section->get('alias'));

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
			'authorized' => 1,
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'section'    => Request::getVar('section', ''),
			'category'   => Request::getVar('category', ''),
			'search'     => Request::getVar('search', ''),
			'scope'      => Request::getWord('scope', 'site'),
			'scope_id'   => Request::getInt('scope_id', 0),
			'state'      => 1,
			'parent'     => 0
		);

		$model = new \Components\Forum\Models\Manager($filters['scope'], $filters['scope_id']);

		$section = $model->section($filters['section'], $model->get('scope'), $model->get('scope_id'));
		if (!$section->exists())
		{
			throw new Exception(Lang::txt('Section not found.'), 404);
		}

		$category = $section->category($filters['category']);
		if (!$category->exists())
		{
			throw new Exception(Lang::txt('Category not found.'), 404);
		}

		$response = new stdClass;

		$response->section = new stdClass;
		$response->section->id         = $section->get('id');
		$response->section->title      = $section->get('title');
		$response->section->alias      = $section->get('alias');
		$response->section->created    = $section->get('created');
		$response->section->scope      = $section->get('scope');
		$response->section->scope_id   = $section->get('scope_id');

		$response->category = new stdClass;
		$response->category->id          = $category->get('id');
		$response->category->title       = $category->get('title');
		$response->category->alias       = $category->get('alias');
		$response->category->description = $category->get('description');
		$response->category->created     = $category->get('created');
		$response->category->scope       = $category->get('scope');
		$response->category->scope_id    = $category->get('scope_id');

		$response->threads = array();
		$response->total = $category->threads('count', array('state' => 1));

		if ($response->total)
		{
			$base = str_replace('/api', '', rtrim(Request::base(), '/'));

			foreach ($category->threads('list', array('state' => 1)) as $thread)
			{
				$obj = new stdClass;
				$obj->id          = $thread->get('id');
				$obj->title       = $thread->get('title');
				//$obj->description = $category->get('description');
				$obj->created     = $thread->get('created');
				$obj->modified    = $thread->get('modified');
				$obj->anonymous   = ($thread->get('anonymous') ? true : false);
				$obj->closed      = ($thread->get('closed') ? true : false);
				$obj->scope       = $thread->get('scope');
				$obj->scope_id    = $thread->get('scope_id');

				$obj->creator = new stdClass;
				$obj->creator->id = $thread->get('created_by');
				$obj->creator->name = $thread->creator('name');

				$obj->posts       = $thread->posts('count');

				$category->set('section_alias', $section->get('alias'));
				$thread->set('section', $section->get('alias'));
				$thread->set('category', $category->get('alias'));

				$obj->url         = $base . '/' . ltrim(Route::url($thread->link()), '/');

				$response->threads[] = $obj;
			}
		}

		$response->success = true;

		$this->send($response);
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
			'state'        => Request::getInt('state', 1),
			'scope'        => Request::getWord('scope', ''),
			'scope_id'     => Request::getInt('scope_id', 0),
			'scope_sub_id' => Request::getInt('scope_sub_id', 0),
			'object_id'    => Request::getInt('object_id', 0),
			'start_id'     => Request::getInt('start_id', 0),
			'start_at'     => Request::getVar('start_at', ''),
			'sticky'       => false
		);
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

		$post = new \Components\Forum\Tables\Post($this->database);

		$data = new stdClass();
		$data->code = 0;

		if ($find == 'count')
		{
			$data->count = 0;
			$data->threads = 0;

			if (isset($filters['thread']))
			{
				$data->count = $post->countTree($filters['thread'], $filters);
			}

			$post->loadByObject($filters['object_id'], $filters['scope_id'], $filters['scope']);
			if ($post->id)
			{
				$filters['start_at'] = Request::getVar('threads_start', '');
				$filters['parent']   = 0;
			}
			$data->threads = $post->count($filters);
		}
		else
		{
			$rows = $post->find($filters);
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
						$pt      = $v->parent;
						$list    = @$children[$pt] ? $children[$pt] : array();
						array_push($list, $v);
						$children[$pt] = $list;
					}

					$list = $this->_treeRecurse($view->post->get('id'), '', array(), $children, max(0, $levellimit-1));

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
	private function _treeRecurse($id, $indent, $list, $children, $maxlevel=9999, $level=0, $type=1)
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

				$list = $this->_treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type);
			}
		}
		return $list;
	}
}
