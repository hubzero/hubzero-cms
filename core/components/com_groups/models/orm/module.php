<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm;

use Hubzero\Database\Relational;
use Date;
use User;

/**
 * Group module model
 */
class Module extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'xgroups';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'lft';

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
		'gidNumber' => 'positive|nonzero',
		'title'     => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'modified',
		'modified_by'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Generates automatic created field value
	 *
	 * @return  string
	 */
	public function automaticModified()
	{
		return Date::of('now')->toSql();
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy()
	{
		return User::get('id');
	}

	/**
	 * Defines a belongs to one relationship between category and creator
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Defines a belongs to one relationship between category and modifier
	 *
	 * @return  object
	 */
	public function modifier()
	{
		return $this->belongsToOne('Hubzero\User\User', 'modified_by');
	}

	/**
	 * Get parent group
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Group', 'gidNumber');
	}
}
