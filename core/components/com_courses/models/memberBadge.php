<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

require_once __DIR__ . DS . 'base.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'member.badge.php';

/**
 * Courses model class for badges
 */
class MemberBadge extends Base
{
	/**
	 * Table class name
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
