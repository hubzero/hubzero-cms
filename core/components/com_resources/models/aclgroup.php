<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Hubzero\Database\Relational;
use Components\Members\Models\Member;

require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

/**
 * Resource license model
 *
 * @uses  \Hubzero\Database\Relational
 */
class AclGroup extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__resource_acl_group';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'resource_id';

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
		'resource_id' => 'positive|nonzero',
		'group_id'    => 'positive|nonzero',
	);

	/**
	 * Defines a belongs to one relationship between resource and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function resource()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Entry', 'resource_id');
	}

	/**
	 * Load a record by resource and user IDs
	 *
	 * @param   integer  $resource_id  Resource ID
	 * @param   integer  $user_id      User ID
	 * @return  object
	 */
	public static function oneByResourceAndUser($resource_id, $group_id)
	{
		return self::all()
			->whereEquals('resource_id', $resource_id)
			->whereEquals('group_id', $group_id)
			->join('#__users','user_id','#__users.id')
			->row();
	}

	public static function listGroups($resource_id)
	{
		$database = App::get('db');
		$sql = "SELECT r.id AS resource_id, g.gidNumber AS group_id, g.cn AS name FROM `#__resources` AS r, `#__resource_acl_group` AS a, `#__xgroups` AS g WHERE r.id=? AND a.resource_id=r.id AND g.gidNumber=a.group_id;";
		$database->prepare($sql)->bind( array($resource_id) );
		$aclgroups = $database->loadObjectList();
		return $aclgroups;
	}

	public static function removeGroups($resource_id, $list)
	{
		$database = App::get('db');
		$binds = array();
		$values = array();

		$resource_id = intval($resource_id);

		if ($resource_id <= 0)
			return;

		$values[] = $resource_id;

		foreach($list as $item)
		{
			$item = intval($item);

			if ($item <= 0)
				continue;

			$binds[] = '?';
			$values[] = $item;
		}

		if ($binds !== array())
		{
			$sql = "DELETE FROM #__resource_acl_group WHERE resource_id = ? AND group_id IN (" . implode(',', $binds) . ");";
			$database->prepare($sql)->bind( $values );
			$database->execute();
		}

		return;
	}

	public static function addGroups($resource_id, $list)
	{
		$database = App::get('db');
		$values = array();

		$resource_id = intval($resource_id);

		if ($resource_id <= 0)
			return;

		foreach($list as $item)
		{
			$item = intval($item);

			if ($item <= 0)
				continue;

			$values[] = '(' . $resource_id . ',' . $item . ')'; 
		}

		if ($values !== array())
		{
			$sql = "INSERT INTO #__resource_acl_group (resource_id, group_id) VALUES " . implode(',', $values) . ";";
			$database->setQuery($sql);
			$database->execute();
		}

		return;
	}


}
