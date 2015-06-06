<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2010-2012 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Password;

class Blacklist
{
	public static function inBlacklist($word)
	{
		$db =  \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (empty($word))
		{
			$word = '';
		}

		$query = 'SELECT 1 FROM #__password_blacklist WHERE word=' .  $db->Quote($word) . ';';

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	public static function simple_l33t($word)
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

		$word2 = str_replace( array_keys($subs), array_values($subs), $word);

		return strtolower($word2);
	}

	private static function l33t($word)
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
			" joo " => " you ",
			" teh " => " the ",
			" wat " => " what ",
			" sas " => " says ",
			" u " => " you "
		);

		$word2 = str_replace( array_keys($subs), array_values($subs), $word);
		$word2 = strtolower($word2);
		$word2 = str_replace( array_keys($wordsubs), array_values($wordsubs), $word2);
		return $word2;
	}

	private static function normalize_word($word)
	{
		$nword = '';

		$len = strlen($word);

		for ($i = 0; $i < $word; $i++)
		{
			$o = ord($word[$i]);

			if ($o < 97)
			{ // convert to lowercase
				$o += 32;
			}

			if ($o > 122 || $o < 97)
			{ // skip anything not a lowercase letter
				continue;
			}

			$nword .= chr($o);
		}

		return $nword;
	}

	public static function basedOnBlacklist($word)
	{
		$db =  \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (empty($word))
		{
			$word = '';
		}

		$words[] = $word;
		$words[] = strtolower($word);
		$words[] = strtolower( strrev($word) );

		$len = strlen($word);
		$word2 = '';
		// @FIXME: badly inefficient
		for ($i = 0; $i < $len; $i++)
		{
			if (preg_match('/[a-zA-Z]/',$word[$i]))
			{
				$word2 .= $word[$i];
			}
		}
		$words[] = strtolower($word2);
		$words[] = strtolower( strrev($word2) );
		$words[] = self::l33t($word);
		$words[] = strrev( self::l33t($word) );
		$words[] = self::simple_l33t($word);
		$words[] = strrev( self::simple_l33t($word) );

		$query = "SELECT 1 FROM #__password_blacklist WHERE word IN (\"";

		$query .= implode($words,'","');

		$query .= "\");";

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result; // returns true if char belongs to class
	}

	public static function nameBlacklist($word,$givenName,$middleName,$surname)
	{
		$word = self::normalize_word($word);
		$givenName = self::normalize_word($givenName);
		$middleName = self::normalize_word($middleName);
		$surname = self::normalize_word($surname);

		$words = array();
		$words[] = $givenName;
		$words[] = strrev($givenName);
		$words[] = $middleName;
		$words[] = strrev($middleName);
		$words[] = $surname;
		$words[] = strrev($surname);
		$words[] = $givenName.$middleName.$surname;
		$words[] = strrev($givenName.$middleName.$surname);
		$words[] = $givenName.$surname;
		$words[] = strrev($givenName.$surname);
		$words[] = $middleName.$surname;
		$words[] = strrev($middleName.$surname);
		$words[] = $surname.$givenName;
		$words[] = strrev($surname.$givenName);
		$words[] = $surname.$middleName.$givenName;
		$words[] = strrev($surname.$middleName.$givenName);

		foreach ($words as $w)
		{
			if ($w == $word)
			{
				return true;
			}
		}

		return false;
	}

	public static function usernameBlacklist($word,$username)
	{
		$word = self::normalize_word($word);
		$username = self::normalize_word($username);

		$words = array();
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
}
