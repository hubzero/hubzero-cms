<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * Numeric helper class. Provides additional formatting methods for
 * working with numeric values.
 *
 * Techniques and inspiration were taken from all over, including:
 *	Kohana Framework: kohanaframework.org
 *	CakePHP: cakephp.org
 *	Fuel Framework: fuelphp.com
 */
class Number
{
	/**
	 * Converts a number of bytes to a human readable number by taking the
	 * number of that unit that the bytes will go into it. Supports TB value.
	 *
	 * Note: Integers in PHP are limited to 32 bits, unless they are on 64 bit
	 * architectures, then they have 64 bit size. If you need to place the
	 * larger size then what the PHP integer type will hold, then use a string.
	 * It will be converted to a double, which should always have 64 bit length.
	 *
	 * @param   integer $bytes
	 * @param   integer $decimals
	 * @return  string
	 */
	public static function formatBytes($bytes = 0, $decimals = 0)
	{
		$quant = array(
			'TB' => 1099511627776,  // pow(1024, 4)
			'GB' => 1073741824,     // pow(1024, 3)
			'MB' => 1048576,        // pow(1024, 2)
			'KB' => 1024,           // pow(1024, 1)
			'B ' => 1,              // pow(1024, 0)
		);

		foreach ($quant as $unit => $mag)
		{
			if (doubleval($bytes) >= $mag)
			{
				return sprintf('%01.' . $decimals . 'f', ($bytes / $mag)) . ' ' . $unit;
			}
		}

		return '';
	}

	/**
	 * Converts a number into a more readable human-type number.
	 *
	 * Usage:
	 * <code>
	 * echo Number::quantity(7000); // 7K
	 * echo Number::quantity(7500); // 8K
	 * echo Number::quantity(7500, 1); // 7.5K
	 * </code>
	 *
	 * @param   integer $num
	 * @param   integer $decimals
	 * @return  string
	 */
	public static function quantity($num, $decimals = 0)
	{
		if ($num >= 1000 && $num < 1000000)
		{
			return sprintf('%01.'.$decimals.'f', (sprintf('%01.0f', $num) / 1000)).'K';
		}
		elseif ($num >= 1000000 && $num < 1000000000)
		{
			return sprintf('%01.'.$decimals.'f', (sprintf('%01.0f', $num) / 1000000)).'M';
		}
		elseif ($num >= 1000000000)
		{
			return sprintf('%01.'.$decimals.'f', (sprintf('%01.0f', $num) / 1000000000)).'B';
		}

		return $num;
	}

	/**
	 * Formats a number by injecting non-numeric characters in a specified
	 * format into the string in the positions they appear in the format.
	 *
	 * Usage:
	 * <code>
	 * echo Number::format('1234567890', '(000) 000-0000'); // (123) 456-7890
	 * echo Number::format('1234567890', '000.000.0000'); // 123.456.7890
	 * </code>
	 *
	 * @link    http://snippets.symfony-project.org/snippet/157
	 * @param   string     the string to format
	 * @param   string     the format to apply
	 * @return  string
	 */
	public static function format($string = '', $format = '')
	{
		if (empty($format) || empty($string))
		{
			return $string;
		}

		if ($format == 'bytes')
		{
			return self::formatBytes($string);
		}

		$result = '';
		$fpos = 0;
		$spos = 0;

		while ((strlen($format) - 1) >= $fpos)
		{
			if (ctype_alnum(substr($format, $fpos, 1)))
			{
				$result .= substr($string, $spos, 1);
				$spos++;
			}
			else
			{
				$result .= substr($format, $fpos, 1);
			}

			$fpos++;
		}

		return $result;
	}
}

