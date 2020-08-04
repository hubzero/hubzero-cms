<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\StringProcessor;

/**
 * None string processor. Does nothing on the string
 */
class NoneStringProcessor implements StringProcessorInterface
{
	/**
	 * Prepare a string
	 *
	 * @param   string  $string
	 * @return  string
	 */
	public function prepare($string)
	{
		return $string;
	}
}
