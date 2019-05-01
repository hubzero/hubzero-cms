<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Section;

use Components\Courses\Models\Base;
use Components\Courses\Tables;
use User;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'section.code.php';
require_once dirname(__DIR__) . DS . 'base.php';

/**
 * Courses model class for a course
 */
class Code extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\SectionCode';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'section_code';

	/**
	 * User
	 *
	 * @var object
	 */
	private $_redeemer = null;

	/**
	 * Constructor
	 *
	 * @param      integer $id Course offering ID or alias
	 * @return     void
	 */
	public function __construct($oid=null, $section_id=null)
	{
		$this->_db = \App::get('db');

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (is_numeric($oid) || is_string($oid))
			{
				$this->_tbl->load($oid, $section_id);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to a course offering model
	 *
	 * This method must be invoked as:
	 *     $offering = \Components\Courses\Models\Offering::getInstance($alias);
	 *
	 * @param      mixed $oid ID (int) or alias (string)
	 * @return     object \Components\Courses\Models\Offering
	 */
	static function &getInstance($oid=null, $section_id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = 0;

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid . ($section_id ? '_' . $section_id : '');
		}
		else if (is_object($oid))
		{
			$key = $oid->get('id') . ($section_id ? '_' . $section_id : '');
		}
		else if (is_array($oid))
		{
			$key = $oid['id'] . ($section_id ? '_' . $section_id : '');
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $section_id);
		}

		return $instances[$key];
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @param   string  $property
	 * @return  mixed
	 */
	public function redeemer($property=null)
	{
		if (!$this->_redeemer)
		{
			$this->_redeemer = User::getInstance($this->get('redeemed_by'));
		}
		if ($property)
		{
			return $this->_redeemer->get($property);
		}
		return $this->_redeemer;
	}

	/**
	 * Check if a code has expired
	 *
	 * @return  bool
	 */
	public function isExpired()
	{
		if (!$this->exists())
		{
			return false;
		}

		if ($this->isRedeemed())
		{
			return true;
		}

		$now = \Date::of('now')->toSql();

		if ($this->get('expires')
		 && $this->get('expires') != $this->_db->getNullDate()
		 && $this->get('expires') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if a code has been redeemed
	 *
	 * @return  bool
	 */
	public function isRedeemed()
	{
		if (!$this->exists())
		{
			return false;
		}
		if ($this->get('redeemed_by'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Generate a coupon code
	 *
	 * @param   integer  $redeemed_by
	 * @param   string   $code
	 * @return  bool
	 */
	public function redeem($redeemed_by=0, $code=null)
	{
		if (!$code)
		{
			$code = $this->get('code');
		}
		if (!$redeemed_by)
		{
			$redeemed_by = User::get('id');
		}
		$this->set('redeemed_by', $redeemed_by);
		$this->set('redeemed', \Date::of('now')->toSql());
		return $this->store();
	}
}
