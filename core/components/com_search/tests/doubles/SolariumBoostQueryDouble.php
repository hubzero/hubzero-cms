<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Doubles;

/**
 * Stand-in for SolariumBoostQuery, used both as a query and as its own factory
 *
 * one() is static on the real class and reached through MockProxy, which turns
 * the static call into an instance one. PHPUnit will not stub a static method,
 * so both methods are declared here in instance form.
 */
class SolariumBoostQueryDouble
{
	/**
	 * Factory call, stubbed by the tests
	 *
	 * @param   array  $args
	 * @return  mixed
	 */
	public function one($args = [])
	{
		return null;
	}

	/**
	 * Query serialisation, stubbed by the tests
	 *
	 * @return  array
	 */
	public function toArray()
	{
		return [];
	}
}
