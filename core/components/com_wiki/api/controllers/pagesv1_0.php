<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Wiki\Api\Controllers;

use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Revision;
use Hubzero\Component\ApiController;
use stdClass;
use Request;
use Event;
use Route;
use Lang;
use User;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'book.php');

/**
 * API controller class for Wiki Pages
 */
class Pagesv1_0 extends ApiController
{
	/**
	 * Displays a list of new content
	 *
	 * @apiMethod GET
	 * @apiUri    /wiki/list
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
	 *      "default":       "title",
	 * 		"allowedValues": "created, id, title"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "asc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$group = Request::getVar('group', '');

		$book = new Book($group ? $group : '__site__');

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'sort'       => Request::getWord('sort', 'title'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'ASC')),
			'state'      => array(0, 1),
			'group'      => Request::getVar('group_cn', '')
		);

		$filters['sortby'] = $filters['sort'] . ' ' . $filters['sort_Dir'];

		$response = new stdClass;
		$response->pages = array();
		$response->total = $book->pages('count', $filters);

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($book->pages('list', $filters) as $i => $entry)
			{
				$obj = new stdClass;
				$obj->id         = $entry->get('id');
				$obj->title      = $entry->get('title');
				$obj->name       = $entry->get('name');
				$obj->scope      = $entry->get('scope');
				$obj->created    = $entry->get('created');
				$obj->created_by = $entry->get('created_by');
				$obj->uri        = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link()), '/'));
				$obj->revisions  = $entry->revisions('count');

				$response->pages[] = $obj;
			}
		}

		$this->send($response);
	}

	/**
	 * Create a page
	 *
	 * @apiMethod POST
	 * @apiUri    /wiki
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Entry title",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "pagename",
	 * 		"description": "Page name",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "pagetext",
	 * 		"description": "Page content",
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
	 * 		"name":        "scope",
	 * 		"description": "Page scope",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "group_cn",
	 * 		"description": "Group name the wiki page belongs to",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "params",
	 * 		"description": "Page options",
	 * 		"type":        "array",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();
	}

	/**
	 * Display info for a page
	 *
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Page identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "version_id",
	 * 		"description":   "Optional revision ID",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return  void
	 */
	public function readTask()
	{
		$id = Request::getInt('id', 0);

		$tag = new Page($id);

		if (!$tag->exists())
		{
			throw new Exception(Lang::txt('Specified page does not exist.'), 404);
		}

		$revision = $page->revision(Request::getInt('revision', 0));

		$response = new stdClass;
		$response->id = $page->get('id');
		$response->pagename = $page->get('pagename');
		$response->title = $page->get('title');
		$response->scope = $page->get('scope');
		$response->pagetext = $revision->content('raw');
		$response->version_id = $page->get('version_id');
		$response->created = $page->get('created');
		$response->created_by = $page->get('created_by');
		$response->group_cn = $page->get('group_cn');
		$response->state = $page->get('state');
		$response->access = $page->get('access');
		$response->revisions = $page->revisions('count');

		$response->uri = str_replace('/api', '', rtrim(Request::base(), '/') . '/' . ltrim(Route::url($page->link()), '/'));

		$this->send($tag->toObject());
	}

	/**
	 * Update a page
	 *
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Entry identifier",
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
	 * 		"name":        "pagename",
	 * 		"description": "Page name",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "pagetext",
	 * 		"description": "Page content",
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
	 * 		"name":        "scope",
	 * 		"description": "Page scope",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "group_cn",
	 * 		"description": "Group name the wiki page belongs to",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "params",
	 * 		"description": "Page options",
	 * 		"type":        "array",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "summary",
	 * 		"description": "Summary of changes made",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$fields = array(
			'title'          => Request::getVar('title', null, '', 'none', 2),
			'pagename'       => Request::getVar('pagename', null),
			'scope'          => Request::getVar('scope', null),
			'created'        => Request::getVar('created', null),
			'created_by'     => Request::getInt('created_by', null),
			'state'          => Request::getInt('state', null),
			'access'         => Request::getInt('access', null),
			'group_cn'       => Request::getVar('group_cn', null)
		);

		if (!$id)
		{
			throw new Exception(Lang::txt('COM_TAGS_ERROR_MISSING_DATA'), 422);
		}

		$page = new Page($id);

		if (!$page->exists())
		{
			throw new Exception(Lang::txt('Specified page not found.'), 404);
		}

		$revision = $page->revision('current');
		if (!$revision->bind($rev))
		{
			throw new Exception($revision->getError(), 500);
		}

		$params = new \Hubzero\Config\Registry($page->get('params', ''));
		$params->merge(Request::getVar('params', array(), 'post'));
		$page->set('params', $params->toString());

		foreach ($fields as $key => $value)
		{
			if (!is_null($value))
			{
				$page->set($key, $value);
			}
		}
		$page->set('modified', Date::toSql());

		if (!$page->store(true))
		{
			throw new Exception($page->getError(), 500);
		}

		if (!$page->updateAuthors(Request::getVar('authors', '', 'post')))
		{
			throw new Exception($page->getError(), 500);
		}

		$revision->set('pagetext', Request::getVar('pagetext', '', '', 'none', 2));
		$revision->set('summary', Request::getVar('summary', null));

		if ($revision->get('pagetext') == '')
		{
			$revision->set('id', 0);
			$revision->set('pageid',   $page->get('id'));
			$revision->set('pagename', $page->get('pagename'));
			$revision->set('scope',    $page->get('scope'));
			$revision->set('group_cn', $page->get('group_cn'));
			$revision->set('version',  $revision->get('version') + 1);

			if ($page->param('mode', 'wiki') == 'knol')
			{
				// Set revisions to NOT approved
				$revision->set('approved', 0);
				// If an author or the original page creator, set to approved
				if ($page->get('created_by') == User::get('id') || $page->isAuthor(User::get('id')))
				{
					$revision->set('approved', 1);
				}
			}
			else
			{
				// Wiki mode, approve revision
				$revision->set('approved', 1);
			}

			$revision->set('pagehtml', $revision->content('parsed'));

			if ($page->access('manage') || $page->access('edit'))
			{
				$revision->set('approved', 1);
			}

			// Store content
			if (!$revision->store(true))
			{
				throw new Exception(Lang::txt('COM_WIKI_ERROR_SAVING_REVISION'), 500);
			}

			$page->set('version_id', $revision->get('id'));

			if (!$page->store(true))
			{
				throw new Exception($page->getError(), 500);
			}
		}

		$page->tag(Request::getVar('tags', ''));

		$this->send($page->toObject());
	}

	/**
	 * Delete a page
	 *
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Page identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       0
	 * }
	 * @return  void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$id = Request::getInt('id', 0);

		$record = new Page($id);

		if (!$record->exists())
		{
			throw new Exception(Lang::txt('Specified page does not exist.'), 404);
		}

		if (!$record->delete())
		{
			throw new Exception(Lang::txt('Failed to delete page.'), 500);
		}

		$this->send(null, 202);
	}
}
