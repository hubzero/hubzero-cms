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
	public $orderBy = 'name';

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
