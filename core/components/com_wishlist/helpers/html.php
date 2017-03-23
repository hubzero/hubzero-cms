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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Helpers;

use Lang;

/**
 * Wishlist helper class for misc. HTML
 */
class Html
{
	/**
	 * Generate a select form
	 *
	 * @param   string  $name   Field name
	 * @param   array   $array  Data to populate select with
	 * @param   mixed   $value  Value to select
	 * @param   string  $class  Class to add
	 * @return  string  HTML
	 */
	public static function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="' . $name . '" id="' . $name . '"';
		$out .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		foreach ($array as $avalue => $alabel)
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="' . $avalue . '"' . $selected . '>' . $alabel . '</option>' . "\n";
		}
		$out .= '</select>' . "\n";
		return $out;
	}

	/**
	 * Convert a numerical vote value to a readable text value
	 *
	 * @param   integer  $rawnum    Vote value
	 * @param   string   $category  Vote type
	 * @param   string   $output    Value to append to
	 * @return  string
	 */
	public static function convertVote($rawnum, $category, $output='')
	{
		$rawnum = round($rawnum);
		if ($category == 'importance')
		{
			switch ($rawnum)
			{
				case 0: $output = Lang::txt('COM_WISHLIST_RUBBISH');     break;
				case 1: $output = Lang::txt('COM_WISHLIST_MAYBE');       break;
				case 2: $output = Lang::txt('COM_WISHLIST_INTERESTING'); break;
				case 3: $output = Lang::txt('COM_WISHLIST_GOODIDEA');    break;
				case 4: $output = Lang::txt('COM_WISHLIST_IMPORTANT');   break;
				case 5: $output = Lang::txt('COM_WISHLIST_CRITICAL');    break;
			}
		}
		else if ($category == 'effort')
		{
			switch ($rawnum)
			{
				case 0: $output = Lang::txt('COM_WISHLIST_TWOMONTHS');   break;
				case 1: $output = Lang::txt('COM_WISHLIST_TWOWEEKS');    break;
				case 2: $output = Lang::txt('COM_WISHLIST_ONEWEEK');     break;
				case 3: $output = Lang::txt('COM_WISHLIST_TWODAYS');     break;
				case 4: $output = Lang::txt('COM_WISHLIST_ONEDAY');      break;
				case 5: $output = Lang::txt('COM_WISHLIST_FOURHOURS');   break;
				case 6: $output = Lang::txt('COM_WISHLIST_DONT_KNOW');   break;
				case 7: $output = Lang::txt('COM_WISHLIST_NA');          break;
			}
		}

		return $output;
	}

	/**
	 * Convert a timestamp to a more human readable string such as "3 days ago"
	 *
	 * @param   string  $date  Timestamp
	 * @return  string
	 */
	public static function nicetime($date)
	{
		if (empty($date))
		{
			return Lang::txt('No date provided');
		}

		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
		$lengths = array('60', '60', '24', '7', '4.35', '12', '10');

		$now = time();
		$unix_date = strtotime($date);

		// check validity of date
		if (empty($unix_date))
		{
			return Lang::txt('Bad date');
		}

		// is it future date or past date
		if ($now > $unix_date)
		{
			$difference = $now - $unix_date;
			$tense = 'ago';

		}
		else
		{
			$difference = $unix_date - $now;
			$tense = '';
		}

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++)
		{
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if ($difference != 1)
		{
			$periods[$j] .= 's';
		}

		return "$difference $periods[$j] {$tense}";
	}
}
