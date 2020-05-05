<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Models;

use Hubzero\Database\Relational;
use Date;
use User;

/**
 * UsersCategory model
 */
class UsersCategory extends Relational
{
	/**
	 * The table name
	 *
	 * @var  string
	 */
	protected $table = '#__forum_users_categories';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'category_id' => 'notempty',
		'user_id'     => 'notempty'
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
	 * Defines a belongs to one relationship to a category
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->belongsToOne('Components\Forum\Models\Category', 'category_id');
	}

	/**
	 * Defines a belongs to one relationship to a user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}
}
