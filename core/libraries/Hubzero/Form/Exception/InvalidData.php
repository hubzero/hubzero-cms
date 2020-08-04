<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Exception;

class InvalidData extends \Exception
{
	/**
	 * Returns to error message
	 *
	 * @return  string  Error message
	 */
	public function __toString()
	{
		return $this->getMessage();
	}

	/**
	 * Returns to error message
	 *
	 * @return  string  Error message
	 */
	public function toString()
	{
		return $this->__toString();
	}
}
