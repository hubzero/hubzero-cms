<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Item;

use Hubzero\Database\Relational;
use User;

/**
 * Item Announcement
 */
class Watch extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'item';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__item_watch';

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
		'item_id'   => 'positive|nonzero',
		'item_type' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'email',
		'item_type'
	);

	/**
	 * Generates automatic email field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticEmail($data)
	{
		if (!isset($data['email']))
		{
			$data['email'] = User::get('email');
		}

		return $data['email'];
	}

	/**
	 * Generates automatic email field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticItemType($data)
	{
		if (isset($data['item_type']))
		{
			$data['item_type'] = strtolower(preg_replace("/[^a-zA-Z0-9\-]/", '', trim($data['item_type'])));
		}

		return $data['item_type'];
	}

	/**
	 * Defines a belongs to one relationship between article and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Is user watching item?
	 *
	 * @param   integer  $item_id
	 * @param   string   $item_type
	 * @param   integer  $created_by
	 * @return  boolean
	 */
	public static function isWatching($item_id, $item_type, $created_by)
	{
		if ($item_id && $item_type && $created_by)
		{
			$total = self::all()
				->whereEquals('state', 1)
				->whereEquals('created_by', (int)$created_by)
				->whereEquals('item_id', (int)$item_id)
				->whereEquals('item_type', (string)$item_type)
				->total();

			if ($total)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Load a record by scope and scope ID
	 *
	 * @param   integer  $item_id
	 * @param   string   $item_type
	 * @param   integer  $created_by
	 * @param   string   $email
	 * @return  object
	 */
	public static function oneByScope($item_id, $item_type, $created_by = 0, $email = null)
	{
		$model = self::all()
			->whereEquals('item_id', (int)$item_id)
			->whereEquals('item_type', (string)$item_type);

		if ($created_by)
		{
			$model->whereEquals('created_by', (int)$created_by);
		}

		if ($email)
		{
			$model->whereEquals('email', (string)$email);
		}

		return $model->row();
	}
}
