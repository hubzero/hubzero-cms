<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * String handling methods.
 *
 * Largely inspired by CakePHP (http://cakephp.org) and Zend (http://framework.zend.com)
 */
class Str
{
	/**
	 * Replaces variable placeholders inside a $str with any given $data. Each key in the $data array
	 * corresponds to a variable placeholder name in $str.
	 * Example: `Str::insert(':name is :age years old.', array('name' => 'Bob', '65'));`
	 * Returns: Bob is 65 years old.
	 *
	 * Available $options are:
	 *
	 * - before: The character or string in front of the name of the variable placeholder (Defaults to `:`)
	 * - after: The character or string after the name of the variable placeholder (Defaults to null)
	 * - escape: The character or string used to escape the before character / string (Defaults to `\`)
	 * - format: A regex to use for matching variable placeholders. Default is: `/(?<!\\)\:%s/`
	 *   (Overwrites before, after, breaks escape / clean)
	 * - clean: A boolean or array with instructions for Str::cleanInsert
	 *
	 * @param   string  $str      A string containing variable placeholders
	 * @param   array   $data     A key => val array where each key stands for a placeholder to be replaced with val
	 * @param   array   $options  An array of options, see description above
	 * @return  string
	 */
	public static function insert($str, $data, $options = array())
	{
		$defaults = array(
			'before' => ':',
			'after'  => null,
			'escape' => '\\',
			'format' => null,
			'clean'  => false
		);
		$options += $defaults;
		$format = $options['format'];
		$data   = (array)$data;
		if (empty($data))
		{
			return ($options['clean']) ? self::cleanInsert($str, $options) : $str;
		}

		if (!isset($format))
		{
			$format = sprintf(
				'/(?<!%s)%s%%s%s/',
				preg_quote($options['escape'], '/'),
				str_replace('%', '%%', preg_quote($options['before'], '/')),
				str_replace('%', '%%', preg_quote($options['after'], '/'))
			);
		}

		if (strpos($str, '?') !== false && is_numeric(key($data)))
		{
			$offset = 0;
			while (($pos = strpos($str, '?', $offset)) !== false)
			{
				$val = array_shift($data);
				$offset = $pos + strlen($val);
				$str = substr_replace($str, $val, $pos, 1);
			}
			return ($options['clean']) ? self::cleanInsert($str, $options) : $str;
		}

		asort($data);

		$dataKeys = array_keys($data);
		$hashKeys = array_map('crc32', $dataKeys);
		$tempData = array_combine($dataKeys, $hashKeys);
		krsort($tempData);

		foreach ($tempData as $key => $hashVal)
		{
			$key = sprintf($format, preg_quote($key, '/'));
			$str = preg_replace($key, $hashVal, $str);
		}
		$dataReplacements = array_combine($hashKeys, array_values($data));
		foreach ($dataReplacements as $tmpHash => $tmpValue)
		{
			$tmpValue = (is_array($tmpValue)) ? '' : $tmpValue;
			$str = str_replace($tmpHash, $tmpValue, $str);
		}

		if (!isset($options['format']) && isset($options['before']))
		{
			$str = str_replace($options['escape'] . $options['before'], $options['before'], $str);
		}
		return ($options['clean']) ? self::cleanInsert($str, $options) : $str;
	}

	/**
	 * Cleans up a Str::insert() formatted string with given $options depending on the 'clean' key in
	 * $options. The default method used is text but html is also available. The goal of this function
	 * is to replace all whitespace and unneeded markup around placeholders that did not get replaced
	 * by Str::insert().
	 *
	 * @param   string  $str
	 * @param   array   $options
	 * @return  string
	 * @see     Str::insert()
	 */
	public static function cleanInsert($str, $options)
	{
		$clean = $options['clean'];
		if (!$clean)
		{
			return $str;
		}
		if ($clean === true)
		{
			$clean = array('method' => 'text');
		}
		if (!is_array($clean))
		{
			$clean = array('method' => $options['clean']);
		}
		switch ($clean['method'])
		{
			case 'html':
				$clean = array_merge(array(
					'word' => '[\w,.]+',
					'andText' => true,
					'replacement' => '',
				), $clean);
				$kleenex = sprintf(
					'/[\s]*[a-z]+=(")(%s%s%s[\s]*)+\\1/i',
					preg_quote($options['before'], '/'),
					$clean['word'],
					preg_quote($options['after'], '/')
				);
				$str = preg_replace($kleenex, $clean['replacement'], $str);
				if ($clean['andText'])
				{
					$options['clean'] = array('method' => 'text');
					$str = self::cleanInsert($str, $options);
				}
			break;

			case 'text':
				$clean = array_merge(array(
					'word' => '[\w,.]+',
					'gap' => '[\s]*(?:(?:and|or)[\s]*)?',
					'replacement' => '',
				), $clean);

				$kleenex = sprintf(
					'/(%s%s%s%s|%s%s%s%s)/',
					preg_quote($options['before'], '/'),
					$clean['word'],
					preg_quote($options['after'], '/'),
					$clean['gap'],
					$clean['gap'],
					preg_quote($options['before'], '/'),
					$clean['word'],
					preg_quote($options['after'], '/')
				);
				$str = preg_replace($kleenex, $clean['replacement'], $str);
			break;
		}
		return $str;
	}

	/**
	 * Highlights a given phrase in a text. You can specify any expression in highlighter that
	 * may include the \1 expression to include the $phrase found.
	 *
	 * ### Options:
	 *
	 * - `format` The piece of html with that the phrase will be highlighted
	 * - `html` If true, will ignore any HTML tags, ensuring that only the correct text is highlighted
	 * - `regex` a custom regex rule that is used to match words, default is '|$tag|iu'
	 *
	 * @param   string  $text     Text to search the phrase in
	 * @param   string  $phrase   The phrase that will be searched
	 * @param   array   $options  An array of html attributes and options.
	 * @return  string  The highlighted text
	 */
	public static function highlight($text, $phrase, $options = array())
	{
		if (empty($phrase))
		{
			return $text;
		}

		$default = array(
			'format' => '<span class="highlight">\1</span>',
			'html'   => false,
			'regex'  => "|%s|iu"
		);
		$options = array_merge($default, $options);
		extract($options);

		if (is_array($phrase))
		{
			$replace = array();
			$with    = array();

			foreach ($phrase as $key => $segment)
			{
				$segment = '(' . preg_quote($segment, '|') . ')';
				if ($html)
				{
					$segment = "(?![^<]+>)$segment(?![^<]+>)";
				}

				$with[]    = (is_array($format)) ? $format[$key] : $format;
				$replace[] = sprintf($options['regex'], $segment);
			}

			return preg_replace($replace, $with, $text);
		}

		$phrase = '(' . preg_quote($phrase, '|') . ')';
		if ($html)
		{
			$phrase = "(?![^<]+>)$phrase(?![^<]+>)";
		}

		return preg_replace(sprintf($options['regex'], $phrase), $format, $text);
	}

	/**
	 * Truncates text starting from the end.
	 *
	 * Cuts a string to the length of $length and replaces the first characters
	 * with the ellipsis if the text is longer than length.
	 *
	 * ### Options:
	 *
	 * - `ellipsis` Will be used as Beginning and prepended to the trimmed string
	 * - `exact` If false, $text will not be cut mid-word
	 *
	 * @param   string   $text     String to truncate.
	 * @param   integer  $length   Length of returned string, including ellipsis.
	 * @param   array    $options  An array of options.
	 * @return  string   Trimmed string.
	 */
	public static function tail($text, $length = 100, $options = array())
	{
		$default = array(
			'ellipsis' => '...',
			'exact'    => true
		);
		$options = array_merge($default, $options);
		extract($options);

		if (mb_strlen($text) <= $length)
		{
			return $text;
		}

		$truncate = mb_substr($text, mb_strlen($text) - $length + mb_strlen($ellipsis));
		if (!$exact)
		{
			$spacepos = mb_strpos($truncate, ' ');
			$truncate = $spacepos === false ? '' : trim(mb_substr($truncate, $spacepos));
		}

		return $ellipsis . $truncate;
	}

	/**
	 * Truncates text.
	 *
	 * Cuts a string to the length of $length and replaces the last characters
	 * with the ellipsis if the text is longer than length.
	 *
	 * ### Options:
	 *
	 * - `ellipsis` Will be used as Ending and appended to the trimmed string
	 * - `exact` If false, $text will not be cut mid-word
	 * - `html` If true, HTML tags would be handled correctly
	 *
	 * @param   string   $text     String to truncate.
	 * @param   integer  $length   Length of returned string, including ellipsis.
	 * @param   array    $options  An array of html attributes and options.
	 * @return  string   Trimmed string.
	 */
	public static function truncate($text, $length = 100, $options = array())
	{
		$default = array(
			'ellipsis' => '...',
			'exact'    => false,
			'html'     => false
		);

		if (!empty($options['html']) && strtolower(mb_internal_encoding()) === 'utf-8')
		{
			$default['ellipsis'] = "\xe2\x80\xa6";
		}
		$options += $default;
		$prefix = '';
		$suffix = $options['ellipsis'];

		if ($options['html'])
		{
			$ellipsisLength = mb_strlen(strip_tags($options['ellipsis']));
			$truncateLength = 0;
			$totalLength = 0;
			$openTags = array();
			$truncate = '';
			$htmlNoCount = array('style', 'script');

			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);

			foreach ($tags as $tag)
			{
				$contentLength = 0;
				if (!in_array($tag[2], $htmlNoCount, true))
				{
					$contentLength = mb_strlen($tag[3]);
				}
				if ($truncate === '')
				{
					if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/i', $tag[2]))
					{
						if (preg_match('/<[\w]+[^>]*>/', $tag[0]))
						{
							array_unshift($openTags, $tag[2]);
						}
						elseif (preg_match('/<\/([\w]+)[^>]*>/', $tag[0], $closeTag))
						{
							$pos = array_search($closeTag[1], $openTags);
							if ($pos !== false)
							{
								array_splice($openTags, $pos, 1);
							}
						}
					}
					$prefix .= $tag[1];
					if ($totalLength + $contentLength + $ellipsisLength > $length)
					{
						$truncate = $tag[3];
						$truncateLength = $length - $totalLength;
					}
					else
					{
						$prefix .= $tag[3];
					}
				}
				$totalLength += $contentLength;
				if ($totalLength > $length)
				{
					break;
				}
			}
			if ($totalLength <= $length)
			{
				return $text;
			}
			$text = $truncate;
			$length = $truncateLength;
			foreach ($openTags as $tag)
			{
				$suffix .= '</' . $tag . '>';
			}
		}
		else
		{
			if (mb_strlen($text) <= $length)
			{
				return $text;
			}
			$ellipsisLength = mb_strlen($options['ellipsis']);
		}

		$result = mb_substr($text, 0, $length - $ellipsisLength);

		if (!$options['exact'])
		{
			if (mb_substr($text, $length - $ellipsisLength, 1) !== ' ')
			{
				//$result = self::_removeLastWord($result);
				$spacepos = mb_strrpos($result, ' ');

				if ($spacepos !== false)
				{
					$lastWord = mb_strrpos($result, $spacepos);
					// Some languages are written without word separation.
					// We recognize a string as a word if it doesn't contain any full-width characters.
					if (mb_strwidth($lastWord) === mb_strlen($lastWord))
					{
						$result = mb_substr($result, 0, $spacepos);
					}
				}
			}

			// If result is empty, then we don't need to count ellipsis in the cut.
			if (!strlen($result))
			{
				$result = mb_substr($text, 0, $length);
			}
		}

		return $prefix . $result . $suffix;
	}

	/**
	 * Extracts an excerpt from the text surrounding the phrase with a number of characters on each side
	 * determined by radius.
	 *
	 * @param   string   $text      String to search the phrase in
	 * @param   string   $phrase    Phrase that will be searched for
	 * @param   integer  $radius    The amount of characters that will be returned on each side of the founded phrase
	 * @param   string   $ellipsis  Ending that will be appended
	 * @return  string   Modified string
	 */
	public static function excerpt($text, $phrase, $radius = 100, $ellipsis = '...')
	{
		if (empty($text) || empty($phrase))
		{
			return self::truncate($text, $radius * 2, array('ellipsis' => $ellipsis));
		}

		$append = $prepend = $ellipsis;

		$phraseLen = mb_strlen($phrase);
		$textLen = mb_strlen($text);

		$pos = mb_strpos(mb_strtolower($text), mb_strtolower($phrase));
		if ($pos === false)
		{
			return mb_substr($text, 0, $radius) . $ellipsis;
		}

		$startPos = $pos - $radius;
		if ($startPos <= 0)
		{
			$startPos = 0;
			$prepend  = '';
		}

		$endPos = $pos + $phraseLen + $radius;
		if ($endPos >= $textLen)
		{
			$endPos = $textLen;
			$append = '';
		}

		$excerpt = mb_substr($text, $startPos, $endPos - $startPos);
		$excerpt = $prepend . $excerpt . $append;

		return $excerpt;
	}

	/**
	 * Obfuscate a string to prevent spam-bots from sniffing it.
	 *
	 * @param   string  $value
	 * @return  string
	 */
	public static function obfuscate($value)
	{
		$safe = '';

		foreach (str_split($value) as $letter)
		{
			// To properly obfuscate the value, we will randomly convert each letter to
			// its entity or hexadecimal representation, keeping a bot from sniffing
			// the randomly obfuscated letters out of the string on the responses.
			switch (rand(1, 3))
			{
				case 1:
					$safe .= '&#' . ord($letter) . ';';
					break;

				case 2:
					$safe .= '&#x' . dechex(ord($letter)) . ';';
					break;

				case 3:
					$safe .= $letter;
			}
		}

		return $safe;
	}

	/**
	 * Format a number by prefixing a character to a specificed length.
	 *
	 * @param   integer  $value   Number to format
	 * @param   integer  $length  Final string length
	 * @param   integer  $prfx    Character to prepend
	 * @return  string
	 */
	public static function pad($value, $length = 5, $prfx = 0)
	{
		$pre = '';

		if (is_numeric($value) && $value < 0)
		{
			$pre = 'n';
			$value = abs($value);
		}

		while (strlen($value) < $length)
		{
			$value = $prfx . "$value";
		}
		return $pre . $value;
	}

	/**
	 * Looks for literal occurances of a string (i.e. unquoted) and returns their positions
	 *
	 * @param   string  $needle    The item of interest
	 * @param   string  $haystack  The text in which to look for the needle
	 * @return  array
	 * @since   2.0.0
	 */
	public static function findLiteral($needle, $haystack)
	{
		// Initialize vars
		$open      = false;
		$quoteChar = '';
		$length    = strlen($haystack);
		$instances = [];

		// Go character by character through the sql statement
		for ($i = 0; $i < $length; $i++)
		{
			// Grab the current character
			$current = substr($haystack, $i, 1);

			// If we come across a quote...
			if ($current == '"' || $current == '\'')
			{
				// Work backwards to make sure the quote isn't escaped
				$n = 2;
				while (substr($haystack, $i - $n + 1, 1) == '\\' && $n < $i)
				{
					$n++;
				}

				// Even number of escapes means it's a real quote, odd number means it's actually escaped
				if ($n % 2 == 0)
				{
					// If we had an open quote already, then make sure this is a close of the same type
					if ($open)
					{
						// We're at a closing quote
						if ($current == $quoteChar)
						{
							// Reset open status and quote character
							$open      = false;
							$quoteChar = '';
						}
					}
					else
					{
						// This is an open quote
						$open      = true;
						$quoteChar = $current;
					}
				}
			}

			// If we find a needle and we're not in open quotes
			if ($current == substr($needle, 0, 1) && !$open)
			{
				$match = true;

				// Make sure the entire needle matches by going forward the length of the needle
				for ($j=0; $j < strlen($needle); $j++)
				{
					// If at any point we stop matching, break out
					if (substr($needle, $j, 1) != substr($haystack, $i + $j, 1))
					{
						$match = false;
						break;
					}
				}

				// If it all matched, record the position
				if ($match)
				{
					$instances[] = $i;
				}
			}
		}

		return $instances;
	}

	/**
	 * Convert a string to snake case.
	 * "this text is snake case" -> this_text_is_snake_case
	 *
	 * @param   string  $value
	 * @param   string  $delimiter
	 * @return  string
	 * @since   2.0.0
	 */
	public static function snake($value, $delimiter = '_')
	{
		if (!ctype_lower($value))
		{
			$value = preg_replace('/\s+/u', '', ucwords($value));
			$value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
		}
		return $value;
	}

	/**
	 * Convert a value to camel case.
	 * "this text is camel case" -> ThisTextIsCamelCase
	 *
	 * @param   string  $value
	 * @return  string
	 * @since   2.0.0
	 */
	public static function camel($value)
	{
		$value = ucwords(str_replace(array('-', '_'), ' ', $value));

		return str_replace(' ', '', $value);
	}

	/**
	 * Split a string in camel case format
	 * ThisTextIsCamelCase -> "This Text Is Camel Case"
	 *
	 * @param   string  $string  The source string.
	 * @return  array   The splitted string.
	 * @since   2.0.0
	 */
	public static function splitCamel($string)
	{
		return preg_split('/(?<=[^A-Z_])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][^A-Z_])/x', $string);
	}

	/**
	 * Determine if a given string contains a given substring.
	 *
	 * @param   string  $haystack
	 * @param   mixed   $needles   string|array
	 * @return  bool
	 * @since   2.0.0
	 */
	public static function contains($haystack, $needles)
	{
		foreach ((array) $needles as $needle)
		{
			if ($needle != '' && strpos($haystack, $needle) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine if a given string starts with a given substring.
	 *
	 * @param   string  $haystack
	 * @param   mixed   $needles   string|array
	 * @return  bool
	 * @since   2.0.0
	 */
	public static function startsWith($haystack, $needles)
	{
		foreach ((array) $needles as $needle)
		{
			if ($needle != '' && strpos($haystack, $needle) === 0)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine if a given string ends with a given substring.
	 *
	 * @param   string  $haystack
	 * @param   mixed   $needles   string|array
	 * @return  bool
	 * @since   2.0.0
	 */
	public static function endsWith($haystack, $needles)
	{
		foreach ((array) $needles as $needle)
		{
			if ((string) $needle === substr($haystack, -strlen($needle)))
			{
				return true;
			}
		}

		return false;
	}

		/**
   * Replaces &amp; with & for XHTML compliance
   *
   * @param   string  $text  Text to process
   * @return  string  Processed string.
   */
	public static function ampReplace($text)
	{
		$text = str_replace('&&', '*--*', $text);
		$text = str_replace('&#', '*-*', $text);
		$text = str_replace('&amp;', '&', $text);
		$text = preg_replace('|&(?![\w]+;)|', '&amp;', $text);
		$text = str_replace('*-*', '&#', $text);
		$text = str_replace('*--*', '&&', $text);

		return $text;
	}

	/**
	 * Method to extract key/value pairs out of a string with XML style attributes
	 *
	 * @param   string  $string  String containing XML style attributes
	 * @return  array   Key/Value pairs for the attributes
	 */
	public static function parseAttributes($string)
	{
		// Initialise variables.
		$attr = array();
		$ret  = array();

		// Let's grab all the key/value pairs using a regular expression
		preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

		if (is_array($attr))
		{
			$numPairs = count($attr[1]);
			for ($i = 0; $i < $numPairs; $i++)
			{
				$ret[$attr[1][$i]] = $attr[2][$i];
			}
		}

		return $ret;
	}
}
