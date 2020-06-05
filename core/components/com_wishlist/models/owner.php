<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models;

use Hubzero\Database\Relational;

/**
 * Wishlist class for a owner model
 */
class Owner extends Relational
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
		'userid'   => 'positive|nonzero',
		'wishlist' => 'positive|nonzero'
	);

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'userid');
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
	 * Load a record by wishlist and userid
	 *
	 * @param   integer  $wishlist
	 * @param   integer  $userid
	 * @return  object
	 */
	public static function oneByWishlistAndUser($wishlist, $userid)
	{
		return self::all()
			->whereEquals('wishlist', $wishlist)
			->whereEquals('userid', $userid)
			->row();
	}
}
