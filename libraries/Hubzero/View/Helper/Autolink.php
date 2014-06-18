<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\View\Helper;

use Hubzero\Utility\String;

/**
 * Helper for autolinking text that matches a URL or email address pattern
 */
class Autolink extends AbstractHelper
{
	/**
	 * Link some text
	 *
	 * @param  string $text Text to autolink
	 * @return string
	 * @throws \InvalidArgumentException If no text passed
	 */
	public function __invoke($text = null)
	{
		if (null === $text)
		{
			throw new \InvalidArgumentException(
				__CLASS__ .'::' . __METHOD__ . '(); No text passed.'
			);
		}

		// Parse for link syntax
		// e.g. [mylink My Link] => <a href="mylink">My Link</a>
		$char_regexes = array(
			// URL pattern
			'autourl'    => "(?<=[^=\"\'\[])\!?" .  // Make sure it's not preceeded by quotes and brackets
				//"(https?:|mailto:|ftp:|gopher:|news:|file:)" .  // protocol
				//"([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\'\<]*[A-Za-z0-9\\/?=&~_])",  // link
				"(?i)\b((?:(https?:|mailto:|ftp:|gopher:|news:|file:)\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)([^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))",

			// Email pattern
			'autoemail'    => "([\s]*)" .  // whitespace
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
	 * @param      array $matches Text matching link pattern
	 * @return     string
	 */
	public function linkAutourl($matches)
	{
		return $this->linkAuto($matches);
	}

	/**
	 * Automatically links any strings matching an email pattern
	 *
	 * @param      array $matches Text matching link pattern
	 * @return     string
	 */
	public function linkAutoemail($matches)
	{
		array_splice($matches, 1, 0, 'mailto:');
		return $this->linkAuto($matches);
	}

	/**
	 * Automatically links any strings matching a URL or email pattern
	 *
	 * Link is pushed to internal array and placeholder returned
	 * This is to ensure links aren't parsed twice. We put the links back in place
	 * towards the end of parsing.
	 *
	 * @param      array $matches Text matching link pattern
	 * @return     string
	 */
	public function linkAuto($matches)
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
			$href = 'mailto:' . String::obfuscate($url);
		}

		return $prfx . '<a class="ext-link" href="' . $href . '" rel="external">' . $txt . '</a>';
	}
}
