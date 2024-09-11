<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error\Exception;

/**
 * 'Method Not Allowed' Exception.
 * Defaults to 405 code.
 */
class MethodNotAllowedException extends \Exception
{
	/**
	 * Constructor
	 *
	 * @param   string   $message   The Exception message to throw.
	 * @param   integer  $code      The Exception code.
	 * @param   object   $previous  The previous exception used for the exception chaining.
	 * @return  void
	 */
	public function __construct($message = '', $code = 405, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}

