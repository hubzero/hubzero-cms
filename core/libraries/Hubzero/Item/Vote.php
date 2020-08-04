<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Item;

use Hubzero\Database\Relational;
use Hubzero\Utility\Validate;
use Lang;

/**
 * Model for votes
 */
class Vote extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'item';

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
		'item_type' => 'notempty',
		'vote'      => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'vote',
		'item_type'
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
	 * Runs extra setup code when creating a new model
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('ip', function($data)
		{
			if (isset($data['ip']) && !Validate::ip($data['ip']))
			{
				return Lang::txt('Invalid IP address');
			}

			return false;
		});
	}

	/**
	 * Generates automatic item type value
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
	 * Generates automatic vote value
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticVote($data)
	{
		if (!isset($data['vote']))
		{
			$data['vote'] = 1;
		}

		switch ($data['vote'])
		{
			case 'no':
			case 'down':
			case 'dislike':
			case 'negative':
			case 'minus':
			case '-':
			case '-1':
			case -1:
				$data['vote'] = -1;
			break;

			case 'yes':
			case 'up':
			case 'like':
			case 'positive':
			case 'plus':
			case '+':
			case '1':
			case 1:
			default:
				$data['vote'] = 1;
			break;
		}

		return $data['vote'];
	}

	/**
	 * Defines a belongs to one relationship between entry and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function voter()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Load a record by scope and scope ID
	 *
	 * @param   integer  $item_id     Item type
	 * @param   string   $item_type   Item ID
	 * @param   integer  $created_by  User ID
	 * @param   string   $ip          IP address
	 * @return  object
	 */
	public static function oneByScope($item_id, $item_type, $created_by = 0, $ip = null)
	{
		$model = self::all()
			->whereEquals('item_id', (int)$item_id)
			->whereEquals('item_type', (string)$item_type);

		if ($created_by)
		{
			$model->whereEquals('created_by', (int)$created_by);
		}

		if ($ip)
		{
			$model->whereEquals('ip', $ip);
		}

		return $model->order('created', 'desc')->row();
	}

	/**
	 * Check if a user has voted
	 *
	 * @param   integer  $item_type  Item type
	 * @param   integer  $item_id    Item ID
	 * @param   integer  $user_id    User ID
	 * @param   string   $ip         IP address
	 * @return  integer
	 */
	public function hasVoted($item_type, $item_id, $user_id=null, $ip=null)
	{
		return self::oneByScope($item_type, $item_id, $user_id, $ip)->get('id');
	}
}
