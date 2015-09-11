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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Api\Controllers;

use Components\Publications\Models\Publication;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'publication.php');

/**
 * API controller for the publications component
 */
class Publicationsv1_0 extends ApiController
{
	/**
	 * Display publications user authors
	 *
	 * @apiMethod GET
	 * @apiUri    /publications/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "title",
	 * 		"allowedValues": "title, created, alias"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$model = new Publication();

		Lang::load('plg_projects_publications', PATH_CORE . DS . 'plugins' . DS . 'projects' . DS . 'publications');

		// Set filters
		$filters = array(
			'limit'      => Request::getInt('limit', 0, 'post'),
			'start'      => Request::getInt('start', 0, 'post'),
			'sortby'     => Request::getWord('sort', 'title', 'post'),
			'sortdir'    => strtoupper(Request::getWord('sort_Dir', 'ASC')),
			'author'     => User::get('id')
		);

		$response = new stdClass;
		$response->publications = array();
		$response->total = $model->entries('count', $filters);

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($model->entries('list', $filters) as $i => $entry)
			{
				$obj = new stdClass;
				$obj->id            = $entry->get('id');
				$obj->alias         = $entry->get('alias');
				$obj->title         = $entry->get('title');
				$obj->abstract      = $entry->get('abstract');
				$obj->creator       = $entry->creator('name');
				$obj->created       = $entry->get('created');
				$obj->published     = $entry->published('date');
				$obj->masterType    = $entry->masterType()->type;
				$obj->category      = $entry->category()->name;
				$obj->version       = $entry->get('version_number');
				$obj->versionLabel  = $entry->get('version_label');
				$obj->status        = $entry->get('state');
				$obj->statusName    = $entry->getStatusName();

				$obj->thumbUrl      = str_replace('/api', '', $base . '/'
									. ltrim(Route::url($entry->link('thumb')), '/'));
				$obj->uri           = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link('version')), '/'));
				$obj->manageUri     = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link('editversion')), '/'));
				$obj->project       = $entry->project()->get('alias');

				$response->publications[] = $obj;
			}
		}

		$this->send($response);
	}
}
