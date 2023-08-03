<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User;

use Hubzero\Base\Obj;
use Hubzero\Utility\Validate;
use Hubzero\Utility\Str;
use Hubzero\User\User;

/**
 * Group model
 */
class Group extends Obj
{
	/**
	 * Group ID
	 *
	 * @var  integer
	 */
	private $gidNumber = null;

	/**
	 * Group alias
	 *
	 * @var  string
	 */
	private $cn = null;

	/**
	 * Group title
	 *
	 * @var  string
	 */
	private $description = null;

	/**
	 * Published state
	 *
	 * @var  integer
	 */
	private $published = null;

	/**
	 * Approved/not approves
	 *
	 * @var  integer
	 */
	private $approved = null;

	/**
	 * Group type
	 *
	 * @var  integer
	 */
	private $type = null;

	/**
	 * Public description
	 *
	 * @var  string
	 */
	private $public_desc = null;

	/**
	 * Pivate description
	 *
	 * @var  string
	 */
	private $private_desc = null;

	/**
	 * Message for restricted access
	 *
	 * @var  string
	 */
	private $restrict_msg = null;

	/**
	 * Join policy
	 *
	 * @var  string
	 */
	private $join_policy = null;

	/**
	 * Discoverability
	 *
	 * @var integer
	 */
	private $discoverability = null;

	/**
	 * Autosubscribe to discussion emails
	 *
	 * @var  integer
	 */
	private $discussion_email_autosubscribe = 0;

	/**
	 * Description for 'logo'
	 *
	 * @var  string
	 */
	private $logo = null;

	/**
	 * Description for 'plugins'
	 *
	 * @var  string
	 */
	private $plugins = null;

	/**
	 * Created timestamp
	 *
	 * @var  string
	 */
	private $created = null;

	/**
	 * Created by user ID
	 *
	 * @var  integer
	 */
	private $created_by = null;

	/**
	 * Paramters
	 *
	 * @var  string
	 */
	private $params = null;

	/**
	 * List of members
	 *
	 * @var  array
	 */
	private $members = array();

	/**
	 * List of managers
	 *
	 * @var  array
	 */
	private $managers = array();

	/**
	 * List of applicants
	 *
	 * @var  array
	 */
	private $applicants = array();

	/**
	 * List of invitees
	 *
	 * @var  array
	 */
	private $invitees = array();

	/**
	 * Alternate table keys
	 *
	 * @var  array
	 */
	static $_list_keys = array('members', 'managers', 'applicants', 'invitees');

	/**
	 * List of updated keys
	 *
	 * @var  array
	 */
	private $_updatedkeys = array();

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
	}

	/**
	 * Clear data
	 *
	 * @return  boolean
	 */
	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);

		$this->_updatedkeys = array();

		foreach ($cvars as $key => $value)
		{
			if ($key[0] != '_')
			{
				unset($this->$key);

				if (!in_array($key, self::$_list_keys))
				{
					$this->$key = null;
				}
				else
				{
					$this->$key = array();
				}
			}
		}

		$this->_updatedkeys = array();

		return true;
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
	 * Creates a new group in the CMS.
	 * Creates a new entry under the #__xgroups table.
	 *
	 * @return  mixed  Returns false if error or gid upon success.
	 */
	public function create()
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		$cn = $this->cn;
		$gidNumber = $this->gidNumber;

		if (empty($cn) && empty($gidNumber))
		{
			return false;
		}

		if (is_numeric($gidNumber))
		{
			if (empty($cn))
			{
				$cn = '_gid' . $gidNumber;
			}

			$query = "INSERT INTO `#__xgroups` (gidNumber,cn) VALUES (" . $db->quote($gidNumber) . "," . $db->quote($cn) . ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() != 1062)
			{
				return false;
			}
		}
		else
		{
			$query = "INSERT INTO `#__xgroups` (cn) VALUES (" . $db->quote($cn) . ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() == 1062) // row exists
			{
				$query = "SELECT gidNumber FROM `#__xgroups` WHERE cn=" . $db->quote($cn) . ";";

				$db->setQeury($query);

				$result = $db->loadResult();

				if (empty($result))
				{
					return false;
				}

				$this->__set('gidNumber', $result);
			}
			else if ($result !== false)
			{
				$this->__set('gidNumber', $db->insertid());
			}
			else
			{
				return false;
			}
		}

		//trigger the onAfterStoreGroup event
		\Event::trigger('user.onAfterStoreGroup', array($this));

		return $this->gidNumber;
	}

	/**
	 * Read a record
	 *
	 * @param   mixed    $name
	 * @return  boolean
	 */
	public function read($name = null)
	{
		$this->clear();

		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (!is_null($name))
		{
			if (Validate::positiveInteger($name))
			{
				$this->gidNumber = $name;
			}
			else
			{
				$this->cn = $name;
			}
		}

		$result = true;
		$lazyloading = false;

		if (is_numeric($this->gidNumber))
		{
			$query = "SELECT * FROM `#__xgroups` WHERE gidNumber = " . $db->quote($this->gidNumber) . ";";
		}
		else
		{
			$query = "SELECT * FROM `#__xgroups` WHERE cn = " . $db->quote($this->cn) . ";";
		}

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result))
		{
			$this->clear();
			return false;
		}

		foreach ($result as $key => $value)
		{
			if (property_exists(__CLASS__, $key) && $key[0] != '_')
			{
				$this->__set($key, $value);
			}
		}

		$this->__unset('members'); // we unset the lists so we can detect whether they have been loaded or not
		$this->__unset('invitees');
		$this->__unset('applicants');
		$this->__unset('managers');

		if (!$lazyloading)
		{
			$this->__get('members');
			$this->__get('invitees');
			$this->__get('applicants');
			$this->__get('managers');
		}

		$this->_updatedkeys = array();

		return true;
	}

	/**
	 * Update a record
	 *
	 * @return  boolean
	 */
	public function update()
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (!is_numeric($this->gidNumber))
		{
			return false;
		}

		if (empty($this->_updatedkeys))
		{
			return true;
		}

		$query = "UPDATE `#__xgroups` SET ";

		$classvars = get_class_vars(__CLASS__);

		$first = true;
		$affected = 0;

		foreach ($classvars as $property => $value)
		{
			if (($property[0] == '_') || in_array($property, self::$_list_keys))
			{
				continue;
			}

			if (!in_array($property, $this->_updatedkeys))
			{
				continue;
			}

			if (!$first)
			{
				$query .= ',';
			}
			else
			{
				$first = false;
			}

			$value = $this->$property;

			if ($value === null)
			{
				$query .= "`$property`=NULL";
			}
			else
			{
				$query .= "`$property`=" . $db->quote($value);
			}
		}

		$query .= " WHERE `gidNumber`=" . $db->quote($this->gidNumber) . ";";

		if (($first != true) && !empty($query))
		{
			$db->setQuery($query);

			$result = $db->query();

			if ($result === false)
			{
				return false;
			}

			$affected = $db->getAffectedRows();
		}

		$aNewUserGroupEnrollments = array();

		foreach (self::$_list_keys as $property)
		{
			if (!in_array($property, $this->_updatedkeys))
			{
				continue;
			}

			$aux_table = "#__xgroups_" . $property;

			$list = $this->$property;

			if (!is_null($list) && !is_array($list))
			{
				$list = array($list);
			}

			$ulist = null;
			$tlist = null;

			foreach ($list as $value)
			{
				if (!is_null($ulist))
				{
					$ulist .= ',';
					$tlist .= ',';
				}

				$ulist .= $db->quote($value);
				$tlist .= '(' . $db->quote($this->gidNumber) . ',' . $db->quote($value) . ')';
			}

			// @FIXME: I don't have a better solution yet. But the next refactoring of this class
			// should eliminate the ability to read the entire member table due to problems with
			// scale on a large (thousands of members) groups. The add function should track the members
			// being added to a group, but would need to be verified to handle adding members
			// already in group. *njk*

			// @FIXME: Not neat, but because all group membership is resaved every time even for single additions
			// there is no nice way to detect only *new* additions without this check. I don't want to
			// fire off an 'onUserGroupEnrollment' event for users unless they are really being enrolled. *drb*

			if (in_array($property, array('members', 'managers')))
			{
				$query = "SELECT uidNumber FROM `#__xgroups_members` WHERE gidNumber=" . $this->gidNumber;
				$db->setQuery($query);

				// compile current list of members in this group
				$aExistingUserMembership = array();

				if (($results = $db->loadAssoc()))
				{
					foreach ($results as $uid)
					{
						$aExistingUserMembership[] = $uid;
					}
				}

				// see who is new, merge with previous additions so we have a complete list after we are done
				$aNewUserGroupEnrollments = array_merge($aNewUserGroupEnrollments, array_diff($list, $aExistingUserMembership));

			}

			if (is_array($list) && count($list) > 0)
			{
				if (in_array($property, array('members', 'managers', 'applicants', 'invitees')))
				{
					$query = "REPLACE INTO $aux_table (gidNumber,uidNumber) VALUES $tlist;";
				}

				$db->setQuery($query);

				if ($db->query())
				{
					$affected += $db->getAffectedRows();
				}
			}

			if (!is_array($list) || count($list) == 0)
			{
				if (in_array($property, array('members', 'managers', 'applicants', 'invitees')))
				{
					$query = "DELETE FROM $aux_table WHERE gidNumber=" . $db->quote($this->gidNumber) . ";";
				}
			}
			else
			{
				if (in_array($property, array('members', 'managers', 'applicants', 'invitees')))
				{
					$query = "DELETE m FROM `#__xgroups_$property` AS m WHERE " . " m.gidNumber=" .
						$db->quote($this->gidNumber) . " AND m.uidNumber NOT IN (" . $ulist . ");";
				}
			}

			$db->setQuery($query);

			if ($db->query())
			{
				$affected += $db->getAffectedRows();
			}
		}

		// After SQL is done and has no errors, fire off onGroupUserEnrolledEvents
		// for every user added to this group
		foreach ($aNewUserGroupEnrollments as $userid)
		{
			\Event::trigger('groups.onGroupUserEnrollment', array($this->gidNumber, $userid));
		}

		if ($affected > 0)
		{
			\Event::trigger('user.onAfterStoreGroup', array($this));
		}

		return true;
	}

	/**
	 * Delete a record
	 *
	 * @return  boolean
	 */
	public function delete()
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (!is_numeric($this->gidNumber))
		{
			$db->setQuery("SELECT gidNumber FROM `#__xgroups` WHERE cn=" . $db->quote($this->cn) . ";");

			$gidNumber = $db->loadResult();

			if (!is_numeric($this->gidNumber))
			{
				return false;
			}

			$this->gidNumber = $gidNumber;
		}

		$db->setQuery("DELETE FROM `#__xgroups` WHERE gidNumber=" . $db->quote($this->gidNumber) . ";");

		$result = $db->query();

		if ($result === false)
		{
			return false;
		}

		$db->setQuery("DELETE FROM `#__xgroups_applicants` WHERE gidNumber=" . $db->quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM `#__xgroups_invitees` WHERE gidNumber=" . $db->quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM `#__xgroups_managers` WHERE gidNumber=" . $db->quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM `#__xgroups_members` WHERE gidNumber=" . $db->quote($this->gidNumber) . ";");
		$db->query();

		//trigger the onAfterStoreGroup event
		\Event::trigger('user.onAfterStoreGroup', array($this));

		return true;
	}

	/**
	 * Get a property's value
	 *
	 * @param   string  $property
	 * @return  mixed
	 */
	public function __get($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property[0] == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		if (in_array($property, self::$_list_keys))
		{
			if (!array_key_exists($property, get_object_vars($this)))
			{
				$db = \App::get('db');

				if (is_object($db))
				{
					$groups = array('applicants'=>array(), 'invitees'=>array(), 'members'=>array(), 'managers'=>array());

					foreach ($groups as $key => $data)
					{
						$this->__set($key, $data);
					}

					$query = "(select uidNumber, 'invitees' AS role from #__xgroups_invitees where gidNumber=" . $db->quote($this->gidNumber) . ")
						UNION
							(select uidNumber, 'applicants' AS role from #__xgroups_applicants where gidNumber=" . $db->quote($this->gidNumber) . ")
						UNION
							(select uidNumber, 'members' AS role from #__xgroups_members where gidNumber=" . $db->quote($this->gidNumber) . ")
						UNION
							(select uidNumber, 'managers' AS role from #__xgroups_managers where gidNumber=" . $db->quote($this->gidNumber) . ")";

					$db->setQuery($query);

					if (($results = $db->loadObjectList()))
					{
						foreach ($results as $result)
						{
							if (isset($groups[$result->role]))
							{
								$groups[$result->role][] = $result->uidNumber;
							}
						}

						foreach ($groups as $key => $data)
						{
							$this->__set($key, $data);
						}
					}
				}
			}
		}

		if (isset($this->$property))
		{
			return $this->$property;
		}

		if (array_key_exists($property, get_object_vars($this)))
		{
			return null;
		}

		$this->_error("Undefined property " . __CLASS__ . "::$" . $property, E_USER_NOTICE);

		return null;
	}

	/**
	 * Set a property's value
	 *
	 * @param   string  $property
	 * @param   mixed   $value
	 * @return  void
	 */
	public function __set($property = null, $value = null)
	{
		if (!property_exists(__CLASS__, $property) || $property[0] == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		if (in_array($property, self::$_list_keys))
		{
			$value = array_diff((array) $value, array(''));

			if (in_array($property, array('managers', 'members', 'applicants', 'invitees')))
			{
				$value = $this->_userids($value);
			}

			$value = array_unique($value);
			$value = array_values($value);
			$this->$property = $value;
		}
		else
		{
			$this->$property = $value;
		}

		if (!in_array($property, $this->_updatedkeys))
		{
			$this->_updatedkeys[] = $property;
		}
	}

	/**
	 * Check if a property is set
	 *
	 * @param   string  $property
	 * @return  bool
	 */
	public function __isset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property[0] == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		return isset($this->$property);
	}

	/**
	 * Unset a property
	 *
	 * @param   string  $property
	 * @return  void
	 */
	public function __unset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property[0] == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		$this->_updatedkeys = array_diff($this->_updatedkeys, array($property));

		unset($this->$property);
	}

	/**
	 * Output an error message
	 *
	 * @param   string   $message
	 * @param   integer  $level
	 * @return  void
	 */
	private function _error($message, $level = E_USER_NOTICE)
	{
		$backtrace = debug_backtrace();

		$caller = next($backtrace);

		switch ($level)
		{
			case E_USER_NOTICE:
				echo "Notice: ";
				break;
			case E_USER_ERROR:
				echo "Fatal error: ";
				break;
			default:
				echo "Unknown error: ";
				break;
		}

		echo $message . ' in ' . $caller['file'] . ' on line ' . $caller['line'] . "\n";
	}

	/**
	 * Get a property's value
	 *
	 * @param   string  $property
	 * @return  mixed
	 */
	public function get($key, $default=null)
	{
		return $this->__get($key);
	}

	/**
	 * Set a property's value
	 *
	 * @param   string  $property
	 * @param   mixed   $value
	 * @return  void
	 */
	public function set($key, $value)
	{
		return $this->__set($key, $value);
	}

	/**
	 * Get a list of user IDs from a string, list, or list of usernames
	 *
	 * @param   array  $users
	 * @return  mixed
	 */
	private function _userids($users)
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

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
				$usernames[] = $db->quote($u);
			}
		}

		if (empty($usernames))
		{
			return $userids;
		}

		$set = implode(",", $usernames);

		$sql = "SELECT id FROM `#__users` WHERE username IN ($set);";

		$db->setQuery($sql);

		$result = $db->loadColumn();

		if (empty($result))
		{
			$result = array();
		}

		$result = array_merge($result, $userids);

		return $result;
	}

	/**
	 * Add users to a table
	 *
	 * @param   string  $key
	 * @param   array   $value
	 * @return  void
	 */
	public function add($key = null, $value = array())
	{
		$users = $this->_userids($value);

		$this->__set($key, array_merge($this->__get($key), $users));
	}

	/**
	 * Remove users form a table
	 *
	 * @param   string  $key
	 * @param   array   $value
	 * @return  void
	 */
	public function remove($key = null, $value = array())
	{
		$users = $this->_userids($value);

		$this->__set($key, array_diff($this->__get($key), $users));
	}

	/**
	 * Iterate through each group and do something
	 *
	 * @param   string  $func
	 * @return  boolean
	 */
	static function iterate($func)
	{
		$db = \App::get('db');

		$query = "SELECT cn FROM `#__xgroups`;";

		$db->setQuery($query);

		$result = $db->loadColumn();

		if ($result === false)
		{
			return false;
		}

		foreach ($result as $row)
		{
			call_user_func($func, $row);
		}

		return true;
	}

	/**
	 * Check if a group exists.
	 * Given the group id, returns true if group exists.
	 *
	 * @param   integer  $group         The group id number (GID) of the group being verified.
	 * @param   boolean  $check_system  Boolean for checking against POSIX user.
	 * @return  boolean  Returns false if group does not exist; true if group exists.
	 */
	public static function exists($group, $check_system = false)
	{
		$db = \App::get('db');

		if (empty($group))
		{
			return false;
		}

		if ($check_system)
		{
			if (is_numeric($group) && posix_getgrgid($group))
			{
				return true;
			}

			if (!is_numeric($group) && posix_getgrnam($group))
			{
				return true;
			}
		}

		// check reserved
		if (Validate::reserved('group', $group))
		{
			return true;
		}

		if (is_numeric($group))
		{
			$query = 'SELECT gidNumber FROM `#__xgroups` WHERE gidNumber=' . $db->quote($group);
		}
		else
		{
			$query = 'SELECT gidNumber FROM `#__xgroups` WHERE cn=' . $db->quote($group);
		}

		$db->setQuery($query);

		if (!$db->query())
		{
			return false;
		}

		if ($db->loadResult() > 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Find groups
	 *
	 * @param   array  $filters
	 * @return  mixed
	 */
	static function find($filters = array())
	{
		$db = \App::get('db');

		// Type 0 - System Group
		// Type 1 - HUB Group
		// Type 2 - Project Group
		// Type 3 - Partner "Special" Group
		// Type 4 - Course group
		$gTypes = array('all', 'system', 'hub', 'project', 'super', 'course', '0', '1', '2', '3', '4');

		$types = !empty($filters['type']) ? $filters['type'] : array('all');

		foreach ($types as $type)
		{
			if (!in_array($type, $gTypes))
			{
				return false;
			}
		}

		$where = array();

		if (!in_array('all', $types))
		{
			$t = implode(",", $types);

			//replace group type names with group type id
			$t = str_replace('hub', 1, $t);
			$t = str_replace('project', 2, $t);
			$t = str_replace('super', 3, $t);
			$t = str_replace('course', 4, $t);
			$t = str_replace('system', 0, $t);

			$where[] = 'type IN (' . $t . ')';
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			if (is_numeric($filters['search']))
			{
				$where[] = "gidNumber=" . $db->quote($filters['search']);
			}
			else
			{
				$where[] = "(LOWER(description) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%') . " OR LOWER(cn) LIKE " . $db->quote('%' . strtolower($filters['search']) . '%') . ")";
			}
		}

		if (isset($filters['index']) && $filters['index'] != '')
		{
			$where[] = "(LOWER(description) LIKE " . $db->quote(strtolower($filters['index']) . '%') . ")";
		}

		if (isset($filters['authorized']) && $filters['authorized'] === 'admin')
		{
			if (isset($filters['discoverability']) && $filters['discoverability'] != '')
			{
				switch ($filters['discoverability'])
				{
					case 0:
						$where[] = "discoverability=0";
						break;
					case 1:
						$where[] = "discoverability=1";
						break;
				}
			}
		}
		else
		{
			$where[] = "discoverability=0";
		}

		if (isset($filters['policy']) && $filters['policy'])
		{
			switch ($filters['policy'])
			{
				case 'closed':
					$where[] = "join_policy=3";
					break;
				case 'invite':
					$where[] = "join_policy=2";
					break;
				case 'restricted':
					$where[] = "join_policy=1";
					break;
				case 'open':
				default:
					$where[] = "join_policy=0";
					break;
			}
		}

		if (isset($filters['published']) && $filters['published'] != '')
		{
			$where[] = "published=" . $db->quote($filters['published']);
		}

		if (isset($filters['approved']) && $filters['approved'] != '')
		{
			$where[] = "approved=" . $db->quote($filters['approved']);
		}

		if (isset($filters['created']) && $filters['created'] != '')
		{
			if ($filters['created'] == 'pastday')
			{
				$pastDay = date("Y-m-d H:i:s", strtotime('-1 DAY'));
				$where[] = "created >= " . $db->quote($pastDay);
			}
		}


		if (empty($filters['fields']))
		{
			$filters['fields'][] = 'cn';
		}

		$field = implode(',', $filters['fields']);

		$query = "SELECT $field FROM `#__xgroups`";
		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['sortby']) && $filters['sortby'] != '')
		{
			$query .= " ORDER BY ";

			switch ($filters['sortby'])
			{
				case 'alias':
					$query .= 'cn ASC';
					break;
				case 'title':
					$query .= 'description ASC';
					break;
				default:
					$query .= $filters['sortby'];
					break;
			}
		}

		if (isset($filters['limit']) && $filters['limit'] != 'all')
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$query .= ";";

		$db->setQuery($query);

		if (!in_array('COUNT(*)', $filters['fields']))
		{
			$result = $db->loadObjectList();
		}
		else
		{
			$result = $db->loadResult();
		}

		if (empty($result))
		{
			return false;
		}

		return $result;
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
	 * Get emails for users
	 *
	 * @param   string  $tbl
	 * @return  array
	 */
	public function getEmails($tbl = 'managers')
	{
		if (!in_array($tbl, array('applicants', 'members', 'managers', 'invitees')))
		{
			return false;
		}

		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		$query = 'SELECT u.email FROM #__users AS u, #__xgroups_' . $tbl . ' AS gm WHERE gm.gidNumber=' . $db->quote($this->gidNumber) . ' AND u.id=gm.uidNumber;';

		$db->setQuery($query);

		$emails = $db->loadColumn();

		return $emails;
	}

	/**
	 * Search
	 *
	 * @param   string  $tbl
	 * @param   string  $q
	 * @return  array
	 */
	public function search($tbl = '', $q = '')
	{
		if (!in_array($tbl, array('applicants', 'members', 'managers', 'invitees')))
		{
			return false;
		}

		if ($q == '')
		{
			return false;
		}

		$table = '#__xgroups_' . $tbl;
		$user_table = '#__users';

		$db = \App::get('db');

		$query = "SELECT u.id FROM {$table} AS t, {$user_table} AS u
					WHERE t.gidNumber={$db->quote($this->gidNumber)}
					AND u.id=t.uidNumber
					AND LOWER(u.name) LIKE " . $db->quote('%' . strtolower($q) . '%') . ";";
		$db->setQuery($query);
		return $db->loadColumn();
	}

	/**
	 * Is a group a super group?
	 *
	 * @return  bool
	 */
	public function isSuperGroup()
	{
		return ($this->get('type') == 3) ? true : false;
	}

	/**
	 * Return a groups logo
	 *
	 * @param   string  $what  What data to return?
	 * @return  mixed
	 */
	public function getLogo($what='')
	{
		//default logo
		static $default_logo;

		if (!$default_logo)
		{
			$default_logo = '/core/components/com_groups/site/assets/img/group_default_logo.png';
		}

		//logo link - links to group overview page
		$link = \Route::url('index.php?option=com_groups&cn=' . $this->get('cn'));

		//path to group uploaded logo
		$path = substr(PATH_APP, strlen(PATH_ROOT)) . '/site/groups/' . $this->get('gidNumber') . DS . 'uploads' . DS . $this->get('logo');

		//if logo exists and file is uploaded use that logo instead of default
		$src = ($this->get('logo') != '' && is_file(PATH_ROOT . $path)) ? $path : $default_logo;

		//check to make sure were a member to show logo for hidden group
		$members_and_invitees = array_merge($this->get('members'), $this->get('invitees'));
		if ($this->get('discoverability') == 1
		 && !in_array(\User::get('id'), $members_and_invitees))
		{
			$src = $default_logo;
		}

		$what = strtolower($what);
		if ($what == 'size')
		{
			return getimagesize(PATH_ROOT . $src);
		}

		if ($what == 'path')
		{
			return $src;
		}

		return \Request::base(true) . $src;
	}

	/**
	 * Get groups path
	 *
	 * @return  string
	 */
	public function getBasePath()
	{
		$groupParams = \Component::params('com_groups');
		$uploadPath  = $groupParams->get('uploadpath', '/site/groups');
		return $uploadPath . DS . $this->get('gidNumber');
	}

	/**
	 * Return serve up path
	 *
	 * @param   string  $path
	 * @return  string
	 */
	public function downloadLinkForPath($base = 'uploads', $path = '', $type = 'file')
	{
		// get base path
		$groupFolder = $this->getBasePath();

		// split segments by directory separator
		$segments = array_map('trim', explode(DS, $path));

		// prepend base segments to original segments
		if ($base != 'uploads')
		{
			$baseSegments = array_map('trim', explode(DS, $base));
			$segments = array_merge($baseSegments, $segments);
		}

		// build link
		$link  = \Route::url('index.php?option=com_groups&cn=' . $this->get('cn'));
		$link .= '/' . ucfirst($type) . ':' . implode('/', $segments);

		// return link
		return $link;
	}

	/**
	 * Get the content of the entry
	 *
	 * @param   string   $as       Format to return state in [text, number]
	 * @param   integer  $shorten  Number of characters to shorten text to
	 * @param   string   $type     Type to get [public, private]
	 * @return  string
	 */
	public function getDescription($as='parsed', $shorten=0, $type='public')
	{
		$options = array();

		// get description before parsing
		$before = $this->get($type . '_desc');

		switch (strtolower($as))
		{
			case 'parsed':
				$config = array(
					'option'   => 'com_groups',
					'scope'    => '', //$this->get('cn') . DS . 'wiki',
					'pagename' => $this->get('cn'),
					'pageid'   => 0, //$this->get('gidNumber'),
					'filepath' => \Component::params('com_groups')->get('uploadpath', '/site/groups') . DS . $this->get('gidNumber') . DS . 'uploads',
					'domain'   => $this->get('cn'),
					'camelcase' => 0
				);

				\Event::trigger('content.onContentPrepare', array(
					'com_groups.group.' . $type . '_desc',
					&$this,
					&$config
				));
				$content = $this->get($type . '_desc');

				$options = array('html' => true);
			break;

			case 'clean':
				$content = strip_tags($this->getDescription('parsed', 0, $type));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get($type . '_desc'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = Str::truncate($content, $shorten, $options);
		}

		// set our descriptions to be html
		if ($before != $content && $as == 'parsed')
		{
			$this->set($type . '_desc', trim($content));
			//$this->update();
		}

		return $content;
	}
}
