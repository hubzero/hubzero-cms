<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * User Secret plugin for checking for existence of user secret on login
 */
class plgUserSecret extends \Hubzero\Plugin\Plugin
{

	/**
	 * Name of table where user secret column resides.
	 * 
	 * @var  String 
	 * 
	 */
	private $tableName = '#__users';

	/** 
	 * Affects constructor behavior. If true, language files will be loaded automatically. 
	 * 
	 * @var  boolean 
	 */
    protected $_autoloadLanguage = true;

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array    $user     Holds the user data
	 * @param   array    $options  Array holding options (remember, autoregister, group)
	 * @return  boolean  True on success
	 */
	public function onUserLogin($user, $options = array())
	{
		// Acquire user id.
		$userId = User::getInstance()->get('id');

		// Determine whether user has an existing secret, create if needed.
		return $this->verifyUserSecret($userId);
	}

	/**
	 * Replace user secret with null when the specified user is deidentified.
	 *
	 * @param   integer	$userId  Primary key identifying user in jos_users table
	 * @return  boolean	True if secret column deleted successfully.
	 */
	public function onUserDeidentify($userId)
	{
		return $this->nullifyUserSecret($userId);
	}

	/**
	 * This utility method checks whether current user has a secret set
	 * in the database, and if not, it generates and saves one.
	 *
	 * @param   integer	$userId  Primary key identifying user in jos_users table
	 * @return  boolean	True if user has secret column populated in database
	 */
	protected function verifyUserSecret($userId)
	{
		// create a new user secret if none exists in database:
		if (!$this->checkForUserSecret($userId))
		{
			$newSecret = $this->createUserSecret();

			// Attempt to save the new user secret to the database:
			if (!$this->saveUserSecret($userId, $newSecret))
			{
				return false;
			}
		}
		// User secret exists in database:
		return true;
	}

	/**
	 * This utility method will return true if current user has a secret set, false otherwise.
	 *
	 * @param   integer	$userId    Primary key identifying user in jos_users table
	 * @return  boolean	$hasSecret True if user has secret column populated in database
	 */
	protected function checkForUserSecret($userId)
	{
		$query = new \Hubzero\Database\Query;

		// Determine whether user's secret is different from null
		$foundSecret = $query->select('*')
               		->from($this->tableName)
               		->whereEquals('id', $userId)
               		->whereIsNotNull('secret')
               		->fetch();

		// User has secret set if one row was returned:
       	if (is_array($foundSecret) && count($foundSecret) == 1)
       	{
       		return true;
       	}
       	return false;
	}

	/**
	 * This utility method generates a new user secret.
	 *
	 * @return String user secret
	 */
	protected function createUserSecret()
	{
		// create 32-character secret:
		$newSecret = shell_exec("head -c 500 /dev/urandom | tr -dc '[:alnum:]' | head -c 32");

		if (!is_null($newSecret)) {
			return $newSecret;
		}
		return false;
	}

	/**
	 * This utility method saves a new user secret.
	 *
	 * @param   integer	$userId  Primary key identifying user in jos_users table
	 * @param   String $secret   User secret
	 * @return  boolean	True if secret column successfully populated in database
	 */
	protected function saveUserSecret($userId, $secret)
	{
		$query = new \Hubzero\Database\Query;

		// Set the secret generated for this user:
		$query->update($this->tableName)
				->set(['secret' => $secret])
				->whereEquals('id', $userId)
				->execute();

		return true;
	}


	/**
	 * Replaces user secret with null.
	 *
	 * @param   integer	$userId  Primary key identifying user in jos_users table
	 * @return  boolean	True if secret value was overwritten successfully.
	 */
	protected function nullifyUserSecret($userId)
	{
		$query = new \Hubzero\Database\Query;

		// If user exists:
		$user = User::oneOrFail($userId);
		if (isset($user))
		{
			// NULL out any existing secret for this user:
			$query->update($this->tableName)
				->set(['secret' => NULL])
				->whereEquals('id', $userId)
				->execute();
			return true;
		}
		return false;
	}
} // plgUserSecret
