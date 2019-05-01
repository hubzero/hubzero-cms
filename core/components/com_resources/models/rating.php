<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Hubzero\Database\Relational;

/**
 * Resource rating model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Rating extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource';

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
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'resource_id' => 'positive|nonzero',
		'user_id'     => 'positive|nonzero',
		'rating'      => 'notempty'
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
	 * Defines a belongs to one relationship between resource and audience
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
			->row();
	}
}
