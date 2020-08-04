<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\Mock;

use Hubzero\Database\Relational;

/**
 * Project mock model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Project extends Relational
{
	/**
	 * One shifts to many relationship with members
	 *
	 * @return  \Hubzero\Database\Relationship\OneShiftsToMany
	 **/
	public function members()
	{
		return $this->oneShiftsToMany('Member');
	}

	/**
	 * Many shifts to many relationship with permissions
	 *
	 * @return  \Hubzero\Database\Relationship\ManyShiftsToMany
	 **/
	public function permissions()
	{
		return $this->manyShiftsToMany('Permission');
	}
}
