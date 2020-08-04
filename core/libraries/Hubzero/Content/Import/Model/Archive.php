<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Model;

use Hubzero\Base\Obj;

/**
 * Import archive model
 */
class Archive extends Obj
{
	/**
	 * Type
	 *
	 * @var  string
	 */
	private $type = null;

	/**
	 * Constructor
	 *
	 * @param   object  $db
	 * @return  void
	 */
	public function __construct($type = null)
	{
		$this->type = $type;
	}

	/**
	 * Get Instance of Page Archive
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
			$instances[$key] = new static($key);
		}

		return $instances[$key];
	}

	/**
	 * Get a list or count of imports
	 *
	 * @param   string   $rtrn     What data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   boolean  $boolean  Clear cached data?
	 * @return  mixed
	 */
	public function imports($rtrn = 'list', $filters = array(), $clear = false)
	{
		$model = Import::all();

		if (isset($filters['state']) && $filters['state'])
		{
			if (!is_array($filters['state']))
			{
				$filters['state'] = array($filters['state']);
			}
			$filters['state'] = array_map('intval', $filters['state']);

			$model->whereIn('state', $filters['state']);
		}

		if (!isset($filters['type']))
		{
			$filters['type'] = $this->type;
		}

		if (isset($filters['type']) && $filters['type'])
		{
			$model->whereEquals('type', $filters['type']);
		}

		if (isset($filters['created_by']) && $filters['created_by'] >= 0)
		{
			$model->whereEquals('created_by', $filters['created_by']);
		}

		if (strtolower($rtrn) == 'count')
		{
			return $model->total();
		}

		return $model->ordered()
			->paginated()
			->rows();
	}
}
