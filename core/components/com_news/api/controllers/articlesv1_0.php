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
		$limit    = Request::getVar('limit', 5);
		$section  = Request::getVar('section', 'news');
		$category = Request::getVar('category', 'latest');

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
