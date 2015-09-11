<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
