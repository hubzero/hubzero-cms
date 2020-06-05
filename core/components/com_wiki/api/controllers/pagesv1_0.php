<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Api\Controllers;

use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Version;
use Components\Wiki\Models\Author;
use Hubzero\Component\ApiController;
use stdClass;
use Request;
use Event;
use Route;
use Lang;
use User;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'book.php';

/**
 * API controller class for Wiki Pages
 */
class Pagesv1_0 extends ApiController
{
	/**
	 * Display a list of pages
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
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Page scope",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "site"
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Page scope ID",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getString('search', ''),
			'sort'       => Request::getWord('sort', 'title'),
			'sort_Dir'   => strtoupper(Request::getWord('sort_Dir', 'ASC')),
			'state'      => array(Page::STATE_PUBLISHED),
			'scope'      => Request::getWord('scope', 'site'),
			'scope_id'   => Request::getInt('scope_id', 0)
		);

		$book = new Book($filters['scope'], $filters['scope_id']);

		$response = new stdClass;
		$response->pages = array();
		$response->total = $book->pages($filters)->count();

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			$pages = $book->pages($filters)
				->order($filters['sort'], $filters['sort_Dir'])
				->rows();

			foreach ($pages as $i => $entry)
			{
				$obj = $entry->toObject();
				$obj->url       = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link()), '/'));
				$obj->revisions = $entry->versions()
					->whereEquals('approved', 1)
					->count();

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
	 * 		"default":     "site"
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Page scope ID",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
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
	 * @apiMethod GET
	 * @apiUri    /wiki/{id}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Page identifier",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "version_id",
	 * 		"description":   "Optional revision ID. If none specified, page will default to most current approved revision.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return  void
	 */
	public function readTask()
	{
		$id = Request::getInt('id', 0);

		$page = Page::oneOrFail($id);

		if (!$page->get('id'))
		{
			throw new Exception(Lang::txt('COM_WIKI_ERROR_PAGE_NOT_FOUND'), 404);
		}

		$version_id = Request::getInt('revision', $page->get('version_id'));

		$page->set('version_id', $version_id);

		$version = $page->version;

		if (!$version->get('id'))
		{
			throw new Exception(Lang::txt('COM_WIKI_WARNING_NO_REVISION_FOUND', $version_id), 404);
		}

		$response = $page->toObject();
		$response->revisions = $page->versions()
			->whereEquals('approved', 1)
			->count();

		$response->version = $version->toObject();

		// Remove redundant info
		unset($response->version->page_id);
		unset($response->version_id);

		$response->url = str_replace('/api', '', rtrim(Request::base(), '/') . '/' . ltrim(Route::url($page->link()), '/'));

		$this->send($response);
	}

	/**
	 * Update a page
	 *
	 * @apiMethod PUT
	 * @apiUri    /wiki/{id}
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
	 * 		"default":     "site"
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Page scope ID",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
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
			'title'          => Request::getString('title', null, '', 'none', 2),
			'pagename'       => Request::getString('pagename', null),
			'scope'          => Request::getWord('scope', 'site'),
			'scope_id'       => Request::getInt('scope_id', 0),
			'created'        => Request::getString('created', null),
			'created_by'     => Request::getInt('created_by', null),
			'state'          => Request::getInt('state', 0),
			'access'         => Request::getInt('access', 0),
			'params'         => Request::getArray('params', array())
		);

		if (!$id)
		{
			throw new Exception(Lang::txt('COM_WIKI_ERROR_PAGE_NOT_SPECIFIED'), 422);
		}

		$page = Page::oneOrFail($id);

		if (!$page->get('id'))
		{
			throw new Exception(Lang::txt('COM_WIKI_ERROR_PAGE_NOT_FOUND'), 404);
		}

		if ($page->isLocked() && !$page->access('manage'))
		{
			throw new Exception(Lang::txt('COM_WIKI_ERROR_NOTAUTH'), 403);
		}

		$revision = $page->version;

		// Get parameters
		$params = new \Hubzero\Config\Registry($page->get('params', ''));
		$params->merge(Request::getArray('params', array(), 'post'));

		$page->set('params', $params->toString());

		// Set data
		foreach ($fields as $key => $value)
		{
			if (!is_null($value))
			{
				$page->set($key, $value);
			}
		}

		$page->set('modified', Date::toSql());

		if (!$page->save())
		{
			throw new Exception($page->getError(), 500);
		}

		// Set authors
		if (!Author::setForPage(Request::getString('authors', '', 'post'), $page->get('id')))
		{
			throw new Exception(Lang::txt('COM_WIKI_ERROR_SAVING_AUTHORS'), 500);
		}

		$old = $revision->get('pagetext');

		$revision->set('id', 0);
		$revision->set('page_id', $page->get('id'));
		$revision->set('pagetext', Request::getString('pagetext', ''));
		$revision->set('summary', Request::getString('summary', null));
		$revision->set('version', $revision->get('version') + 1);

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

		// Compare against previous revision
		// We don't want to create a whole new revision if just the tags were changed
		if (rtrim($old) != rtrim($revision->get('pagetext')))
		{
			$revision->set('pagehtml', $revision->content());

			if ($page->access('manage') || $page->access('edit'))
			{
				$revision->set('approved', 1);
			}

			if (!$revision->save())
			{
				throw new Exception(Lang::txt('COM_WIKI_ERROR_SAVING_REVISION'), 500);
			}

			$page->set('version_id', $revision->get('id'));
			$page->set('modified', $revision->get('created'));
		}

		// Store changes
		if (!$page->save())
		{
			throw new Exception($page->getError(), 500);
		}

		// Process tags
		$page->tag(Request::getString('tags', ''));

		$this->send($page->toObject());
	}

	/**
	 * Delete a page
	 *
	 * @apiMethod DELETE
	 * @apiUri    /wiki/{id}
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

		$page = Page::oneOrFail(Request::getInt('id', 0));

		if (!$page->get('id'))
		{
			throw new Exception(Lang::txt('COM_WIKI_ERROR_PAGE_NOT_FOUND'), 404);
		}

		if (!$page->access('delete'))
		{
			throw new Exception(Lang::txt('COM_WIKI_ERROR_NOTAUTH'), 403);
		}

		$page->set('state', Page::STATE_DELETED);

		if (!$page->save()) //$page->destroy()
		{
			throw new Exception(Lang::txt('COM_WIKI_UNABLE_TO_DELETE'), 500);
		}

		$this->send(null, 202);
	}
}
