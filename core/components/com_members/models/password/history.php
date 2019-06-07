<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Password;

use Hubzero\Database\Relational;

/**
 * Password history model
 */
class History extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'users_password';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'user_id';

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
		'user_id'  => 'notempty',
		'passhash' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Get parent user
	 *
	 * @return  object
	 */
	public function member()
	{
		return $this->belongsToOne('Components\Members\Models\Member', 'user_id');
	}
}
