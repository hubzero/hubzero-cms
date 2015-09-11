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

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'project.php');

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
		Lang::load('com_projects') || Lang::load('com_projects', PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'site');

		// Incoming
		$id = Request::getVar('id', '');

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
			'sortby'  => Request::getVar( 'sortby', 'name', 'post'),
			'sortdir' => Request::getVar( 'sortdir', 'ASC', 'post'),
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
