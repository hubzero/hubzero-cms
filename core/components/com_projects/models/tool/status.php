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
 * Project Tool Status model
 */
class Status extends Model
{
	/**
	 * Table class name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\ToolStatus';

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $config = null;

	/**
	 * Constructor
	 *
	 * @param   integer  $oid
	 * @return  void
	 */
	public function __construct($oid = null)
	{
		$this->_db = \App::get('db');

		if (!isset($this->_tbl))
		{
			$this->_tbl = new Tables\ToolStatus($this->_db);
		}
		if (!isset($this->_statuses))
		{
			$this->_statuses = array();
			$statuses = $this->_tbl->getItems();
			foreach ($statuses as $status)
			{
				$this->_statuses[$status->id] = $status;
			}
		}

		if (is_numeric($oid))
		{
			if (isset($this->_statuses[$oid]))
			{
				$this->_tbl->bind($this->_statuses[$oid]);
			}
			else
			{
				$this->_tbl->load($oid);
			}
		}
	}

	/**
	 * Returns a reference to the model
	 *
	 * @param   mixed  $oid  status ID
	 * @return  void
	 */
	public function getStatus($oid=null)
	{
		if (isset($this->_statuses[$oid]))
		{
			$this->_tbl->bind($this->_statuses[$oid]);
		}
	}

	/**
	 * Returns a reference to the model
	 *
	 * @param   mixed  $oid  status ID
	 * @return  object
	 */
	static function &getInstance($oid=null)
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
}
