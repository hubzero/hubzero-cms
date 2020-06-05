<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Content\Moderator;
use Hubzero\Utility\Validate;
use stdClass;
use Event;
use Lang;
use Date;

require_once __DIR__ . DS . 'invitee.php';
require_once __DIR__ . DS . 'applicant.php';
require_once __DIR__ . DS . 'member.php';
require_once __DIR__ . DS . 'manager.php';
require_once __DIR__ . DS . 'page.php';
require_once __DIR__ . DS . 'module.php';
require_once __DIR__ . DS . 'role.php';
require_once __DIR__ . DS . 'log.php';

/**
 * Group model
 */
class Group extends Relational implements \Hubzero\Search\Searchable
{
	/**
	 * Group types
	 *
	 * @var  integer
	 **/
	const TYPE_SYSTEM  = 0;
	const TYPE_HUB     = 1;
	const TYPE_PROJECT = 2;
	const TYPE_SUPER   = 3;
	const TYPE_COURSE  = 4;

	/**
	 * Group join policies
	 *
	 * @var  integer
	 **/
	const JOIN_POLICY_OPEN       = 0;
	const JOIN_POLICY_RESTRICTED = 1;
	const JOIN_POLICY_INVITE     = 2;
	const JOIN_POLICY_CLOSED     = 3;

	/**
	 * The table to which the class pertains
	 *
	 * @var  string
	 **/
	protected $table = '#__xgroups';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'gidNumber';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'description';

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
		'description' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'cn'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 */
	protected $parsed = array(
		'public_desc',
		'private_desc'
	);

	/**
	 * Gets an attribute by key
	 *
	 * This will not retrieve properties directly attached to the model,
	 * even if they are public - those should be accessed directly!
	 *
	 * Also, make sure to access properties in transformers using the get method.
	 * Otherwise you'll just get stuck in a loop!
	 *
	 * @param   string  $key      The attribute key to get
	 * @param   mixed   $default  The value to provide, should the key be non-existent
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		if ($key == 'id')
		{
			$key = 'gidNumber';
		}

		// Legacy code expects get('id') to always return an integer
		if ($key == 'gidNumber' && is_null($default))
		{
			$default = 0;
		}

		if (in_array($key, array('applicants', 'invitees', 'members', 'managers')))
		{
			return $this->$key()
				->rows()
				->fieldsByKey('uidNumber');
		}

		return parent::get($key, $default);
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticCn($data)
	{
		$alias = (isset($data['cn']) && $data['cn'] ? $data['cn'] : $data['title']);
		$alias = str_replace(' ', '-', $alias);
		return preg_replace("/[^a-zA-Z0-9\-\_]/", '', strtolower($alias));
	}

	/**
	 * Defines a belongs to one relationship between article and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('created');
	}

	/**
	 * Get a list of applicants
	 *
	 * @return  object
	 */
	public function applicants()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Applicant', 'gidNumber');
	}

	/**
	 * Get a list of invitees
	 *
	 * @return  object
	 */
	public function invitees()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Invitee', 'gidNumber');
	}

	/**
	 * Get a list of members
	 *
	 * @return  object
	 */
	public function members()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Member', 'gidNumber');
	}

	/**
	 * Get a list of managers
	 *
	 * @return  object
	 */
	public function managers()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Manager', 'gidNumber');
	}

	/**
	 * Get a list of categories
	 *
	 * @return  object
	 */
	public function categories()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Page\\Category', 'gidNumber');
	}

	/**
	 * Get a list of pages
	 *
	 * @return  object
	 */
	public function pages()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Page', 'gidNumber');
	}

	/**
	 * Get a list of modules
	 *
	 * @return  object
	 */
	public function modules()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Module', 'gidNumber');
	}

	/**
	 * Get a list of roles
	 *
	 * @return  object
	 */
	public function roles()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Role', 'gidNumber');
	}

	/**
	 * Get a list of logs
	 *
	 * @return  object
	 */
	public function logs()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Log', 'gidNumber');
	}

	/**
	 * Is the record with the given alias unique?
	 *
	 * @return  bool
	 */
	public function isUnique()
	{
		$query = self::all()
			->whereEquals('cn', $this->get('cn'));

		if (!$this->isNew())
		{
			$query->where('gidNumber', '!=', $this->get('gidNumber'));
		}

		$row = $query->row();

		return ($row->get('gidNumber') <= 0);
	}

	/**
	 * Is a group a super group?
	 *
	 * @return  bool
	 */
	public function isSuperGroup()
	{
		return ($this->get('type') == self::TYPE_SUPER);
	}

	/**
	 * Check if the user is a member of a given table
	 *
	 * @param   string   $table  Table to check
	 * @param   integer  $uid    User ID
	 * @return  boolean
	 */
	public function is_member_of($table, $uid)
	{
		if (!in_array($table, array('applicants', 'members', 'managers', 'invitees')))
		{
			return false;
		}

		if (!is_numeric($uid))
		{
			$uid = User::oneByUsername($uid)->get('id');
		}

		return in_array($uid, $this->get($table));
	}

	/**
	 * Is user a member of the group?
	 *
	 * @param   integer  $uid
	 * @return  bool
	 */
	public function isMember($uid)
	{
		return $this->is_member_of('members', $uid);
	}

	/**
	 * Is user an applicant of the group?
	 *
	 * @param   integer  $uid
	 * @return  bool
	 */
	public function isApplicant($uid)
	{
		return $this->is_member_of('applicants', $uid);
	}

	/**
	 * Is user a manager of the group?
	 *
	 * @param   integer  $uid
	 * @return  bool
	 */
	public function isManager($uid)
	{
		return $this->is_member_of('managers', $uid);
	}

	/**
	 * Is user an invitee of the group?
	 *
	 * @param   integer  $uid
	 * @return  bool
	 */
	public function isInvitee($uid)
	{
		return $this->is_member_of('invitees', $uid);
	}

	/**
	 * Add users to the group
	 *
	 * @return  bool
	 **/
	public function add($role, $users = array())
	{
		$users = $this->normalizeUserIds($users);

		$existing = $this->$role()
			->rows()
			->fieldsByKey('uidNumber');

		$ids = array_diff($users, $existing);

		foreach ($ids as $id)
		{
			$model = rtrim($role, 's');
			$model = __NAMESPACE__ . '\\' . ucfirst($model);

			$row = $model::oneByGroupAndUser($this->get('gidNumber'), $id);

			if ($row->isNew())
			{
				$row->set(array(
					'uidNumber' => $id,
					'gidNumber' => $this->get('gidNumber')
				));

				if (!$row->save())
				{
					$this->addError($row->getError());
					return false;
				}
			}

			// Managers are a special case in that they
			// need an entry in both the members and
			// managers tables
			if ($role == 'managers')
			{
				$row = Member::oneByGroupAndUser($this->get('gidNumber'), $id);

				if ($row->isNew())
				{
					$row->set(array(
						'uidNumber' => $id,
						'gidNumber' => $this->get('gidNumber')
					));
					if (!$row->save())
					{
						$this->addError($row->getError());
						return false;
					}
				}
			}
		}

		if (in_array($role, array('members', 'managers')))
		{
			foreach ($ids as $userid)
			{
				Event::trigger('groups.onGroupUserEnrollment', array($this->get('gidNumber'), $userid));
			}
		}

		return true;
	}

	/**
	 * Remove users form the group
	 *
	 * @return  bool
	 **/
	public function remove($role, $users = array())
	{
		$users = $this->normalizeUserIds($users);

		foreach ($users as $id)
		{
			$model = rtrim($role, 's');
			$model = __NAMESPACE__ . '\\' . ucfirst($model);

			$row = $model::oneByGroupAndUser($this->get('gidNumber'), $id);
			if ($row)
			{
				if (!$row->destroy())
				{
					$this->addError($row->getError());
				}
			}
		}

		return true;
	}

	/**
	 * Get a list of user IDs from a string, list, or list of usernames
	 *
	 * @param   array  $users
	 * @return  mixed
	 */
	private function normalizeUserIds($users)
	{
		$usernames = array();
		$userids = array();

		if (!is_array($users))
		{
			$users = array($users);
		}

		foreach ($users as $u)
		{
			if (is_numeric($u))
			{
				$userids[] = $u;
			}
			else
			{
				$usernames[] = $u;
			}
		}

		if (empty($usernames))
		{
			return $userids;
		}

		$result = \Hubzero\User\User::all()
			->select('id')
			->whereIn('username', $usernames)
			->rows()
			->fieldsByKey('id');

		if (empty($result))
		{
			$result = array();
		}

		$result = array_merge($result, $userids);

		return $result;
	}

	/**
	 * Read a record
	 *
	 * @param   mixed    $name
	 * @return  boolean
	 */
	public function read($name = null)
	{
		if (!is_null($name))
		{
			if (Validate::positiveInteger($name))
			{
				$this->set('gidNumber', $name);
			}
			else
			{
				$this->set('cn', $name);
			}
		}

		if ($id = $this->get('gidNumber'))
		{
			$row = self::oneOrNew($id);
		}
		else
		{
			$row = self::oneByCn($this->get('cn'));
		}

		if (!$row || !$row->get('gidNumber'))
		{
			return false;
		}

		$this->set($row->toArray());

		return true;
	}

	/**
	 * Saves the current model to the database
	 *
	 * @return  bool
	 **/
	public function save()
	{
		$result = parent::save();

		if ($result)
		{
			Event::trigger('user.onAfterStoreGroup', array($this));
		}

		return $result;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove applicants
		foreach ($this->applicants()->rows() as $applicant)
		{
			if (!$applicant->destroy())
			{
				$this->addError($applicant->getError());
				return false;
			}
		}

		// Remove invitees
		foreach ($this->invitees()->rows() as $invitee)
		{
			if (!$invitee->destroy())
			{
				$this->addError($invitee->getError());
				return false;
			}
		}

		// Remove members
		foreach ($this->members()->rows() as $member)
		{
			if (!$member->destroy())
			{
				$this->addError($member->getError());
				return false;
			}
		}

		// Remove managers
		foreach ($this->managers()->rows() as $manager)
		{
			if (!$manager->destroy())
			{
				$this->addError($manager->getError());
				return false;
			}
		}

		// Remove pages
		foreach ($this->pages()->rows() as $page)
		{
			if (!$page->destroy())
			{
				$this->addError($page->getError());
				return false;
			}
		}

		// Remove modules
		foreach ($this->modules()->rows() as $module)
		{
			if (!$module->destroy())
			{
				$this->addError($module->getError());
				return false;
			}
		}

		// Remove roles
		foreach ($this->roles()->rows() as $role)
		{
			if (!$role->destroy())
			{
				$this->addError($role->getError());
				return false;
			}
		}

		// Attempt to delete the record
		$result = parent::destroy();

		if ($result)
		{
			Event::trigger('user.onAfterStoreGroup', array($this));
		}

		return $result;
	}

	/**
	 * Load a single record by CN
	 *
	 * @param   string  $cn
	 * @return  object
	 */
	public static function oneByCn($cn)
	{
		return self::all()
			->whereEquals('cn', (string)$cn)
			->row();
	}

	/**
	 * Returns a reference to a group object
	 *
	 * @param   mixed  $group  A string (cn) or integer (ID)
	 * @return  mixed  Object if instance found, false if not
	 */
	public static function getInstance($group)
	{
		static $instances;

		// Set instances array
		if (!isset($instances))
		{
			$instances = array();
		}

		// Do we have a matching instance?
		if (!isset($instances[$group]))
		{
			// If an ID is passed, check for a match in existing instances
			if (is_numeric($group))
			{
				foreach ($instances as $instance)
				{
					if ($instance && $instance->get('gidNumber') == $group)
					{
						// Match found
						return $instance;
						break;
					}
				}
			}

			// No matches
			// Create group object
			$hzg = new self();

			if ($hzg->read($group) === false)
			{
				$instances[$group] = false;
			}
			else
			{
				$instances[$group] = $hzg;
			}
		}

		// Return instance
		return $instances[$group];
	}

	/**
	 * Get a group's picture
	 *
	 * @param   boolean  $thumbnail  Show thumbnail or full picture?
	 * @return  string
	 */
	public function picture($thumbnail=true)
	{
		static $fallback;

		if (!isset($fallback))
		{
			$image = "<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64' style='stroke-width: 0px; background-color: #ffffff;'>" .
					"<path fill='#d9d9d9' d='M0,64c0,0,2.5,0,3.1,0H44H48c0,0-0.6-1.9-0.9-2.8c-0.2-0.5-0.8-1.7-1.6-3.7c-0.8-2-1.7-3.4-2.6-4.2" .
					"c-0.9-0.8-1.8-1.2-2.8-1.5l-1.7-0.2c-0.7-0.1-1.2-0.2-1.5-0.2l-6.8-1.1c-0.1-0.3-0.2-0.9-0.3-1.6c-0.1-0.7-0.2-1.5-0.4-2l0.7-0.9" .
					"c1-1.3,1.6-2.7,2-3.9c0.4-1.2,0.7-3,0.9-5.2c0.1-1.3,0.2-2.1,0.3-2.5c0.3-2.8-0.6-5.2-2.6-7.2c-2-2-4.5-3-7.3-3c-2.8,0-5.2,1-7.3,3" .
					"c-2,2-2.9,4.5-2.6,7.2c0,0.3,0.1,1.1,0.3,2.5c0.1,1.3,0.2,2.4,0.4,3.1c0.1,0.8,0.3,1.7,0.6,2.6c0.2,0.9,0.9,2,2,3.4l0.6,0.9" .
					"c-0.1,0.4-0.3,1.1-0.4,2c-0.1,0.9-0.2,1.6-0.2,1.7l0,0c-0.1,0.1-0.2,0.1-0.3,0.1l-0.8,0.1l-1.1,0.2l-2.4,0.3" .
					"c-2.7,0.4-4.2,0.7-4.5,0.7c-1.6,0.2-2.8,0.7-3.7,1.5c-0.8,0.8-1.5,1.7-1.9,2.6s-0.8,1.9-1.2,2.8c-0.3,1-0.7,1.7-0.9,2.1L0,64z" .
					" M64,62.5c0-0.1-0.1-0.4-0.4-1.1c-0.3-0.9-0.8-2.1-1.5-3.5c-0.2-0.4-0.6-1-1-1.5c-0.4-0.6-1-0.9-1.7-1.1c-0.7-0.2-1.6-0.4-2.8-0.4" .
					"l-3.5-0.4c-1.1-0.1-1.7-0.3-1.7-0.7l0,0l-0.4-2c-0.1-0.2,0-0.6,0.2-0.8c0.1,0,0.2-0.1,0.3-0.3l0.7-1c0.3-0.6,0.7-1.1,0.7-1.9" .
					"c0.1-0.7,0.2-1.6,0.4-2.7c0.1-1.1,0.2-1.9,0.3-2.5c0.2-1.3,0-2.6-0.6-3.8c-0.6-1.2-1.5-2.1-2.5-2.8c-2.7-1.6-5.2-1.7-7.4-0.4" .
					"c-1.2,0.6-2.1,1.5-2.9,2.7s-1.1,2.4-1,3.7c0,0.2,0.1,1.5,0.4,3.9l0.2,1.5c0.1,0.7,0.2,1.1,0.3,1.2c0.2,0.2,0.7,0.3,1.5,0.4" .
					"c2.6,0.4,4.5,1.9,5.8,4.2c0.6,0.9,1.2,2.2,2,4.1c0.8,1.9,1.8,4.3,1.8,4.4l0.9,2.3H64V62.5z'/>" .
					"</svg>";

			$fallback = sprintf('data:image/svg+xml;base64,%s', base64_encode($image));
		}

		if (!$this->get('gidNumber'))
		{
			return $fallback;
		}

		$picture = null;

		/* Placeholder for resolvers.
		   Ideally, this should work the same as members with
		   resolvers for things such as Identicon, Initialcon, etc.
		foreach (self::$pictureResolvers as $resolver)
		{
			$picture = $resolver->picture(
				$this->get('gidNumber'),
				$this->get('cn'),
				$thumbnail
			);

			if ($picture)
			{
				break;
			}
		}*/

		if ($logo = $this->get('logo'))
		{
			$path = PATH_APP . '/site/groups/' . $this->get('gidNumber') . '/uploads/' . $logo;

			if (is_file($path))
			{
				$picture = with(new Moderator($path))->getUrl();
			}
		}

		$picture = $picture ?: $fallback;

		return $picture;
	}

	/**
	 * Find groups
	 *
	 * @param   array  $filters
	 * @return  mixed
	 */
	public static function find($filters = array())
	{
		$gTypes = array('all', 'system', 'hub', 'project', 'super', 'course', '0', '1', '2', '3', '4');

		$types = !empty($filters['type']) ? $filters['type'] : array('all');

		foreach ($types as $type)
		{
			if (!in_array($type, $gTypes))
			{
				return false;
			}
		}

		$query = self::all();

		if (!in_array('all', $types))
		{
			foreach ($types as $i => $type)
			{
				switch ($type)
				{
					case 'system':
						$types[$i] = self::TYPE_SYSTEM;
					break;

					case 'hub':
						$types[$i] = self::TYPE_HUB;
					break;

					case 'project':
						$types[$i] = self::TYPE_PROJECT;
					break;

					case 'super':
						$types[$i] = self::TYPE_SUPER;
					break;

					default:
						$types[$i] = intval($type);
					break;
				}
			}

			$query->whereIn('type', $types);
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			if (is_numeric($filters['search']))
			{
				$query->whereEquals('gidNumber', (int)$filters['search']);
			}
			else
			{
				$query->whereLike('description', $filters['search'], 1)
					->orWhereLike('cn', $filters['search'], 1)
					->resetDepth();
			}
		}

		if (isset($filters['authorized']) && $filters['authorized'] === 'admin')
		{
			if (isset($filters['discoverability']))
			{
				$query->whereEquals('discoverability', $filters['discoverability']);
			}
		}
		else
		{
			$query->whereEquals('discoverability', 0);
		}

		if (isset($filters['policy']) && $filters['policy'])
		{
			switch ($filters['policy'])
			{
				case 'closed':
					$query->whereEquals('join_policy', self::JOIN_POLICY_CLOSED);
				break;
				case 'invite':
					$query->whereEquals('join_policy', self::JOIN_POLICY_INVITE);
				break;
				case 'restricted':
					$query->whereEquals('join_policy', self::JOIN_POLICY_RESTRICTED);
				break;
				case 'open':
				default:
					$query->whereEquals('join_policy', self::JOIN_POLICY_OPEN);
				break;
			}
		}

		if (isset($filters['published']))
		{
			$query->whereEquals('published', (int)$filters['published']);
		}

		if (isset($filters['approved']))
		{
			$query->whereEquals('approved', (int)$filters['approved']);
		}

		if (isset($filters['created']) && $filters['created'] != '')
		{
			if ($filters['created'] == 'pastday')
			{
				$pastDay = gmdate("Y-m-d H:i:s", strtotime('-1 DAY'));
				$query->where('created', '>=', $pastDay);
			}
		}

		if (isset($filters['sortby']) && $filters['sortby'] != '')
		{
			$filters['sortdir'] = 'asc';

			if ($filters['sortby'] == 'alias')
			{
				$filters['sortby'] = 'cn';
			}

			if ($filters['sortby'] == 'title')
			{
				$filters['sortby'] = 'description';
			}

			$query->order($filters['sortby'], $filters['sortdir']);
		}

		if (isset($filters['limit']) && $filters['limit'] != 'all')
		{
			$query->start($filters['start'])
				->limit($filters['limit']);
		}

		$result = $query->rows();

		if (!$result)
		{
			return false;
		}

		return $result;
	}

	/**
	 * Get total number of records that will be indexed by search.
	 *
	 * @return integer
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
		return 'group';
	}

	/**
	 * Generate search Id
	 *
	 * @return  string
	 */
	public function searchId()
	{
		$searchId = $this->searchNamespace() . '-' . $this->get('id');
		return $searchId;
	}

	/**
	 * Generate search document for search
	 *
	 * @return  array
	 */
	public function searchResult()
	{
		$groupTypes = array(1, 3);
		if (!in_array($this->type, $groupTypes) || $this->get('published') != 1 || $this->get('approved') != 1)
		{
			return false;
		}
		$group = new \stdClass;
		if ($this->get('discoverability') == 0)
		{
			$access_level = 'public';
		}
		else
		{
			$access_level = 'private';
		}

		$group->url = \Request::root() . 'groups/' . $this->cn;
		$group->access_level = $access_level;
		$group->owner_type = 'group';
		$group->owner = $this->get('id');
		$group->id = $this->searchId();
		$group->title = $this->description;
		$group->hubtype = $this->searchNamespace();
		$group->description = \Hubzero\Utility\Sanitize::stripAll($this->public_desc);

		return $group;
	}
}
