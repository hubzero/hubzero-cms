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

namespace Components\Blog\Api\Controllers;

use Hubzero\Component\ApiController;
use stdClass;
use Request;
use Lang;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'archive.php');

/**
 * API controller class for blog entries
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Displays a available options and parameters the API
	 * for this comonent offers.
	 *
	 * @apiMethod GET
	 * @apiUri    /blog
	 * @return  void
	 */
	public function indexTask()
	{
		$response = new stdClass();
		$response->component = 'blog';
		$response->tasks = array(
			'archive' => array(
				'description' => Lang::txt('Get a list of categories for a specific section.'),
				'parameters'  => array(
					'sort' => array(
						'description' => Lang::txt('Field to sort results by.'),
						'type'        => 'string',
						'default'     => 'created',
						'accepts'     => array('created', 'title', 'alias', 'id', 'publish_up', 'publish_down', 'state')
					),
					'sort_Dir' => array(
						'description' => Lang::txt('Direction to sort results by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
					'search' => array(
						'description' => Lang::txt('A word or phrase to search for.'),
						'type'        => 'string',
						'default'     => 'null'
					),
					'limit' => array(
						'description' => Lang::txt('Number of result to return.'),
						'type'        => 'integer',
						'default'     => '25'
					),
					'limitstart' => array(
						'description' => Lang::txt('Number of where to start returning results.'),
						'type'        => 'integer',
						'default'     => '0'
					),
				),
			),
		);

		$this->send($response);
	}

	/**
	 * Displays a list of entries
	 *
	 * @apiMethod GET
	 * @apiUri    /blog/entries
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"required":      false,
	 *      "default":       "created"
	 * 		"allowedValues": "created, title, alias, id, publish_up, publish_down, state"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"required":      false,
	 * 		"default":       ""
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return  void
	 */
	public function showTask()
	{
		$model = new \Components\Blog\Models\Archive('site');

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'sort'       => Request::getWord('sort', 'created'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'DESC'))
		);

		$response = new stdClass;
		$response->posts = array();
		$response->total = $model->entries('count', $filters);

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($model->entries('list', $filters) as $i => $entry)
			{
				$obj = new stdClass;
				$obj->id        = $entry->get('id');
				$obj->title     = $entry->get('title');
				$obj->alias     = $entry->get('alias');
				$obj->state     = $entry->get('state');
				$obj->published = $entry->get('publish_up');
				$obj->scope     = $entry->get('scope');
				$obj->author    = $entry->creator('name');
				$obj->url       = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link()), DS));
				$obj->comments  = $entry->comments('count');

				$response->posts[] = $obj;
			}
		}

		$response->success = true;

		$this->send($response);
	}
}
