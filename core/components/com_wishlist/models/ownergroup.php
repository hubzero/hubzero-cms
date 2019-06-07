<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models;

use Hubzero\Database\Relational;

/**
 * Wishlist class for a ownergroup model
 */
class Ownergroup extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wishlist';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'groupid'  => 'positive|nonzero',
		'wishlist' => 'positive|nonzero'
	);

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function group()
	{
		$group = new \Hubzero\User\Group();
		$group->read($this->get('groupid'));
		return $group;
	}

	/**
	 * Get the owning wishlist
	 *
	 * @return  object
	 */
	public function wishlist()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Wishlist', 'wishlist');
	}

	/**
	 * Load a record by wishlist and groupid
	 *
	 * @param   integer  $wishlist
	 * @param   integer  $groupid
	 * @return  object
	 */
	public static function oneByWishlistAndGroup($wishlist, $groupid)
	{
		return self::all()
			->whereEquals('wishlist', $wishlist)
			->whereEquals('groupid', $groupid)
			->row();
	}
}
