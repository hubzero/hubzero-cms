<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Picture;

/**
 * User picture resolver
 */
interface Resolver
{
	/**
	 * Get a path or URL to a user pciture
	 *
	 * @param   integer  $id
	 * @param   string   $name
	 * @param   string   $email
	 * @param   bool     $thumbnail
	 * @return  string
	 */
	public function picture($id, $name, $email, $thumbnail = true);
}
