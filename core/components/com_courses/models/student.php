<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Models\Member;

require_once __DIR__ . DS . 'member.php';

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
