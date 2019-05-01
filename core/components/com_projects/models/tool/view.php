<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Tool;

use Hubzero\Base\Model;
use Components\Projects\Tables;

/**
 * Project Tool View model
 */
class View extends Model
{
	/**
	 * Table class name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\ToolView';

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $config = null;

	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  view ID
	 * @return  void
	 */
	public function __construct($oid = null)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\ToolView($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
	}

	/**
	 * Returns a reference to the model
	 *
	 * @param   mixed  $oid  view ID
	 * @return  object
	 */
	public static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}

	/**
	 * Check if page was viewed recently
	 *
	 * @param   integer  $toolid  Project tool id
	 * @param   integer  $userid  User id
	 * @return  mixed    Return string or NULL
	 */
	public function lastView($toolid = 0, $userid = 0)
	{
		return $this->_tbl->checkView($toolid, $userid);
	}
}
