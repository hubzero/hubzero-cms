<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Utility\Str;

/**
 * Helper for autolinking text that matches a URL or email address pattern
 */
class Autolink extends AbstractHelper
{
	/**
	 * Link some text
	 *
	 * @param   string  $text  Text to autolink
	 * @return  string
	 * @throws  \InvalidArgumentException If no text passed
	 */
	public function __invoke($text = null)
	{
		if (null === $text)
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); No text passed.');
		}

		// Parse for link syntax
		// e.g. [mylink My Link] => <a href="mylink">My Link</a>
		$char_regexes = array(
			// URL pattern
			'url'   => "(?<=[^=\"\'\[])\!?" .  // Make sure it's not preceeded by quotes and brackets
				//"(https?:|mailto:|ftp:|gopher:|news:|file:)" .  // protocol
				//"([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\'\<]*[A-Za-z0-9\\/?=&~_])",  // link
				"(?i)\b((?:(https?:|mailto:|ftp:|gopher:|news:|file:)\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)([^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))",

			// Email pattern
			'email' => "([\s]*)" .  // whitespace
				"([\._a-zA-Z0-9-\+]+@" .  // characters leading up to @
				"(?:[0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6})"  // everything after @
		);

		foreach ($char_regexes as $func => $regex)
		{
			$text = preg_replace_callback("/$regex/i", array(&$this, 'link' . ucfirst($func)), $text);
		}

		return $text;
	}

	/**
	 * Automatically links any strings matching a URL pattern
	 *
	 * @param   array   $matches  Text matching link pattern
	 * @return  string
	 */
	public function linkUrl($matches)
	{
		return $this->anchor($matches);
	}

	/**
	 * Automatically links any strings matching an email pattern
	 *
	 * @param   array   $matches  Text matching link pattern
	 * @return  string
	 */
	public function linkEmail($matches)
	{
		array_splice($matches, 1, 0, 'mailto:');

		return $this->anchor($matches);
	}

	/**
	 * Automatically links any strings matching a URL or email pattern
	 *
	 * Link is pushed to internal array and placeholder returned
	 * This is to ensure links aren't parsed twice. We put the links back in place
	 * towards the end of parsing.
	 *
	 * @param   array   $matches  Text matching link pattern
	 * @return  string
	 */
	public function anchor($matches)
	{
		if (empty($matches))
		{
			return '';
		}

		$whole = $matches[0];
		$prtcl = rtrim($matches[1], ':');

		$url   = $matches[3];
		$url  .= (isset($matches[4])) ? $matches[4] : '';
		$url  .= (isset($matches[5])) ? $matches[5] : '';
		$url  .= (isset($matches[6])) ? $matches[6] : '';

		$prfx  = preg_replace('/^([\s]*)(.*)/i', "$1", $whole);
		$href  = trim($whole);
		if (substr($href, 0, 1) == '>')
		{
			$href  = ltrim($href, '>');
			$prfx .= '>';
		}

		$txt = $href;

		if ($prtcl == 'mailto')
		{
			$txt  = $url;
			$href = 'mailto:' . Str::obfuscate($url);
		}

		return $prfx . '<a class="ext-link" href="' . $href . '" rel="external">' . $txt . '</a>';
	}
}
