<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Bank;

use Hubzero\Database\Relational;

/**
 * Table class for bank accounts
 */
class Account extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'users';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__users_points';

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
		'uid' => 'positive|nonzero'
	);

	/**
	 * Load a record by user ID
	 *
	 * @param   integer  $user_id  User ID
	 * @return  object
	 */
	public static function oneByUserId($user_id)
	{
		return self::all()
			->whereEquals('uid', (int)$user_id)
			->row();
	}
}
