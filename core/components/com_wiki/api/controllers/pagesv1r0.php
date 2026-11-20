<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Api\Controllers;

use Components\Wiki\Models\Book;
use Components\Wiki\Models\Page;
use Components\Wiki\Models\Author;
use Components\Wiki\Models\Version;
use Hubzero\Component\ApiController;
use stdClass;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Hubzero\Facades\Date;

/**
 * API controller class for Wiki Pages
 *
 */
class Pagesv1r0 extends ApiController
{
    /**
     * Display a list of pages
     *
     * @apiMethod GET
     * @apiUri    /wiki/list
     * @apiParameter {
     *      "name":          "limit",
     *      "description":   "Number of result to return.",
     *      "type":          "integer",
     *      "required":      false,
     *      "default":       25
     * }
     * @apiParameter {
     *      "name":          "start",
     *      "description":   "Number of where to start returning results.",
     *      "type":          "integer",
     *      "required":      false,
     *      "default":       0
     * }
     * @apiParameter {
     *      "name":          "search",
     *      "description":   "A word or phrase to search for.",
     *      "type":          "string",
     *      "required":      false,
     *      "default":       ""
     * }
     * @apiParameter {
     *      "name":          "sort",
     *      "description":   "Field to sort results by.",
     *      "type":          "string",
     *      "required":      false,
     *      "default":       "title",
     *      "allowedValues": "created, id, title"
     * }
     * @apiParameter {
     *      "name":          "sort_Dir",
     *      "description":   "Direction to sort results by.",
     *      "type":          "string",
     *      "required":      false,
     *      "default":       "asc",
     *      "allowedValues": "asc, desc"
     * }
     * @apiParameter {
     *      "name":        "scope",
     *      "description": "Page scope",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     "site"
     * }
     * @apiParameter {
     *      "name":        "scope_id",
     *      "description": "Page scope ID",
     *      "type":        "integer",
     *      "required":    false,
     *      "default":     0
     * }
     * @return  void
     */
    public function listTask()
    {
        $filters = [
            'limit'      => Request::getInt('limit', 25),
            'start'      => Request::getInt('limitstart', 0),
            'search'     => Request::getString('search', ''),
            'sort'       => Request::getWord('sort', 'title'),
            'sort_Dir'   => strtoupper(Request::getWord('sort_Dir', 'ASC')),
            'state'      => [Page::STATE_PUBLISHED],
            'scope'      => Request::getWord('scope', 'site'),
            'scope_id'   => Request::getInt('scope_id', 0),
            'access'     => User::getAuthorisedViewLevels(),
        ];

        $book = new Book($filters['scope'], $filters['scope_id']);

        $response = new stdClass();
        $response->pages = [];
        $response->total = $book->pages($filters)->count();

        if ($response->total) {
            $base = rtrim(Request::base(), '/');

            $pages = $book
                ->pages($filters)
                ->order($filters['sort'], $filters['sort_Dir'])
                ->rows();

            foreach ($pages as $i => $entry) {
                $obj = $entry->toObject();
                $obj->url       = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link()), '/'));
                $obj->revisions = $entry
                    ->versions()
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
     * The caller is its creator; a page name already used in the scope is a 409.
     *
     * @apiMethod POST
     * @apiUri    /wiki
     * @apiParameter {
     *      "name":        "title",
     *      "description": "Entry title",
     *      "type":        "string",
     *      "required":    true,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "pagename",
     *      "description": "Page name (letters, digits, _ and :); made from the title when absent",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "pagetext",
     *      "description": "Page content",
     *      "type":        "string",
     *      "required":    true,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "summary",
     *      "description": "Summary of the first revision",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "tags",
     *      "description": "Comma-separated list of tags",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "authors",
     *      "description": "Comma-separated usernames who may also edit the page",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "state",
     *      "description": "Published state (0 = unpublished, 1 = published); managers only",
     *      "type":        "integer",
     *      "required":    false,
     *      "default":     1
     * }
     * @apiParameter {
     *      "name":        "access",
     *      "description": "Access level (0 = public, 1 = registered users, 4 = private); managers only",
     *      "type":        "integer",
     *      "required":    false,
     *      "default":     0
     * }
     * @apiParameter {
     *      "name":        "scope",
     *      "description": "Page scope: site, or group (with scope_id)",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     "site"
     * }
     * @apiParameter {
     *      "name":        "scope_id",
     *      "description": "Group ID, for a group page",
     *      "type":        "integer",
     *      "required":    false,
     *      "default":     0
     * }
     * @apiParameter {
     *      "name":        "params",
     *      "description": "Page options",
     *      "type":        "array",
     *      "required":    false,
     *      "default":     null
     * }
     * @return    void
     */
    public function createTask()
    {
        $this->requiresAuthentication();

        $title    = trim(Request::getString('title', ''));
        $pagetext = Request::getString('pagetext', '');

        if ($title === '') {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_MISSING_TITLE'), 422);
        }
        if (trim($pagetext) === '') {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_MISSING_PAGETEXT'), 422);
        }

        // The site wiki or a group's; the site controller takes this from the
        // wiki in the URL, here it is asked for
        $scope   = preg_replace('/[^a-z_]/i', '', Request::getString('scope', 'site')) ?: 'site';
        $scopeId = $scope == 'site' ? 0 : Request::getInt('scope_id', 0);
        if (!in_array($scope, ['site', 'group'], true) || ($scope == 'group' && !$scopeId)) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_INVALID_SCOPE'), 422);
        }

        $pagename = Page::normalize(Request::getString('pagename', '') ?: $title);
        if ($pagename === '') {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_MISSING_TITLE'), 422);
        }

        if (Page::oneByPath($pagename, $scope, $scopeId)->get('id')) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_PAGE_EXIST'), 409);
        }

        $page = Page::blank();
        $page->set('scope', $scope);
        $page->set('scope_id', $scopeId);

        // As on the site: creating takes what editing the blank page takes
        if (!$page->access('edit') && !$page->access('modify')) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_NOTAUTH'), 403);
        }

        $page->set('title', $title);
        $page->set('pagename', $pagename);
        $page->set('created', Date::toSql());
        $page->set('created_by', User::get('id'));
        $page->set('modified', Date::toSql());

        // A new page starts public and published; managers may say otherwise
        $page->set('access', 0);
        $page->set('state', Page::STATE_PUBLISHED);
        if ($page->access('manage')) {
            if (!is_null(Request::getVar('access', null))) {
                $page->set('access', Request::getInt('access', 0));
            }
            if (!is_null(Request::getVar('state', null))) {
                $page->set('state', Request::getInt('state', Page::STATE_PUBLISHED));
            }
        }

        $params = new \Hubzero\Config\Registry('');
        $params->merge(Request::getArray('params', [], 'post'));
        $page->set('params', $params->toString());

        if (!$page->save()) {
            throw new \Exception($page->getError(), 500);
        }

        if (!Author::setForPage(Request::getString('authors', '', 'post'), $page->get('id'))) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_SAVING_AUTHORS'), 500);
        }

        $revision = Version::blank();
        $revision->set('page_id', $page->get('id'));
        $revision->set('version', 1);
        $revision->set('pagetext', $pagetext);
        $revision->set('summary', Request::getString('summary', ''));
        $revision->set('created', Date::toSql());
        $revision->set('created_by', User::get('id'));
        $revision->set('approved', 1);
        $revision->set('pagehtml', $revision->content());

        if (!$revision->save()) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_SAVING_REVISION'), 500);
        }

        $page->set('version_id', $revision->get('id'));
        if (!$page->save()) {
            throw new \Exception($page->getError(), 500);
        }

        $page->tag(Request::getString('tags', ''));

        $this->send($page->toObject(), 201);
    }

    /**
     * Display info for a page
     *
     * @apiMethod GET
     * @apiUri    /wiki/{id}
     * @apiParameter {
     *      "name":          "id",
     *      "description":   "Page identifier",
     *      "type":          "integer",
     *      "required":      true,
     *      "default":       0
     * }
     * @apiParameter {
     *      "name":          "version_id",
     *      "description":   [
     *          "Optional revision ID. If none specified, page will default to most",
     *          "current approved revision."
     *      ],
     *      "type":          "integer",
     *      "required":      false,
     *      "default":       0
     * }
     * @return  void
     */
    public function readTask()
    {
        $id = Request::getInt('id', 0);

        $page = Page::oneOrFail($id);

        // Deleted pages, pages above the caller's view level and pages in a
        // group wiki the caller is not admitted to read the same as on the site
        if (!$page->get('id') || $page->isDeleted() || !$page->access('view')) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_PAGE_NOT_FOUND'), 404);
        }

        $version_id = Request::getInt('revision', $page->get('version_id'));

        $page->set('version_id', $version_id);

        $version = $page->version;

        // revision= resolves any revision on the hub; it has to be one of this page's
        if (!$version->get('id') || (int) $version->get('page_id') !== (int) $page->get('id')) {
            throw new \Exception(Lang::txt('COM_WIKI_WARNING_NO_REVISION_FOUND', $version_id), 404);
        }

        $response = $page->toObject();
        $response->revisions = $page
            ->versions()
            ->whereEquals('approved', 1)
            ->count();

        $response->version = $version->toObject();

        // Remove redundant info
        unset($response->version->page_id);
        unset($response->version_id);

        $response->url = str_replace('/api', '', rtrim(Request::base(), '/'))
            . '/' . ltrim(Route::url($page->link()), '/');

        $this->send($response);
    }

    /**
     * Update a page
     *
     * @apiMethod PUT
     * @apiUri    /wiki/{id}
     * @apiParameter {
     *      "name":        "id",
     *      "description": "Entry identifier",
     *      "type":        "integer",
     *      "required":    true,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "title",
     *      "description": "Entry title",
     *      "type":        "string",
     *      "required":    true,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "pagename",
     *      "description": "Page name",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "pagetext",
     *      "description": "Page content",
     *      "type":        "string",
     *      "required":    true,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "created",
     *      "description": "Created timestamp (YYYY-MM-DD HH:mm:ss)",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     "now"
     * }
     * @apiParameter {
     *      "name":        "created_by",
     *      "description": "User ID of entry creator",
     *      "type":        "integer",
     *      "required":    false,
     *      "default":     0
     * }
     * @apiParameter {
     *      "name":        "state",
     *      "description": "Published state (0 = unpublished, 1 = published)",
     *      "type":        "integer",
     *      "required":    false,
     *      "default":     0
     * }
     * @apiParameter {
     *      "name":        "access",
     *      "description": "Access level (0 = public, 1 = registered users, 4 = private)",
     *      "type":        "integer",
     *      "required":    false,
     *      "default":     0
     * }
     * @apiParameter {
     *      "name":        "scope",
     *      "description": "Page scope",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     "site"
     * }
     * @apiParameter {
     *      "name":        "scope_id",
     *      "description": "Page scope ID",
     *      "type":        "integer",
     *      "required":    false,
     *      "default":     0
     * }
     * @apiParameter {
     *      "name":        "params",
     *      "description": "Page options",
     *      "type":        "array",
     *      "required":    false,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "summary",
     *      "description": "Summary of changes made",
     *      "type":        "string",
     *      "required":    false,
     *      "default":     null
     * }
     * @return  void
     */
    public function updateTask()
    {
        $this->requiresAuthentication();

        // $id was never assigned here, so the `if (!$id)` below threw 422 on
        // every call and the whole task -- including its authorization -- was
        // unreachable. readTask reads the same parameter.
        $id = Request::getInt('id', 0);

        // Every default here is null, so the writer loop below (which skips
        // is_null) leaves a field the caller did not send alone. With the
        // non-null defaults this had -- 'site', 0, 0, 0 -- a manager who posted
        // only id and pagetext silently moved the page out of its group into the
        // site wiki, published it and made it public, because modify() writes
        // every attribute that is a real column. That was latent until this task
        // became reachable; it is reachable now.
        // getWord() runs its filter over `$result ? $result : ''`, so an absent
        // parameter comes back as '' rather than the null default -- and the
        // writer loop below, which skips only is_null, would then write that
        // empty scope. Read the raw var and filter it only when one was sent.
        $scope = Request::getVar('scope', null);
        $scope = (is_string($scope) && $scope !== '') ? preg_replace('/[^A-Z_]/i', '', $scope) : null;

        $fields = [
            'title'          => Request::getString('title', null),
            'pagename'       => Request::getString('pagename', null),
            'scope'          => $scope,
            'scope_id'       => Request::getInt('scope_id', null),
            'created'        => Request::getString('created', null),
            'created_by'     => Request::getInt('created_by', null),
            'state'          => Request::getInt('state', null),
            'access'         => Request::getInt('access', null),
        ];

        if (!$id) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_PAGE_NOT_SPECIFIED'), 422);
        }

        $page = Page::oneOrFail($id);

        if (!$page->get('id')) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_PAGE_NOT_FOUND'), 404);
        }

        if ($page->isLocked() && !$page->access('manage')) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_NOTAUTH'), 403);
        }

        // Editing a page requires edit or manage access
        if (!$page->access('edit') && !$page->access('manage')) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_NOTAUTH'), 403);
        }

        // Only managers may reassign ownership, state, access or scope
        if (!$page->access('manage')) {
            unset($fields['created_by'], $fields['state'], $fields['access'], $fields['scope'], $fields['scope_id']);
        }

        $revision = $page->version;

        // Get parameters
        $params = new \Hubzero\Config\Registry($page->get('params', ''));
        $params->merge(Request::getArray('params', [], 'post'));

        $page->set('params', $params->toString());

        // Set data
        foreach ($fields as $key => $value) {
            if (!is_null($value)) {
                $page->set($key, $value);
            }
        }

        $page->set('modified', Date::toSql());

        if (!$page->save()) {
            throw new \Exception($page->getError(), 500);
        }

        // Set authors
        if (!Author::setForPage(Request::getString('authors', '', 'post'), $page->get('id'))) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_SAVING_AUTHORS'), 500);
        }

        $old = $revision->get('pagetext');

        $revision->set('id', 0);
        $revision->set('page_id', $page->get('id'));
        $revision->set('pagetext', Request::getString('pagetext', ''));
        $revision->set('summary', Request::getString('summary', null));
        $revision->set('version', $revision->get('version') + 1);

        if ($page->param('mode', 'wiki') == 'knol') {
            // Set revisions to NOT approved
            $revision->set('approved', 0);

            // If an author or the original page creator, set to approved
            if ($page->get('created_by') == User::get('id') || $page->isAuthor(User::get('id'))) {
                $revision->set('approved', 1);
            }
        } else {
            // Wiki mode, approve revision
            $revision->set('approved', 1);
        }

        // Compare against previous revision
        // We don't want to create a whole new revision if just the tags were changed
        if (rtrim($old) != rtrim($revision->get('pagetext'))) {
            $revision->set('pagehtml', $revision->content());

            if ($page->access('manage') || $page->access('edit')) {
                $revision->set('approved', 1);
            }

            if (!$revision->save()) {
                throw new \Exception(Lang::txt('COM_WIKI_ERROR_SAVING_REVISION'), 500);
            }

            $page->set('version_id', $revision->get('id'));
            $page->set('modified', $revision->get('created'));
        }

        // Store changes
        if (!$page->save()) {
            throw new \Exception($page->getError(), 500);
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
     *      "name":          "id",
     *      "description":   "Page identifier",
     *      "type":          "integer",
     *      "required":      true,
     *      "default":       0
     * }
     * @return  void
     */
    public function deleteTask()
    {
        $this->requiresAuthentication();

        $page = Page::oneOrFail(Request::getInt('id', 0));

        if (!$page->get('id')) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_PAGE_NOT_FOUND'), 404);
        }

        if (!$page->access('delete')) {
            throw new \Exception(Lang::txt('COM_WIKI_ERROR_NOTAUTH'), 403);
        }

        $page->set('state', Page::STATE_DELETED);

        if (!$page->save()) { //$page->destroy()
            throw new \Exception(Lang::txt('COM_WIKI_UNABLE_TO_DELETE'), 500);
        }

        $this->send(null, 202);
    }
}
