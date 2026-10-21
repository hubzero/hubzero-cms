<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Api\Controllers;

use Components\Categories\Models\Category as Node;
use Components\Kb\Models\Article;
use Components\Kb\Models\Category;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Hubzero\Access\Viewlevel;
use Hubzero\Access\Asset;
use Exception;
use stdClass;
use Component;
use Request;
use Route;
use Lang;
use User;

// article.php brings com_categories' nested model with it, which the
// category writes below save through
require_once Component::path('com_kb') . '/models/article.php';
require_once Component::path('com_kb') . '/models/category.php';

/**
 * API controller for the knowledge base
 *
 * For a caller who is not a Super User this is read-only, with the
 * visibility the site applies: a published article, in a published
 * category, at an access level the caller holds. A guest therefore sees
 * public articles only, and an authenticated caller sees what they could
 * read on the site.
 *
 * A Super User may additionally ask for unpublished content, and may
 * create, change and remove both articles and the categories they sit in.
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Date format for timestamps in responses
	 *
	 * @var  string
	 */
	const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

	/**
	 * The extension knowledge base categories belong to
	 *
	 * @var  string
	 */
	const EXTENSION = 'com_kb';

	/**
	 * Fields of an article the API will set from a request
	 *
	 * Everything else -- the modification timestamps, the hit and vote
	 * counters, the checkout columns -- is the component's bookkeeping,
	 * not the caller's, and a posted value for it is ignored.
	 *
	 * @var  array
	 */
	const ARTICLE_FIELDS = array('title', 'alias', 'fulltxt', 'category', 'state', 'access', 'created', 'created_by');

	/**
	 * Request variables a JSON body may not set: they come from the route
	 *
	 * @var  array
	 */
	const ROUTE_FIELDS = array('option', 'controller', 'task', 'id', 'version', 'format', 'no_html', 'tmpl');

	/**
	 * Load the component's strings and the request body
	 *
	 * The API loader reads api/language; the site's strings are loaded as
	 * well for the messages shared with it. The JSON body is not parsed
	 * for us at all.
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load('com_kb', dirname(dirname(__DIR__)) . '/site');

		$this->payload();

		parent::execute();
	}

	/**
	 * List knowledge base articles
	 *
	 * Without a state, every caller gets what v1.0 always returned: published
	 * articles in published categories. A Super User who passes one gets
	 * articles in that state, from every category.
	 *
	 * @apiMethod GET
	 * @apiUri    /kb/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of results to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
	 * 		"description":   "Offset to start returning results from (snapped to a multiple of limit).",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "Only articles in this category (ID).",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "Only articles whose title or text contains this.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "state",
	 * 		"description":   "Only articles in this state, from published and unpublished categories alike. Super Users only; anyone else sees published articles whatever they pass. 'all' for every state.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "1",
	 * 		"allowedValues": "0, 1, 2, all"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "title",
	 * 		"allowedValues": "title, created, modified, id, hits"
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
		$state = User::authorise('core.admin') ? strtolower(Request::getString('state', '')) : '';

		if (!in_array($state, array('', '0', '1', '2', 'all'), true))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_INVALID_STATE'), 400);
		}

		$categories = $this->categories($state !== '');

		$query = Article::all()
			->whereIn('category', array_keys($categories) ?: array(0));

		if ($state !== '')
		{
			if ($state != 'all')
			{
				$query->whereEquals('state', (int) $state);
			}
		}
		else
		{
			$query->whereEquals('state', Article::STATE_PUBLISHED)
			      ->whereIn('access', User::getAuthorisedViewLevels());
		}

		if ($category = Request::getInt('category', 0))
		{
			$query->whereEquals('category', $category);
		}

		if ($search = Request::getString('search', ''))
		{
			$query->whereLike('title', $search, 1)
			      ->orWhereLike('fulltxt', $search, 1)
			      ->resetDepth();
		}

		// ordered() treats a dotted sort as relationship.field, so the
		// field is confined to the documented list
		if (!in_array(Request::getCmd('sort', 'title'), array('title', 'created', 'modified', 'id', 'hits'), true))
		{
			Request::setVar('sort', 'title', 'get');
		}

		if (!in_array(strtolower(Request::getCmd('sort_Dir', 'asc')), array('asc', 'desc'), true))
		{
			Request::setVar('sort_Dir', 'asc', 'get');
		}

		$response = new stdClass;
		$response->articles = array();
		$response->total    = $query->copy()->total();

		if ($response->total)
		{
			$rows = $query
				->ordered('sort', 'sort_Dir')
				->paginated('limitstart', 'limit')
				->rows();

			foreach ($rows as $article)
			{
				$response->articles[] = $this->format($article, $categories[$article->get('category')]);
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Get a single knowledge base article
	 *
	 * A Super User may read an unpublished one; anyone else is told it does
	 * not exist, as v1.0 always did.
	 *
	 * @apiMethod GET
	 * @apiUri    /kb/{id}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Article ID",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @return  void
	 */
	public function readTask()
	{
		$article = Article::oneOrNew(Request::getInt('id', 0));

		if (!$article->get('id'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		$category = Category::oneOrNew($article->get('category'));

		if (!User::authorise('core.admin'))
		{
			if ($article->get('state') != Article::STATE_PUBLISHED
			 || !$category->get('id')
			 || !$category->get('published'))
			{
				throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
			}

			if (!in_array($article->get('access'), User::getAuthorisedViewLevels()))
			{
				throw new Exception(Lang::txt('JERROR_ALERTNOAUTHOR'), 403);
			}
		}

		$this->send($this->format($article, $category, true));
	}

	/**
	 * Create a knowledge base article
	 *
	 * @apiMethod POST
	 * @apiUri    /kb
	 * @apiParameter {
	 * 		"name":          "title",
	 * 		"description":   "Article title.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "fulltxt",
	 * 		"description":   "Article body, as wiki markup.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "ID of the knowledge base category the article belongs to.",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "alias",
	 * 		"description":   "URL alias, unique within the category. Derived from the title when absent.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "state",
	 * 		"description":   "Published state.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       1,
	 * 		"allowedValues": "0, 1, 2"
	 * }
	 * @apiParameter {
	 * 		"name":          "access",
	 * 		"description":   "Viewing access level.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       1
	 * }
	 * @apiParameter {
	 * 		"name":          "created",
	 * 		"description":   "Creation timestamp. Defaults to now.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "created_by",
	 * 		"description":   "ID of the user the article is credited to. Defaults to the caller.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "tags",
	 * 		"description":   "Comma-separated list of tags to apply.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @return  void
	 */
	public function createTask()
	{
		$this->requiresSuperuser();

		$article = Article::blank();

		$article->set(array(
			'title'      => Request::getString('title', ''),
			'fulltxt'    => Request::getString('fulltxt', ''),
			'category'   => Request::getInt('category', 0),
			'alias'      => Request::getString('alias', ''),
			'state'      => Request::getInt('state', Article::STATE_PUBLISHED),
			'access'     => Request::getInt('access', Article::ACCESS_PUBLIC),
			'created'    => Request::getString('created', ''),
			'created_by' => Request::getInt('created_by', User::get('id'))
		));

		$this->storeArticle($article);

		$this->send($this->format($article, Category::oneOrNew($article->get('category')), true), 201);
	}

	/**
	 * Update a knowledge base article
	 *
	 * Only the fields the request carries are changed; the rest are left as
	 * they are.
	 *
	 * @apiMethod PUT
	 * @apiUri    /kb/{id}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Article ID",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "title",
	 * 		"description":   "Article title.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "fulltxt",
	 * 		"description":   "Article body, as wiki markup.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "ID of the knowledge base category the article belongs to.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "alias",
	 * 		"description":   "URL alias, unique within the category.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "state",
	 * 		"description":   "Published state.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null,
	 * 		"allowedValues": "0, 1, 2"
	 * }
	 * @apiParameter {
	 * 		"name":          "access",
	 * 		"description":   "Viewing access level.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "tags",
	 * 		"description":   "Comma-separated list of tags, replacing the article's current ones.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @return  void
	 */
	public function updateTask()
	{
		$this->requiresSuperuser();

		$article = Article::oneOrNew(Request::getInt('id', 0));

		if (!$article->get('id'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		foreach (self::ARTICLE_FIELDS as $field)
		{
			if (!$this->has($field))
			{
				continue;
			}

			$article->set($field, in_array($field, array('category', 'state', 'access', 'created_by'))
				? Request::getInt($field, 0)
				: Request::getString($field, ''));
		}

		$this->storeArticle($article);

		$this->send($this->format($article, Category::oneOrNew($article->get('category')), true));
	}

	/**
	 * Delete a knowledge base article
	 *
	 * Its comments, votes and tags go with it.
	 *
	 * @apiMethod DELETE
	 * @apiUri    /kb/{id}
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Article ID",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       null
	 * }
	 * @return  void
	 */
	public function deleteTask()
	{
		$this->requiresSuperuser();

		$article = Article::oneOrNew(Request::getInt('id', 0));

		if (!$article->get('id'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		if (!$article->destroy())
		{
			throw new Exception($article->getError() ?: Lang::txt('COM_KB_ERROR_SAVE_FAILED'), 500);
		}

		$this->send(null, 204);
	}

	/**
	 * The knowledge base categories
	 *
	 * One endpoint for the whole collection, because the component's router
	 * gives a second path segment to the task rather than to an id: there is
	 * no /kb/categories/{id} to route. The category is named by id instead,
	 * and the request method picks the action -- GET without an id lists,
	 * GET with one reads, POST creates, PUT updates, DELETE removes.
	 *
	 * @apiMethod GET, POST, PUT, DELETE
	 * @apiUri    /kb/categories
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Category ID. Required to read, update or delete one; omitted to list them or create one.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "title",
	 * 		"description":   "Category title. Required on create.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "alias",
	 * 		"description":   "URL alias, unique among the category's siblings. Derived from the title when absent.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "parent_id",
	 * 		"description":   "ID of the parent category; the root (1) makes it top-level. Passing it on an update moves the category, and its descendants with it.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       1
	 * }
	 * @apiParameter {
	 * 		"name":          "description",
	 * 		"description":   "Category description.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "note",
	 * 		"description":   "Administrative note.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @apiParameter {
	 * 		"name":          "published",
	 * 		"description":   "Published state. On a listing, the state to filter by -- Super Users only, 'all' for every state.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       1,
	 * 		"allowedValues": "0, 1, all"
	 * }
	 * @apiParameter {
	 * 		"name":          "access",
	 * 		"description":   "Viewing access level.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       1
	 * }
	 * @apiParameter {
	 * 		"name":          "parent",
	 * 		"description":   "On a listing, only the immediate children of this category (ID).",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @return  void
	 */
	public function categoriesTask()
	{
		switch (strtoupper(Request::method()))
		{
			case 'POST':
				return $this->createCategory();

			case 'PUT':
				return $this->updateCategory();

			case 'DELETE':
				return $this->deleteCategory();

			case 'GET':
			case 'HEAD':
				return Request::getInt('id', 0)
					? $this->readCategory()
					: $this->listCategories();
		}

		throw new Exception(Lang::txt('COM_KB_ERROR_METHOD_NOT_ALLOWED'), 405);
	}

	/**
	 * List the knowledge base categories
	 *
	 * @return  void
	 */
	private function listCategories()
	{
		$query = Category::all();

		if (User::authorise('core.admin'))
		{
			$published = Request::getString('published', '1');

			if (strtolower($published) != 'all')
			{
				$query->whereEquals('published', (int) $published);
			}
		}
		else
		{
			$query->whereEquals('published', 1)
			      ->whereIn('access', User::getAuthorisedViewLevels());
		}

		if ($parent = Request::getInt('parent', 0))
		{
			$query->whereEquals('parent_id', $parent);
		}

		$response = new stdClass;
		$response->categories = array();
		$response->total      = $query->copy()->total();

		if ($response->total)
		{
			$rows = $query
				->order('lft', 'asc')
				->paginated('limitstart', 'limit')
				->rows();

			foreach ($rows as $category)
			{
				$response->categories[] = $this->formatCategory($category);
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Get a single knowledge base category
	 *
	 * @return  void
	 * @throws  Exception
	 */
	private function readCategory()
	{
		$category = $this->category(Request::getInt('id', 0));

		if (!User::authorise('core.admin'))
		{
			if (!$category->get('published'))
			{
				throw new Exception(Lang::txt('COM_KB_ERROR_CATEGORY_NOT_FOUND'), 404);
			}

			if (!in_array($category->get('access'), User::getAuthorisedViewLevels()))
			{
				throw new Exception(Lang::txt('JERROR_ALERTNOAUTHOR'), 403);
			}
		}

		$this->send($this->formatCategory($category, true));
	}

	/**
	 * Create a knowledge base category
	 *
	 * Knowledge base categories are rows of #__categories with an extension
	 * of com_kb, so this goes through com_categories' own model: it is the
	 * one that keeps the nested set (lft, rgt, level), the materialised path
	 * the site routes on, and the access asset in step.
	 *
	 * @return  void
	 * @throws  Exception
	 */
	private function createCategory()
	{
		$this->requiresSuperuser();

		$node = Node::blank();

		// Asset names are built from the model's namespace, which this model
		// leaves unset; without this the row's asset is named com_.category.N
		// and never matches the com_kb.category.N the ACL checks ask for
		$node->setNameSpace(self::EXTENSION);

		$node->set(array(
			'extension'   => self::EXTENSION,
			'title'       => Request::getString('title', ''),
			'alias'       => Request::getString('alias', ''),
			'description' => Request::getString('description', ''),
			'note'        => Request::getString('note', ''),
			'published'   => Request::getInt('published', 1),
			'access'      => Request::getInt('access', 1),
			'language'    => '*',
			'params'      => '{}',
			'metadata'    => '{}',
			'metadesc'    => '',
			'metakey'     => ''
		));

		// The root of the category tree, which every extension's top-level
		// categories hang from
		$parent = $this->resolveParent(Request::getInt('parent_id', 1));

		$this->assertAliasIsFree($node);

		if (!$node->saveAsChildOf($parent))
		{
			throw new Exception($this->errors($node), 422);
		}

		if (!$node->rebuild(1))
		{
			throw new Exception($this->errors($node), 500);
		}

		$this->send($this->formatCategory(Category::oneOrFail($node->get('id')), true), 201);
	}

	/**
	 * Update a knowledge base category
	 *
	 * Only the fields the request carries are changed; the rest are left as
	 * they are.
	 *
	 * @return  void
	 * @throws  Exception
	 */
	private function updateCategory()
	{
		$this->requiresSuperuser();

		$node = Node::oneOrNew(Request::getInt('id', 0));

		if (!$node->get('id') || $node->get('extension') != self::EXTENSION)
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_CATEGORY_NOT_FOUND'), 404);
		}

		$node->setNameSpace(self::EXTENSION);

		foreach (array('title', 'alias', 'description', 'note') as $field)
		{
			if ($this->has($field))
			{
				$node->set($field, Request::getString($field, ''));
			}
		}

		// The model derives an alias only when a row is created, so a posted
		// one is normalised here the same way; an empty one falls back to
		// the title, as it would on create
		if ($this->has('alias'))
		{
			$node->set('alias', $node->automaticAlias($node->toArray()));
		}

		foreach (array('published', 'access') as $field)
		{
			if ($this->has($field))
			{
				$node->set($field, Request::getInt($field, 0));
			}
		}

		$parent = $this->resolveParent($this->has('parent_id')
			? Request::getInt('parent_id', 0)
			: $node->get('parent_id'));

		if ($parent->get('id') == $node->get('id')
		 || ($parent->get('lft') >= $node->get('lft') && $parent->get('rgt') <= $node->get('rgt')))
		{
			// Reparenting a category under itself or under one of its own
			// descendants would cut that whole subtree out of the tree
			throw new Exception(Lang::txt('COM_KB_ERROR_PARENT_IS_DESCENDANT'), 409);
		}

		$this->assertAliasIsFree($node);

		// Only a move needs repositioning: saveAsChildOf() puts the row last
		// among its siblings, which would quietly reorder the category list
		// every time one of its members was edited
		$moved = ($parent->get('id') != $node->get('parent_id'));
		$saved = $moved ? $node->saveAsChildOf($parent) : $node->save();

		if (!$saved)
		{
			throw new Exception($this->errors($node), 422);
		}

		// rebuild() walks the tree from the root, so the descendants a moved
		// category brought with it follow it, and so their stored paths pick
		// up its new alias or parent
		if (!$node->rebuild(1))
		{
			throw new Exception($this->errors($node), 500);
		}

		$this->send($this->formatCategory(Category::oneOrFail($node->get('id')), true));
	}

	/**
	 * Delete a knowledge base category
	 *
	 * Refused while the category, or anything below it, still holds
	 * articles: deleting those is a separate, deliberate act.
	 *
	 * @return  void
	 * @throws  Exception
	 */
	private function deleteCategory()
	{
		$this->requiresSuperuser();

		$node = Node::oneOrNew(Request::getInt('id', 0));

		if (!$node->get('id') || $node->get('extension') != self::EXTENSION)
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_CATEGORY_NOT_FOUND'), 404);
		}

		// Deepest first, so that each row is a leaf by the time it goes:
		// Nested::destroy() recurses into descendants itself, but closes the
		// gap they leave once per level and again for the stale copies it
		// holds, which skews lft/rgt for everything after the subtree
		$nodes = array();

		foreach ($node->getDescendants() as $descendant)
		{
			$nodes[] = $descendant;
		}

		usort($nodes, function ($a, $b)
		{
			return $b->get('level') - $a->get('level');
		});

		$nodes[] = $node;

		$ids = array();

		foreach ($nodes as $row)
		{
			$ids[] = (int) $row->get('id');
		}

		if (Article::all()->whereIn('category', $ids)->total())
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_CATEGORY_NOT_EMPTY'), 409);
		}

		foreach ($nodes as $row)
		{
			if (!Node::oneOrFail($row->get('id'))->destroy())
			{
				throw new Exception($this->errors($row), 500);
			}
		}

		// The tree is shared by every extension, so leave nothing to chance
		if (!Node::blank()->rebuild(1))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_SAVE_FAILED'), 500);
		}

		// destroy() does not take the access assets: it builds each row's asset
		// name from the model's namespace, which these rows were loaded without.
		// Sweeping them by the names createCategory() gave them leaves nothing
		// behind in #__assets.
		foreach ($ids as $id)
		{
			$asset = Asset::oneByName(self::EXTENSION . '.category.' . $id);

			if ($asset->get('id'))
			{
				$asset->destroy();
			}
		}

		$this->send(null, 204);
	}

	/**
	 * Only a Super User may create, change or remove knowledge base content
	 *
	 * The site has no authoring interface for the knowledge base at all and
	 * the admin one is behind core.manage on com_kb; writing through the API
	 * is held to the stricter of the two, so a token that happens to belong
	 * to a component manager cannot quietly rewrite the knowledge base.
	 *
	 * @return  void
	 * @throws  Exception
	 */
	private function requiresSuperuser()
	{
		$this->requiresAuthentication();

		if (!User::authorise('core.admin'))
		{
			throw new Exception(Lang::txt('JERROR_ALERTNOAUTHOR'), 403);
		}
	}

	/**
	 * Merge a JSON request body into the request's own variables
	 *
	 * A form-encoded body is parsed for us, on PUT as well as POST, but a
	 * JSON one is not -- and JSON is what a client writing articles is most
	 * likely to send. Decoding it here lets every task read its input
	 * through Request, whichever of the two encodings arrived.
	 *
	 * @return  void
	 * @throws  Exception
	 */
	private function payload()
	{
		if (!preg_match('#^application/([^+]+\+)?json#i', (string) Request::header('content-type', '')))
		{
			return;
		}

		$body = file_get_contents('php://input');

		if ($body === false || trim($body) === '')
		{
			return;
		}

		$data = json_decode($body, true);

		if (!is_array($data))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_INVALID_JSON'), 400);
		}

		foreach ($data as $key => $value)
		{
			// The id and task come from the route; a body naming another
			// article, or another task, does not get to redirect the request
			if (in_array(strtolower($key), self::ROUTE_FIELDS, true))
			{
				continue;
			}

			// 'post' is the bag Request reads from for anything that is not a
			// GET, so this reaches getString()/getInt() on POST, PUT and DELETE
			Request::setVar($key, $value, 'post');
		}
	}

	/**
	 * Whether the request carried a value for a field
	 *
	 * An update applies only the fields it was given, so "absent" and
	 * "present but empty" have to be told apart.
	 *
	 * @param   string   $key
	 * @return  boolean
	 */
	private function has($key)
	{
		return !is_null(Request::getVar($key, null));
	}

	/**
	 * Validate and save an article, and apply any tags that came with it
	 *
	 * @param   object  $article
	 * @return  void
	 * @throws  Exception
	 */
	private function storeArticle($article)
	{
		// Ahead of the checks below, so that a request missing a title or a
		// body is told that rather than being tripped up by the alias an
		// empty title derives
		if (!$article->validate())
		{
			throw new Exception(implode('. ', (array) $article->getErrors()), 422);
		}

		$category = Category::oneOrNew($article->get('category'));

		if (!$category->get('id') || $category->get('extension') != self::EXTENSION)
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_CATEGORY_NOT_FOUND'), 404);
		}

		$this->assertArticleFields($article);

		// The site looks an article up by its alias within its category, so a
		// second article sharing both is unreachable. The alias the row ends
		// up with is the model's, which falls back to the title.
		$alias = $article->automaticAlias($article->toArray());

		$clash = Article::all()
			->whereEquals('alias', $alias)
			->whereEquals('category', $article->get('category'))
			->where('id', '!=', (int) $article->get('id'))
			->total();

		if ($clash)
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ALIAS_IN_USE'), 409);
		}

		if (!$article->save())
		{
			throw new Exception(implode('. ', (array) $article->getErrors()) ?: Lang::txt('COM_KB_ERROR_SAVE_FAILED'), 422);
		}

		$tags = Request::getVar('tags', null);

		if (!is_null($tags) && is_string($tags))
		{
			$article->tag($tags, User::get('id'));
		}
	}

	/**
	 * Load a knowledge base category
	 *
	 * @param   integer  $id
	 * @return  object
	 * @throws  Exception
	 */
	private function category($id)
	{
		$category = Category::oneOrNew($id);

		if (!$category->get('id') || $category->get('extension') != self::EXTENSION)
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_CATEGORY_NOT_FOUND'), 404);
		}

		return $category;
	}

	/**
	 * Load the category a row is to hang from
	 *
	 * The root node (id 1, extension 'system') is the parent of every
	 * extension's top-level categories, so it is allowed alongside com_kb's
	 * own; nothing else is, or the knowledge base would end up with a
	 * category belonging to another component.
	 *
	 * @param   integer  $id
	 * @return  object
	 * @throws  Exception
	 */
	private function resolveParent($id)
	{
		$parent = Node::oneOrNew($id);

		if (!$parent->get('id'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_CATEGORY_NOT_FOUND'), 404);
		}

		if ($parent->get('extension') != self::EXTENSION && $parent->get('level') > 0)
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_INVALID_PARENT'), 409);
		}

		return $parent;
	}

	/**
	 * Refuse an alias already taken by another knowledge base category
	 *
	 * The site resolves a category by its alias alone, anywhere in the
	 * knowledge base, so two of them sharing one leave the second
	 * unreachable; a category of another extension never collides.
	 *
	 * @param   object  $node  The category being saved
	 * @return  void
	 * @throws  Exception
	 */
	private function assertAliasIsFree($node)
	{
		$alias = $node->automaticAlias($node->toArray());

		$clash = Node::all()
			->whereEquals('extension', self::EXTENSION)
			->whereEquals('alias', $alias)
			->where('id', '!=', (int) $node->get('id', 0))
			->total();

		if ($clash)
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ALIAS_IN_USE'), 409);
		}
	}

	/**
	 * Refuse article values the site could not show
	 *
	 * @param   object  $article
	 * @return  void
	 * @throws  Exception
	 */
	private function assertArticleFields($article)
	{
		if (!in_array((int) $article->get('state'), array(Article::STATE_UNPUBLISHED, Article::STATE_PUBLISHED, Article::STATE_DELETED), true))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_INVALID_STATE'), 422);
		}

		if (!Viewlevel::oneOrNew((int) $article->get('access'))->get('id'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_INVALID_ACCESS'), 422);
		}

		if (!User::oneOrNew((int) $article->get('created_by'))->get('id'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_INVALID_USER'), 422);
		}

		// The model fills an empty creation time in; anything else has to
		// be a date it can store
		$created = $article->get('created');

		if ($created)
		{
			try
			{
				$article->set('created', with(new Date($created))->toSql());
			}
			catch (Exception $e)
			{
				throw new Exception(Lang::txt('COM_KB_ERROR_INVALID_DATE'), 422);
			}
		}
	}

	/**
	 * A model's errors as one message
	 *
	 * @param   object  $model
	 * @return  string
	 */
	private function errors($model)
	{
		$errors = implode('. ', (array) $model->getErrors());

		return $errors ?: Lang::txt('COM_KB_ERROR_SAVE_FAILED');
	}

	/**
	 * Knowledge base categories, keyed by ID
	 *
	 * @param   boolean  $unpublished  Include unpublished categories
	 * @return  array
	 */
	private function categories($unpublished = false)
	{
		$categories = array();

		$query = Category::all();

		if (!$unpublished)
		{
			$query->whereEquals('published', 1);
		}

		foreach ($query->rows() as $category)
		{
			$categories[$category->get('id')] = $category;
		}

		return $categories;
	}

	/**
	 * The response shape for one article
	 *
	 * @param   object   $article
	 * @param   object   $category
	 * @param   boolean  $detailed  Include the fields too costly to gather per row in a listing
	 * @return  object
	 */
	private function format($article, $category, $detailed = false)
	{
		$base = rtrim(Request::base(), '/');

		$obj = new stdClass;
		$obj->id          = (int) $article->get('id');
		$obj->title       = $article->get('title');
		$obj->alias       = $article->get('alias');
		$obj->category    = new stdClass;
		$obj->category->id    = (int) $category->get('id');
		$obj->category->title = $category->get('title');
		$obj->category->alias = $category->get('alias');
		$obj->category->path  = $category->get('path');
		$obj->state       = (int) $article->get('state');
		$obj->access      = (int) $article->get('access');
		$obj->created     = $this->timestamp($article->get('created'));
		$obj->created_by  = (int) $article->get('created_by');
		$obj->modified    = $this->timestamp($article->get('modified'));
		$obj->modified_by = (int) $article->get('modified_by');
		$obj->hits        = (int) $article->get('hits');
		$obj->helpful     = (int) $article->get('helpful');
		$obj->nothelpful  = (int) $article->get('nothelpful');
		$obj->url         = str_replace('/api', '', $base . '/' . ltrim(Route::url($article->link()), '/'));
		// The article body as stored: wiki markup, not rendered HTML
		$obj->fulltxt     = $article->get('fulltxt');

		if ($detailed)
		{
			// One query per article, so a listing does without it
			$obj->tags = $article->tags('string');
		}

		return $obj;
	}

	/**
	 * The response shape for one category
	 *
	 * @param   object   $category
	 * @param   boolean  $detailed  Include the counts too costly to gather per row in a listing
	 * @return  object
	 */
	private function formatCategory($category, $detailed = false)
	{
		$base = rtrim(Request::base(), '/');

		$obj = new stdClass;
		$obj->id               = (int) $category->get('id');
		$obj->title            = $category->get('title');
		$obj->alias            = $category->get('alias');
		$obj->path             = $category->get('path');
		$obj->parent_id        = (int) $category->get('parent_id');
		$obj->level            = (int) $category->get('level');
		$obj->description      = $category->get('description');
		$obj->note             = $category->get('note');
		$obj->published        = (int) $category->get('published');
		$obj->access           = (int) $category->get('access');
		$obj->created_time     = $this->timestamp($category->get('created_time'));
		$obj->created_user_id  = (int) $category->get('created_user_id');
		$obj->modified_time    = $this->timestamp($category->get('modified_time'));
		$obj->modified_user_id = (int) $category->get('modified_user_id');
		$obj->hits             = (int) $category->get('hits');
		$obj->url              = str_replace('/api', '', $base . '/' . ltrim(Route::url($category->link()), '/'));

		if ($detailed)
		{
			// A query apiece, so a listing does without them
			$obj->articles = (int) Article::all()->whereEquals('category', $category->get('id'))->total();
			$obj->children = (int) Category::all()->whereEquals('parent_id', $category->get('id'))->total();
		}

		return $obj;
	}

	/**
	 * A stored datetime as an ISO 8601 UTC timestamp, or null when unset
	 *
	 * @param   string  $value
	 * @return  string|null
	 */
	private function timestamp($value)
	{
		if (!$value || $value == '0000-00-00 00:00:00')
		{
			return null;
		}

		return with(new Date($value))->format(self::DATE_FORMAT);
	}
}
