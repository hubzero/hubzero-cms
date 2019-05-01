<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
