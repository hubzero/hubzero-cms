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

use Components\Courses\Tables;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'member.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'role.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'memberBadge.php');
require_once(__DIR__ . DS . 'prerequisite.php');

/**
 * Member model class for a course
 */
class Member extends Base
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\Member';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'manager';

	/**
	 * \Components\Courses\Models\MemberBadge
	 *
	 * @var object
	 */
	private $_badge = NULL;

	/**
	 * \Components\Courses\Models\Prerequisites
	 *
	 * @var object
	 **/
	private $_prerequisites = null;

	/**
	 * Constructor
	 *
	 * @param   string $uid User ID
	 * @param   string $cid Course ID
	 * @param   string $oid Offering ID
	 * @param   string $sid Section ID
	 * @return  void
	 */
	public function __construct($uid, $cid=0, $oid=0, $sid=0)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\Member($this->_db);

		if (is_numeric($uid) || is_string($uid))
		{
			$this->_tbl->load($uid, $cid, $oid, $sid);
		}
		else if (is_object($uid) || is_array($uid))
		{
			$this->bind($uid);
		}

		if (!$this->get('role_alias'))
		{
			$result = new Tables\Role($this->_db);
			if ($result->load($this->get('role_id')))
			{
				foreach ($result->getProperties() as $key => $property)
				{
					$this->_tbl->set('__role_' . $key, $property);
				}
			}
		}
	}

	/**
	 * Returns a reference to a member object
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

		if (!isset($instances[$oid . '_' . $uid . '_' . $sid]))
		{
			$instances[$oid . '_' . $uid . '_' . $sid] = new self($uid, $cid, $oid, $sid);
		}

		return $instances[$oid . '_' . $uid . '_' . $sid];
	}

	/**
	 * Get member badge
	 *
	 * @return  object \Components\Courses\Models\MemberBadge
	 */
	public function badge()
	{
		if (!isset($this->_badge))
		{
			$this->_badge = MemberBadge::loadByMemberId($this->get('id'));
		}

		return $this->_badge;
	}

	/**
	 * Get courses prerequisites per member
	 *
	 * @param   object $gradebook
	 * @return  object \Components\Courses\Models\Prerequisite
	 */
	public function prerequisites($gradebook)
	{
		if (!isset($this->_prerequisites))
		{
			$this->_prerequisites = new Prerequisite($this->get('section_id'), $gradebook, $this->get('id'));
		}

		return $this->_prerequisites;
	}

	/**
	 * Delete an entry and associated data
	 *
	 * @return  boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove gradebook information

		return parent::delete();
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string  $action Action to check
	 * @param   string  $item   Item type to check action against
	 * @return  boolean True if authorized, false if not
	 */
	public function access($action='', $item='offering')
	{
		if (!$action)
		{
			return $this->get('permissions');
		}
		return $this->get('permissions')->get('access-' . strtolower($action) . '-' . $item);
	}

	/**
	 * Get a unique token, generating one if it doesn't exist
	 *
	 * @return  string
	 */
	public function token()
	{
		if (!$this->get('token'))
		{
			$this->set('token', $this->generateToken());
			$this->store(false);
		}

		return $this->get('token');
	}

	/**
	 * Generate a unique token
	 *
	 * @return  string
	 */
	public function generateToken()
	{
		$chars = array(0,1,2,3,4,5,6,7,8,9); //,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$sn = '';
		$max = count($chars)-1;
		for ($i=0;$i<20;$i++)
		{
			$sn .= (!($i % 5) && $i ? '-' : '') . $chars[rand(0, $max)];
		}

		if ($this->_tbl->tokenExists($sn))
		{
			return $this->generateToken();
		}

		return $sn;
	}
}

