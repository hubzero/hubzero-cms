<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\User\Group;

/**
 * Projects owner model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Owner extends Relational
{
	/**
	 * Role values
	 *
	 * @var  int
	 **/
	const ROLE_COLLABORATOR = 0;
	const ROLE_MANAGER      = 1;
	const ROLE_INVITEE      = 2;
	const ROLE_AUTHOR       = 3;
	const ROLE_REVIEWER     = 5;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'project';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'projectid' => 'positive|nonzero'
	);

	/**
	 * Params
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * Defines a belongs to one relationship between owner and project
	 *
	 * @return  object
	 */
	public function project()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Project', 'projectid');
	}

	/**
	 * Defines a belongs to one relationship between owner and user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'userid');
	}

	/**
	 * Defines a belongs to one relationship between owner and group
	 *
	 * @return  object
	 */
	public function group()
	{
		$group = Group::getInstance($this->get('groupid'));
		if (!$group)
		{
			$group = new Group();
		}
		return $group;
	}

	/**
	 * Load a single record by project ID and user ID
	 *
	 * @param   integer  $projectid
	 * @param   integer  $userid
	 * @return  object
	 */
	public static function oneByProjectAndUser($projectid, $userid)
	{
		return self::all()
			->whereEquals('projectid', $projectid)
			->whereEquals('userid', $userid)
			->row();
	}

	/**
	 * Get managers for a project
	 *
	 * @param   integer  $projectid
	 * @return  object
	 */
	public static function getProjectManagers($projectid)
	{
		return self::all()
			->including('user')
			->whereEquals('projectid', $projectid)
			->whereEquals('role', self::ROLE_MANAGER)
			->rows();
	}

	/**
	 * Is the user a manager of the project?
	 *
	 * @return  bool
	 */
	public function isManager()
	{
		return ($this->get('role') == self::ROLE_MANAGER);
	}

	/**
	 * Is the user a collaborator of the project?
	 *
	 * @return  bool
	 */
	public function isCollaborator()
	{
		return ($this->get('role') == self::ROLE_COLLABORATOR);
	}

	/**
	 * Is the user a reviewer of the project?
	 *
	 * @return  bool
	 */
	public function isReviewer()
	{
		return ($this->get('role') == self::ROLE_REVIEWER);
	}

	/**
	 * Is the user invited to the project?
	 *
	 * @return  bool
	 */
	public function isInvited()
	{
		return ($this->get('role') == self::ROLE_INVITEE);
	}

	/**
	 * Get a param value
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!is_object($this->paramsRegistry))
		{
			$this->paramsRegistry = new Registry($this->get('params'));
		}

		return $this->paramsRegistry;
	}

	/**
	 * Get ids of project owners
	 *
	 * @param   integer  $projectid
	 * @param   string   $role       get owners in specific role or all
	 * @param   integer  $get_uids   get user ids (1) or owner ids (0)
	 * @param   integer  $active     get only active users (1) or any
	 * @return  array
	 */
	public static function getIds($projectid, $role = null, $uids = 0, $active = 1)
	{
		if (is_null($role))
		{
			$role = self::ROLE_MANAGER;
		}

		if (!is_numeric($projectid))
		{
			$project = Project::oneByAlias($projectid);
			$projectid = $project->id;
		}

		$key = $uids ? 'userid' : 'id';

		$query = self::all()
			->whereEquals('projectid', $projectid);

		if ($active)
		{
			$query->whereEquals('status', 1);
		}
		else
		{
			$query->whereEquals('status', '!=', 2);
		}

		if ($role != 'all')
		{
			$query->whereEquals('role', (int)$role);
		}

		if ($uids)
		{
			$query->where('userid', '!=', 0);
		}

		return $query
			->rows()
			->fieldsByKey($key);
	}
}
