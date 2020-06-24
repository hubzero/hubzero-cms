<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models;

use Hubzero\Database\Relational;

/**
 * Recently visited groups
 */
class Recent extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xgroups';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'user_id'  => 'nonzero|positive',
		'group_id' => 'nonzero|positive'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * Retrieves one row loaded by user ID and group ID combo
	 *
	 * @param   integer  $user_id
	 * @param   integer  $group_id
	 * @return  object
	 */
	public static function oneByUserAndGroup($user_id, $group_id)
	{
		return self::blank()
			->whereEquals('user_id', $user_id)
			->whereEquals('group_id', $group_id)
			->row();
	}

	/**
	 * Update or create and entry and set the timestamp to 'now'
	 *
	 * @param   integer  $user_id
	 * @param   integer  $group_id
	 * @return  void
	 */
	public static function hit($user_id, $group_id)
	{
		$recent = self::oneByUserAndGroup($user_id, $group_id);
		$recent->set('user_id', $user_id);
		$recent->set('group_id', $group_id);
		$recent->set('created', Date::of('now')->toSql());
		$recent->save();
	}
}
