<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Access;

use Hubzero\Database\Relational;

/**
 * User/Group map
 */
class Map extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'user_usergroup';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__user_usergroup_map';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'user_id';

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
		'user_id'  => 'positive|nonzero',
		'group_id' => 'positive|nonzero'
	);

	/**
	 * Defines a relationship to the User/Group Map
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Defines a relationship to the User/Group Map
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->belongsToOne('Hubzero\Access\Group', 'group_id');
	}

	/**
	 * Delete this object and its dependencies
	 *
	 * @return  boolean
	 */
	public function destroy()
	{
		$query = $this->getQuery()
			->delete($this->getTableName())
			->whereEquals('group_id', $this->get('group_id'))
			->whereEquals('user_id', $this->get('user_id'));

		if (!$query->execute())
		{
			$this->addError($query->getError());
			return false;
		}

		return true;
	}

	/**
	 * Delete objects of this type by Access Group ID
	 *
	 * @param   mixed    $group_id  Integer or array of integers
	 * @return  boolean
	 */
	public static function destroyByGroup($group_id)
	{
		$group_id = (is_array($group_id) ? $group_id : array($group_id));

		$blank = self::blank();

		$query = $blank->getQuery()
			->delete($blank->getTableName())
			->whereIn('group_id', $group_id);

		if (!$query->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * Delete objects of this type by User ID
	 *
	 * @param   mixed    $user_id  Integer or array of integers
	 * @return  boolean
	 */
	public static function destroyByUser($user_id)
	{
		$user_id = (is_array($user_id) ? $user_id : array($user_id));

		$blank = self::blank();

		$query = $blank->getQuery()
			->delete($blank->getTableName())
			->whereIn('user_id', $user_id);

		if (!$query->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * Add a user to access groups
	 *
	 * @param   mixed    $user_id   Integer
	 * @param   mixed    $group_id  Integer or array of integers
	 * @return  boolean
	 */
	public static function addUserToGroup($user_id, $group_id)
	{
		// Get the user's existing entries
		$entries = self::all()
			->whereEquals('user_id', $user_id)
			->rows();

		$existing = array();
		foreach ($entries as $entry)
		{
			$existing[] = $entry->get('group_id');
		}

		$group_id = (is_array($group_id) ? $group_id : array($group_id));

		$blank = self::blank();

		// Loop through groups to be added
		foreach ($group_id as $group)
		{
			$group = intval($group);

			// Is the group already an existing entry?
			if (in_array($group, $existing))
			{
				// Skip.
				continue;
			}

			$query = $blank->getQuery()
				->insert($blank->getTableName())
				->values(array(
					'user_id'  => $user_id,
					'group_id' => $group
				));

			if (!$query->execute())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove a user from an access groups
	 *
	 * @param   mixed    $user_id   Integer
	 * @param   mixed    $group_id  Integer or array of integers
	 * @return  boolean
	 */
	public static function removeUserFromGroup($user_id, $group_id)
	{
		$group_id = (is_array($group_id) ? $group_id : array($group_id));

		$blank = self::blank();

		$query = $blank->getQuery()
			->delete($blank->getTableName())
			->whereEquals('user_id', $user_id)
			->whereIn('group_id', $group_id);

		if (!$query->execute())
		{
			return false;
		}

		return true;
	}
}
