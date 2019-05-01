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
 * Project Tool Instance model
 */
class Instance extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\ToolInstance';

	/**
	 * Registry
	 *
	 * @var object
	 */
	public $config = null;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct($oid, $parent = null)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\ToolInstance($this->_db);

		if ($oid && $oid != 'dev')
		{
			if (is_numeric($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_string($oid))
			{
				$this->_tbl->loadFromInstanceName($oid);
			}
			else if (is_object($oid))
			{
				$this->bind($oid);
			}
		}
		elseif ($parent)
		{
			// Load dev instance
			$this->_tbl->loadFromParent($parent, 'dev');
		}

		$this->params = new \Hubzero\Config\Registry($this->_tbl->get('params'));
	}

	/**
	 * Returns a reference to the model
	 *
	 * @param      mixed $oid object ID
	 * @return     object Todo
	 */
	static function &getInstance($oid=null, $parent=null)
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
			$instances[$key] = new self($oid, $parent);
		}

		return $instances[$key];
	}

	/**
	 * Update Parent Name
	 *
	 * @param      integer $id
	 * @param      string $name
	 * @return     boolean
	 */
	public function updateParentName($id=null, $name=null)
	{
		if (!$id || !$name)
		{
			return false;
		}
		return $this->_tbl->updateParentName($id, $name);
	}
}
