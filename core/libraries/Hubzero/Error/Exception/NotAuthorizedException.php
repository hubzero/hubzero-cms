<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error\Exception;

/**
 * 'Not authorized' Exception.
 * Defaults to 403 code.
 */
class NotAuthorizedException extends \Exception
{
	/**
	 * Constructor
	 *
	 * @param   string   $message   The Exception message to throw.
	 * @param   integer  $code      The Exception code.
	 * @param   object   $previous  The previous exception used for the exception chaining.
	 * @return  void
	 */
	public function __construct($message = '', $code = 403, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
