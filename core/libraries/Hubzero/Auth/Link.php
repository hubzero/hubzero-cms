<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Auth;

use Hubzero\Database\Relational;
use Date;

/**
 * Authentication Link
 */
class Link extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'auth';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__auth_link';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

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
		'auth_domain_id' => 'positive|nonzero',
		'username'       => 'notempty'
	);

	/**
	 * Defines a belongs to one relationship between entry and user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Defines a belongs to one relationship between entry and user
	 *
	 * @return  object
	 */
	public function domain()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Domain', 'auth_domain_id');
	}

	/**
	 * Get associated data
	 *
	 * @return  object
	 */
	public function data()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Link\\Data', 'link_id');
	}

	/**
	 * Read a record
	 *
	 * @return  boolean  True on success, False on failure
	 */
	public function read()
	{
		if ($this->get('id'))
		{
			$row = self::oneOrNew($this->get('id'));
		}
		elseif ($this->get('user_id'))
		{
			$row = self::all()
				->whereEquals('auth_domain_id', $this->get('auth_domain_id'))
				->whereEquals('user_id', $this->get('user_id'))
				->row();
		}
		elseif ($this->get('username'))
		{
			$row = self::all()
				->whereEquals('auth_domain_id', $this->get('auth_domain_id'))
				->whereEquals('username', $this->get('username'))
				->row();
		}

		if (!$row || !$row->get('id'))
		{
			return false;
		}

		foreach (array_keys($this->getAttributes()) as $key)
		{
			$this->set($key, $row->get($key));
		}

		return true;
	}

	/**
	 * Create a record
	 *
	 * @return  boolean  True on success, False on failure
	 */
	public function create()
	{
		return $this->save();
	}

	/**
	 * Update a record
	 *
	 * @param   boolean  $all  Update all properties?
	 * @return  boolean
	 */
	public function update($all = false)
	{
		return $this->save();
	}

	/**
	 * Delete a record
	 *
	 * @return  boolean
	 */
	public function delete()
	{
		return $this->destroy();
	}

	/**
	 * Get an instance of a record
	 *
	 * @param   integer  $auth_domain_id
	 * @param   string   $username
	 * @return  mixed    Object on success, False on failure
	 */
	public static function getInstance($auth_domain_id, $username)
	{
		$row = self::all()
			->whereEquals('auth_domain_id', $auth_domain_id)
			->whereEquals('username', $username)
			->row();

		if (!$row || !$row->get('id'))
		{
			return false;
		}

		return $row;
	}

	/**
	 * Create a new instance and return it
	 *
	 * @param   integer  $auth_domain_id
	 * @param   string   $username
	 * @return  mixed
	 */
	public static function createInstance($auth_domain_id, $username)
	{
		if (empty($auth_domain_id) || empty($username))
		{
			return false;
		}

		$row = self::blank();
		$row->set('auth_domain_id', $auth_domain_id);
		$row->set('username', $username);
		$row->save();

		if (!$row->get('id'))
		{
			return false;
		}

		return $row;
	}

	/**
	 * Find existing auth_link entry, return false if none exists
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @param   string  $username
	 * @return  mixed   object on success and false on failure
	 */
	public static function find($type, $authenticator, $domain, $username)
	{
		$hzad = Domain::find_or_create($type, $authenticator, $domain);

		if (!is_object($hzad))
		{
			return false;
		}

		if (empty($username))
		{
			return false;
		}

		$row = self::all()
			->whereEquals('auth_domain_id', $hzad->get('id'))
			->whereEquals('username', $username)
			->row();

		if (!$row || !$row->get('id'))
		{
			return false;
		}

		return $row;
	}

	/**
	 * Find a record by ID
	 *
	 * @param   integer  $id
	 * @return  mixed    Object on success, False on failure
	 */
	public static function find_by_id($id)
	{
		$row = self::oneOrNew($id);

		if (!$row->get('id'))
		{
			return false;
		}

		return $row;
	}

	/**
	 * Find a record, creating it if not found.
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @param   string  $username
	 * @return  mixed   Object on success, False on failure
	 */
	public static function find_or_create($type, $authenticator, $domain, $username)
	{
		$hzad = Domain::find_or_create($type, $authenticator, $domain);

		if (!$hzad)
		{
			return false;
		}

		if (empty($username))
		{
			return false;
		}

		$row = self::all()
			->whereEquals('auth_domain_id', $hzad->get('id'))
			->whereEquals('username', $username)
			->row();

		if (!$row || !$row->get('id'))
		{
			$row = self::blank();
			$row->set('auth_domain_id', $hzad->get('id'));
			$row->set('username', $username);
			$row->save();
		}

		if (!$row->get('id'))
		{
			return false;
		}

		return $row;
	}

	/**
	 * Return array of linked accounts associated with a given user id
	 * Also include auth domain name for easy display of domain name
	 *
	 * @param   integer  $user_id  ID of user to return accounts for
	 * @return  array    Array of auth link entries for the given user_id
	 */
	public static function find_by_user_id($user_id = null)
	{
		if (empty($user_id))
		{
			return false;
		}

		$l = self::blank()->getTableName();
		$d = Domain::blank()->getTableName();

		$results = self::all()
			->select($l . '.*')
			->select($d . '.authenticator', 'auth_domain_name')
			->join($d, $d . '.id', $l . '.auth_domain_id', 'inner')
			->whereEquals($l . '.user_id', $user_id)
			->rows();

		if (empty($results))
		{
			return false;
		}

		return $results->toArray();
	}

	/**
	 * Find trusted emails by User ID
	 *
	 * @param   integer  $user_id  USer ID
	 * @return  mixed
	 */
	public static function find_trusted_emails($user_id)
	{
		if (empty($user_id) || !is_numeric($user_id))
		{
			return false;
		}

		$results = self::all()
			->whereEquals('user_id', $user_id)
			->rows()
			->fieldsByKey('email');

		if (empty($results))
		{
			return false;
		}

		return $results;
	}

	/**
	 * Delete a record by User ID
	 *
	 * @param   integer  $user_id  User ID
	 * @return  boolean
	 */
	public static function delete_by_user_id($user_id = null)
	{
		if (empty($user_id))
		{
			return true;
		}

		$results = self::all()
			->whereEquals('user_id', $user_id)
			->rows();

		foreach ($results as $result)
		{
			if (!$result->destroy())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Return array of linked accounts associated with a given email address
	 * Also include auth domain name for easy display of domain name
	 *
	 * @param   string  $email
	 * @param   array   $exclude
	 * @return  mixed
	 */
	public static function find_by_email($email, $exclude = array())
	{
		if (empty($email))
		{
			return false;
		}

		$query = self::all()
			->whereEquals('email', $email);

		if (!empty($exclude[0]))
		{
			foreach ($exclude as $e)
			{
				$query->where('auth_domain_id', '!=', $e);
			}
		}

		$rows = $query->rows();

		$results = array();

		foreach ($rows as $row)
		{
			$result = $row->toArray();
			$result['auth_domain_name'] = $row->domain->get('authenticator');

			$results[] = $result;
		}

		if (empty($results))
		{
			return false;
		}

		return $results;
	}
}
