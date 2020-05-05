<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Tool SessionClassGroup model
 *
 * @uses \Hubzero\Database\Relational
 */
class SessionClassGroup extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'tool_session_class';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__tool_session_class_groups';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'class_id' => 'positive|nonzero',
		'group_id' => 'positive|nonzero'
	);

	/**
	 * Get relationship to sessionclass
	 *
	 * @return  object
	 */
	public function sessionclass()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\SessionClass', 'class_id', 'id');
	}

	/**
	 * Get relationship to groups
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->oneToOne('Hubzero\User\Group', 'group_id', 'gidNumber');
	}

	/**
	 * Remove records by class_id
	 *
	 * @param   integer  $class_id
	 * @return  bool
	 */
	public static function destroyByClass($class_id)
	{
		$records = self::all()
			->whereEquals('class_id', $class_id)
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

	/**
	 * Remove records by group_id
	 *
	 * @param   integer  $group_id
	 * @return  bool
	 */
	public static function destroyByGroup($group_id)
	{
		$records = self::all()
			->whereEquals('group_id', $group_id)
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
