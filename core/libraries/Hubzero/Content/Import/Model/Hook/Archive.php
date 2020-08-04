<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Model\Hook;

use Hubzero\Base\Obj;
use Hubzero\Content\Import\Model\Hook;

/**
 * Import Hook archive model
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
	 * @param   string  $type
	 * @return  void
	 */
	public function __construct($type = null)
	{
		$this->type = $type;
	}

	/**
	 * Get Instance of Archive
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
	 * Get a count or list of import hooks
	 *
	 * @param   string   $rtrn     What data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   boolean  $boolean  Clear cached data?
	 * @return  mixed
	 */
	public function hooks($rtrn = 'list', $filters = array(), $clear = false)
	{
		$model = Hook::all();

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

		if (isset($filters['event']) && $filters['event'])
		{
			$model->whereEquals('event', $filters['event']);
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
