<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Log;

use Components\Groups\Tables;
use Components\Groups\Models\Log;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;

// include needed models
require_once dirname(__DIR__) . DS . 'log.php';

/**
 * Group log archive model class
 */
class Archive extends Model
{
	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_logs = null;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// create database object
		$this->_db = \App::get('db');
	}

	/**
	 * Get Instance of Log Archive
	 *
	 * @param   string  $key  Instance Key
	 * @return  object
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
	 * Get a list of logs
	 *
	 * @param   string  $rtrn    What data to return
	 * @param   array   $filters Filters to apply to data retrieval
	 * @param   boolean $boolean Clear cached data?
	 * @return  object
	 */
	public function logs($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'list':
			default:
				if (!($this->_logs instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Log($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Log($result);
						}
					}
					$this->_logs = new ItemList($results);
				}
				return $this->_logs;
			break;
		}
	}
}
