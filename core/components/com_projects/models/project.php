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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'project.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'activity.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'microblog.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'comment.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'owner.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'type.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'todo.php');

require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'html.php');

require_once(__DIR__ . DS . 'tags.php');

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Hubzero\Base\ItemList;

/**
 * Project model
 */
class Project extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\Project';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_projects.project.about';

	/**
	 * Registry
	 *
	 * @var object
	 */
	protected $_config = null;

	/**
	 * Authorized
	 *
	 * @var mixed
	 */
	private $_authorized = false;

	/**
	 * Constructor
	 *
	 * @param   mixed    $oid       ID (int) or alias (string)
	 *
	 * @return  void
	 */
	public function __construct($oid = null)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\Project($this->_db);

		if ($oid)
		{
			if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
			else
			{
				$this->_tbl->loadProject($oid);
			}
		}

		$this->params = new \Hubzero\Config\Registry($this->_tbl->get('params'));

	}

	/**
	 * Returns a reference to an article model
	 *
	 * @param      mixed $oid Article ID or alias
	 * @return     object KbModelArticle
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}

	/**
	 * Reload project
	 *
	 * @return   void
	 */
	public function reloadProject()
	{
		$this->_tbl->loadProject($this->get('id'));
	}

	/**
	 * Get project object
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function project($reload = false)
	{
		if (!isset($this->_project) || $reload == true)
		{
			$this->_project = $this->_tbl->getProject($this->get('id'), User::get('id'));
		}

		return $this->_project;
	}

	/**
	 * Get project local repo
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function repo()
	{
		require_once(__DIR__ . DS . 'repo.php');
		if (!isset($this->_repo))
		{
			$this->_repo = new Repo($this, 'local');
		}

		return $this->_repo;
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		return $this->_date('created', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function modified($as='')
	{
		return $this->_date('modified', $as);
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $key Field to return
	 * @param      string $as  What data to return
	 * @return     string
	 */
	protected function _date($key, $as='')
	{
		if ($this->get($key) == $this->_db->getNullDate())
		{
			return null;
		}
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			case 'datetime':
				return $this->_date($key, 'date') . ' &#64; ' . $this->_date($key, 'time');
			break;

			case 'timeago':
				return \Components\Projects\Helpers\Html::timeAgo($this->get($key));
			break;


			default:
				return $this->get($key);
			break;
		}
	}

	/**
	 * Get project member
	 *
	 * @return     Components\Projects\Tables\Owner
	 */
	public function member($reload = false)
	{
		if (!$this->exists())
		{
			return false;
		}
		if (!isset($this->_tblOwner))
		{
			$this->_tblOwner = new Tables\Owner($this->_db);
		}
		if (!isset($this->_member) || $reload == true)
		{
			$this->_tblOwner->loadOwner($this->get('id'), User::get('id'));
			$this->_member = $this->_tblOwner->id && $this->_tblOwner->status != 2 ? $this->_tblOwner : false;
		}

		return $this->_member;
	}

	/**
	 * Check if the member is confirmed
	 *
	 * @return     array
	 */
	public function isMemberConfirmed()
	{
		$member = $this->member();

		if ($member && $member->status == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if the project is public
	 *
	 * @return     array
	 */
	public function isPublic()
	{
		if (!$this->exists())
		{
			return false;
		}
		if ($this->get('private') == 1)
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if the project is active
	 *
	 * @return     array
	 */
	public function isActive()
	{
		if (!$this->exists())
		{
			return false;
		}

		$setupComplete = $this->config()->get('confirm_step') ? 3 : 2;

		if ($this->get('state') == 1 && $this->get('setup_stage') >= $setupComplete)
		{
			return true;
		}

		return false;
	}

	/**
	 * Is project archived?
	 *
	 * @return  boolean
	 */
	public function isArchived()
	{
		if ($this->get('state') == 3)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is project deleted?
	 *
	 * @return     boolean
	 */
	public function isDeleted()
	{
		if ($this->get('state') == 2)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is project provisioned?
	 *
	 * @return     boolean
	 */
	public function isProvisioned()
	{
		if ($this->get('provisioned') == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Get publication of a provisioned project
	 *
	 * @return     boolean
	 */
	public function getPublication()
	{
		if (!$this->exists() || !$this->isProvisioned())
		{
			return false;
		}
		if (!isset($this->_publication))
		{
			$this->_objPub = new \Components\Publications\Tables\Publication($this->_db);
			$this->_publication = $this->_objPub->getProvPublication($this->get('id'));
		}
		return $this->_publication;
	}

	/**
	 * Get provisioned project
	 *
	 * @return     boolean
	 */
	public function loadProvisioned($pid = null)
	{
		if (!intval($pid))
		{
			return false;
		}

		// Load by publication ID
		$this->_tbl->loadProvisionedProject($pid);
		$this->params = new \Hubzero\Config\Registry($this->_tbl->get('params'));
	}

	/**
	 * Is project pending approval?
	 *
	 * @return     boolean
	 */
	public function isPending()
	{
		if ($this->get('state') == 5)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is project suspended?
	 *
	 * @return     boolean
	 */
	public function isInactive()
	{
		if ($this->get('state') == 0 && !$this->inSetup())
		{
			return true;
		}
		return false;
	}

	/**
	 * Is project in setup?
	 *
	 * @return     boolean
	 */
	public function inSetup()
	{
		$setupComplete = $this->config()->get('confirm_step') ? 3 : 2;

		if ($this->get('setup_stage') < $setupComplete)
		{
			return true;
		}
		return false;
	}

	/**
	 * Authorize current user
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	private function _authorize($reviewer = false)
	{
		$this->_authorized = true;

		// NOT logged in
		if (User::isGuest())
		{
			// If the project is active and public
			if ($this->isPublic() && $this->isActive())
			{
				// Allow public view access
				$this->params->set('access-view-project', true);
			}
			return;
		}

		// Check reviewer access?
		if ($reviewer)
		{
			// Get user groups
			if (!isset($this->_userGroups))
			{
				$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
				$this->_userGroups = $this->getGroupProperty($ugs);
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
			if ($this->_userGroups && count($this->_userGroups) > 0)
			{
				foreach ($this->_userGroups as $cn)
				{
					if ($group && $cn == $group->get('cn'))
					{
						$authorized = true;
					}
				}
			}

			$this->params->set('access-reviewer-' . strtolower($reviewer) . '-project', $authorized);
			return;
		}

		// Allowed to create a project
		if (!$this->exists())
		{
			$cg = $this->config()->get('creatorgroup');
			$cg = explode(',', $cg);
			$cg = array_map('trim', $cg);

			if (!empty($cg) && !empty($cg[0]))
			{
				foreach ($cg as $c)
				{
					$group = \Hubzero\User\Group::getInstance($c);
					if ($group)
					{
						if ($group->is_member_of('members', User::get('id')) ||
							$group->is_member_of('managers', User::get('id')))
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

		// Is user project member?
		$member = $this->member();
		if (empty($member) || !$member->id)
		{
			if ($this->isPublic() && $this->isActive())
			{
				// Allow public view access
				$this->params->set('access-view-project', true);
			}
			// If an open project
			if ($this->get('private') < 0)
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

			if ($this->isArchived())
			{
				// Read-only
				$this->params->set('access-readonly-project', true);
				return;
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
					}
				break;

				case 2:
				case 3:
				default:
					// Collaborator/author
					$this->params->set('access-content-project', true);
				break;

				case 5:
					// Read-only
					$this->params->set('access-readonly-project', true);
				break;
			}
		}
	}

	/**
	 * Check a user's authorization
	 *
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action = 'view')
	{
		if (!$this->_authorized)
		{
			$this->_authorize();
		}
		return $this->params->get('access-' . strtolower($action) . '-project');
	}

	/**
	 * Check a reviewer's authorization
	 *
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function reviewerAccess($reviewer = false)
	{
		if (!$reviewer)
		{
			return false;
		}

		$this->_authorize($reviewer);
		return $this->params->get('access-reviewer-' . strtolower($reviewer) . '-project');
	}

	/**
	 * Get the owner of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return     mixed
	 */
	public function owner($property=null)
	{
		if (!isset($this->_owner) || !($this->_owner instanceof \Hubzero\User\User))
		{
			$this->_owner = \User::getInstance($this->get('owned_by_user'));
		}
		if ($property)
		{
			return $this->_owner->get($property);
		}
		return $this->_owner;
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !($this->_creator instanceof \Hubzero\User\User))
		{
			$this->_creator = \User::getInstance($this->get('created_by_user'));
		}
		if ($property)
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Get the group owner of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire Group object
	 *
	 * @return     mixed
	 */
	public function groupOwner($property=null)
	{
		if (!$this->get('owned_by_group'))
		{
			return false;
		}
		if (!isset($this->_groupOwner) || !($this->_groupOwner instanceof \Hubzero\User\Group))
		{
			$this->_groupOwner = \Hubzero\User\Group::getInstance($this->get('owned_by_group'));
		}
		if ($property)
		{
			$property = ($property == 'id' ? 'gidNumber' : $property);
			return $this->_groupOwner ? $this->_groupOwner->get($property) : null;
		}
		return $this->_groupOwner;
	}

	/**
	 * Get the content of the record.
	 * Optional argument to determine how content should be handled
	 *
	 * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
	 * clean  - parses content and then strips tags
	 * raw    - as is, no parsing
	 *
	 * @param      string  $as      Format to return content in [parsed, clean, raw]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function about($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('about.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => Request::getCmd('option', 'com_projects'),
						'scope'    => $this->get('alias') . DS . 'notes',
						'pagename' => 'projects',
						'pageid'   => $this->get('id'),
						'filepath' => $this->config('webpath'),
						'domain'   => $this->get('alias')
					);

					$content = (string) stripslashes($this->get('about', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						'com_projects.project.about',
						&$this,
						&$config
					));

					$this->set('about.parsed', (string) $this->get('about', ''));
					$this->set('about', $content);

					return $this->about($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->about('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('about'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param      string $key Config property to retrieve
	 * @return     mixed
	 */
	public function config($key=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = Component::params('com_projects');
		}
		if ($key)
		{
			return $this->_config->get($key);
		}
		return $this->_config;
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$this->_tbl->store();
		if (!$this->_tbl->getError())
		{
			return true;
		}
		$this->setError($this->_tbl->getError());
		return false;
	}

	/**
	 * Check alias name
	 *
	 * @param     string $name Alias name
	 * @return    boolean False if error, True on success
	 */
	public function check($name = '', $pid = 0, $ajax = 0)
	{
		// Load config
		$this->config();

		// Set name length
		$minLength = $this->config('min_name_length', 3);
		$maxLength = $this->config('max_name_length', 30);

		// Array of reserved names (task names and default dirs)
		$reserved = explode(',', $this->config('reserved_names'));
		$tasks    = array('start', 'setup', 'browse',
			'intro', 'features', 'deleteimg',
			'reports', 'stats', 'view', 'edit',
			'suspend', 'reinstate', 'fixownership',
			'delete', 'intro', 'activate', 'process',
			'upload', 'img', 'verify', 'autocomplete',
			'showcount', 'preview', 'auth', 'public',
			'get', 'media'
		);

		if ($name)
		{
			$name = preg_replace('/ /', '', $name);
			$name = strtolower($name);
		}

		// Perform checks
		if (!$name)
		{
			// Cannot be empty
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_EMPTY'));
		}
		elseif (strlen($name) < intval($minLength))
		{
			// Check for length
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_SHORT'));
		}
		elseif (strlen($name) > intval($maxLength))
		{
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_LONG'));
		}
		elseif (preg_match('/[^a-z0-9]/', $name))
		{
			// Check for illegal characters
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID'));
		}
		elseif (is_numeric($name))
		{
			// Check for all numeric (not allowed)
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_NUMERIC'));
		}
		else
		{
			// Verify name uniqueness
			if (!$this->_tbl->checkUniqueName($name, $pid)
				|| in_array($name, $reserved) ||
				in_array($name, $tasks))
			{
				$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_NOT_UNIQUE'));
			}
		}
		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Get group property
	 *
	 * @param      object 	$groups
	 * @param      string 	$get
	 *
	 * @return     array
	 */
	public function getGroupProperty($groups, $get = 'cn')
	{
		$arr = array();
		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed)
				{
					$arr[] = $get == 'cn' ? $group->cn : $group->gidNumber;
				}
			}
		}
		return $arr;
	}

	/**
	 * Save param
	 *
	 * @param      string 	$param
	 * @param      string 	$value
	 *
	 * @return     void
	 */
	public function saveParam($param = '', $value = '')
	{
		$this->_tbl->saveParam($this->get('id'), trim($param), htmlentities($value));
	}

	/**
	 * Get params
	 *
	 * @param      boolean 	$refresh
	 *
	 * @return     void
	 */
	public function getParams($refresh = true)
	{
		return $this->params;
	}

	/**
	 * Get a count of new activity
	 *
	 * @return  integer
	 */
	public function newCount($refresh = false)
	{
		if (!isset($this->_tblActivity))
		{
			$this->_tblActivity = new Tables\Activity($this->_db);
		}
		if (!isset($this->_newCount) || $refresh == true)
		{
			$this->_newCount = $this->_tblActivity->getNewActivityCount($this->get('id'), User::get('id'));
		}

		return $this->_newCount;
	}

	/**
	 * Get project team
	 *
	 * @return  object
	 */
	public function team($filters = array(), $refresh = false)
	{
		if (!isset($this->_tblOwner))
		{
			$this->_tblOwner = new Tables\Owner($this->_db);
		}
		if (!isset($this->_team) || $refresh == true)
		{
			$this->_team = $this->_tblOwner->getOwners($this->get('id'), $filters);
		}

		return $this->_team;
	}

	/**
	 * Get project table
	 *
	 * @param   string  $name
	 * @return  object
	 */
	public function table($name = null)
	{
		if ($name == 'Activity')
		{
			if (!isset($this->_tblActivity))
			{
				$this->_tblActivity = new Tables\Activity($this->_db);
			}
			return $this->_tblActivity;
		}
		if ($name == 'Owner')
		{
			if (!isset($this->_tblOwner))
			{
				$this->_tblOwner = new Tables\Owner($this->_db);
			}
			return $this->_tblOwner;
		}
		if ($name == 'Type')
		{
			if (!isset($this->_tblType))
			{
				$this->_tblType = new Tables\Type($this->_db);
			}
			return $this->_tblType;
		}
		if ($name == 'Blog')
		{
			if (!isset($this->_tblBlog))
			{
				$this->_tblBlog = new Tables\Blog($this->_db);
			}
			return $this->_tblBlog;
		}
		if ($name == 'Comment')
		{
			if (!isset($this->_tblComment))
			{
				$this->_tblComment = new Tables\Comment($this->_db);
			}
			return $this->_tblComment;
		}
		if ($name == 'Todo')
		{
			if (!isset($this->_tblTodo))
			{
				$this->_tblTodo = new Tables\Todo($this->_db);
			}
			return $this->_tblTodo;
		}

		return $this->_tbl;
	}

	/**
	 * Get a count of, model for, or list of entries
	 *
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   boolean  $admin    Admin?
	 * @return  mixed
	 */
	public function entries($rtrn = 'list', $filters = array(), $admin = false)
	{
		$showDeleted = $admin ? true : false;
		$setupComplete = $this->config()->get('confirm_step') ? 3 : 2;

		$filters['uid'] = isset($filters['uid']) ? $filters['uid'] : User::get('id');

		switch (strtolower($rtrn))
		{
			case 'count':
				return (int) $this->_tbl->getCount($filters, $admin, $filters['uid'], $showDeleted, $setupComplete);
			break;

			case 'group':
				$group = isset($filters['group']) ? $filters['group'] : null;
				$results = $this->_tbl->getGroupProjects(
					$group,
					User::get('id'),
					$filters,
					$setupComplete
				);
			break;

			default:
			$results = $this->_tbl->getRecords($filters, $admin, $filters['uid'], $showDeleted, $setupComplete);
			break;
		}

		if ($results)
		{
			foreach ($results as $key => $result)
			{
				$results[$key] = new self($result);
			}
		}

		return new ItemList($results);
	}

	/**
	 * Record activity
	 *
	 * @return  integer
	 */
	public function recordActivity($activity = '', $refid = '', $underline = '', $url = '', $class = 'project', $commentable = 0, $admin = 0, $managers_only = 0)
	{
		if (!isset($this->_tblActivity))
		{
			$this->_tblActivity = new Tables\Activity($this->_db);
		}
		if ($activity)
		{
			$refid = $refid ? $refid : $this->get('id');

			$aid = $this->_tblActivity->recordActivity(
				$this->get('id'),
				User::get('id'),
				$activity,
				$refid,
				$underline,
				$url,
				$class,
				$commentable,
				$admin,
				$managers_only
			);

			// Notify subscribers
			if ($aid && !User::isGuest() && !$this->isProvisioned())
			{
				Event::trigger('projects.onWatch', array($this, $class, array($aid), User::get('id')));
			}
			return $aid;
		}

		return false;
	}

	/**
	 * Record project page visit
	 *
	 * @return  void
	 */
	public function recordVisit()
	{
		$member = $this->member();

		if ($member && $this->isActive() && $this->isMemberConfirmed() && !$this->isProvisioned())
		{
			$timecheck = Date::of(time() - (6 * 60 * 60))->toSql(); // visit in last 6 hours
			if ($member->num_visits == 0 or $member->lastvisit < $timecheck)
			{
				$member->num_visits = $member->num_visits + 1; // record visit in a day
				$member->prev_visit = $member->lastvisit;
			}
			$member->lastvisit = Date::toSql();
			$member->store();
		}
	}

	/**
	 * Record first join activity
	 *
	 * @return  void
	 */
	public function checkActivity($activity = null)
	{
		if (!isset($this->_tblActivity))
		{
			$this->_tblActivity = new Tables\Activity($this->_db);
		}
		if ($activity)
		{
			return $this->_tblActivity->checkActivity($this->get('id'), $activity);
		}
		return false;
	}

	/**
	 * Record first join activity
	 *
	 * @return  void
	 */
	public function recordFirstJoinActivity()
	{
		if (!isset($this->_tblActivity))
		{
			$this->_tblActivity = new Tables\Activity($this->_db);
		}

		if ($this->isMemberConfirmed() && !$this->isProvisioned() && $this->isActive())
		{
			if (!$this->member()->lastvisit)
			{
				$aid = $this->recordActivity(Lang::txt('COM_PROJECTS_ACTIVITY_JOINED_THE_PROJECT'), $this->get('id'), '', '', 'team', 1);
				if ($aid)
				{
					$this->member()->saveParam(
						$this->get('id'),
						User::get('id'),
						'join_activityid',
						$aid
					);
				}

				// If newly created - remove join activity of project creator
				$timecheck = Date::of(time() - (10 * 60)); // last second
				if ($this->access('owner') && $timecheck <= $this->get('created'))
				{
					$this->_tblActivity->deleteActivity($aid);
				}
			}
		}
	}

	/**
	 * Get the project type
	 *
	 * @return     mixed
	 */
	public function type()
	{
		if (empty($this->_type))
		{
			$this->_type = new Tables\Type($this->_db);
			$this->_type->load($this->get('type'));
			$this->_type->_params = new \Hubzero\Html\Parameter($this->_type->params);
		}

		return $this->_type;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     boolean
	 */
	public function link($type = '')
	{
		if (!isset($this->_base))
		{
			$this->_base  = 'index.php?option=com_projects';
			$this->_base .= '&alias=' . $this->get('alias');
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'publications':
			case 'files':
			case 'team':
			case 'feed':
			case 'links':
			case 'databases':
			case 'notes':
			case 'todo':
				$link = $this->_base . '&active=' . strtolower($type);
			break;

			case 'setup':
				$link = $this->_base . '&task=setup';
			break;

			case 'edit':
				$link = $this->_base . '&task=edit';
			break;

			case 'thumb':
				$link = $this->_base . '&controller=media&media=thumb';
			break;

			case 'view':
				$link = $this->_base . '&task=view';
			break;

			case 'stamp':
				$link = 'index.php?option=com_projects&task=get';
			break;

			case 'permalink':
			default:
				$link = $this->_base;
			break;
		}

		return $link;
	}
}
