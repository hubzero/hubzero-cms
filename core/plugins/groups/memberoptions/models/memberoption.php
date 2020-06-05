<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Memberoptions\Models;

use Hubzero\Database\Relational;

/**
 * Model class for group member options
 */
class Memberoption extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xgroups';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__xgroups_memberoption';

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
		'optionname'  => 'notempty',
		'gidNumber'   => 'positive|nonzero',
		'userid'      => 'positive|nonzero'
	);

	/**
	 * Retrieves one row by a combination of group ID, user ID, and option name
	 *
	 * @param   integer  $group_id
	 * @param   integer  $user_id
	 * @param   string   $optionname
	 * @return  mixed
	 */
	public static function oneByUserAndOption($group_id, $user_id, $optionname)
	{
		return self::blank()
			->whereEquals('gidNumber', $group_id)
			->whereEquals('userid', $user_id)
			->whereEquals('optionname', $optionname)
			->row();
	}

	/**
	 * Get the parent user associated with this entry
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'userid');
	}

	/**
	 * Get the parent group associated with this entry
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->belongsToOne('Hubzero\User\Group', 'gidNumber');
	}
}
