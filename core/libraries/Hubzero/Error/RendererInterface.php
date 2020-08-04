<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error;

use Exception;

/**
 * Error renderer interface
 */
interface RendererInterface
{
	/**
	 * Display the given exception to the user.
	 *
	 * @param  object  $error
	 */
	public function render($error);
}
