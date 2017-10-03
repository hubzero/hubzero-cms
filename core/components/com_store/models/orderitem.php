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

/**
 * Store order item model
 *
 * @uses \Hubzero\Database\Relational
 */
class Orderitem extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'store_order';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__order_items';

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
		'oid'    => 'positive|nonzero',
		'uid'    => 'positive|nonzero',
		'itemid' => 'positive|nonzero'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	protected $_selections = null;

	/**
	 * Get the store order
	 *
	 * @return  object
	 */
	public function order()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Order', 'oid');
	}

	/**
	 * Get the store item
	 *
	 * @return  object
	 */
	public function item()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Store', 'id', 'itemid');
	}

	/**
	 * Get the related user
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
	public function transformSelections()
	{
		if (!is_object($this->_selections))
		{
			$this->_selections = new Registry($this->get('selections'));
		}

		return $this->_selections;
	}
}
