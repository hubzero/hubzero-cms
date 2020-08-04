<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\Mock;

use Hubzero\Database\Relational;

/**
 * User mock model
 *
 * @uses  \Hubzero\Database\Relational
 */
class User extends Relational
{
	/**
	 * Splits name and returns the first part
	 *
	 * @return  string
	 **/
	public function helperGetFirstName()
	{
		return (strpos($this->name, ' ')) ? explode(' ', $this->name)[0] : $this->name;
	}

	/**
	 * Transforms name to a silly nickname
	 *
	 * @return  string
	 **/
	public function transformNickname()
	{
		return $this->getFirstName() . 'er';
	}

	/**
	 * One to many relationship with posts
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function posts()
	{
		return $this->oneToMany('Post');
	}
}
