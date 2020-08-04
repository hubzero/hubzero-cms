<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Password;

use Hubzero\Database\Relational;

/**
 * Password blacklist model
 */
class Blacklist extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'password';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__password_blacklist';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'word' => 'notempty'
	);

	/**
	 * Load a record by word
	 *
	 * @param   string  $word
	 * @return  object
	 */
	public static function oneByWord($word)
	{
		$word = trim($word);
		$word = strtolower($word);

		return self::blank()
			->whereEquals('word', $word)
			->row();
	}

	/**
	 * Check if a word is in the blacklist
	 *
	 * @param   string  $word
	 * @return  bool
	 */
	public static function wordInBlacklist($word)
	{
		$result = self::oneByWord($word);

		return ($result->get('id') > 0);
	}

	/**
	 * Check if a username is in the blacklist
	 *
	 * @param   string  $word
	 * @return  bool
	 */
	public static function usernameInBlacklist($word, $username)
	{
		$word     = self::normalize($word);
		$username = self::normalize($username);

		$words   = array();
		$words[] = $username;
		$words[] = strrev($username);

		foreach ($words as $w)
		{
			if ($w == $word)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a name is in the blacklist
	 *
	 * @param   string  $word
	 * @return  bool
	 */
	public static function nameInBlacklist($word, $givenName, $middleName, $surname)
	{
		$word       = self::normalize($word);
		$givenName  = self::normalize($givenName);
		$middleName = self::normalize($middleName);
		$surname    = self::normalize($surname);

		$words   = array();
		$words[] = $givenName;
		$words[] = strrev($givenName);
		$words[] = $middleName;
		$words[] = strrev($middleName);
		$words[] = $surname;
		$words[] = strrev($surname);
		$words[] = $givenName . $middleName . $surname;
		$words[] = strrev($givenName . $middleName . $surname);
		$words[] = $givenName . $surname;
		$words[] = strrev($givenName . $surname);
		$words[] = $middleName . $surname;
		$words[] = strrev($middleName . $surname);
		$words[] = $surname . $givenName;
		$words[] = strrev($surname . $givenName);
		$words[] = $surname . $middleName . $givenName;
		$words[] = strrev($surname . $middleName . $givenName);

		foreach ($words as $w)
		{
			if ($w == $word)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a word is based on a blacklisted word
	 *
	 * @param   string  $word
	 * @return  bool
	 */
	public static function basedOnBlacklist($word)
	{
		$words[] = (string)$word;
		$words[] = strtolower($word);
		$words[] = strtolower(strrev($word));

		$len = strlen($word);
		$word2 = '';

		// @FIXME: badly inefficient
		for ($i = 0; $i < $len; $i++)
		{
			if (preg_match('/[a-zA-Z]/', $word[$i]))
			{
				$word2 .= $word[$i];
			}
		}

		$words[] = strtolower($word2);
		$words[] = strtolower(strrev($word2));
		$words[] = self::toL33t($word);
		$words[] = strrev(self::toL33t($word));
		$words[] = self::toSimpleL33t($word);
		$words[] = strrev(self::toSimpleL33t($word));

		$total = self::all()
			->whereIn('word', $words)
			->total();

		return ($total > 1); // returns true if char belongs to class
	}

	/**
	 * Turn a word into simple l33t type
	 *
	 * @param   string  $word
	 * @return  string
	 */
	protected static function toSimpleL33t($word)
	{
		$subs = array(
			'4' => 'A',
			'@' => 'A',
			'^' => 'A',
			'8' => 'B',
			'(' => 'C',
			'{' => 'C',
			'<' => 'C',
			')' => 'D',
			'3' => 'E',
			'6' => 'G',
			'9' => 'G',
			'&' => 'G',
			'#' => 'H',
			'1' => 'I',
			'!' => 'I',
			//'|' => 'I',
			'|' => 'L',
			'1' => 'L',
			'~' => 'N',
			'0' => 'O',
			'*' => 'O',
			'5' => 'S',
			'$' => 'S',
			'7' => 'T',
			'+' => 'T',
			'%' => 'Y',
			'2' => 'Z',
		);

		$word2 = str_replace(array_keys($subs), array_values($subs), $word);

		return strtolower($word2);
	}

	/**
	 * Turn a word into l33t type
	 *
	 * @param   string  $word
	 * @return  string
	 */
	protected static function toL33t($word)
	{
		$subs = array(
			'][\\//][' => 'M',

			//'\\/\//' => 'W',

			'//=\\' => 'A',
			'[]-[]' => 'H',
			']]-[[' => 'H',
			'[]V[]' => 'M',
			'][\][' => 'N',
			'[]\[]' => 'N',
			'[]_[]' => 'U',

			';_[]' => 'J',
			';_]]' => 'J',
			'/\\/\\' => 'M',
			'|\\/|' => 'M',
			'[\\/]' => 'M',
			'(\\/)' => 'M',
			'[[]]' => 'O',
			'\'][\'' => 'T',
			'\\\\//' => 'V',
			'\\/\\/' => 'W',
			'|/\\|' => 'W',
			'[/\\]' => 'W',
			'(/\\)' => 'W',
			'1/\\/' => 'W',
			'\\/1/' => 'W',
			'1/1/' => 'W',
			'``//' => 'Y',

			'133' => 'LEE',
			'/-\\' => 'A',
			']]3' => 'B',
			']])' => 'D',
			']]=' => 'F',
			'(_>' => 'G',
			'[[6' => 'G',
			'|-|' => 'H',
			'(-)' => 'H',
			')-(' => 'H',
			'}-{' => 'H',
			'{-}' => 'H',
			'/-/' => 'H',
			'\\-\\' => 'H',
			'|~|' => 'H',
			'][<' => 'K',
			']]<' => 'K',
			'[]<' => 'K',
			'[]_' => 'L',
			'][_' => 'L',
			'/V\\' => 'M',
			'\\\\\\' => 'M',
			'(T)' => 'M',
			'.\\\\' => 'M',
			'//.' => 'M',
			'JVL' => 'M',
			'/\\/' => 'N',
			'|\\|' => 'N',
			'(\\)' => 'N',
			'/|/' => 'N',
			'[\\]' => 'N',
			'{\\}' => 'N',
			'[]D' => 'P',
			'][D' => 'P',
			'(,)' => 'Q',
			'[]\\' => 'Q',
			']]2' => 'R',
			'[]2' => 'R',
			'][2' => 'R',
			'\']\'' => 'T',
			'~|~' => 'T',
			'-|-' => 'T',
			'\'|\'' => 'T',
			'(_)' => 'U',
			'|_|' => 'U',
			'\\_\\' => 'U',
			'/_/' => 'U',
			'\\_/' => 'U',
			']_[' => 'U',
			'///' => 'W',
			'\\^/' => 'W',
			'\\|/' => 'Y',
			'`/_' => 'Z',

			'/\\' => 'A',
			'F|' => 'FI',
			'f|' => 'FI',
			'|7' => 'IT',
			'|5' => 'IS',
			']3' => 'B',
			']8' => 'B',
			'|3' => 'B',
			'|8' => 'B',
			'13' => 'B',
			'[[' => 'C',
			'[}' => 'D',
			'|)' => 'D',
			'|}' => 'D',
			'|>' => 'D',
			'[>' => 'D',
			'o|' => 'D',
			'ii' => 'E',
			'|=' => 'F',
			'(=' => 'F',
			'ph' => 'F',
			'}{' => 'H',
			'][' => 'I',
			//'[]' => 'I',
			'_|' => 'J',
			'u|' => 'J',
			'|<' => 'K',
			'|{' => 'K',
			'|_' => 'L',
			'^^' => 'M',
			'()' => 'O',
			'[]' => 'O',
			'<>' => 'O',
			'|o' => 'P',
			'|D' => 'P',
			'|*' => 'P',
			'|>' => 'P',
			'0,' => 'Q',
			'O,' => 'Q',
			'O\\' => 'Q',
			'|2' => 'R',
			'|?' => 'R',
			'|-' => 'R',
			'7`' => 'T',
			'\\/' => 'V',
			'VV' => 'W',
			'><' => 'X',
			//'}{' => 'X',
			')(' => 'X',
			'}[' => 'X',
			'\'/' => 'Y',
			'`/' => 'Y',
			'\\j' => 'Y',
			'-/' => 'Y',
			'7_' => 'Z',
			't1' => 'thi',
			'T1' => 'THI',
			'4' => 'A',
			'@' => 'A',
			'^' => 'A',
			'8' => 'B',
			'(' => 'C',
			'{' => 'C',
			'<' => 'C',
			')' => 'D',
			'3' => 'E',
			'6' => 'G',
			'9' => 'G',
			'&' => 'G',
			'#' => 'H',
			'1' => 'I',
			'!' => 'I',
			//'|' => 'I',
			'|' => 'L',
			'1' => 'L',
			'~' => 'N',
			'0' => 'O',
			'*' => 'O',
			'5' => 'S',
			'$' => 'S',
			'7' => 'T',
			'+' => 'T',
			'%' => 'Y',
			//'j' => 'Y',
			'2' => 'Z',
			'z' => 'Z',
		);

		$wordsubs = array(
			' joo ' => ' you ',
			' teh ' => ' the ',
			' wat ' => ' what ',
			' sas ' => ' says ',
			' u '   => ' you '
		);

		$word2 = str_replace( array_keys($subs), array_values($subs), $word);
		$word2 = strtolower($word2);
		$word2 = str_replace( array_keys($wordsubs), array_values($wordsubs), $word2);

		return $word2;
	}

	/**
	 * Normalize a word
	 *
	 * @param   string  $word
	 * @return  string
	 */
	protected static function normalize($word)
	{
		$nword = '';

		$len = strlen($word);

		for ($i = 0; $i < $word; $i++)
		{
			$o = ord($word[$i]);

			if ($o < 97)
			{
				// convert to lowercase
				$o += 32;
			}

			if ($o > 122 || $o < 97)
			{
				// skip anything not a lowercase letter
				continue;
			}

			$nword .= chr($o);
		}

		return $nword;
	}
}
