<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Value;

/**
 * Database basic value class
 */
class Raw extends Basic
{
	/**
	 * Builds the given string representation of the value object
	 *
	 * @param   object  $syntax  The syntax object with which the query is being built
	 * @return  string
	 * @since   2.1.0
	 **/
	public function build($syntax)
	{
		return $this->content;
	}
}
