<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\News\Api\Controllers;

use Hubzero\Component\ApiController;
use Exception;
use stdClass;
use Request;
use Lang;
use User;
use App;

/**
 * API controller class for news
 */
class Articlesv1_0 extends ApiController
{
	/**
	 * Displays a list of articles
	 *
	 * @apiMethod GET
	 * @apiUri    /news/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "section",
	 * 		"description":   "A section to filter on.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "news"
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "A category to filter on.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "latest"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		//get the userid
		$userid = User::get('id');

		//if we dont have a user return nothing
		if ($userid == null)
		{
			throw new Exception(Lang::txt('Not Found'), 404);
		}

		//get the request vars
		$limit    = Request::getInt('limit', 5);
		$section  = Request::getString('section', 'news');
		$category = Request::getString('category', 'latest');

		//load up the news articles
		$database = App::get('db');

		$query = "SELECT c.*
					FROM `#__content` as c, `#__categories` as cat
					WHERE cat.alias='{$category}'
					AND c.catid=cat.id
					AND state=1
					ORDER BY c.ordering ASC
					LIMIT {$limit}";

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		//return the results
		$object = new stdClass();
		$object->news = $rows;

		$this->send($object);
	}
}
