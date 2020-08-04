<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Utility\Sanitize;

/**
 * Helper for cleaning text. Strips some unwatned tags and scripts.
 */
class Clean extends AbstractHelper
{
	/**
	 * Clean some text
	 *
	 * @param   string  $text  Text to clean
	 * @return  string
	 * @throws  \InvalidArgumentException If no text passed
	 */
	public function __invoke($text = null)
	{
		if (null === $text)
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); No text passed.');
		}

		return Sanitize::clean($text);
	}
}
