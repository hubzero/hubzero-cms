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

namespace Components\Courses\Models;

use Components\Courses\Models\Member;

require_once(__DIR__ . DS . 'member.php');

/**
 * Courses model class for a student
 */
class Student extends Member
{
	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'student';

	/**
	 * Constructor
	 *
	 * @param   string $uid User ID
	 * @param   string $cid Course ID
	 * @param   string $oid Offering ID
	 * @param   string $sid Section ID
	 * @return  void
	 */
	public function __construct($uid, $cid=null, $oid=null, $sid=null)
	{
		$this->_db = \App::get('db');

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (is_numeric($uid) || is_string($uid))
			{
				if ($sid !== null)
				{
					$this->_tbl->loadBySection($uid, $sid);
				}
				else if ($cid !== null)
				{
					$this->_tbl->load($uid, $cid, null, null, 1);
				}
				else
				{
					$this->_tbl->load($uid);
				}
			}
			else if (is_object($uid) || is_array($uid))
			{
				$this->bind($uid);
			}
		}
	}

	/**
	 * Returns a reference to a student object
	 *
	 * @param   string $uid User ID
	 * @param   string $cid Course ID
	 * @param   string $oid Offering ID
	 * @param   string $sid Section ID
	 * @return  object \Components\Courses\Models\Member
	 */
	static function &getInstance($uid=null, $cid=0, $oid=0, $sid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid . '_' . $uid]))
		{
			$instances[$oid . '_' . $uid] = new self($uid, $cid, $oid, $sid);
		}

		return $instances[$oid . '_' . $uid];
	}

	/**
	 * Get the gradebook
	 *
	 * @return  boolean True on success, false on error
	 */
	public function gradebook()
	{
		return true;
	}
}

