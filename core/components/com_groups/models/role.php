<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\User\Group;

include_once __DIR__ . DS . 'member' . DS . 'role.php';

/**
 * Group role
 */
class Role extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'xgroups';

	/**
	 * Default order by for model
	 *
	 * @var string
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
		'name'      => 'notempty',
		'gidNumber' => 'positive|nonzero'
	);

	/**
	 * A group's roles in the order its managers have set
	 *
	 * Roles never reordered all carry 0 and so list by name.
	 *
	 * @param   int  $gidNumber
	 * @return  \Hubzero\Database\Relational
	 */
	public static function forGroup($gidNumber)
	{
		return self::all()
			->whereEquals('gidNumber', (int) $gidNumber)
			->order('ordering', 'asc')
			->order('name', 'asc');
	}

	/**
	 * Store a new order for a group's roles, in one statement
	 *
	 * Ids that do not belong to the group are ignored, and roles of the
	 * group left out of the list keep their ordering.
	 *
	 * @param   int    $gidNumber
	 * @param   array  $ids        Role ids, first to last
	 * @return  bool
	 */
	public static function saveOrder($gidNumber, array $ids)
	{
		$ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

		if (!$ids)
		{
			return false;
		}

		$db = \App::get('db');

		$cases = '';
		foreach ($ids as $position => $id)
		{
			$cases .= ' WHEN ' . $id . ' THEN ' . ($position + 1);
		}

		$db->setQuery(
			"UPDATE `#__xgroups_roles` SET `ordering` = CASE `id`" . $cases . " ELSE `ordering` END" .
			" WHERE `gidNumber` = " . (int) $gidNumber . " AND `id` IN (" . implode(',', $ids) . ")"
		);

		return (bool) $db->query();
	}

	/**
	 * Get parent group
	 *
	 * @return  object
	 */
	public function group()
	{
		return Group::getInstance($this->get('gidNumber'));
	}

	/**
	 * Get group permissions
	 *
	 * @return  object
	 */
	public function transformPermissions()
	{
		$registry = new Registry($this->get('permissions'));
		$registry->separator = '/';
		return $registry;
	}

	/**
	 * Get a list of members
	 *
	 * @return  object
	 */
	public function members()
	{
		return $this->oneToMany('\Components\Groups\Models\Member\Role', 'roleid');
	}

	/**
	 * Save the record
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		if (!is_string($this->get('permissions')))
		{
			$this->set('permissions', json_encode($this->get('permissions')));
		}

		// A new role goes to the end of its group's list rather than jumping
		// ahead of roles a manager has already put in order
		if ($this->isNew() && !$this->get('ordering'))
		{
			$last = self::blank()
				->whereEquals('gidNumber', (int) $this->get('gidNumber'))
				->order('ordering', 'desc')
				->limit(1)
				->row();

			$this->set('ordering', (int) $last->get('ordering') + 1);
		}

		return parent::save();
	}

	/**
	 * Delete record and associated content
	 *
	 * @return  object
	 */
	public function destroy()
	{
		foreach ($this->members()->rows() as $member)
		{
			if (!$member->destroy())
			{
				$this->addError($member->getError());
				return false;
			}
		}

		return parent::destroy();
	}
}
