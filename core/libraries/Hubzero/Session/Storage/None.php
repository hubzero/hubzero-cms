<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Session\Storage;

use Hubzero\Session\Store;

/**
 * Session handler for 'None'
 */
class None extends Store
{
	/**
	 * Register the functions of this class with PHP's session handler
	 *
	 * @param   array  $options  Optional parameters.
	 * @return  void
	 */
	public function register($options = array())
	{
		// Let php handle the session storage
	}
}
