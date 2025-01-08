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
class AclUser extends Relational
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
	protected $table = '#__resource_acl_user';

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
		'user_id'     => 'positive|nonzero',
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
	public static function oneByResourceAndUser($resource_id, $user_id)
	{
		return self::all()
			->whereEquals('resource_id', $resource_id)
			->whereEquals('user_id', $user_id)
			->join('#__users','user_id','#__users.id')
			->row();
	}

	public static function listUsers($resource_id)
	{
		$database = App::get('db');
		$sql = "SELECT r.id AS resource_id, u.id AS user_id, u.name, u.givenName, u.middleName, u.surname, u.username FROM `#__resources` AS r, `#__resource_acl_user` AS a, `#__users` AS u WHERE r.id=? AND a.resource_id=r.id AND a.user_id=u.id;";
		$database->prepare($sql)->bind( array($resource_id) );
		$aclusers = $database->loadObjectList();
		return $aclusers;
	}

	public static function removeUsers($resource_id, $list)
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
			$sql = "DELETE FROM #__resource_acl_user WHERE resource_id = ? AND user_id IN (" . implode(',', $binds) . ");";
			$database->prepare($sql)->bind( $values );
			$database->execute();
		}

		return;
	}

	public static function addUsers($resource_id, $list)
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
			$sql = "INSERT INTO #__resource_acl_user (resource_id, user_id) VALUES " . implode(',', $values) . ";";
			$database->setQuery($sql);
			$database->execute();
		}

		return;
	}
}
