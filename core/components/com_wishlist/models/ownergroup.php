<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
