<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Utility\Str;

/**
 * Helper for highlighting a phrase or word in a body of text
 */
class Highlight extends AbstractHelper
{
	/**
	 * Highlight some text
	 *
	 * @param   string   $text     Text to find phrases in
	 * @param   integer  $phrase   Phrase to highlight
	 * @param   array    $options  Options for highlighting
	 * @return  string
	 * @throws  \InvalidArgumentException If no text was passed
	 */
	public function __invoke($text=null, $phrase=null, $options = array())
	{
		if (null === $text)
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); No text passed.');
		}

		return Str::highlight($text, $phrase, $options);
	}
}
