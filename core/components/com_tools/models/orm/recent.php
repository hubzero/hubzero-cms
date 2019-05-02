<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;
use Date;

/**
 * Tool preferences model
 *
 * @uses \Hubzero\Database\Relational
 */
class Recent extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'tool';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__recent_tools';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'uid'  => 'positive|nonzero',
		'tool' => 'notempty'
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
	 * Get relationship to user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->oneToOne('Hubzero\User\User', 'id', 'uid');
	}

	/**
	 * Get relationship to tool
	 *
	 * @return  object
	 */
	public function tool()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Tool', 'toolname', 'tool');
	}

	/**
	 * Record usage of a tool by a user
	 *
	 * @param   integer  $uid
	 * @param   string   $tool
	 * @return  object
	 */
	public static function hit($uid, $tool)
	{
		// Check if any recent tools are the same as the one just launched
		$records = self::all()
			->whereEquals('uid', $uid)
			->order('created', 'desc')
			->row();

		foreach ($records as $record)
		{
			if ($record->get('tool') == $tool)
			{
				$record->set('created', Date::of('now')->toSql());

				if (!$record->save())
				{
					return false;
				}

				return true;
			}
		}

		// Check if we've reached 5 recent tools or not
		if ($records->count() < 5)
		{
			// Still under 5, so insert a new record
			$recent = self::blank()->set(array(
				'uid'  => $uid,
				'tool' => $tool
			));

			if (!$recent->save())
			{
				return false;
			}

			return true;
		}

		// We reached the limit, so update the oldest entry effectively replacing it
		$recent = $records->last();
		$record->set('tool', $tool);
		$record->set('created', Date::of('now')->toSql());

		if (!$recent->save())
		{
			return false;
		}

		return true;
	}

	/**
	 * Remove records by user ID
	 *
	 * @param   integer  $uid
	 * @return  bool
	 */
	public static function destroyByUser($uid)
	{
		$records = self::all()
			->whereEquals('uid', $uid)
			->rows();

		foreach ($records as $record)
		{
			if (!$record->destroy())
			{
				return false;
			}
		}

		return true;
	}
}
