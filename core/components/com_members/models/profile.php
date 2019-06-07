<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models;

use Hubzero\Database\Relational;

/**
 * User profile model
 */
class Profile extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'user';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

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
		'profile_key' => 'notempty',
		'user_id'     => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ordering',
		'access'
	);

	/**
	 * Get parent member
	 *
	 * @return  object
	 */
	public function member()
	{
		return $this->belongsToOne('Member', 'user_id');
	}

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrdering($data)
	{
		if (!isset($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->whereEquals('user_id', $this->get('user_id'))
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Generates automatic access field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAccess($data)
	{
		if (!isset($data['access']) || !$data['access'])
		{
			$data['access'] = 1;
		}

		return $data['access'];
	}

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   string   $profile_key
	 * @param   integer  $user_id
	 * @return  object
	 */
	public static function oneByKeyAndUser($profile_key, $user_id)
	{
		return self::all()
			->whereEquals('profile_key', $profile_key)
			->whereEquals('user_id', $user_id)
			->row();
	}

	/**
	 * Helper method to collect multi-value fields
	 *
	 * @param   mixed
	 * @return  array
	 */
	public static function collect($data)
	{
		$arr = array();

		foreach ($data as $profile)
		{
			if (!isset($arr[$profile->get('profile_key')]))
			{
				$arr[$profile->get('profile_key')] = $profile->get('profile_value');
			}
			else
			{
				$values = $arr[$profile->get('profile_key')];
				if (!is_array($values))
				{
					$values = array($values);
				}
				$values[] = $profile->get('profile_value');

				$arr[$profile->get('profile_key')] = $values;
			}
		}

		return $arr;
	}
}
