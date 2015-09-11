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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

require_once(__DIR__ . DS . 'base.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'member.badge.php');

/**
 * Courses model class for badges
 */
class MemberBadge extends Base
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\MemberBadge';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'memberbadge';

	/**
	 * Constructor
	 *
	 * @param   integer $oid Record ID
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new $this->_tbl_name($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
	}

	/**
	 * Load by member id
	 *
	 * Member id is unique to a course and section, and badges are unique to members.
	 * Therefore, member id also serves as a primary key of this table.
	 *
	 * @param   integer $id Member ID
	 * @return  mixed   Object on success, False on error
	 */
	public static function loadByMemberId($id)
	{
		if (is_numeric($id))
		{
			$obj = new MemberBadge();
			$obj->_tbl->loadByMemberId($id);
			return $obj;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Load by validation token
	 *
	 * Validation token is a unique hash that allows us to identify a users badge evidence without exposing their user id
	 *
	 * @param    string $token Badge assertion token
	 * @return   mixed  Object on success, False on error
	 */
	public static function loadByToken($token)
	{
		$obj = new MemberBadge();
		$obj->_tbl->load(array('validation_token' => $token));

		if (isset($obj->_tbl->id))
		{
			return $obj;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Store member badge
	 *
	 * @param   boolean $check Perform data validation? 
	 * @return  boolean
	 */
	public function store($check=true)
	{
		if (!$this->get('validation_token'))
		{
			// Generate validation token
			$token = str_replace(array('/', '+'), array('-', '-'), substr(base64_encode(openssl_random_pseudo_bytes(21)), 0, 20));
			$this->set('validation_token', $token);
		}

		return parent::store();
	}

	/**
	 * Check whether or not a student has earned the badge
	 *
	 * @return  bool
	 */
	public function hasEarned()
	{
		return ($this->get('earned') == 1) ? true : false;
	}
}