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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Api\Controllers;

use Components\Publications\Models\Publication;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Config;
use Route;
use Lang;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'publication.php');

/**
 * API controller for the publications component
 */
class Publicationsv1_1 extends ApiController
{
	/**
	 * Display publications user is listed as author
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
			'limit'      => Request::getInt('limit', Config::get('list_limit')),
			'start'      => Request::getInt('start', 0),
			'sortby'     => Request::getWord('sort', 'title'),
			'sortdir'    => strtoupper(Request::getWord('sort_Dir', 'ASC')),
			'author'     => User::get('id')
		);

		$response = new stdClass;
		$response->publications = array();

		$database = \App::get('db');
		$pa = new \Components\Publications\Tables\Author($database);

		if (User::authorise('core.manage', 'com_publications'))
		{
			// Administrators can see all publications in any state
			$filters['dev'] = 1;
			unset($filters['author']);

			$publications = $model->entries('list', $filters, true);
			$response->total = $model->entries('count', $filters, true);
			$searchable = Request::getVar('searchable', false);
		}
		else
		{
			$publications = $model->entries('list', $filters);
			$response->total = $model->entries('count', $filters);
		}


		if ($response->total && !isset($searchable))
		{
			$base = rtrim(Request::base(), '/');

			foreach ($publications as $i => $entry)
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

				$obj->authors       = $pa->getAuthors($entry->get('version_id'));

				$obj->thumbUrl      = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link('thumb')), '/'));
				$obj->uri           = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link('version')), '/'));
				$obj->manageUri     = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link('editversion')), '/'));
				$obj->project       = $entry->project()->get('alias');

				$response->publications[] = $obj;
			}
		}
		elseif (isset($searchable))
		{
			foreach ($publications as $i => $entry)
			{
				$obj = new stdClass;
				$obj->id            = 'publication-' . $entry->get('id');
				$obj->title         = $entry->get('title');
				$obj->description   = $entry->get('abstract');
				$obj->url           = $entry->link('version');
				$obj->doi           = $entry->get('doi');
				$statusName         = $entry->getStatusName();
				$obj->status        = $statusName;

				$authors       = $pa->getAuthors($entry->get('version_id'));

				foreach ($authors as $author)
				{
					$obj->authors[] = $author->name;
				}

				$obj->owner_type = 'user';
				$obj->owner = $entry->creator('id');

				if ($statusName != 'published')
				{
					$obj->access_level = 'private';
				}
				elseif ($statusName == 'published')
				{
					if ($entry->access == 0)
					{
						$obj->access_level = 'public';
					}
					elseif ($entry->access == 1)
					{
						$obj->access_level = 'registered';
					}
					else
					{
						$obj->access_level = 'private';
					}
				}

				$response->publications[] = $obj;
			}
		}

		$this->send($response);
	}
}
