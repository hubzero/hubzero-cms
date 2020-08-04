<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Log;

use Hubzero\Database\Relational;

/**
 * User authentication log database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Auth extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * @var  string
	 **/
	protected $table = '#__users_log_auth';

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $initiate = [
		'logged',
		'ip'
	];

	/**
	 * Generates automatic owned logged date/time
	 *
	 * @param   array  $data  The data being saved
	 * @return  string
	 **/
	public function automaticLogged($data)
	{
		return \Date::of()->toSql();
	}

	/**
	 * Generates automatic source ip
	 *
	 * @param   array  $data  The data being saved
	 * @return  string
	 **/
	public function automaticIp($data)
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	}
}
