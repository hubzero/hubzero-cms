<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\Mock;

use Hubzero\Database\Relational;

/**
 * Post mock model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Post extends Relational
{
	/**
	 * Belongs to one relationship with user
	 *
	 * @return  \Hubzero\Database\Relationship\BelongToOne
	 **/
	public function user()
	{
		// Be explicit, otherwise it will find the User facade
		return $this->belongsToOne('Hubzero\Database\Tests\Mock\User');
	}

	/**
	 * Many to many relationship with tags
	 *
	 * @return  \Hubzero\Database\Relationship\ManyToMany
	 **/
	public function tags()
	{
		return $this->manyToMany('Tag');
	}
}
