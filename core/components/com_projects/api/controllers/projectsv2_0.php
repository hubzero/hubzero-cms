<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Api\Controllers;

use Components\Projects\Models\Orm\Project;
use Components\Projects\Models\Orm\Owner;
use Components\Projects\Models\Repo;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use Request;
use stdClass;
use Route;
use Lang;

require_once dirname(dirname(__DIR__)) . '/models/orm/project.php';

/**
 * API controller for the projects component
 */
class Projectsv2_0 extends ApiController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('get', 'read');

		parent::execute();
	}

	/**
	 * List projects
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/list
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
	 * 		"default":       "asc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @apiParameter {
	 * 		"name":          "verbose",
	 * 		"description":   "Receive verbose output for project status, team member role and privacy.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       "0",
	 * 		"allowedValues": "0, 1"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		// Incoming
		$verbose = Request::getInt('verbose', 0);

		// Set filters
		$filters = array(
			'limit'      => Request::getInt('limit', 20),
			'start'      => Request::getInt('start', 0),
			'sortby'     => Request::getWord('sort', 'title'),
			'sortdir'    => strtoupper(Request::getWord('sort_Dir', 'ASC')),
			'getowner'   => 1,
			'updates'    => 1,
			'mine'       => 1
		);

		$admin = false;
		if (User::authorise('core.admin', 'com_projects'))
		{
			$searchable = Request::getBool('searchable', false);
			unset($filters['mine']);
			$admin = true;
		}

		$query = Project::all();

		if (!$admin)
		{
			$query->whereEquals('state', Project::STATE_PUBLISHED);
			//$query->whereIn('private', [Project::PRIVACY_PUBLIC, Project::PRIVACY_OPEN]);
			$query->whereIn('access', User::getAuthorisedViewLevels());
		}

		$response = new stdClass;
		$response->projects = array();

		$total = clone $query;
		$response->total = $total->deselect()->select('*', null, true)->row()->get('COUNT(*)');

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			$query->order($filters['sortby'], $filters['sortdir']);
			$query->limit($filters['limit']);
			$query->start($filters['start']);

			foreach ($query->rows() as $i => $entry)
			{
				if (isset($searchable))
				{
					$obj = new stdClass;
					$obj->id          = 'project-' . $entry->get('id');
					$obj->hubtype     = 'project';
					$obj->title       = $entry->get('title');
					$obj->description = $entry->get('about');
					$obj->url         = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link()), '/'));

					$obj->owner_type = 'user';

					foreach ($entry->team()->rows() as $member)
					{
						$obj->owner[] = $member->userid;
					}

					if (!$entry->isPrivate() && $entry->isProvisioned())
					{
						$obj->access_level = 'public';
					}
					else
					{
						$obj->access_level = 'private';
					}
				}
				else
				{
					$obj = new stdClass;
					$obj->id            = $entry->get('id');
					$obj->alias         = $entry->get('alias');
					$obj->title         = $entry->get('title');
					$obj->description   = $entry->get('about');
					$obj->state         = $entry->get('state');
					$obj->inSetup       = $entry->inSetup();
					$obj->owner         = $entry->owner()->get('name');
					$obj->created       = $entry->get('created');
					//$obj->userRole      = $entry->member()->role;
					$obj->thumbUrl      = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link('thumb')), '/'));
					$obj->privacy       = $entry->get('private');
					$obj->access        = $entry->get('access');
					$obj->provisioned   = $entry->isProvisioned();
					$obj->groupOwnerId  = $entry->get('owned_by_group');
					$obj->userOwnerId   = $entry->get('owned_by_user');
					$obj->uri           = str_replace('/api', '', $base . '/' . ltrim(Route::url($entry->link()), '/'));

					// Explain what status/role means
					if ($verbose)
					{
						// Project status
						switch ($entry->get('state'))
						{
							case 0:
								$obj->state = $entry->inSetup() ? Lang::txt('setup') : Lang::txt('suspended');
								break;

							case 1:
							default:
								$obj->state = Lang::txt('active');
								break;

							case 2:
								$obj->state = Lang::txt('deleted');
								break;

							case 5:
								$obj->state = Lang::txt('pending approval');
								break;
						}

						// Privacy
						$obj->privacy = $entry->isPrivate() ? Lang::txt('private') : Lang::txt('public');

						// Team role
						switch ($obj->userRole)
						{
							case 0:
							default:
								$obj->userRole = Lang::txt('collaborator');
								break;
							case 1:
								$obj->userRole = Lang::txt('manager');
								break;
							case 2:
								$obj->userRole = Lang::txt('author');
								break;
							case 5:
								$obj->userRole = Lang::txt('reviewer');
								break;
						}
					}
				}

				$response->projects[] = $obj;
			}
		}

		$this->send($response);
	}

	/**
	 * Create a project
	 *
	 * @apiMethod POST
	 * @apiUri    /projects
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Project title",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "alias",
	 * 		"description": "Project alias",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "about",
	 * 		"description": "Blurb about the project.",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "owned_by_user",
	 * 		"description": "User ID of entry owner. Defaults to entry creator if not specified.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "owned_by_group",
	 * 		"description": "Group ID of entry owner.Specifies if a project is owned by a group.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "state",
	 * 		"description": "Published state (0 = unpublished, 1 = published)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "private",
	 * 		"description": "Private (1) project or publicly disoverable (0)?",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "sync_group",
	 * 		"description": "Sync group membership to projects? (only applies if owned_by_group is set.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "agree",
	 * 		"description": "Agree to terms & conditions",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "restricted",
	 * 		"description": "Project contains restricted data?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "hipaa",
	 * 		"description": "Project contains HIPAA data?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "no",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "ferpa",
	 * 		"description": "Project contains FERPA data?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "no",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "agree_ferpa",
	 * 		"description": "Agree to terms & conditions for FERPA data. Required if 'ferpa'='yes'.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "irb",
	 * 		"description": "Project contains IRB data?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "no",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "agree_irb",
	 * 		"description": "Agree to terms & conditions for IRB data. Required if 'irb'='yes'.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "export",
	 * 		"description": "Project data can be exported?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "no",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "grant_title",
	 * 		"description": "Grant title",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "grant_agency",
	 * 		"description": "Grant agency",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "grant_PI",
	 * 		"description": "Grant PI",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "grant_budget",
	 * 		"description": "Grant budget",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$row = Project::blank();

		$fields = array(
			'title'           => Request::getString('title', '', 'post'),
			'alias'           => Request::getString('alias', '', 'post'),
			'about'           => Request::getString('about', '', 'post'),
			'created'         => with(new Date('now'))->toSql(),
			'owned_by_user'   => Request::getInt('owned_by_user', User::get('id'), 'post'),
			'created_by_user' => User::get('id'), //Request::getInt('created_by', User::get('id'), 'post'),
			'state'           => Request::getInt('state', Project::STATE_PUBLISHED, 'post'),
			'type'            => 1,
			'provisioned'     => 0,
			'private'         => Request::getInt('private', $row->config('privacy', 1), 'post'),
			//'access'          => Request::getInt('access', $row->config('access', 5), 'post'),
			'owned_by_group'  => Request::getInt('owned_by_group', 0, 'post')
		);

		$fields['access'] = Project::PRIVACY_PUBLIC;
		if ($fields['private'])
		{
			$fields['access'] = Project::PRIVACY_PRIVATE;
		}

		if (!$row->access('create'))
		{
			throw new Exception(Lang::txt('COM_PROJECTS_SETUP_ERROR_NOT_FROM_CREATOR_GROUP'), 403);
		}

		if (!$row->set($fields))
		{
			throw new Exception(Lang::txt('COM_PROJECTS_ERROR_BINDING_DATA'), 500);
		}

		$exists = Project::all()
			->whereEquals('alias', $row->get('alias'))
			->row();

		if ($exists && !$exists->isNew())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_ERROR_ALIAS_TAKEN'), 409);
		}

		$row->set('title', \Hubzero\Utility\Str::truncate($row->get('title'), 250));

		if ($row->get('owned_by_user'))
		{
			$owner = User::getInstance($row->get('owned_by_user'));

			if (!$owner || !$owner->get('id'))
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ERROR_OWNER_NOT_FOUND'), 409);
			}
		}

		if ($row->get('owned_by_group'))
		{
			$group = $row->group;

			if (!$group || !$group->get('gidNumber'))
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ERROR_GROUP_NOT_FOUND'), 409);
			}

			$row->set('sync_group', Request::getInt('sync_group', 0, 'post'));
		}

		// General restricted data question
		$restricted  = Request::getString('restricted', '', 'post');
		$agree       = Request::getInt('agree', 0, 'post');
		$agree_irb   = Request::getInt('agree_irb', 0, 'post');
		$agree_ferpa = Request::getInt('agree_ferpa', 0, 'post');

		if ($row->config('restricted_data', 0) == 2)
		{
			if (!$restricted)
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA'), 409);
			}

			// Save params
			$row->param->set('restricted_data', $restricted);
		}

		// Restricted data with specific questions
		if ($row->config('restricted_data', 0) == 1)
		{
			$restrictions = array(
				'hipaa_data'  => Request::getString('hipaa', 'no', 'post'),
				'ferpa_data'  => Request::getString('ferpa', 'no', 'post'),
				'export_data' => Request::getString('export', 'no', 'post'),
				'irb_data'    => Request::getString('irb', 'no', 'post')
			);

			// Save individual restrictions
			foreach ($restrictions as $key => $value)
			{
				$row->params->set($key, $value);
			}

			// No selections?
			if (empty($restricted))
			{
				foreach ($restrictions as $key => $value)
				{
					if ($value == 'yes')
					{
						$restricted = 'yes';
						break;
					}
				}

				if ($restricted != 'yes')
				{
					throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_HIPAA'), 409);
				}
			}

			// Handle restricted data choice, save params
			$row->params->set('restricted_data', $restricted);

			if ($restricted == 'yes')
			{
				// Check selections
				$selected = 0;
				foreach ($restrictions as $key => $value)
				{
					if ($value == 'yes')
					{
						$selected++;
					}
				}
				// Make sure user made selections
				if ($selected == 0)
				{
					throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_SPECIFY_DATA'), 409);
				}

				// Check for required confirmations
				if (($restrictions['ferpa_data'] == 'yes' && !$agree_ferpa)
				 || ($restrictions['irb_data'] == 'yes' && !$agree_irb))
				{
					throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA_AGREE_REQUIRED'), 409);
				}

				// Stop if hipaa/export controlled, or send to extra approval screen
				if ($row->config('approve_restricted', 0))
				{
					if ($restrictions['export_data'] == 'yes'
					 || $restrictions['hipaa_data'] == 'yes'
					 || $restrictions['ferpa_data'] == 'yes')
					{
						// pending approval
						$row->set('state', Project::STATE_PENDING);
					}
				}
			}
			elseif ($restricted == 'maybe')
			{
				$row->params->set('followup', 'yes');
			}
		}

		// Check to make sure user has agreed to terms
		if ($agree == 0)
		{
			throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS'), 409);
		}

		// Collect grant information
		if ($row->config('grantinfo', 0))
		{
			$row->params->set('grant_budget', Request::getString('grant_budget', ''));
			$row->params->set('grant_agency', Request::getString('grant_agency', ''));
			$row->params->set('grant_title', Request::getString('grant_title', ''));
			$row->params->set('grant_PI', Request::getString('grant_PI', ''));
			$row->params->set('grant_status', 0);
		}

		// Trigger before save event
		$isNew  = true;
		$result = Event::trigger('projects.onProjectBeforeSave', array(&$row, $isNew));

		if (in_array(false, $result, true))
		{
			throw new Exception($row->getError(), 500);
		}

		$setupComplete = $row->config('confirm_step') ? 3 : 2;

		$row->set('setup_stage', $setupComplete - 1);

		if (!$row->save())
		{
			throw new Exception($row->getError(), 500);
		}

		// Trigger after save event
		Event::trigger('projects.onProjectAfterSave', array(&$row, $isNew));

		// Save owners for new projects
		$team = array(User::get('id'));

		if ($row->get('owned_by_group'))
		{
			if ($row->config('init_team') == 1 && $row->get('sync_group'))
			{
				$group = \Hubzero\User\Group::getInstance($row->get('owned_by_group'));

				if ($group)
				{
					$team = array_merge($group->get('members'), $team);
					$team = array_unique($team);
				}
			}
		}

		$o = array(
			'projectid' => $row->get('id'),
			'groupid'   => $row->get('owned_by_group'),
			'status'    => 1,
			'role'      => 1,
			'native'    => 1
		);

		foreach ($team as $user_id)
		{
			$owner = Owner::oneByProjectAndUser($row->get('id'), $user_id);
			$owner = $owner ? $owner : Owner::blank();

			$owner->set($o);
			$owner->set('userid', $user_id);

			if (!$owner->save())
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SAVING_AUTHORS') . ': ' . $owner->getError());
			}
		}

		$row->set('setup_stage', $setupComplete);
		$row->save();

		// Sync with system group
		if (!$row->syncSystemGroup())
		{
			throw new Exception($row->getError());
		}

		require_once \Component::path('com_projects') . '/models/repo.php';

		$repo = new Repo($row, 'local');
		if (!$repo->iniLocal())
		{
			throw new Exception($repo->getError());
		}

		// Set timestamp with timezone
		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));
		if ($row->get('modified') && $row->get('modified') != '0000-00-00 00:00:00')
		{
			$row->set('modified', with(new Date($row->get('modified')))->format('Y-m-d\TH:i:s\Z'));
		}

		// Log activity
		$base = rtrim(Request::base(), '/');
		$url  = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link()), '/'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'created',
				'scope'       => 'project',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('COM_PROJECTS_ACTIVITY_ENTRY_CREATED', '<a href="' . $url . '">' . $row->get('title') . ' (' . $row->get('alias') . ')</a>'),
				'details'     => $fields
			],
			'recipients' => [
				$row->get('created_by_user'),
				$row->get('owned_by_user')
			]
		]);

		// Trigger project create event
		Event::trigger('projects.onProjectCreate', array($row));

		$this->send($row->toObject());
	}

	/**
	 * Get project info (if user is in project)
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}
	 * @apiReplaces  getTask
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "integer|string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function readTask()
	{
		// Incoming
		$id = Request::getString('id', '');

		if (is_numeric($id))
		{
			$row = Project::oneOrFail($id);
		}
		else
		{
			$row = Project::oneByAlias($id);
		}

		// Project did not load?
		if (!$row || $row->isNew())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		// Check authorization
		if (!$row->access('member') && !$row->isPublic())
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 401);
		}

		$base = rtrim(Request::base(), '/');

		// Set timestamp with timezone
		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));
		if ($row->get('modified') && $row->get('modified') != '0000-00-00 00:00:00')
		{
			$row->set('modified', with(new Date($row->get('modified')))->format('Y-m-d\TH:i:s\Z'));
		}

		$obj = $row->toObject();
		$obj->url       = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link()), '/'));
		$obj->picture   = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link('master')), '/'));
		$obj->thumbnail = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link('thumb')), '/'));

		/*if ($row->access('member'))
		{
			$obj->inSetup       = $this->model->inSetup();
			$obj->userRole      = $this->model->member()->role;
		}*/

		$this->send($obj);
	}

	/**
	 * Update a project
	 *
	 * @apiMethod PUT
	 * @apiUri    /projects/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "integer|string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Project title",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "alias",
	 * 		"description": "Project alias",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "about",
	 * 		"description": "Blurb about the project.",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "owned_by_user",
	 * 		"description": "User ID of entry owner. Defaults to entry creator if nto specified.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "owned_by_group",
	 * 		"description": "Group ID of entry owner.Specifies if a project is owned by a group.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "state",
	 * 		"description": "Published state (0 = unpublished, 1 = published)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "private",
	 * 		"description": "Private (1) project or publicly disoverable (0)?",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     1
	 * }
	 * @apiParameter {
	 * 		"name":        "sync_group",
	 * 		"description": "Sync group membership to projects? (only applies if owned_by_group is set. ",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "restricted",
	 * 		"description": "Project contains restricted data?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "hipaa",
	 * 		"description": "Project contains HIPAA data?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "no",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "ferpa",
	 * 		"description": "Project contains FERPA data?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "no",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "agree_ferpa",
	 * 		"description": "Agree to terms & conditions for FERPA data. Required if 'ferpa'='yes'.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "irb",
	 * 		"description": "Project contains IRB data?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "no",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "agree_irb",
	 * 		"description": "Agree to terms & conditions for IRB data. Required if 'irb'='yes'.",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "export",
	 * 		"description": "Project data can be exported?",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "no",
	 * 		"allowedValues": "yes, no"
	 * }
	 * @apiParameter {
	 * 		"name":        "grant_title",
	 * 		"description": "Grant title",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "grant_agency",
	 * 		"description": "Grant agency",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "grant_PI",
	 * 		"description": "Grant PI",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @apiParameter {
	 * 		"name":        "grant_budget",
	 * 		"description": "Grant budget",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     ""
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$id = Request::getString('id');

		if (is_numeric($id))
		{
			$row = Project::oneOrNew(intval($id));
		}
		else
		{
			$row = Project::oneByAlias($id);
		}

		if (!$row || $row->isNew())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_ERROR_MISSING_RECORD'), 404);
		}

		// Check authorization
		if (!($row->access('owner')
		 || $row->access('manager')
		 || ($row->access('content') && $row->config('edit_description'))))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		$fields = array(
			'title'          => Request::getString('title', $row->get('title')),
			'alias'          => Request::getString('alias', $row->get('alias')),
			'about'          => Request::getString('about', $row->get('about')),
			'owned_by_user'  => Request::getInt('owned_by_user', $row->get('owned_by_user')),
			'state'          => Request::getInt('state', $row->get('state')),
			'type'           => 1,
			'provisioned'    => 0,
			'private'        => Request::getInt('private', $row->get('private')),
			'owned_by_group' => Request::getInt('owned_by_group', $row->get('owned_by_group'))
		);

		$fields['access'] = $row->get('access');
		if ($fields['private'])
		{
			$fields['access'] = Project::PRIVACY_PRIVATE;
		}

		if (!$row->set($fields))
		{
			throw new Exception(Lang::txt('COM_PROJECTS_ERROR_BINDING_DATA'), 422);
		}

		$row->set('title', \Hubzero\Utility\Str::truncate($row->get('title'), 250));

		if ($row->get('owned_by_user'))
		{
			$owner = User::getInstance($row->get('owned_by_user'));

			if (!$owner || !$owner->get('id'))
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ERROR_OWNER_NOT_FOUND'), 409);
			}
		}

		if ($row->get('owned_by_group'))
		{
			$group = $row->group();

			if (!$group || !$group->get('gidNumber'))
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ERROR_GROUP_NOT_FOUND'), 409);
			}

			$row->set('sync_group', Request::getInt('sync_group', $row->get('sync_group')));
		}

		// General restricted data question
		$restricted  = Request::getString('restricted', $row->params->get('restricted_data'));
		$agree_irb   = Request::getInt('agree_irb', $row->params->get('agree_irb'));
		$agree_ferpa = Request::getInt('agree_ferpa', $row->params->get('agree_ferpa'));

		if ($row->config('restricted_data', 0) == 2)
		{
			if (!$restricted)
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA'), 409);
			}

			// Save params
			$row->params->set('restricted_data', $restricted);
		}

		// Restricted data with specific questions
		if ($row->config('restricted_data', 0) == 1)
		{
			$restrictions = array(
				'hipaa_data'  => Request::getString('hipaa', $row->params->get('hipaa_data')),
				'ferpa_data'  => Request::getString('ferpa', $row->params->get('ferpa_data')),
				'export_data' => Request::getString('export', $row->params->get('export_data')),
				'irb_data'    => Request::getString('irb', $row->params->get('irb_data'))
			);

			// Save individual restrictions
			foreach ($restrictions as $key => $value)
			{
				$row->params->set($key, $value);
			}

			// No selections?
			if (empty($restricted))
			{
				foreach ($restrictions as $key => $value)
				{
					if ($value == 'yes')
					{
						$restricted = 'yes';
						break;
					}
				}

				if ($restricted != 'yes')
				{
					throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_HIPAA'), 409);
				}
			}

			// Handle restricted data choice, save params
			$row->params->set('restricted_data', $restricted);

			if ($restricted == 'yes')
			{
				// Check selections
				$selected = 0;
				foreach ($restrictions as $key => $value)
				{
					if ($value == 'yes')
					{
						$selected++;
					}
				}
				// Make sure user made selections
				if ($selected == 0)
				{
					throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_SPECIFY_DATA'), 409);
				}

				// Check for required confirmations
				if (($restrictions['ferpa_data'] == 'yes' && !$agree_ferpa)
				 || ($restrictions['irb_data'] == 'yes' && !$agree_irb))
				{
					throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA_AGREE_REQUIRED'), 409);
				}

				// Stop if hipaa/export controlled, or send to extra approval screen
				if ($row->config('approve_restricted', 0))
				{
					if ($restrictions['export_data'] == 'yes'
					 || $restrictions['hipaa_data'] == 'yes'
					 || $restrictions['ferpa_data'] == 'yes')
					{
						// pending approval
						$row->set('state', Project::STATE_PENDING);
					}
				}
			}
			elseif ($restricted == 'maybe')
			{
				$row->params->set('followup', 'yes');
			}
		}

		// Collect grant information
		if ($row->config('grantinfo', 0))
		{
			$budget = $row->params->get('grant_budget');
			$agency = $row->params->get('grant_agency');
			$title  = $row->params->get('grant_title');
			$pi     = $row->params->get('grant_PI');

			$row->params->set('grant_budget', Request::getString('grant_budget', $budget));
			$row->params->set('grant_agency', Request::getString('grant_agency', $agency));
			$row->params->set('grant_title', Request::getString('grant_title', $title));
			$row->params->set('grant_PI', Request::getString('grant_PI', $pi));

			if ($row->params->get('grant_budget') != $budget
			 || $row->params->get('grant_agency') != $agency
			 || $row->params->get('grant_title') != $title
			 || $row->params->get('grant_PI') != $pi)
			{
				$row->params->set('grant_status', 0);
			}
		}

		// Trigger before save event
		$isNew  = false;
		$result = Event::trigger('projects.onProjectBeforeSave', array(&$row, $isNew));

		if (in_array(false, $result, true))
		{
			throw new Exception($row->getError(), 500);
		}

		if (!$row->save())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SAVING_DATA'), 500);
		}

		// Trigger after save event
		Event::trigger('projects.onProjectAfterSave', array(&$row, $isNew));

		// Save owners for synced groups (in case the sync setting changed)
		if ($row->get('owned_by_group') && $row->get('sync_group'))
		{
			// Check if the group membership needs syncing
			$group = \Hubzero\User\Group::getInstance($row->get('owned_by_group'));

			if ($group)
			{
				$team = $group->get('members');
				$team = array_unique($team);

				foreach ($team as $user_id)
				{
					$owner = Owner::oneByProjectAndUser($row->get('id'), $user_id);
					$owner = $owner ? $owner : Owner::blank();

					// We only need to add new people
					if (!$owner->isNew())
					{
						continue;
					}

					$owner->set(array(
						'projectid' => $row->get('id'),
						'groupid'   => $row->get('owned_by_group'),
						'status'    => 1,
						'role'      => 1,
						'native'    => 1,
						'userid'    => $user_id
					));

					if (!$owner->save())
					{
						throw new Exception(Lang::txt('COM_PROJECTS_ERROR_SAVING_AUTHORS') . ': ' . $owner->getError());
					}
				}
			}
		}

		// Sync with system group
		if (!$row->syncSystemGroup())
		{
			throw new Exception($row->getError());
		}

		// Set timestamp with timezone
		$row->set('created', with(new Date($row->get('created')))->format('Y-m-d\TH:i:s\Z'));
		if ($row->get('modified') && $row->get('modified') != '0000-00-00 00:00:00')
		{
			$row->set('modified', with(new Date($row->get('modified')))->format('Y-m-d\TH:i:s\Z'));
		}

		// Log activity
		$base = rtrim(Request::base(), '/');
		$url  = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link()), '/'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'updated',
				'scope'       => 'project',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('COM_PROJECTS_ACTIVITY_ENTRY_UPDATED', '<a href="' . $url . '">' . $row->get('title') . ' (' . $row->get('alias') . ')</a>'),
				'details'     => array(
					'title' => $row->get('title'),
					'url'   => $url
				)
			],
			'recipients' => [
				$row->get('created_by_user'),
				$row->get('owned_by_user')
			]
		]);

		$this->send($row->toObject());
	}

	/**
	 * Delete a project
	 *
	 * @apiMethod DELETE
	 * @apiUri    /projects/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "integer|string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			throw new Exception(Lang::txt('COM_PROJECTS_ERROR_MISSING_ID'), 500);
		}

		foreach ($ids as $id)
		{
			if (is_numeric($id))
			{
				$row = Project::oneOrNew(intval($id));
			}
			else
			{
				$row = Project::oneByAlias($id);
			}

			if (!$row || $row->isNew())
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ERROR_MISSING_RECORD'), 404);
			}

			if (!$row->access('delete'))
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ERROR_NOT_AUTHORIZED'), 403);
			}

			$data = $row->toArray();

			if (!$row->destroy())
			{
				throw new Exception($row->getError(), 500);
			}

			// Trigger before delete event
			Event::trigger('onProjectAfterDelete', array($id));

			// Log activity
			$base = rtrim(Request::base(), '/');
			$url  = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link()), '/'));

			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'deleted',
					'scope'       => 'project',
					'scope_id'    => $id,
					'description' => Lang::txt('COM_PROJECTS_ACTIVITY_ENTRY_DELETED', '<a href="' . $url . '">' . $data['title'] . ' (' . $data['alias'] . ')</a>'),
					'details'     => $data
				],
				'recipients' => [
					$row->get('created_by_user'),
					$row->get('owned_by_user')
				]
			]);
		}

		$this->send(null, 204);
	}
}
