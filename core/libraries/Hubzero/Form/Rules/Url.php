<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Rules;

use Hubzero\Form\Rule;

/**
 * Form Rule class for URLs.
 */
class Url extends Rule
{
	/**
	 * Method to test for a valid color in hexadecimal.
	 *
	 * @param   object   &$element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed    $value     The form field value to validate.
	 * @param   string   $group     The field name group control value. This acts as as an array container for the field.
	 *                              For example if the field has name="foo" and the group value is set to "bar" then the
	 *                              full field name would end up being "bar[foo]".
	 * @param   object   &$input    An optional Registry object with the entire data set to validate against the entire form.
	 * @param   object   &$form     The form object for which the field is being tested.
	 * @return  boolean  True if the value is valid, false otherwise.
	 */
	public function test(&$element, $value, $group = null, &$input = null, &$form = null)
	{
		// If the field is empty and not required, the field is valid.
		$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');
		if (!$required && empty($value))
		{
			return true;
		}

		$urlParts = self::parseUrl($value);

		// See http://www.w3.org/Addressing/URL/url-spec.txt
		// Use the full list or optionally specify a list of permitted schemes.
		if ($element['schemes'] == '')
		{
			$scheme = array('http', 'https', 'ftp', 'ftps', 'gopher', 'mailto', 'news', 'prospero', 'telnet', 'rlogin', 'tn3270', 'wais', 'url',
				'mid', 'cid', 'nntp', 'tel', 'urn', 'ldap', 'file', 'fax', 'modem', 'git');
		}
		else
		{
			$scheme = explode(',', $element['schemes']);

		}
		// This rule is only for full URLs with schemes because  parse_url does not parse
		// accurately without a scheme.
		// @see http://php.net/manual/en/function.parse-url.php
		if (!array_key_exists('scheme', $urlParts))
		{
			return false;
		}
		$urlScheme = (string) $urlParts['scheme'];
		$urlScheme = strtolower($urlScheme);
		if (in_array($urlScheme, $scheme) == false)
		{
			return false;
		}

		// For some schemes here must be two slashes.
		if (($urlScheme == 'http' || $urlScheme == 'https' || $urlScheme == 'ftp' || $urlScheme == 'sftp' || $urlScheme == 'gopher'
			|| $urlScheme == 'wais' || $urlScheme == 'gopher' || $urlScheme == 'prospero' || $urlScheme == 'telnet' || $urlScheme == 'git')
			&& ((substr($value, strlen($urlScheme), 3)) !== '://'))
		{
			return false;
		}

		// The best we can do for the rest is make sure that the strings are valid UTF-8
		// and the port is an integer.
		if (array_key_exists('host', $urlParts) && !self::valid((string) $urlParts['host']))
		{
			return false;
		}
		if (array_key_exists('port', $urlParts) && !is_int((int) $urlParts['port']))
		{
			return false;
		}
		if (array_key_exists('path', $urlParts) && !self::valid((string) $urlParts['path']))
		{
			return false;
		}

		return true;
	}

	/**
	 * Does a UTF-8 safe version of PHP parse_url function
	 *
	 * @param   string  $url  URL to parse
	 * @return  mixed   Associative array or false if badly formed URL.
	 */
	public static function parseUrl($url)
	{
		$result = false;

		// Build arrays of values we need to decode before parsing
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "$", ",", "/", "?", "%", "#", "[", "]");

		// Create encoded URL with special URL characters decoded so it can be parsed
		// All other characters will be encoded
		$encodedURL = str_replace($entities, $replacements, urlencode($url));

		// Parse the encoded URL
		$encodedParts = parse_url($encodedURL);

		// Now, decode each value of the resulting array
		if ($encodedParts)
		{
			foreach ($encodedParts as $key => $value)
			{
				$result[$key] = urldecode($value);
			}
		}

		return $result;
	}

	/**
	 * Tests a string as to whether it's valid UTF-8 and supported by the Unicode standard.
	 *
	 * Note: this function has been modified to simple return true or false.
	 *
	 * @param   string   $str  UTF-8 encoded string.
	 * @return  boolean  true if valid
	 */
	public static function valid($str)
	{
		// Cached expected number of octets after the current octet
		// until the beginning of the next UTF8 character sequence
		$mState = 0;

		// Cached Unicode character
		$mUcs4 = 0;

		// Cached expected number of octets in the current sequence
		$mBytes = 1;

		$len = strlen($str);

		for ($i = 0; $i < $len; $i++)
		{
			$in = ord($str{$i});

			if ($mState == 0)
			{
				// When mState is zero we expect either a US-ASCII character or a
				// multi-octet sequence.
				if (0 == (0x80 & ($in)))
				{
					// US-ASCII, pass straight through.
					$mBytes = 1;
				}
				elseif (0xC0 == (0xE0 & ($in)))
				{
					// First octet of 2 octet sequence
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 0x1F) << 6;
					$mState = 1;
					$mBytes = 2;
				}
				elseif (0xE0 == (0xF0 & ($in)))
				{
					// First octet of 3 octet sequence
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 0x0F) << 12;
					$mState = 2;
					$mBytes = 3;
				}
				elseif (0xF0 == (0xF8 & ($in)))
				{
					// First octet of 4 octet sequence
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 0x07) << 18;
					$mState = 3;
					$mBytes = 4;
				}
				elseif (0xF8 == (0xFC & ($in)))
				{
					// First octet of 5 octet sequence.
					//
					// This is illegal because the encoded codepoint must be either
					// (a) not the shortest form or
					// (b) outside the Unicode range of 0-0x10FFFF.
					// Rather than trying to resynchronize, we will carry on until the end
					// of the sequence and let the later error handling code catch it.
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 0x03) << 24;
					$mState = 4;
					$mBytes = 5;
				}
				elseif (0xFC == (0xFE & ($in)))
				{
					// First octet of 6 octet sequence, see comments for 5 octet sequence.
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 1) << 30;
					$mState = 5;
					$mBytes = 6;
				}
				else
				{
					// Current octet is neither in the US-ASCII range nor a legal first
					// octet of a multi-octet sequence.
					return false;
				}
			}
			else
			{
				// When mState is non-zero, we expect a continuation of the multi-octet
				// sequence
				if (0x80 == (0xC0 & ($in)))
				{
					// Legal continuation.
					$shift = ($mState - 1) * 6;
					$tmp = $in;
					$tmp = ($tmp & 0x0000003F) << $shift;
					$mUcs4 |= $tmp;

					// End of the multi-octet sequence. mUcs4 now contains the final
					// Unicode codepoint to be output
					if (0 == --$mState)
					{
						// Check for illegal sequences and codepoints.
						// From Unicode 3.1, non-shortest form is illegal
						if (((2 == $mBytes) && ($mUcs4 < 0x0080)) || ((3 == $mBytes) && ($mUcs4 < 0x0800)) || ((4 == $mBytes) && ($mUcs4 < 0x10000))
							|| (4 < $mBytes)
							|| (($mUcs4 & 0xFFFFF800) == 0xD800) // From Unicode 3.2, surrogate characters are illegal
							|| ($mUcs4 > 0x10FFFF)) // Codepoints outside the Unicode range are illegal
						{
							return false;
						}

						// Initialize UTF8 cache.
						$mState = 0;
						$mUcs4 = 0;
						$mBytes = 1;
					}
				}
				else
				{
					//((0xC0 & (*in) != 0x80) && (mState != 0))
					// Incomplete multi-octet sequence.
					return false;
				}
			}
		}

		return true;
	}
}
