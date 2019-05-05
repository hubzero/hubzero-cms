<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
require_once __DIR__ . '/replacer.php';
require_once __DIR__ . '/regexlikereplacer.php';
require_once __DIR__ . '/doublereplacer.php';
require_once __DIR__ . '/hashtablereplacer.php';
require_once __DIR__ . '/replacementarray.php';
/**
 * A collection of static methods to play with strings.
 */
class StringUtils
{
	/**
	 * Perform an operation equivalent to preg_replace("!$startDelim(.*?)$endDelim!", $replace, $subject);
	 * except that it's worst-case O(N) instead of O(N^2)
	 * Compared to delimiterReplace(), this implementation is fast but memory-
	 * hungry and inflexible. The memory requirements are such that I don't
	 * recommend using it on anything but guaranteed small chunks of text.
	 *
	 * @param      string $startDelim Parameter description (if any) ...
	 * @param      unknown $endDelim Parameter description (if any) ...
	 * @param      string $replace Parameter description (if any) ...
	 * @param      unknown $subject Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	static function hungryDelimiterReplace($startDelim, $endDelim, $replace, $subject)
	{
		$segments = explode($startDelim, $subject);
		$output = array_shift($segments);
		foreach ($segments as $s)
		{
			$endDelimPos = strpos($s, $endDelim);
			if ($endDelimPos === false)
			{
				$output .= $startDelim . $s;
			}
			else
			{
				$output .= $replace . substr($s, $endDelimPos + strlen($endDelim));
			}
		}
		return $output;
	}

	/**
	 * Perform an operation equivalent to
	 *
	 * preg_replace_callback("!$startDelim(.*)$endDelim!s$flags", $callback, $subject)
	 *
	 * This implementation is slower than hungryDelimiterReplace but uses far less
	 * memory. The delimiters are literal strings, not regular expressions.
	 *
	 * @param string $flags Regular expression flags
	 *
	 * If the start delimiter ends with an initial substring of the end delimiter,
	 * e.g. in the case of C-style comments, the behaviour differs from the model
	 * regex. In this implementation, the end must share no characters with the
	 * start, so e.g. /asterisk/ is not considered to be both the start and end of a
	 * comment. /asterisk/xy/asterisk/ is considered to be a single comment with contents /xy/.
	 *
	 * @param      unknown $startDelim Parameter description (if any) ...
	 * @param      unknown $endDelim Parameter description (if any) ...
	 * @param      unknown $callback Parameter description (if any) ...
	 * @param      unknown $subject Parameter description (if any) ...
	 * @param      string $flags Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 * @throws MWException  Exception description (if any) ...
	 * @throws MWException  Exception description (if any) ...
	 */
	static function delimiterReplaceCallback($startDelim, $endDelim, $callback, $subject, $flags = '')
	{
		$inputPos = 0;
		$outputPos = 0;
		$output = '';
		$foundStart = false;
		$encStart = preg_quote($startDelim, '!');
		$encEnd = preg_quote($endDelim, '!');
		$strcmp = strpos($flags, 'i') === false ? 'strcmp' : 'strcasecmp';
		$endLength = strlen($endDelim);
		$m = array();

		while ($inputPos < strlen($subject) &&
		  preg_match("!($encStart)|($encEnd)!S$flags", $subject, $m, PREG_OFFSET_CAPTURE, $inputPos))
		{
			$tokenOffset = $m[0][1];
			if ($m[1][0] != '')
			{
				if ($foundStart &&
				  $strcmp($endDelim, substr($subject, $tokenOffset, $endLength)) == 0)
				{
					// An end match is present at the same location
					$tokenType = 'end';
					$tokenLength = $endLength;
				}
				else
				{
					$tokenType = 'start';
					$tokenLength = strlen($m[0][0]);
				}
			}
			elseif ($m[2][0] != '')
			{
				$tokenType = 'end';
				$tokenLength = strlen($m[0][0]);
			}
			else
			{
				throw new Exception('Invalid delimiter given to ' . __METHOD__);
			}

			if ($tokenType == 'start')
			{
				$inputPos = $tokenOffset + $tokenLength;
				// Only move the start position if we haven't already found a start
				// This means that START START END matches outer pair
				if (!$foundStart)
				{
					// Found start
					// Write out the non-matching section
					$output .= substr($subject, $outputPos, $tokenOffset - $outputPos);
					$outputPos = $tokenOffset;
					$contentPos = $inputPos;
					$foundStart = true;
				}
			}
			elseif ($tokenType == 'end')
			{
				if ($foundStart)
				{
					// Found match
					$output .= call_user_func($callback, array(
						substr($subject, $outputPos, $tokenOffset + $tokenLength - $outputPos),
						substr($subject, $contentPos, $tokenOffset - $contentPos)
					));
					$foundStart = false;
				}
				else
				{
					// Non-matching end, write it out
					$output .= substr($subject, $inputPos, $tokenOffset + $tokenLength - $outputPos);
				}
				$inputPos = $outputPos = $tokenOffset + $tokenLength;
			}
			else
			{
				throw new Exception('Invalid delimiter given to ' . __METHOD__);
			}
		}
		if ($outputPos < strlen($subject))
		{
			$output .= substr($subject, $outputPos);
		}
		return $output;
	}

	/**
	 * Perform an operation equivalent to preg_replace("!$startDelim(.*)$endDelim!$flags", $replace, $subject)
	 *
	 * @param      string $startDelim Start delimiter regular expression
	 * @param      string $endDelim   End delimiter regular expression
	 * @param      string $replace    Replacement string. May contain $1, which will be replaced by the text between the delimiters
	 * @param      string $subject    String to search
	 * @param      string $flags Parameter description (if any) ...
	 * @return     string The string with the matches replaced
	 */
	static function delimiterReplace($startDelim, $endDelim, $replace, $subject, $flags = '')
	{
		$replacer = new RegexlikeReplacer($replace);
		return self::delimiterReplaceCallback($startDelim, $endDelim, $replacer->cb(), $subject, $flags);
	}

	/**
	 * More or less "markup-safe" explode()
	 * Ignores any instances of the separator inside <...>
	 *
	 * @param      unknown $separator Parameter description (if any) ...
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	static function explodeMarkup($separator, $text)
	{
		$placeholder = "\x00";

		// Remove placeholder instances
		$text = str_replace($placeholder, '', $text);

		// Replace instances of the separator inside HTML-like tags with the placeholder
		$replacer = new DoubleReplacer($separator, $placeholder);
		$cleaned = self::delimiterReplaceCallback('<', '>', $replacer->cb(), $text);

		// Explode, then put the replaced separators back in
		$items = explode($separator, $cleaned);
		foreach ($items as $i => $str)
		{
			$items[$i] = str_replace($placeholder, $separator, $str);
		}

		return $items;
	}

	/**
	 * Escape a string to make it suitable for inclusion in a preg_replace() replacement parameter.
	 *
	 * @param      unknown $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	static function escapeRegexReplacement($string)
	{
		$string = str_replace('\\', '\\\\', $string);
		$string = str_replace('$', '\\$', $string);
		return $string;
	}
}
