<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use Request;
use Event;
use Route;
use User;
use Lang;
use stdClass;

include_once __DIR__ . '/owner.php';
include_once __DIR__ . '/description.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/activity.php';
include_once __DIR__ . '/type.php';

/**
 * Projects database model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Project extends Relational implements \Hubzero\Search\Searchable
{
	/**
	 * State constants
	 *
	 * @var  integer
	 **/
	const STATE_ARCHIVED = 3;
	const STATE_PENDING  = 5;

	/**
	 * Privacy constants
	 *
	 * @var  integer
	 **/
	const PRIVACY_PRIVATE = 1;
	const PRIVACY_PUBLIC  = 0;
	const PRIVACY_OPEN    = -1;

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'title';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by_user'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 */
	protected $parsed = array(
		'about'
	);

	/**
	 * Hubzero\Config\Registry
	 *
	 * @var  object
	 */
	public $config = null;

	/**
	 * Get the URI
	 *
	 * @var  string
	 */
	protected $url = null;

	/**
	 * Params registry
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * Authorization check flag
	 *
	 * @var  boolean
	 */
	protected $authorized = false;

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('alias', function($data)
		{
			$alias = $this->automaticAlias($data);

			// Set name length
			$minLength = (int)$this->config('min_name_length', 3);
			$maxLength = (int)$this->config('max_name_length', 30);

			if (strlen($alias) < $minLength)
			{
				return Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_SHORT');
			}

			if (strlen($alias) > $maxLength)
			{
				return Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_LONG');
			}

			if (preg_match('/[^a-z0-9]/', $alias))
			{
				// Check for illegal characters
				return Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID');
			}

			if (is_numeric($alias))
			{
				// Check for all numeric (not allowed)
				return Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_NUMERIC');
			}

			// Array of reserved names (task names and default dirs)
			$reserved = explode(',', $this->config('reserved_names'));
			$reserved = array_map('trim', $reserved);
			$reserved = array_filter($reserved);
			$tasks    = array(
				'start', 'setup', 'browse',
				'intro', 'features', 'deleteimg',
				'reports', 'stats', 'view', 'edit',
				'suspend', 'reinstate', 'fixownership',
				'delete', 'intro', 'activate', 'process',
				'upload', 'img', 'verify', 'autocomplete',
				'showcount', 'preview', 'auth', 'public',
				'get', 'media'
			);
			$reserved = array_merge($reserved, $tasks);
			$reserved = array_unique($reserved);

			// Verify name uniqueness
			if (in_array($alias, $reserved))
			{
				return Lang::txt('COM_PROJECTS_ERROR_NAME_RESERVED');
			}

			/*$taken = self::all()
				->clear('select')
				->select('alias')
				->where('id', '!=', $this->get('id'))
				->rows()
				->fieldsByKey('alias');*/
			$taken = self::oneByAlias($alias);

			if ($taken && $taken->get('id') != $this->get('id'))
			{
				return Lang::txt('COM_PROJECTS_ERROR_NAME_NOT_UNIQUE');
			}

			return false;
		});
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = strip_tags($alias);

		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 */
	public function automaticCreatedByUser($data)
	{
		return (isset($data['created_by_user']) && $data['created_by_user'] ? (int)$data['created_by_user'] : (int)User::get('id'));
	}

	/**
	 * Defines a one to many relationship between projects and connections
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function connections()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Connection');
	}

	/**
	 * Defines a one to many relationship between projects and description fields
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function descriptions()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Description', 'project_id');
	}

	/**
	 * Defines a one to many relationship between projects and team
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function team()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Owner', 'projectid');
	}

	/**
	 * Defines a one to one relationship between project and type
	 *
	 * @return  \Hubzero\Database\Relationship\OneToOne
	 **/
	public function type()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Type', 'id', 'type');
	}

	/**
	 * Defines a one to one relationship between project and group
	 *
	 * @return  \Hubzero\User\Group
	 **/
	public function group()
	{
		$group = \Hubzero\User\Group::getInstance($this->get('owned_by_group'));
		if (!$group)
		{
			$group = new \Hubzero\User\Group;
		}
		return $group;
	}

	/**
	 * Get the system group
	 *
	 * @return  \Hubzero\User\Group
	 **/
	public function systemGroup()
	{
		$cn = $this->config('group_prefix', 'pr-') . $this->get('alias');

		$group = \Hubzero\User\Group::getInstance($cn);

		if (!$group)
		{
			$group = new \Hubzero\User\Group();
			$group->set('cn', $cn);
			$group->create();
		}

		return $group;
	}

	/**
	 * Defines a one to one relationship between project and owner
	 *
	 * @return  \Hubzero\Database\Relationship\OneToOne
	 **/
	public function owner()
	{
		return $this->oneToOne('Hubzero\User\User', 'id', 'owned_by_user');
	}

	/**
	 * Defines a one to one relationship between project and  creator
	 *
	 * @return  \Hubzero\Database\Relationship\OneToOne
	 **/
	public function creator()
	{
		return $this->oneToOne('Hubzero\User\User', 'id', 'created_by_user');
	}

	/**
	 * Check if the project is private
	 *
	 * @return  boolean
	 */
	public function isPrivate()
	{
		//return ($this->get('private') == self::PRIVACY_PRIVATE);
		return ($this->get('access') == 5);
	}

	/**
	 * Check if the project is public
	 *
	 * @return  boolean
	 */
	public function isPublic()
	{
		if ($this->isNew())
		{
			return false;
		}

		if ($this->isPrivate())
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if the project is open
	 *
	 * @return  boolean
	 */
	public function isOpen()
	{
		if ($this->isNew())
		{
			return false;
		}

		if ($this->get('private') != self::PRIVACY_OPEN)
		{
			return false;
		}

		return true;
	}

	/**
	 * Is project archived?
	 *
	 * @return  boolean
	 */
	public function isArchived()
	{
		return ($this->get('state') == self::STATE_ARCHIVED);
	}

	/**
	 * Is project deleted?
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('state') == self::STATE_DELETED);
	}

	/**
	 * Is project pending approval?
	 *
	 * @return  boolean
	 */
	public function isPending()
	{
		return ($this->get('state') == self::STATE_PENDING);
	}

	/**
	 * Is project suspended?
	 *
	 * @return     boolean
	 */
	public function isInactive()
	{
		return ($this->get('state') == 0 && !$this->inSetup());
	}

	/**
	 * Is project provisioned?
	 *
	 * @return  boolean
	 */
	public function isProvisioned()
	{
		return ($this->get('provisioned') == 1);
	}

	/**
	 * Is project in setup?
	 *
	 * @return  boolean
	 */
	public function inSetup()
	{
		$setupComplete = $this->config()->get('confirm_step') ? 3 : 2;

		return ($this->get('setup_stage') < $setupComplete);
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param   string  $key      Config property to retrieve
	 * @param   mixed   $default  Default value if property is not found
	 * @return  mixed
	 */
	public function config($key=null, $default=null)
	{
		if (!isset($this->config))
		{
			$this->config = \Component::params('com_projects');
		}
		if ($key)
		{
			return $this->config->get($key, $default);
		}
		return $this->config;
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param   string  $key      Config property to retrieve
	 * @param   mixed   $default  Default value if property is not found
	 * @return  mixed
	 */
	public function transformParams()
	{
		if (is_null($this->paramsRegistry))
		{
			$this->paramsRegistry = new \Hubzero\Config\Registry($this->get('params'));
		}
		return $this->paramsRegistry;
	}

	/**
	 * Allow membership requests?
	 *
	 * @return  boolean
	 */
	public function allowMembershipRequest()
	{
		return ($this->params->get('allow_membershiprequest') == 1);
	}

	/**
	 * Deletes the existing/current model
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		$data = $this->toArray();

		// Trigger before delete event
		Event::trigger('projects.onProjectBeforeDelete', array($data));

		// Remove associated data
		foreach ($this->connections as $connection)
		{
			if (!$connection->destroy())
			{
				$this->addError($connection->getError());
				return false;
			}
		}

		foreach ($this->descriptions as $description)
		{
			if (!$description->destroy())
			{
				$this->addError($description->getError());
				return false;
			}
		}

		foreach ($this->team as $team)
		{
			if (!$team->destroy())
			{
				$this->addError($team->getError());
				return false;
			}
		}

		// Attempt to delete the record
		$result = parent::destroy();

		if ($result)
		{
			// Trigger after delete event
			Event::trigger('projects.onProjectAfterDelete', array($data));
		}

		return $result;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string   $type  The type of link to return
	 * @return  boolean
	 */
	public function link($type = '')
	{
		if (!isset($this->url))
		{
			$this->url = 'index.php?option=com_projects&alias=' . $this->get('alias');
		}

		$type = strtolower($type);

		// If it doesn't exist or isn't published
		switch ($type)
		{
			case 'setup':
			case 'edit':
				$link = $this->url . '&task=' . $type;
			break;

			case 'thumb':
				$link = $this->picture();
			break;

			case 'stamp':
				$link = 'index.php?option=com_projects&task=get';
			break;

			case 'permalink':
			default:
				$link = $this->url;

				if ($type)
				{
					if (\Plugin::isEnabled('projects', $type))
					{
						$link .= '&active=' . $type;
					}
				}
			break;
		}

		return $link;
	}

	/**
	 * Generate and return path to a picture for the project
	 *
	 * @param   string   $size      Thumbnail (thumb) or full size (master)?
	 * @param   boolean  $realpath  Return the actual file path? When FALSE, it returns a link to /files/{hash}
	 * @return  string
	 */
	public function picture($size = 'thumb', $realpath = false)
	{
		$src  = '';
		$path = PATH_APP . DS . trim($this->config()->get('imagepath', '/site/projects'), DS) . DS . $this->get('alias') . DS . 'images';

		if ($size == 'thumb')
		{
			// Does a thumb exist?
			if (file_exists($path . DS . 'thumb.png'))
			{
				$src = $path . DS . 'thumb.png';
			}

			// No thumb. Try to create it...
			if (!$src && $this->get('picture'))
			{
				include_once dirname(dirname(__DIR__)) . '/helpers/html.php';

				$thumb = \Components\Projects\Helpers\Html::createThumbName($this->get('picture'));

				if ($thumb && file_exists($path . DS . $thumb))
				{
					$src = $path . DS . $thumb;
				}
			}
		}
		elseif (is_file($path . DS . 'master.png') && $size != 'original')
		{
			$src = $path . DS . 'master.png';
		}
		else
		{
			// Get the picture if set
			if ($this->get('picture') && is_file($path . DS . $this->get('picture')))
			{
				$src = $path . DS . $this->get('picture');
			}
		}

		// Still no file? Let's use the default
		if (!$src)
		{
			$deprecated = array(
				'components/com_projects/site/assets/img/project.png',
				'components/com_projects/assets/img/project.png',
				'components/com_projects/site/assets/img/projects-large.gif',
				'components/com_projects/assets/img/projects-large.gif'
			);

			$path = trim($this->config()->get('defaultpic', 'components/com_projects/site/assets/img/project.png'), DS);

			if (in_array($path, $deprecated))
			{
				$path = 'components/com_projects/site/assets/img/project.svg';
				$rootPath = PATH_CORE;
			}
			else
			{
				$rootPath = PATH_APP;
			}

			$src = $rootPath . DS . $path;
		}

		// Gnerate a file link
		if (!$realpath)
		{
			$src = with(new \Hubzero\Content\Moderator($src, 'public'))->getUrl();
		}

		return $src;
	}

	/**
	 * Get total number of records that will be indexed for search
	 *
	 * @return  integer
	 */
	public static function searchTotal()
	{
		$total = self::all()->total();
		return $total;
	}

	/**
	 * Get records to be included in search index
	 *
	 * @param   integer  $limit
	 * @param   integer  $offset
	 * @return  object   Hubzero\Database\Rows
	 */
	public static function searchResults($limit, $offset = 0)
	{
		return self::all()->start($offset)->limit($limit)->rows();
	}

	/**
	 * Namespace used for Search
	 *
	 * @return  string
	 */
	public static function searchNamespace()
	{
		return 'project';
	}

	/**
	 * Generate solr search Id
	 *
	 * @return  string
	 */
	public function searchId()
	{
		$searchId = self::searchNamespace() . '-' . $this->id;
		return $searchId;
	}

	/**
	 * Generate search document for Solr
	 *
	 * @return  object
	 */
	public function searchResult()
	{
		if ($this->get('state') != self::STATE_PUBLISHED)
		{
			return false;
		}
		$page = new stdClass;

		if ($this->get('state') == self::STATE_PUBLISHED
		 && ($this->isPublic() || $this->isOpen()))
		{
			$access_level = 'public';
		}
		else
		{
			$access_level = 'private';
		}

		$page->url = rtrim(Request::root(), '/') . Route::urlForClient('site', $this->link());
		$page->access_level = $access_level;
		$page->owner_type = 'user';

		$team = array();
		$team = array_map(
			function($member)
			{
				if ($member['status'] == 1 && !empty($member['userid']) && $member['userid'] > 0)
				{
					return $member['userid'];
				}
			},
			$this->team->toArray()
		);

		$page->owner       = $team;
		$page->id          = $this->searchId();
		$page->title       = $this->title;
		$page->hubtype     = self::searchNamespace();
		$page->description = \Hubzero\Utility\Sanitize::stripAll($this->about);

		return $page;
	}

	/**
	 * Get project member
	 *
	 * @return  object
	 */
	public function member()
	{
		return Owner::oneByProjectAndUser($this->get('id'), User::get('id'));
	}

	/**
	 * Authorize current user
	 *
	 * @param   boolean  $reviewer
	 * @return  boolean
	 */
	private function authorize($reviewer = false)
	{
		$this->authorized = true;

		if (in_array($this->get('access'), User::getAuthorisedViewLevels())
		 && ($this->isActive() || $this->isArchived()))
		{
			$this->params->set('access-view-project', true);
		}

		// NOT logged in
		/*if (User::isGuest())
		{
			// If the project is active and public
			if ($this->isPublic() && $this->isActive())
			{
				// Allow public view access
				$this->params->set('access-view-project', true);
			}
			// If an open project
			if ($this->isOpen() && ($this->isActive() || $this->isArchived()))
			{
				// Allow read-only mode for everything
				$this->params->set('access-member-project', true);
				$this->params->set('access-readonly-project', true);
			}
			return true;
		}*/

		// Check reviewer access?
		if ($reviewer)
		{
			// Get user groups
			$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));

			$userGroups = array();
			if (!empty($ugs))
			{
				foreach ($ugs as $group)
				{
					if ($group->regconfirmed)
					{
						$userGroups[] = $group->gidNumber;
					}
				}
			}

			switch (strtolower($reviewer))
			{
				case 'general':
				case 'admin':
				default:
					$reviewer = 'admin';
					$group = \Hubzero\User\Group::getInstance($this->config()->get('admingroup'));
				break;

				case 'sensitive':
					$group = \Hubzero\User\Group::getInstance($this->config()->get('sdata_group'));
				break;

				case 'sponsored':
					$group = \Hubzero\User\Group::getInstance($this->config()->get('ginfo_group'));
				break;

				case 'reports':
					$group = \Hubzero\User\Group::getInstance($this->config()->get('reportgroup'));
				break;
			}

			$authorized = false;
			if (!empty($userGroups))
			{
				if (in_array($group->get('gidNumber'), $userGroups))
				{
					$authorized = true;
				}
			}

			$this->params->set('access-reviewer-' . strtolower($reviewer) . '-project', $authorized);
			return true;
		}

		// Allowed to create a project
		if ($this->isNew())
		{
			$cg = $this->config()->get('creatorgroup');
			$cg = explode(',', $cg);
			$cg = array_map('trim', $cg);
			$cg = array_filter($cg);

			if (!empty($cg))
			{
				foreach ($cg as $c)
				{
					$group = \Hubzero\User\Group::getInstance($c);

					if ($group)
					{
						if ($group->is_member_of('members', User::get('id'))
						 || $group->is_member_of('managers', User::get('id')))
						{
							$this->params->set('access-create-project', true);
						}
					}
				}
			}
			else
			{
				$this->params->set('access-create-project', true);
			}
		}

		// Is the user a manager of the component? (set in component permissions)
		if (User::authorise('core.manage', 'com_projects'))
		{
			$this->params->set('access-view-project', true);
			$this->params->set('access-member-project', true);

			if ($this->isArchived())
			{
				$this->params->set('access-readonly-project', true);
			}
			else
			{
				$this->params->set('access-manager-project', true); // May edit project properties
				$this->params->set('access-content-project', true); // May add/edit/delete all content
				$this->params->set('access-owner-project', true);
				$this->params->set('access-componentmanager-project', true);
				$this->params->set('access-delete-project', true);
			}
			return true;
		}

		// Is user project member?
		$member = $this->member();
		if (empty($member) || $member->get('status') != 1)
		{
			// If an open project
			if ($this->isOpen())
			{
				// Allow read-only mode for everything
				$this->params->set('access-member-project', true);
				$this->params->set('access-readonly-project', true);
			}
		}
		else
		{
			$this->params->set('access-view-project', true);
			$this->params->set('access-member-project', true); // internal project view

			if ($this->isArchived() || $this->isOpen())
			{
				// Read-only
				$this->params->set('access-readonly-project', true);
				return true;
			}

			// Project roles
			switch ($member->role)
			{
				case 1:
					// Manager
					$this->params->set('access-manager-project', true); // May edit project properties
					$this->params->set('access-content-project', true); // May add/edit/delete all content

					// Owner (principal user/creator)
					if ($this->owner('id') == $member->userid)
					{
						$this->params->set('access-owner-project', true);
						$this->params->set('access-delete-project', true);
					}
				break;

				case 5:
					// Read-only
					$this->params->set('access-readonly-project', true);
				break;

				case 2:
				case 3:
				default:
					// Collaborator/author
					$this->params->set('access-content-project', true);
				break;
			}
		}

		return true;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string   $action  Action to check
	 * @return  boolean
	 */
	public function access($action = 'view')
	{
		if (!$this->authorized)
		{
			$this->authorize();
		}
		return $this->params->get('access-' . strtolower($action) . '-project');
	}

	/**
	 * Sync with system project group
	 *
	 * @param   string   $alias   project alias
	 * @param   string   $prefix  all project group names start with this
	 * @return  boolean
	 */
	public function syncSystemGroup()
	{
		$members  = Owner::getIds($this->get('id'), Owner::ROLE_COLLABORATOR, 1);
		$authors  = Owner::getIds($this->get('id'), Owner::ROLE_AUTHOR, 1);
		$managers = Owner::getIds($this->get('id'), Owner::ROLE_MANAGER, 1);

		$all = array_merge($members, $managers, $authors);
		$all = array_unique($all);

		$group = $this->systemGroup();
		$group->set('members', $all);
		$group->set('managers', $managers);
		$group->set('type', 2);
		$group->set('published', 1);
		$group->set('discoverability', 1);

		if (!$group->update())
		{
			$this->addError(Lang::txt('COM_PROJECTS_ERROR_SAVING_SYS_GROUP'));
			return false;
		}

		return true;
	}
}
