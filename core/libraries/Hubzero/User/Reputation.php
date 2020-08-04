<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Hubzero\User;

use Hubzero\Database\Relational;
use Session;

/**
 * Reputation database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Reputation extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * @var  string
	 */
	protected $table = '#__user_reputation';

	/**
	 * Increments user spam count, both globally and in current session
	 *
	 * @return  bool
	 */
	public function incrementSpamCount()
	{
		// Save global spam count
		$current = $this->get('spam_count', 0);
		$this->set('spam_count', ($current+1));
		$this->set('user_id', \User::get('id'));
		$this->save();

		// Also increment session spam count
		$current = Session::get('spam_count', 0);
		Session::set('spam_count', ($current+1));
	}

	/**
	 * Checks to see if user is jailed
	 *
	 * @return  bool
	 */
	public function isJailed()
	{
		if ($this->get('user_id', false))
		{
			$params        = Plugin::params('system', 'spamjail');
			$sessionCount  = $params->get('session_count', 5);
			$lifetimeCount = $params->get('user_count', 10);
			if (Session::get('spam_count', 0) > $sessionCount || $this->get('spam_count', 0) > $lifetimeCount)
			{
				return true;
			}
		}

		return false;
	}
}
