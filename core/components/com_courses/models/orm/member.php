<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Component;

/**
 * Model class for a member
 */
class Member extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'courses';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'publish_up';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $params = null;

	/**
	 * Get parent course
	 *
	 * @return  object
	 */
	public function course()
	{
		return $this->belongsToOne('course');
	}

	/**
	 * Get role
	 *
	 * @return  object
	 */
	public function role()
	{
		return $this->belongsToOne('role');
	}

	/**
	 * Get user profile object
	 * 
	 * @return object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User');
	}
}
