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

namespace Components\Projects\Api\Controllers;

use Components\Projects\Models\Project;
use Components\Projects\Helpers;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'project.php';
include_once \Component::path('com_publications') . DS . 'models' . DS . 'publication.php';

/**
 * API controller for the project publications
 */
class Publicationsv1_0 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('publications', 'list');
		$this->_task = Request::getCmd('task', 'list');

		// Load language files
		Lang::load('com_projects', dirname(dirname(__DIR__)) . DS . 'site');
		Lang::load('plg_projects_publications', PATH_CORE . DS . 'plugins' . DS . 'projects' . DS . 'publications');

		// Incoming
		$id = Request::getString('id', '');

		$this->model = new Project($id);

		// Project did not load?
		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		// Check authorization
		if (!$this->model->access('member'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 401);
		}

		parent::execute();
	}

	/**
	 * Get a list of project files
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "sortby",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "title",
	 * 		"allowedValues": "title, date, id, category, status"
	 * }
	 * @apiParameter {
	 * 		"name":          "sortdir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "asc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @apiParameter {
	 * 		"name":          "published",
	 * 		"description":   "Get only published datasets (1) or all including drafts (0)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "0",
	 * 		"allowedValues": "0, 1"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$response = new stdClass;

		// Instantiate a publication object
		$pub = new \Components\Publications\Models\Publication();

		// Filters for returning results
		$filters = array(
			'project'       => $this->model->get('id'),
			'limit'         => Request::getInt('limit', 25),
			'limitstart'    => Request::getInt('limitstart', 0),
			'sortby'        => Request::getWord('sortby', 'title', 'post'),
			'sortdir'       => strtoupper(Request::getWord('sortdir', 'ASC')),
			'ignore_access' => 1
		);
		$published = Request::getInt('published', '0', 'post');
		if (!$published)
		{
			$filters['dev'] = 1;
		}

		$response->publications = array();
		$response->total        = $pub->entries('count', $filters);
		$response->project      = $this->model->get('alias');

		$publications = $pub->entries( 'list', $filters );
		if (!empty($publications))
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

				$obj->thumbUrl      = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link('thumb')), '/'));
				$obj->uri           = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link('version')), '/'));

				$response->publications[] = $obj;
			}
		}

		$this->send($response);
	}
}
