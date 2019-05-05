<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Author\Role;

use Hubzero\Database\Relational;

/**
 * Resource author role type model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Type extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'author_role';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'role_id';

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
		'role_id' => 'positive|nonzero',
		'type_id' => 'positive|nonzero'
	);

	/**
	 * Get associated role
	 *
	 * @return  object
	 */
	public function role()
	{
		return $this->belongsToOne('Components\Resources\Models\Author\Role', 'role_id');
	}

	/**
	 * Get associated type
	 *
	 * @return  object
	 */
	public function type()
	{
		return $this->belongsToOne('Components\Resources\Models\Type', 'type_id');
	}

	/**
	 * Get an entry by role and type
	 *
	 * @param   integer  $role_id
	 * @param   integer  $type_id
	 * @return  object
	 */
	public static function oneByRoleAndType($role_id, $type_id)
	{
		return self::all()
			->whereEquals('role_id', $role_id)
			->whereEquals('type_id', $type_id)
			->row();
	}
}
