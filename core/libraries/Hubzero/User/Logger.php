<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User;

use Hubzero\Base\Obj;

/**
 * Logger model
 *
 * This is basically an aggregator class.  It helps us map our relational models
 * and namespaces in a similar fashion.
 */
class Logger extends Obj
{
	/**
	 * User model
	 *
	 * @var  object  \Hubzero\User\User
	 */
	private $user = null;

	/**
	 * Constructs a new user logger class
	 *
	 * @return  void
	 * @since   2.0.0
	 */
	public function __construct($user)
	{
		$this->user = $user;
	}

	/**
	 * Defines a one to many relationship between users and auth log entries
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToMany
	 * @since   2.0.0
	 */
	public function auth()
	{
		return $this->user->oneToMany('Hubzero\User\Log\Auth');
	}
}
