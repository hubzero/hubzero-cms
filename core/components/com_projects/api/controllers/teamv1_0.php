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

/**
 * API controller for the project team
 */
class Teamv1_0 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('team', 'list');
		$this->_task = Request::getWord('task', 'list');

		// Load component language file
		Lang::load('com_projects') || Lang::load('com_projects', dirname(dirname(__DIR__)) . DS . 'site');

		// Incoming
		$id = Request::getString('id', '');

		$this->model = new Project($id);

		// Project did not load?
		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		// Check authorization
		if (!$this->model->access('member') && !$this->model->isPublic())
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 401);
		}

		parent::execute();
	}

	/**
	 * Get a list of project team members
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/team
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
	 * 		"allowedValues": "title, created, alias"
	 * }
	 * @apiParameter {
	 * 		"name":          "sortdir",
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
		$response = new stdClass;
		$filters = array(
			'limit'   => Request::getInt('limit', 0, 'post'),
			'start'   => Request::getInt('limitstart', 0, 'post'),
			'sortby'  => Request::getString( 'sortby', 'name', 'post'),
			'sortdir' => Request::getString( 'sortdir', 'ASC', 'post'),
			'status'  => 'active'
		);
		$response->count   = count($this->model->team());
		$response->team    = array();
		$response->project = $this->model->get('alias');

		$team = $this->model->team($filters, true);
		if (!empty($team))
		{
			foreach ($team as $i => $entry)
			{
				$obj = new stdClass;
				$obj->ownerId     = $entry->id;
				$obj->userId      = $entry->userid;
				$obj->name        = $entry->fullname;
				$obj->joined      = $entry->added;
				$obj->owner       = $entry->userid == $this->model->get('owned_by_user') ? 1 : 0;
				$obj->manager     = $entry->role == 1 ? 1 : 0;
				$obj->editor      = $entry->role == 5 ? 0 : 1;
				$obj->lastVisit   = $entry->lastvisit;
				$obj->group       = $entry->groupname;
				$response->team[] = $obj;
			}
		}

		$this->send($response);
	}
}
