<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User;

use Hubzero\Config\Registry;
use Hubzero\Utility\Date;
use Hubzero\Access\Access;
use Hubzero\Access\Map;
use Exception;
use Event;

/**
 * Users database model
 *
 * @uses \Hubzero\Database\Relational
 */
class User extends \Hubzero\Database\Relational
{
	/**
	 * Default order by for model
	 *
	 * @var    string
	 * @since  2.1.0
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var    string
	 * @since  2.1.0
	 */
	public $orderDir = 'asc';

	/**
	 * Guest status
	 *
	 * @var    bool
	 * @since  2.1.0
	 */
	public $guest = true;

	/**
	 * Fields and their validation criteria
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $rules = array(
		'name'     => 'notempty',
		'email'    => 'notempty',
		'username' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	public $initiate = array(
		'registerDate',
		'registerIP',
		'access'
	);

	/**
	 * A cached switch for if this user has root access rights.
	 *
	 * @var    boolean
	 * @since  2.1.0
	 */
	protected $isRoot = null;

	/**
	 * User params
	 *
	 * @var    object
	 * @since  2.1.0
	 */
	protected $userParams = null;

	/**
	 * Authorised access groups
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $authGroups = null;

	/**
	 * Authorised access levels
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $authLevels = null;

	/**
	 * Authorised access actions
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $authActions = null;

	/**
	 * Link pattern
	 *
	 * @var    string
	 * @since  2.1.0
	 */
	public static $linkBase = null;

	/**
	 * List of picture resolvers
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	public static $pictureResolvers = array();

	/**
	 * Serializes the model data for storage
	 *
	 * @return  string
	 * @since   2.1.0
	 */
	public function serialize()
	{
		$attr = $this->getAttributes();

		$attr['guest'] = $this->guest;

		return serialize($attr);
	}

	/**
	 * Unserializes the data into a new model
	 *
	 * @param   string  $data  The data to build from
	 * @return  void
	 * @since   2.1.0
	 */
	public function unserialize($data)
	{
		$this->__construct();

		$data = unserialize($data);

		if (isset($data['guest']))
		{
			$this->guest = $data['guest'];
			unset($data['guest']);
		}

		$this->set($data);
	}

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		// Check that username conforms to rules
		$this->addRule('username', function($data)
		{
			$username = $data['username'];

			// We do this here because we need to allow one possible
			// "invalid" username to pass through, used when creating
			// temp accounts during the 3rd party auth registration
			if (is_numeric($username) && $username < 0)
			{
				return false;
			}

			if (preg_match('#[<>"\'%;()&\\\\]|\\.\\./#', $username)
			 || strlen(utf8_decode($username)) < 2
			 || trim($username) != $username)
			{
				return \Lang::txt('JLIB_DATABASE_ERROR_VALID_AZ09', 2);
			}

			return false;
		});

		// Check for existing username
		$this->addRule('username', function($data)
		{
			$user = self::oneByUsername($data['username']);

			if ($user->get('id') && $user->get('id') != $data['id'])
			{
				return \Lang::txt('JLIB_DATABASE_ERROR_USERNAME_INUSE');
			}

			return false;
		});

		// Check for valid email address
		// We do this here because we need to allow one possible
		// "invalid" address to pass through, used when creating
		// temp accounts during the 3rd party auth registration
		$this->addRule('email', function($data)
		{
			$email = $data['email'];

			if (preg_match('/^-[0-9]+@invalid$/', $email))
			{
				return false;
			}

			return (\Hubzero\Utility\Validate::email($email) ? false : 'Email does not appear to be valid');
		});
	}

	/**
	 * Generates automatic registerDate field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticRegisterDate($data)
	{
		$dt = new Date('now');

		return $dt->toSql();
	}

	/**
	 * Generates automatic registerIP field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticRegisterIP($data)
	{
		if (!isset($data['registerIP']))
		{
			$data['registerIP'] = \Request::ip();
		}
		return $data['registerIP'];
	}

	/**
	 * Generates automatic access field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAccess($data)
	{
		if (!isset($data['access']) || !$data['access'])
		{
			$data['access'] = 1;
		}
		return $data['access'];
	}

	/**
	 * Defines a one to many relationship between users and reset tokens
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToMany
	 * @since   2.0.0
	 */
	public function tokens()
	{
		return $this->oneToMany('Hubzero\User\Token', 'user_id');
	}

	/**
	 * Defines a one to one relationship between a user and their reputation
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToOne
	 * @since   2.0.0
	 */
	public function reputation()
	{
		return $this->oneToOne('Hubzero\User\Reputation', 'user_id');
	}

	/**
	 * Get access groups
	 *
	 * @return  object
	 */
	public function accessgroups()
	{
		return $this->oneToMany('Hubzero\Access\Map', 'user_id');
	}

	/**
	 * Get groups
	 *
	 * @param   string  $role
	 * @return  array
	 */
	public function groups($role = 'all')
	{
		//return $this->manyToMany('Hubzero\User\Extended\Group', 'id', 'uidNumber');

		static $groups;

		if (!isset($groups))
		{
			$groups = array(
				'applicants' => array(),
				'invitees'   => array(),
				'members'    => array(),
				'managers'   => array(),
				'all'        => array()
			);
			$all = Helper::getGroups($this->get('id'), 'all', 1);

			if ($all)
			{
				$groups['all'] = $all;

				foreach ($groups['all'] as $item)
				{
					if ($item->registered)
					{
						if (!$item->regconfirmed)
						{
							$groups['applicants'][] = $item;
						}
						else
						{
							if ($item->manager)
							{
								$groups['managers'][] = $item;
							}
							else
							{
								$groups['members'][] = $item;
							}
						}
					}
					else
					{
						$groups['invitees'][] = $item;
					}
				}
			}
		}

		if ($role)
		{
			return (isset($groups[$role])) ? $groups[$role] : array();
		}

		return $groups;
	}

	/**
	 * Defines a relationship with a generic user logging class (not a relational model itself)
	 *
	 * @return  object  \Hubzero\User\Logger
	 * @since   2.0.0
	 */
	public function logger()
	{
		return new Logger($this);
	}

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
		if ($key == 'guest')
		{
			return $this->isGuest();
		}

		if ($key == 'uidNumber')
		{
			$key = 'id';
		}

		// If the givenName, middleName, or surname isn't set, try to determine it from the name
		if (($key == 'givenName' || $key == 'middleName' || $key == 'surname') && parent::get($key, null) == null)
		{
			return $this->parseName($key);
		}

		// Legacy code expects get('id') to always
		// return an integer, even if user is logged out
		if ($key == 'id' && is_null($default))
		{
			$default = 0;
		}

		return parent::get($key, $default);
	}

	/**
	 * Sets attributes (i.e. fields) on the model
	 *
	 * This must be used when setting data to be saved. Otherwise, the properties
	 * will be attached directly to the model itself and not included in the save.
	 *
	 * @param   array|string  $key    The key to set, or array of key/value pairs
	 * @param   mixed         $value  The value to set if key is string
	 * @return  object        $this   Chainable
	 * @since   2.1.0
	 */
	public function set($key, $value = null)
	{
		if (is_string($key) && $key == 'guest')
		{
			return $this->guest = $value;
		}

		if (is_string($key) && $key == 'uidNumber')
		{
			$key = 'id';
		}

		return parent::set($key, $value);
	}

	/**
	 * Is the current user a guest (logged out) or not?
	 *
	 * @return  boolean
	 */
	public function isGuest()
	{
		$pubkeyb64 = Config::get('jwt_pub_key', null);
		$env = substr(Config::get('application_env', ''), -5);

		// check for a jwt if user is not logged in
		if ($this->guest && array_key_exists('jwt', $_COOKIE) &&
			$env == 'cloud' && !is_null($pubkeyb64))
		{
			try
			{
				// decode public key and use it to check jwt signature
				$pubkey = base64_decode($pubkeyb64);
				$jwt = \Firebase\JWT\JWT::decode($_COOKIE['jwt'], $pubkey, array('RS512'));

				// if we have information for a user, populate the user variable
				if (isset($jwt->email) && isset($jwt->id) && isset($jwt->username) && isset($jwt->name) && isset($jwt->exp))
				{
					if ($jwt->exp < time())
					{
						setcookie('jwt', -86400, '', '/', '.' . \Hubzero\Utility\Dns::domain(), true, true);
						return $this->guest();
					}
					$jwtid = $jwt->id;
					$jwtemail = $jwt->email;
					$jwtuser = $jwt->username;
					$jwtname = $jwt->name;

					// check if we have a user by this email address
					$user = \User::oneByEmail($jwtemail);

					// this user does not exist
					// we should create this in the hub database
					if ($user->isNew())
					{
						// Using SQL here because the ORM does not currently support writing
						// new records with a specific primary key value
						$db = App::get('db');
						$query = "INSERT INTO `#__users` (`id`, `name`, `username`, `email`, `password`, `usertype`, `block`, " .
							"`approved`, `sendEmail`, `activation`, `params`, `access`, `usageAgreement`, `homeDirectory`, `loginShell`, `ftpShell`)
							VALUES (" . $db->quote($jwtid) . ", " . $db->quote($jwtname) . ", " . $db->quote($jwtuser) .
							", " . $db->quote($jwtemail) . ", " . $db->quote('') . ", " . $db->quote('') . ", " .
							$db->quote('0') . ", " . $db->quote('2') . ", " . $db->quote('0') . ", " . $db->quote('1') .
							", " . $db->quote('') . ", " . $db->quote('5') . ", " . $db->quote('1') . ", " .
							$db->quote('/home/' . $jwtuser) . ", " . $db->quote('/bin/bash') . ", " .
							$db->quote('/usr/lib/sftp-server') . ")";

						$db->setQuery($query);
						$result = $db->query();

						$usersConfig = Component::params('com_members');
						$newUsertype = $usersConfig->get('new_usertype', '2');
						$query = "INSERT INTO `#__user_usergroup_map` (`user_id`, `group_id`) VALUES (" . $db->quote($jwtid) . ", " . $db->quote($newUsertype) . ")";
						$db->setQuery($query);
						$result = $db->query();
						// Clear the session that was not logged in
						App::get('session')->restart();
					}

					// set up the user object to be logged in
					\User::set('id', $user->get('id'));
					\User::set('email', $jwtemail);
					\User::set('username', $jwtuser);
					\User::set('guest', false);
					\User::set('approved', 2);

					// set the user object in the session such that
					// next visit and other plugins that use the session
					// know what user is logged in
					App::get('session')->set('user', App::get('user')->getInstance());
					$this->guest = false;

					$data = App::get('user')->getInstance()->toArray();
					\Event::trigger('user.onUserLogin', array($data));
				}
			}
			catch (Exception $e)
			{
				// something likely went wrong with the jwt
			}
		}
		return $this->guest;
	}

	/**
	 * Transform parameters into object
	 *
	 * @return  object  \Hubzero\Config\Registry
	 * @since   2.1.0
	 */
	public function transformParams()
	{
		if (!isset($this->userParams))
		{
			$this->userParams = new Registry($this->get('params'));
		}

		return $this->userParams;
	}

	/**
	 * Method to get a parameter value
	 *
	 * @param   string  $key      Parameter key
	 * @param   mixed   $default  Parameter default value
	 * @return  mixed   The value or the default if it did not exist
	 * @since   2.1.0
	 */
	public function getParam($key, $default = null)
	{
		return $this->params->get($key, $default);
	}

	/**
	 * Method to set a parameter
	 *
	 * @param   string  $key    Parameter key
	 * @param   mixed   $value  Parameter value
	 * @return  mixed   Set parameter value
	 * @since   2.1.0
	 */
	public function setParam($key, $value)
	{
		return $this->params->set($key, $value);
	}

	/**
	 * Method to set a default parameter if it does not exist
	 *
	 * @param   string  $key    Parameter key
	 * @param   mixed   $value  Parameter value
	 * @return  mixed   Set parameter value
	 * @since   2.1.0
	 */
	public function defParam($key, $value)
	{
		return $this->params->def($key, $value);
	}

	/**
	 * Get a user's picture
	 *
	 * @param   integer  $anonymous  Is user anonymous?
	 * @param   boolean  $thumbnail  Show thumbnail or full picture?
	 * @param   boolean  $serveFile  Serve file?
	 * @return  string
	 * @since   2.1.0
	 */
	public function picture($anonymous=0, $thumbnail=true, $serveFile=true)
	{
		static $fallback;

		if (!isset($fallback))
		{
			$image = "<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64' style='stroke-width: 0px; background-color: #ffffff;'>" .
					"<path fill='#d9d9d9' d='M63.9 64v-3c-.6-.9-1-1.8-1.4-2.8l-1.2-3c-.4-1-.9-1.9-1.4-2.8S58.8 50.9 58 " .
					"50c-.8-.8-1.5-1.3-2.4-1.5-.6-.2-1.1-.3-1.7-.4-.6 0-2.1-.3-4.4-.6l-8.4-1.3c-.2-.8-.4-1.5-.5-2.4-.1-" .
					".8-.3-1.5-.6-2.4.3-.6.7-1 1.1-1.5.4-.6.8-1 1.1-1.5.4-.6.7-1.3 1-2.2.3-.8.8-3.5 1.3-7.8l.4-3c.1-.9." .
					"1-1.4.1-1.5 0-2.9-1-5.6-3.1-8-1-1.3-2.4-2.4-4.1-3.2-1.8-.9-3.7-1.4-6-1.4-2.2 0-4.3.4-6 1.3-1.8.9-3" .
					".1 2-4.2 3.2-1.1 1.3-1.8 2.6-2.3 4.1-.6 1.4-.7 2.5-.7 3.2 0 .7 0 1.5.1 2.3l.4 2.9.4 3.1.4 3.3c.2 1" .
					".1.7 2.4 1.5 3.7.3.6.7 1.1 1.1 1.5l1.1 1.5c-.2.8-.4 1.5-.6 2.4-.1.8-.3 1.5-.6 2.4l-5.6.8-4.6.8c-1." .
					"2.2-2.1.3-2.6.4-.6.1-1.1.2-1.7.4-2.1.8-4 3.1-5.7 6.8L.9 58.5c-.4 1-.8 1.9-1.3 2.8V64h64.3z'/>" .
					"</svg>";

			$fallback = sprintf('data:image/svg+xml;base64,%s', base64_encode($image));
		}

		if (!$this->get('id') || $anonymous)
		{
			return $fallback;
		}

		$picture = null;

		foreach (self::$pictureResolvers as $resolver)
		{
			$picture = $resolver->picture($this->get('id'), $this->get('name'), $this->get('email'), $thumbnail);

			if ($picture)
			{
				break;
			}
		}

		if (!$picture)
		{
			$picture = $fallback;
		}

		return $picture;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 * @since   2.1.0
	 */
	public function link($type='')
	{
		if (!$this->get('id') || !self::$linkBase)
		{
			return '';
		}

		$link = str_replace(
			array(
				'{ID}',
				'{USERNAME}',
				'{EMAIL}',
				'{NAME}'
			),
			array(
				$this->get('id'),
				$this->get('username'),
				$this->get('email'),
				str_replace(' ', '+', $this->get('name'))
			),
			self::$linkBase
		);

		return $link;
	}

	/**
	 * Finds a user by username
	 *
	 * @param   string  $username
	 * @return  object
	 * @since   2.1.0
	 */
	public static function oneByUsername($username)
	{
		return self::all()
			->whereEquals('username', $username)
			->row();
	}

	/**
	 * Finds a user by email
	 *
	 * @param   string  $email
	 * @return  object
	 * @since   2.1.0
	 */
	public static function oneByEmail($email)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			return self::oneByUsername($email);
		}

		return self::all()
			->whereEquals('email', $email)
			->row();
	}

	/**
	 * Finds a user by activation token
	 *
	 * @param   string  $token
	 * @return  object
	 * @since   2.1.0
	 */
	public static function oneByActivationToken($token)
	{
		return self::all()
			->whereEquals('activation', $token)
			->row();
	}

	/**
	 * Pass through method to the table for setting the last visit date
	 *
	 * @param   integer  $timestamp  The timestamp, defaults to 'now'.
	 * @return  boolean  True on success.
	 * @since   2.1.0
	 */
	public function setLastVisit($timestamp = 'now')
	{
		$timestamp = new Date($timestamp);

		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array('lastvisitDate' => $timestamp->toSql()))
			->whereEquals('id', $this->get('id'));

		return $query->execute();
	}

	/**
	 * Alias for authorise() method
	 *
	 * @param   string   $action     The name of the action to check for permission.
	 * @param   string   $assetname  The name of the asset on which to perform the action.
	 * @return  boolean  True if authorised
	 * @since   2.1.0
	 */
	public function authorize($action, $assetname = null)
	{
		return $this->authorise($action, $assetname);
	}

	/**
	 * Method to check User object authorisation against an access control
	 * object and optionally an access extension object
	 *
	 * @param   string   $action     The name of the action to check for permission.
	 * @param   string   $assetname  The name of the asset on which to perform the action.
	 * @return  boolean  True if authorised
	 * @since   2.1.0
	 */
	public function authorise($action, $assetname = null)
	{
		// Make sure we only check for core.admin once during the run.
		if ($this->isRoot === null)
		{
			$this->isRoot = false;

			// Check for the configuration file failsafe.
			$rootUser = \App::get('config')->get('root_user');

			// The root_user variable can be a numeric user ID or a username.
			if (is_numeric($rootUser) && $this->get('id') > 0 && $this->get('id') == $rootUser)
			{
				$this->isRoot = true;
			}
			elseif ($this->username && $this->username == $rootUser)
			{
				$this->isRoot = true;
			}
			else
			{
				// Get all groups against which the user is mapped.
				$identities = $this->getAuthorisedGroups();

				array_unshift($identities, $this->get('id') * -1);

				if (Access::getAssetRules(1)->allow('core.admin', $identities))
				{
					$this->isRoot = true;
					return true;
				}
			}
		}

		return $this->isRoot ? true : Access::check($this->get('id'), $action, $assetname);
	}

	/**
	 * Method to return a list of all categories that a user has permission for a given action
	 *
	 * @param   string  $component  The component from which to retrieve the categories
	 * @param   string  $action     The name of the section within the component from which to retrieve the actions.
	 * @return  array   List of categories that this group can do this action to (empty array if none). Categories must be published.
	 * @since   2.1.0
	 */
	public function getAuthorisedCategories($component, $action)
	{
		// Brute force method: get all published category rows for the component and check each one
		// TODO: Move to ORM-based models
		$db = \App::get('db');
		$query = $db->getQuery()
			->select('c.id', 'id')
			->select('a.name', 'asset_name')
			->from('#__categories', 'c')
			->join('#__assets AS a', 'c.asset_id', 'a.id', 'inner')
			->whereEquals('c.extension', $component)
			->whereEquals('c.published', '1');
		$db->setQuery($query->toString());

		$allCategories = $db->loadObjectList('id');

		$allowedCategories = array();

		foreach ($allCategories as $category)
		{
			if ($this->authorise($action, $category->asset_name))
			{
				$allowedCategories[] = (int) $category->id;
			}
		}

		return $allowedCategories;
	}

	/**
	 * Gets an array of the authorised access levels for the user
	 *
	 * @return  array
	 * @since   2.1.0
	 */
	public function getAuthorisedViewLevels()
	{
		if (is_null($this->authLevels))
		{
			$this->authLevels = array();
		}

		if (empty($this->_authLevels))
		{
			$this->authLevels = Access::getAuthorisedViewLevels($this->get('id'));
		}

		return $this->authLevels;
	}

	/**
	 * Gets an array of the authorised user groups
	 *
	 * @return  array
	 * @since   2.1.0
	 */
	public function getAuthorisedGroups()
	{
		if (is_null($this->authGroups))
		{
			$this->authGroups = array();
		}

		if (empty($this->authGroups))
		{
			$this->authGroups = Access::getGroupsByUser($this->get('id'));
		}

		return $this->authGroups;
	}

	/**
	 * Save data
	 *
	 * @return  boolean
	 */
	public function save()
	{
		// Trigger the onUserBeforeSave event.
		$data  = $this->toArray();
		$isNew = $this->isNew();

		// Allow an exception to be thrown.
		try
		{
			$oldUser = self::oneOrNew($this->get('id'));

			// Trigger the onUserBeforeSave event.
			$result = Event::trigger('user.onUserBeforeSave', array($oldUser->toArray(), $isNew, $data));

			if (in_array(false, $result, true))
			{
				// Plugin will have to raise its own error or throw an exception.
				return false;
			}

			// Get any set access groups
			$groups = null;

			if ($this->hasAttribute('accessgroups'))
			{
				$groups = $this->get('accessgroups');

				$this->removeAttribute('accessgroups');
			}

			// Save record
			$result = parent::save();

			if (!$result)
			{
				throw new Exception($this->getError());
			}

			// Update access groups
			if ($groups && is_array($groups))
			{
				Map::destroyByUser($this->get('id'));

				Map::addUserToGroup($this->get('id'), $groups);
			}

			// In case it's a new user, we need to grab the ID
			$data['id'] = $this->get('id');

			// Fire the onUserAfterSave event
			Event::trigger('user.onUserAfterSave', array($data, $isNew, $result, $this->getError()));

			$this->purgeCache();
		}
		catch (Exception $e)
		{
			$this->addError($e->getMessage());

			$result = false;
		}

		return $result;
	}

	/**
	 * Delete the record and associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		$data = $this->toArray();

		// Trigger the onUserBeforeDelete event
		Event::trigger('user.onUserBeforeDelete', array($data));

		// Remove associated data
		if ($this->reputation->get('id'))
		{
			if (!$this->reputation->destroy())
			{
				$this->addError($this->reputation->getError());
				return false;
			}
		}

		foreach ($this->tokens()->rows() as $token)
		{
			if (!$token->destroy())
			{
				$this->addError($token->getError());
				return false;
			}
		}

		Map::destroyByUser($this->get('id'));

		// Attempt to delete the record
		$result = parent::destroy();

		if ($result)
		{
			// Trigger the onUserAfterDelete event
			Event::trigger('user.onUserAfterDelete', array($data, true, $this->getError()));
		}

		return $result;
	}

	/**
	 * Parse a users name and set the name parts on the instance
	 *
	 * @return void
	 */
	private function parseName($key=null)
	{
		$name = $this->get('name');
		if ($name)
		{
			$firstname  = "";
			$middlename = "";
			$lastname   = "";

			$words = array_map('trim', explode(' ', $this->get('name')));
			$count = count($words);

			if ($count == 1)
			{
				$firstname = $words[0];
			}
			else if ($count == 2)
			{
				$firstname = $words[0];
				$lastname  = $words[1];
			}
			else if ($count == 3)
			{
				$firstname  = $words[0];
				$middlename = $words[1];
				$lastname   = $words[2];
			}
			else
			{
				$firstname  = $words[0];
				$lastname   = $words[$count-1];
				$middlename = $words[1];

				for ($i = 2; $i < $count-1; $i++)
				{
					$middlename .= ' ' . $words[$i];
				}
			}
			switch ($key)
			{
				case 'givenName':
					return trim($firstname);
					break;
				case 'middleName':
					return trim($middlename);
					break;
				case 'surname':
					return trim($lastname);
					break;
				default:
					return '';
			}
		}
	}
}
