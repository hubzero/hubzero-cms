<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Kb\Models\Article;
use Components\Kb\Models\Category;
use stdClass;
use Request;
use App;
use Config;
use User;

/**
 * API controller class for knowledge base articles
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Get a list of published Knowledge Base Articles
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
	 * 		"description":   "Offset to start returning results from.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "Filter by category ID.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		// Get filters from request
		$filters = array(
			'limit'    => Request::getInt('limit', Config::get('list_limit')),
			'start'    => Request::getInt('limitstart', 0),
			'state'    => 1,  // Only published articles
			'access'   => 1,  // Only public access
			'category' => Request::getInt('category', null),
		);

		// Sanitize limit and start
		$filters['limit'] = \Hubzero\Utility\Sanitize::paranoid($filters['limit']);
		$filters['start'] = \Hubzero\Utility\Sanitize::paranoid($filters['start']);

		try
		{
			// Get total count of published articles
			$total_query = Article::all()
				->whereEquals('state', 1)
				->whereEquals('access', 1);

			if ($filters['category'])
			{
				$total_query->whereEquals('category', (int)$filters['category']);
			}

			$total = $total_query->total();

			// Get articles with pagination
			$articles_query = Article::all()
				->whereEquals('state', 1)
				->whereEquals('access', 1)
				->order('title', 'asc')
				->limit($filters['limit'])
				->start($filters['start']);

			if ($filters['category'])
			{
				$articles_query->whereEquals('category', (int)$filters['category']);
			}

			$articles = $articles_query->rows();

			$response_items = array();

			foreach ($articles as $article)
			{
				// Get category information
				$category = Category::oneOrNew($article->get('category'));

				$item = new stdClass();
				$item->id             = $article->get('id');
				$item->title          = $article->get('title');
				$item->alias          = $article->get('alias');
				$item->fulltxt        = $article->get('fulltxt');  // Full article content
				$item->category_id    = $article->get('category');
				$item->category_title = $category->get('title');
				$item->category_alias = $category->get('alias');
				$item->category_path  = $category->get('path');
				$item->created        = $article->get('created');
				$item->created_by     = $article->get('created_by');
				$item->modified       = $article->get('modified');
				$item->modified_by    = $article->get('modified_by');
				$item->state          = $article->get('state');
				$item->access         = $article->get('access');
				$item->helpful        = $article->get('helpful');
				$item->nothelpful     = $article->get('nothelpful');
				$item->url            = '/kb/' . $category->get('path') . '/' . $article->get('alias');

				$response_items[] = $item;
			}

			$response = new stdClass();
			$response->success = true;
			$response->total   = $total;
			$response->limit   = $filters['limit'];
			$response->start   = $filters['start'];
			$response->content = $response_items;

			$this->send($response);
		}
		catch (\Exception $e)
		{
			$response = new stdClass();
			$response->success = false;
			$response->error   = $e->getMessage();

			$this->send($response, 500);
		}
	}
}
