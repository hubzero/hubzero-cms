<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Utility\Str;

/**
 * Helper for truncating text
 */
class Truncate extends AbstractHelper
{
	/**
	 * Truncate some text
	 *
	 * @param   string   $text     Text to truncate
	 * @param   integer  $length   Length to truncate to
	 * @param   array    $options  Options
	 * @return  string
	 * @throws  \InvalidArgumentException If no text is passed or length isn't a positive integer
	 */
	public function __invoke($text = null, $length = null, $options = array())
	{
		if (null === $text)
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); No text passed.');
		}

		if (!$length || !is_numeric($length))
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); Length must be an integer');
		}

		return Str::truncate($text, $length, $options);
	}
}
