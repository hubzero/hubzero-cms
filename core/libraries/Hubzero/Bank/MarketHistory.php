<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Bank;

use Hubzero\Database\Relational;

/**
 * Market History class:
 * Logs batch transactions, royalty distributions and other big transactions
 */
class MarketHistory extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'market';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__market_history';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'category' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'date'
	);

	/**
	 * Generates automatic date value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticDate($data)
	{
		if (!isset($data['date']))
		{
			$dt = new \Hubzero\Utility\Date('now');

			$data['date'] = $dt->toSql();
		}

		return $data['date'];
	}

	/**
	 * Get the ID of a record matching the data passed
	 *
	 * @param   mixed    $itemid    Integer
	 * @param   string   $action    Transaction type
	 * @param   string   $category  Transaction category
	 * @param   string   $created   Transaction date
	 * @param   string   $log       Transaction log
	 * @return  integer
	 */
	public static function getRecord($itemid=0, $action='', $category='', $created='', $log = '')
	{
		$model = self::all()
			->select('id');

		if ($category)
		{
			$model->whereEquals('category', $category);
		}

		if ($itemid)
		{
			$model->whereEquals('itemid', $itemid);
		}

		if ($action)
		{
			$model->whereEquals('action', $action);
		}

		if ($created)
		{
			$model->whereLike('date', $created . '%');
		}

		if ($log)
		{
			$model->whereEquals('log', $log);
		}

		$row = $model->row();

		return $row->get('id');
	}
}
