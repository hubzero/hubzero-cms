<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Module;

use Components\Groups\Tables;
use Components\Groups\Models\Module;
use Hubzero\Base\Obj;
use Hubzero\Base\Model\ItemList;

// include needed models
require_once dirname(__DIR__) . DS . 'module.php';
require_once __DIR__ . DS . 'menu.php';

/**
 * Group module archive model class
 */
class Archive extends Obj
{
	/**
	 * \Hubzero\Base\Model
	 *
	 * @var object
	 */
	private $_modules = null;

	/**
	 * Modules count
	 *
	 * @var integer
	 */
	private $_modules_count = null;

	/**
	 * Database
	 *
	 * @var object
	 */
	private $_db = null;

	/**
	 * Registry
	 *
	 * @var object
	 */
	private $_config;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_db = \App::get('db');
	}

	/**
	 * Get Instance of Module Archive
	 *
	 * @param   string $key Instance Key
	 * @return  object \Components\Groups\Models\Module\Archive
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self();
		}

		return $instances[$key];
	}

	/**
	 * Get a list of group modules
	 *
	 * @param   string  $rtrn    What data to return
	 * @param   array   $filters Filters to apply to data retrieval
	 * @param   boolean $boolean Clear cached data?
	 * @return  mixed
	 */
	public function modules($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'unapproved':
				$unapproved = array();
				if ($results = $this->modules('list', $filters, true))
				{
					foreach ($results as $k => $result)
					{
						// if module is unapproved return it
						if ($result->get('approved') == 0)
						{
							$unapproved[] = $result;
						}
					}
				}
				return new ItemList($unapproved);
			break;
			case 'list':
			default:
				$tbl = new Tables\Module($this->_db);
				if ($results = $tbl->find( $filters ))
				{
					foreach ($results as $key => $result)
					{
						$results[$key] = new Module($result);
					}
				}
				$this->_modules = new ItemList($results);
				return $this->_modules;
			break;
		}
	}
}
