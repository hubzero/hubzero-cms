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

use Components\Members\Models\Member;
use Lang;
use Date;

require_once(__DIR__ . DS . 'base.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'wish' . DS . 'rank.php');
require_once(\Component::path('com_members') . DS . 'models' . DS . 'member.php');

/**
 * Wishlist model class for a vote
 */
class Vote extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Wishlist\\Tables\\Wish\\Rank';

	/**
	 * Hubzero\User\Profile
	 *
	 * @var object
	 */
	private $_creator = null;

	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  Integer (ID), string (alias), object or array
	 * @return  void
	 */
	public function __construct($oid=null, $wish=null)
	{
		$this->_db = \App::get('db');

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \JTable))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of JTable.')
				);
				throw new \LogicException(Lang::txt('Table class must be an instance of JTable.'));
			}

			if (is_numeric($oid) || is_string($oid))
			{
				// Make sure $oid isn't empty
				// This saves a database call
				if ($oid)
				{
					if ($wish) // Load by User ID and Wish ID
					{
						$this->_tbl->load_vote($oid, $wish);
					}
					else
					{
						$this->_tbl->load($oid);
					}
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param   string  $property  What data to return
	 * @param   mixed   $default   Default value
	 * @return  mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof Member))
		{
			$this->_creator = Member::oneOrNew($this->get('userid'));
		}
		if ($property)
		{
			$property = ($property == 'uidNumber' ? 'id' : $property);
			if ($property == 'picture')
			{
				return $this->_creator->picture();
			}
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string   $rtrn  What data to return
	 * @return  boolean
	 */
	public function created($rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
			break;
		}
	}
}

