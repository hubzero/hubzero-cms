<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Bank;

use Hubzero\Database\Relational;

/**
 * Table class for bak transactions
 */
class Transaction extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'users';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__users_transactions';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'uid'      => 'positive|nonzero',
		'type'     => 'notempty',
		'category' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * Get a history of transactions for a user
	 *
	 * @param   integer  $limit  Number of records to return
	 * @param   integer  $uid    User ID
	 * @return  mixed    False if errors, array on success
	 */
	public static function history($limit=50, $uid=null)
	{
		$model = self::all();

		if ($limit)
		{
			$model->limit((int)$limit);
		}

		if ($uid)
		{
			$model->whereEquals('uid', $uid);
		}

		return $model->order('created', 'desc')->rows();
	}

	/**
	 * Delete records for a given category, type, and reference combination
	 *
	 * @param   string   $category     Transaction category (royalties, etc)
	 * @param   string   $type         Transaction type (deposit, withdraw, etc)
	 * @param   integer  $referenceid  Reference ID (resource ID, etc)
	 * @param   integer  $uid          User ID
	 * @return  boolean  False if errors, True on success
	 */
	public static function deleteRecords($category=null, $type=null, $referenceid=null, $uid=null)
	{
		$model = self::all();

		if ($category)
		{
			$model->whereEquals('category', $category);
		}

		if ($type)
		{
			$model->whereEquals('type', $type);
		}

		if ($referenceid)
		{
			$model->whereEquals('referenceid', $referenceid);
		}

		if ($uid)
		{
			$model->whereEquals('uid', $uid);
		}

		foreach ($model->rows() as $row)
		{
			if (!$row->destroy())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Get get the transaction amount for a category, type, reference item and, optionally, user
	 *
	 * @param   string   $category     Transaction category (royalties, etc)
	 * @param   string   $type         Transaction type (deposit, withdraw, etc)
	 * @param   integer  $referenceid  Reference ID (resource ID, etc)
	 * @param   integer  $uid          User ID
	 * @return  integer
	 */
	public static function getAmount($category=null, $type=null, $referenceid=null, $uid=null)
	{
		$model = self::all()
			->select('amount');

		if ($category)
		{
			$model->whereEquals('category', $category);
		}

		if ($type)
		{
			$model->whereEquals('type', $type);
		}

		if ($referenceid)
		{
			$model->whereEquals('referenceid', $referenceid);
		}

		if ($uid)
		{
			$model->whereEquals('uid', $uid);
		}

		$row = $model->row();

		return $row->amount;
	}

	/**
	 * Get a point total/average for a combination of category, type, user, etc.
	 *
	 * @param   string   $category     Transaction category (royalties, etc)
	 * @param   string   $type         Transaction type (deposit, withdraw, etc)
	 * @param   integer  $referenceid  Reference ID (resource ID, etc)
	 * @param   integer  $royalty      If getting royalties
	 * @param   string   $action       Action to filter by (asked, answered, misc)
	 * @param   integer  $uid          User ID
	 * @param   integer  $allusers     Get total for all users?
	 * @param   string   $when         Datetime filter
	 * @param   integer  $calc         How total is calculated (record sum, avg, record count)
	 * @return  integer
	 */
	public static function getTotals($category=null, $type=null, $referenceid=null, $royalty=0, $action=null, $uid=null, $allusers = 0, $when=null, $calc=0)
	{
		$model = self::all();

		if ($calc == 0)
		{
			$model->select("SUM(amount) AS total");
		}
		else if ($calc == 1)
		{
			// average
			$model->select("AVG(amount) AS total");
		}
		else if ($calc == 2)
		{
			// num of transactions
			$model->select("COUNT(*) AS total");
		}

		if ($category)
		{
			$model->whereEquals('category', $category);
		}

		if ($type)
		{
			$model->whereEquals('type', $type);
		}

		if ($referenceid)
		{
			$model->whereEquals('referenceid', $referenceid);
		}

		if ($royalty)
		{
			$model->whereLike('description', 'Royalty payment%');
		}

		if ($action == 'asked')
		{
			$model->whereLike('description', '%posting question%');
		}
		else if ($action == 'answered')
		{
			$model->whereLike('description', '%answering question%');// OR description like 'Answer for question%' OR description like 'Answered question%') ";
		}
		else if ($action == 'misc')
		{
			$model->where('description', 'NOT LIKE', '%posting question%');
			$model->where('description', 'NOT LIKE', '%answering question%');
			$model->where('description', 'NOT LIKE', 'Answer for question%');
			$model->where('description', 'NOT LIKE', 'Answered question%');
		}

		if (!$allusers)
		{
			if ($uid)
			{
				$model->whereEquals('uid', $uid);
			}
		}

		if ($when)
		{
			$model->whereLike('created', $when . '%');
		}

		$row = $model->row();

		return $row->get('total', 0);
	}
}
