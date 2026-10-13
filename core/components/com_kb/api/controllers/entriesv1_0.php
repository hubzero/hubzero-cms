<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Components\Kb\Models\Article;
use Components\Kb\Models\Category;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;
use User;

require_once \Component::path('com_kb') . '/models/article.php';

/**
 * API controller for knowledge base articles
 *
 * Read-only. Both tasks apply the visibility the site applies: a published
 * article, in a published category, at an access level the caller holds.
 * A guest therefore sees public articles only, and an authenticated
 * caller sees what they could read on the site.
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
	 * Load the component's strings: the API app does not do it for us
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load('com_kb') || Lang::load('com_kb', dirname(dirname(__DIR__)) . '/site');

		parent::execute();
	}

	/**
	 * List published knowledge base articles
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
		$categories = $this->categories();

		$query = Article::all()
			->whereEquals('state', Article::STATE_PUBLISHED)
			->whereIn('access', User::getAuthorisedViewLevels())
			->whereIn('category', array_keys($categories) ?: array(0));

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
	 * Get a single published knowledge base article
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

		if (!$article->get('id') || $article->get('state') != Article::STATE_PUBLISHED)
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		$category = Category::oneOrNew($article->get('category'));

		if (!$category->get('id') || !$category->get('published'))
		{
			throw new Exception(Lang::txt('COM_KB_ERROR_ARTICLE_NOT_FOUND'), 404);
		}

		if (!in_array($article->get('access'), User::getAuthorisedViewLevels()))
		{
			throw new Exception(Lang::txt('JERROR_ALERTNOAUTHOR'), 403);
		}

		$this->send($this->format($article, $category));
	}

	/**
	 * Published knowledge base categories, keyed by ID
	 *
	 * @return  array
	 */
	private function categories()
	{
		$categories = array();

		$rows = Category::all()
			->whereEquals('published', 1)
			->rows();

		foreach ($rows as $category)
		{
			$categories[$category->get('id')] = $category;
		}

		return $categories;
	}

	/**
	 * The response shape for one article
	 *
	 * @param   object  $article
	 * @param   object  $category
	 * @return  object
	 */
	private function format($article, $category)
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
