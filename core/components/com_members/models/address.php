<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models;

use Hubzero\Database\Relational;

/**
 * User address model
 */
class Address extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xprofiles';

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
		'uidNumber' => 'positive|nonzero'
	);

	/**
	 * Get parent member
	 *
	 * @return  object
	 */
	public function member()
	{
		return $this->belongsToOne('Components\Members\Models\Member', 'uidNumber');
	}

	/**
	 * Get addresses for a user
	 *
	 * @param   integer  $uidNumber
	 * @return  object
	 */
	public static function getAddressesForMember($uidNumber)
	{
		return self::all()
			->whereEquals('uidNumber', $uidNumber)
			->rows();
	}
}
