<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Hubzero\Database\Relational;

include_once __DIR__ . DS . 'mediatracking' . DS. 'detailed.php';

/**
 * Media tracking model
 *
 * @uses  \Hubzero\Database\Relational
 */
class MediaTracking extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'media';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__media_tracking';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

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
		'object_id'   => 'positive|nonzero',
		'object_type' => 'notempty',
		'session_id'  => 'notempty',
		'ip_address'  => 'notempty'
	);

	/**
	 * Get a record by its doi
	 *
	 * @param   integer  $user_id      User ID
	 * @param   integer  $object_id    Object ID
	 * @param   string   $object_type  Object type
	 * @return  object
	 */
	public static function oneByUserAndResource($user_id, $object_id, $object_type = 'resource')
	{
		$row = self::all()
			->whereEquals('user_id', $user_id)
			->whereEquals('object_id', $object_id)
			->whereEquals('object_type', $object_type)
			->row();

		return $row;
	}
}
