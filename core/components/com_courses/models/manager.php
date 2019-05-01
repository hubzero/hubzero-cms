<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;

require_once __DIR__ . DS . 'member.php';

/**
 * Manager model class for a course
 */
class Manager extends Member
{
	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'manager';

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
			$this->_tbl->load($uid, $cid, $oid, $sid, 0);
		}
		else if (is_object($uid) || is_array($uid))
		{
			$this->bind($uid);
		}

		if (!$this->get('role_permissions'))
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
	 * Returns a reference to a manager object
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
}
