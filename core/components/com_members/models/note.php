<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models;

use Hubzero\Database\Relational;

include_once __DIR__ . DS . 'note' . DS . 'category.php';

/**
 * User note model
 */
class Note extends Relational
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
		'user_id' => 'positive|nonzero',
		'body'    => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created_user_id',
		'created_time'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'modified_time',
		'modified_user_id'
	);

	/**
	 * Get parent member
	 *
	 * @return  object
	 */
	public function member()
	{
		return $this->belongsToOne('Components\Members\Models\Member', 'user_id');
	}

	/**
	 * Get parent category
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->belongsToOne('Components\Members\Models\Note\Category', 'catid');
	}

	/**
	 * Generates automatic created field value
	 *
	 * @return  string
	 */
	public function automaticCreatedTime()
	{
		return Date::of('now')->toSql();
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticCreatedUserId()
	{
		return User::get('id');
	}

	/**
	 * Generates automatic created field value
	 *
	 * @return  string
	 */
	public function automaticModifiedTime()
	{
		return Date::of('now')->toSql();
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedUserId()
	{
		return User::get('id');
	}
}
