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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Store\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Date;

/**
 * Store cart model
 *
 * @uses \Hubzero\Database\Relational
 */
class Cart extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'cart';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__cart';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'uid'    => 'positive|nonzero',
		'itemid' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'added'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	protected $_selections = null;

	/**
	 * Get the store tiem
	 *
	 * @return  object
	 */
	public function item()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Store', 'id', 'itemid');
	}

	/**
	 * Generates a list of authors
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->oneToOne('\Hubzero\User\User', 'uid');
	}

	/**
	 * Transform selections
	 *
	 * @return  string
	 */
	public function transformCost()
	{
		$cost = 0;

		foreach ($this->item as $item)
		{
			$cost += ($item->get('quantity', 1) * $item->get('price'));
		}

		return $cost;
	}

	/**
	 * Generates automatic added field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 **/
	public function automaticAdded($data)
	{
		return (isset($data['added']) && $data['added'] ? $data['added'] : Date::toSql());
	}

	/**
	 * Transform selections
	 *
	 * @return  string
	 */
	public function transformSelections()
	{
		if (!is_object($this->_selections))
		{
			$this->_selections = new Registry($this->get('selections'));
		}

		return $this->_selections;
	}

	/**
	 * Get entries by user ID
	 *
	 * @param   integer  $uid
	 * @return  object
	 */
	public static function allByUser($uid)
	{
		return self::all()
			->whereEquals('uid', $uid)
			->rows();
	}

	/**
	 * Get an entry by item ID and user ID
	 *
	 * @param   integer  $uid
	 * @return  object
	 */
	public static function oneByItemAndUser($itemid, $uid)
	{
		return self::all()
			->whereEquals('itemid', $itemid)
			->whereEquals('uid', $uid)
			->row();
	}

	/**
	 * Remove all entries by user ID
	 *
	 * @param   integer  $uid
	 * @return  bool
	 */
	public static function destroyByUser($uid)
	{
		$rows = self::allByUser($uid);

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
	 * Calculate cost
	 *
	 * @param   integer  $uid
	 * @return  integer
	 */
	public static function calculateCost($uid)
	{
		$cost = 0;

		$rows = self::allByUser($uid);

		foreach ($rows as $row)
		{
			$cost += ($row->get('quantity', 1) * $row->get('price'));
		}

		return $cost;
	}
}
