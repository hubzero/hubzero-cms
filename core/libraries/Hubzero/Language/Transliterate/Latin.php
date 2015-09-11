<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Language\Transliterate;

/**
 * Class to transliterate strings
 */
class Latin
{
	/**
	 * Returns strings transliterated from UTF-8 to Latin
	 *
	 * @param   string   $string  String to transliterate
	 * @param   boolean  $case    Optionally specify upper or lower case.
	 * @return  string   Transliterated string
	 */
	public static function toAscii($string, $case = 0)
	{
		static $lower = null;
		static $upper = null;

		if ($case <= 0)
		{
			if (is_null($lower))
			{
				$lower = array(
					'à' => 'a',
					'ô' => 'o',
					'ď' => 'd',
					'ḟ' => 'f',
					'ë' => 'e',
					'š' => 's',
					'ơ' => 'o',
					'ß' => 'ss',
					'ă' => 'a',
					'ř' => 'r',
					'ț' => 't',
					'ň' => 'n',
					'ā' => 'a',
					'ķ' => 'k',
					'ŝ' => 's',
					'ỳ' => 'y',
					'ņ' => 'n',
					'ĺ' => 'l',
					'ħ' => 'h',
					'ṗ' => 'p',
					'ó' => 'o',
					'ú' => 'u',
					'ě' => 'e',
					'é' => 'e',
					'ç' => 'c',
					'ẁ' => 'w',
					'ċ' => 'c',
					'õ' => 'o',
					'ṡ' => 's',
					'ø' => 'o',
					'ģ' => 'g',
					'ŧ' => 't',
					'ș' => 's',
					'ė' => 'e',
					'ĉ' => 'c',
					'ś' => 's',
					'î' => 'i',
					'ű' => 'u',
					'ć' => 'c',
					'ę' => 'e',
					'ŵ' => 'w',
					'ṫ' => 't',
					'ū' => 'u',
					'č' => 'c',
					'ö' => 'oe',
					'è' => 'e',
					'ŷ' => 'y',
					'ą' => 'a',
					'ł' => 'l',
					'ų' => 'u',
					'ů' => 'u',
					'ş' => 's',
					'ğ' => 'g',
					'ļ' => 'l',
					'ƒ' => 'f',
					'ž' => 'z',
					'ẃ' => 'w',
					'ḃ' => 'b',
					'å' => 'a',
					'ì' => 'i',
					'ï' => 'i',
					'ḋ' => 'd',
					'ť' => 't',
					'ŗ' => 'r',
					'ä' => 'ae',
					'í' => 'i',
					'ŕ' => 'r',
					'ê' => 'e',
					'ü' => 'ue',
					'ò' => 'o',
					'ē' => 'e',
					'ñ' => 'n',
					'ń' => 'n',
					'ĥ' => 'h',
					'ĝ' => 'g',
					'đ' => 'd',
					'ĵ' => 'j',
					'ÿ' => 'y',
					'ũ' => 'u',
					'ŭ' => 'u',
					'ư' => 'u',
					'ţ' => 't',
					'ý' => 'y',
					'ő' => 'o',
					'â' => 'a',
					'ľ' => 'l',
					'ẅ' => 'w',
					'ż' => 'z',
					'ī' => 'i',
					'ã' => 'a',
					'ġ' => 'g',
					'ṁ' => 'm',
					'ō' => 'o',
					'ĩ' => 'i',
					'ù' => 'u',
					'į' => 'i',
					'ź' => 'z',
					'á' => 'a',
					'û' => 'u',
					'þ' => 'th',
					'ð' => 'dh',
					'æ' => 'ae',
					'µ' => 'u',
					'ĕ' => 'e',
					'œ' => 'oe'
				);
			}

			$string = str_replace(array_keys($lower), array_values($lower), $string);
		}

		if ($case >= 0)
		{
			if (is_null($upper))
			{
				$upper = array(
					'À' => 'A',
					'Ô' => 'O',
					'Ď' => 'D',
					'Ḟ' => 'F',
					'Ë' => 'E',
					'Š' => 'S',
					'Ơ' => 'O',
					'Ă' => 'A',
					'Ř' => 'R',
					'Ț' => 'T',
					'Ň' => 'N',
					'Ā' => 'A',
					'Ķ' => 'K',
					'Ŝ' => 'S',
					'Ỳ' => 'Y',
					'Ņ' => 'N',
					'Ĺ' => 'L',
					'Ħ' => 'H',
					'Ṗ' => 'P',
					'Ó' => 'O',
					'Ú' => 'U',
					'Ě' => 'E',
					'É' => 'E',
					'Ç' => 'C',
					'Ẁ' => 'W',
					'Ċ' => 'C',
					'Õ' => 'O',
					'Ṡ' => 'S',
					'Ø' => 'O',
					'Ģ' => 'G',
					'Ŧ' => 'T',
					'Ș' => 'S',
					'Ė' => 'E',
					'Ĉ' => 'C',
					'Ś' => 'S',
					'Î' => 'I',
					'Ű' => 'U',
					'Ć' => 'C',
					'Ę' => 'E',
					'Ŵ' => 'W',
					'Ṫ' => 'T',
					'Ū' => 'U',
					'Č' => 'C',
					'Ö' => 'Oe',
					'È' => 'E',
					'Ŷ' => 'Y',
					'Ą' => 'A',
					'Ł' => 'L',
					'Ų' => 'U',
					'Ů' => 'U',
					'Ş' => 'S',
					'Ğ' => 'G',
					'Ļ' => 'L',
					'Ƒ' => 'F',
					'Ž' => 'Z',
					'Ẃ' => 'W',
					'Ḃ' => 'B',
					'Å' => 'A',
					'Ì' => 'I',
					'Ï' => 'I',
					'Ḋ' => 'D',
					'Ť' => 'T',
					'Ŗ' => 'R',
					'Ä' => 'Ae',
					'Í' => 'I',
					'Ŕ' => 'R',
					'Ê' => 'E',
					'Ü' => 'Ue',
					'Ò' => 'O',
					'Ē' => 'E',
					'Ñ' => 'N',
					'Ń' => 'N',
					'Ĥ' => 'H',
					'Ĝ' => 'G',
					'Đ' => 'D',
					'Ĵ' => 'J',
					'Ÿ' => 'Y',
					'Ũ' => 'U',
					'Ŭ' => 'U',
					'Ư' => 'U',
					'Ţ' => 'T',
					'Ý' => 'Y',
					'Ő' => 'O',
					'Â' => 'A',
					'Ľ' => 'L',
					'Ẅ' => 'W',
					'Ż' => 'Z',
					'Ī' => 'I',
					'Ã' => 'A',
					'Ġ' => 'G',
					'Ṁ' => 'M',
					'Ō' => 'O',
					'Ĩ' => 'I',
					'Ù' => 'U',
					'Į' => 'I',
					'Ź' => 'Z',
					'Á' => 'A',
					'Û' => 'U',
					'Þ' => 'Th',
					'Ð' => 'Dh',
					'Æ' => 'Ae',
					'Ĕ' => 'E',
					'Œ' => 'Oe'
				);
			}
			$string = str_replace(array_keys($upper), array_values($upper), $string);
		}

		return $string;
	}
}
