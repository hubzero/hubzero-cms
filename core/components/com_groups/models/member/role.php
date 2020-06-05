<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Member;

use Hubzero\Database\Relational;

/**
 * Group member role
 */
class Role extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'xgroups_member';

	/**
	 * Default order by for model
	 *
	 * @var string
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
		'roleid'    => 'positive|nonzero',
		'uidNumber' => 'positive|nonzero'
	);

	/**
	 * Get associated role
	 *
	 * @return  object
	 */
	public function role()
	{
		return $this->belongsToOne('\Components\Groups\Models\Role', 'roleid');
	}

	/**
	 * Member profile
	 *
	 * @return  object
	 */
	public function member()
	{
		return $this->belongsToOne('Hubzero\User\User', 'uidNumber');
	}

	/**
	 * Member profile
	 *
	 * @return  object
	 */
	public static function oneByUserAndRole($uidNumber, $roleid)
	{
		return self::all()
			->whereEquals('uidNumber', $uidNumber)
			->whereEquals('roleid', $roleid)
			->row();
	}

	/**
	 * Remove records by user ID
	 *
	 * @param   integer  $user_id
	 * @return  boolean  False if error, True on success
	 */
	public static function destroyByUser($user_id)
	{
		$rows = self::all()
			->whereEquals('uidNumber', $user_id)
			->rows();

		foreach ($rows as $row)
		{
			if (!$row->destroy())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove records by role ID
	 *
	 * @param   integer  $role_id
	 * @return  boolean  False if error, True on success
	 */
	public static function destroyByRole($role_id)
	{
		$rows = self::all()
			->whereEquals('roleid', $role_id)
			->rows();

		foreach ($rows as $row)
		{
			if (!$row->destroy())
			{
				return false;
			}
		}

		return true;
	}
}
