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

namespace Components\Events\Helpers;

use Lang;

/**
 * Events helper class for misc. HTML
 */
class Html
{
	/**
	 * Auto-link strings matching URL patterns
	 * @deprectated  
	 *
	 * @param      array $matches Strings matching URL pattern
	 * @return     string
	 */
	public static function autolink($matches)
	{
		$href = $matches[0];

		if (substr($href, 0, 1) == '!')
		{
			return substr($href, 1);
		}

		$href = str_replace('"', '', $href);
		$href = str_replace("'", '', $href);
		$href = str_replace('&#8221','', $href);

		$h = array('h', 'm', 'f', 'g', 'n');
		if (!in_array(substr($href, 0, 1), $h))
		{
			$href = substr($href, 1);
		}
		$name = trim($href);
		if (substr($name, 0, 7) == 'mailto:')
		{
			$name = substr($name, 7, strlen($name));
			$name = self::obfuscate($name);
			$href = 'mailto:' . $name;
		}
		$l = sprintf(
			' <a class="ext-link" href="%s"%s>%s</a>',
			$href,
			' rel="external"',
			$name
		);
		return $l;
	}

	/**
	 * Obfuscate an email address
	 *
	 * @param      string $email Email address
	 * @return     string
	 */
	public static function obfuscate($email)
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++)
		{
			$obfuscatedEmail .= '&#' . ord($email[$i]) . ';';
		}

		return $obfuscatedEmail;
	}

	/**
	 * Short description for 'buildRadioOption'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $arr Parameter description (if any) ...
	 * @param      string $tag_name Parameter description (if any) ...
	 * @param      string $tag_attribs Parameter description (if any) ...
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      array $selected Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function buildRadioOption($arr, $tag_name, $tag_attribs, $key, $text, $selected)
	{
		$html = '';
		for ($i=0, $n=count($arr); $i < $n; $i++)
		{
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;

			$sel = '';
			if (is_array($selected))
			{
				foreach ($selected as $obj)
				{
					$k2 = $obj->$key;
					if ($k == $k2)
					{
						$sel = ' checked="checked"';
						break;
					}
				}
			}
			else
			{
				$sel = ($k == $selected ? ' checked="checked"' : '');
			}
			$html .= '<label class="option"><input class="option" name="' . $tag_name . '" id="' . $tag_name . $i . '" type="radio" value="' . $k . '"' . $sel . ' ' . $tag_attribs . '/>' . $t . '</label>' . "\n";
		}
		return $html;
	}

	/**
	 * Short description for 'buildReccurDaySelect'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $reccurday Parameter description (if any) ...
	 * @param      unknown $tag_name Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function buildReccurDaySelect($reccurday, $tag_name, $args)
	{
		$day_name = array(
			'<span style="color:red;">'.Lang::txt('EVENTS_CAL_LANG_SUNDAYSHORT').'</span>',
			Lang::txt('EVENTS_CAL_LANG_MONDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_TUESDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_THURSDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_FRIDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_SATURDAYSHORT')
		);
		$daynamelist[] = \Html::select('option', '-1', '&nbsp;' . Lang::txt('EVENTS_CAL_LANG_BYDAYNUMBER') . '<br />', 'value', 'text');
		for ($a=0; $a<7; $a++)
		{
			$name_of_day = '&nbsp;' . $day_name[$a];
			$daynamelist[] = \Html::select('option', $a, $name_of_day, 'value', 'text');
		}
		$tosend = self::buildRadioOption($daynamelist, $tag_name, $args, 'value', 'text', $reccurday);
		return $tosend;
	}

	/**
	 * Build a month select list
	 *
	 * @param      unknown $month Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function buildMonthSelect($month, $args)
	{
		for ($a=1; $a<13; $a++)
		{
			$mnh = $a;
			if ($mnh<="9"&preg_match("/(^[0-9]{1})/", $mnh))
			{
				$mnh="0" . $mnh;
			}
			$name_of_month = self::getMonthName($mnh);
			$monthslist[] = \Html::select('option', $mnh, $name_of_month, 'value', 'text');
		}
		$tosend = \Html::select('genericlist', $monthslist, 'month', $args, 'value', 'text', $month, false, false);
		return $tosend;
	}

	/**
	 * Build a day select list
	 *
	 * @param      unknown $year Parameter description (if any) ...
	 * @param      unknown $month Parameter description (if any) ...
	 * @param      unknown $day Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     string
	 */
	public static function buildDaySelect($year, $month, $day, $args)
	{
		$nbdays = date("d", mktime(0, 0, 0, ($month + 1), 0, $year));
		for ($a=1; $a<=$nbdays; $a++)
		{
			$dys = $a;
			if ($dys<="9"&preg_match("/(^[1-9]{1})/", $dys))
			{
				$dys = "0" . $dys;
			}
			$dayslist[] = \Html::select('option', $dys, $dys, 'value', 'text');
		}
		$tosend = \Html::select('genericlist', $dayslist, 'day', $args, 'value', 'text', $day, false, false);
		return $tosend;
	}

	/**
	 * Build a select list for year
	 *
	 * @param      unknown $year Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     string
	 */
	public static function buildYearSelect($year, $args)
	{
		$y = date("Y");
		if ($year<$y-2)
		{
			$yearslist[] = \Html::select('option', $year, $year, 'value', 'text');
		}
		for ($i=$y-2; $i<=$y+5; $i++)
		{
			$yearslist[] = \Html::select('option', $i, $i, 'value', 'text');
		}
		if ($year>$y+5)
		{
			$yearslist[] = \Html::select('option', $year, $year, 'value', 'text');
		}
		$tosend = \Html::select('genericlist', $yearslist, 'year', $args, 'value', 'text', $year, false, false);
		return $tosend;
	}

	/**
	 * Build a view select list
	 *
	 * @param      unknown $viewtype Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     string
	 */
	public static function buildViewSelect($viewtype, $args)
	{
		$viewlist[] = \Html::select('option', 'view_week', Lang::txt('EVENTS_CAL_LANG_VIEWBYWEEK'), 'value', 'text');
		$viewlist[] = \Html::select('option', 'view_month', Lang::txt('EVENTS_CAL_LANG_VIEWBYMONTH'), 'value', 'text');
		$viewlist[] = \Html::select('option', 'view_year', Lang::txt('EVENTS_CAL_LANG_VIEWBYYEAR'), 'value', 'text');
		//$viewlist[] = \Html::select('option', 'view_day', Lang::txt('EVENTS_CAL_LANG_VIEWBYDAY'), 'value', 'text');
		//$viewlist[] = \Html::select('option', 'view_cat', Lang::txt('EVENTS_CAL_LANG_VIEWBYCAT'), 'value', 'text');
		//$viewlist[] = \Html::select('option', 'view_search', Lang::txt('EVENTS_SEARCH_TITLE'), 'value', 'text');
		$tosend = \Html::select('genericlist', $viewlist, 'task', $args, 'value', 'text', $viewtype, false, false);
		return $tosend;
	}

	/**
	 * Build an hour select list
	 *
	 * @param      integer $start       Parameter description (if any) ...
	 * @param      integer $end         Parameter description (if any) ...
	 * @param      integer $inc         Parameter description (if any) ...
	 * @param      string  $tag_name    Parameter description (if any) ...
	 * @param      string  $tag_attribs Parameter description (if any) ...
	 * @param      string  $selected    Parameter description (if any) ...
	 * @param      string  $format      Parameter description (if any) ...
	 * @return     string
	 */
	public static function buildHourSelect($start, $end, $inc, $tag_name, $tag_attribs, $selected, $format='')
	{
		$start = intval($start);
		$end   = intval($end);
		$inc   = intval($inc);
		$arr   = array();
		$tmpi  = '';
		for ($i=$start; $i <= $end; $i+=$inc)
		{
			if (_CAL_CONF_DATEFORMAT == 1)
			{ // US time
				if ($i > 11)
				{
					$tmpi = ($i-12) . ' pm';
				}
				else
				{
					$tmpi = $i . ' am';
				}
			}
			else
			{
				$tmpi = $format ? sprintf("$format", $i) : "$i";
			}
			$fi = $format ? sprintf("$format", $i) : "$i";
			$arr[] = \Html::select('option', $fi, $tmpi, 'value', 'text');
		}
		return \Html::select('genericlist', $arr, $tag_name, $tag_attribs, 'value', 'text', $selected, false, false);
	}

	/**
	 * Build a select for categories
	 *
	 * @param      integer $catid  Category ID
	 * @param      array   $args   Arguments to add to select element
	 * @param      integer $gid    Group ID
	 * @param      string  $option Component name
	 * @return     string
	 */
	public static function buildCategorySelect($catid, $args, $gid, $option)
	{
		$database = \App::get('db');

		$catsql = "SELECT id AS value, title AS text FROM #__categories "
				. "WHERE extension='$option' AND published='1' ORDER BY lft";

		$categories[] = \Html::select('option', '0', Lang::txt('EVENTS_CAL_LANG_EVENT_CHOOSE_CATEG'), 'value', 'text');

		$database->setQuery($catsql);
		$categories = array_merge($categories, $database->loadObjectList());
		$clist = \Html::select('genericlist', $categories, 'catid', $args, 'value', 'text', $catid, false, false);

		return $clist;
	}

	/**
	 * Build a time zone select list for events
	 *
	 * @param      $tzselected - currently selected time zone
	 * @param      $args - styles for field
	 * @return     Return - select list of time zones, with current time zone selected (if applicable)
	 */
	public static function buildTimeZoneSelect($tzselected, $args)
	{
		$timezones = array(
			\Html::select('option', -12,   Lang::txt('EVENTS_TIME_UTC_-12')),
			\Html::select('option', -11,   Lang::txt('EVENTS_TIME_UTC_-11')),
			\Html::select('option', -10,   Lang::txt('EVENTS_TIME_UTC_-10')),
			\Html::select('option', -9.5,  Lang::txt('EVENTS_TIME_UTC_-930')),
			\Html::select('option', -9,    Lang::txt('EVENTS_TIME_UTC_-9')),
			\Html::select('option', -8,    Lang::txt('EVENTS_TIME_UTC_-8')),
			\Html::select('option', -7,    Lang::txt('EVENTS_TIME_UTC_-7')),
			\Html::select('option', -6,    Lang::txt('EVENTS_TIME_UTC_-6')),
			\Html::select('option', -5,    Lang::txt('EVENTS_TIME_UTC_-5')),
			\Html::select('option', -4,    Lang::txt('EVENTS_TIME_UTC_-4')),
			\Html::select('option', -4.5,  Lang::txt('EVENTS_TIME_UTC_-430')),
			\Html::select('option', -3.5,  Lang::txt('EVENTS_TIME_UTC_-330')),
			\Html::select('option', -3,    Lang::txt('EVENTS_TIME_UTC_-3')),
			\Html::select('option', -2,    Lang::txt('EVENTS_TIME_UTC_-2')),
			\Html::select('option', -1,    Lang::txt('EVENTS_TIME_UTC_-1')),
			\Html::select('option', 0,     Lang::txt('EVENTS_TIME_UTC_0')),
			\Html::select('option', 1,     Lang::txt('EVENTS_TIME_UTC_1')),
			\Html::select('option', 2,     Lang::txt('EVENTS_TIME_UTC_2')),
			\Html::select('option', 3,     Lang::txt('EVENTS_TIME_UTC_3')),
			\Html::select('option', 3.5,   Lang::txt('EVENTS_TIME_UTC_330')),
			\Html::select('option', 4,     Lang::txt('EVENTS_TIME_UTC_4')),
			\Html::select('option', 4.5,   Lang::txt('EVENTS_TIME_UTC_430')),
			\Html::select('option', 5,     Lang::txt('EVENTS_TIME_UTC_5')),
			\Html::select('option', 5.5,   Lang::txt('EVENTS_TIME_UTC_530')),
			\Html::select('option', 5.75,  Lang::txt('EVENTS_TIME_UTC_545')),
			\Html::select('option', 6,     Lang::txt('EVENTS_TIME_UTC_6')),
			\Html::select('option', 6.5,   Lang::txt('EVENTS_TIME_UTC_630')),
			\Html::select('option', 7,     Lang::txt('EVENTS_TIME_UTC_7')),
			\Html::select('option', 8,     Lang::txt('EVENTS_TIME_UTC_8')),
			\Html::select('option', 8.75,  Lang::txt('EVENTS_TIME_UTC_845')),
			\Html::select('option', 9,     Lang::txt('EVENTS_TIME_UTC_9')),
			\Html::select('option', 9.5,   Lang::txt('EVENTS_TIME_UTC_930')),
			\Html::select('option', 10,    Lang::txt('EVENTS_TIME_UTC_10')),
			\Html::select('option', 10.5,  Lang::txt('EVENTS_TIME_UTC_1030')),
			\Html::select('option', 11,    Lang::txt('EVENTS_TIME_UTC_11')),
			\Html::select('option', 11.5,  Lang::txt('EVENTS_TIME_UTC_1130')),
			\Html::select('option', 12,    Lang::txt('EVENTS_TIME_UTC_12')),
			\Html::select('option', 12.75, Lang::txt('EVENTS_TIME_UTC_1245')),
			\Html::select('option', 13,    Lang::txt('EVENTS_TIME_UTC_13')),
			\Html::select('option', 14,    Lang::txt('EVENTS_TIME_UTC_14')),
		);

		return \Html::select('genericlist', $timezones, 'time_zone', $args, 'value', 'text', $tzselected);
	}

	/**
	 * Get text/name for time zone offset number
	 *
	 * @param      string $tz Time zone of which to retrieve name
	 * @return     string Time zone name for offset given
	 */
	public static function getTimeZoneName($tz)
	{
		$timezones = array(
			"-12"   => Lang::txt('EVENTS_TIME_UTC_-12'),
			"-11"   => Lang::txt('EVENTS_TIME_UTC_-11'),
			"-10"   => Lang::txt('EVENTS_TIME_UTC_-10'),
			"-9.5"  => Lang::txt('EVENTS_TIME_UTC_-930'),
			"-9"    => Lang::txt('EVENTS_TIME_UTC_-9'),
			"-8"    => Lang::txt('EVENTS_TIME_UTC_-8'),
			"-7"    => Lang::txt('EVENTS_TIME_UTC_-7'),
			"-6"    => Lang::txt('EVENTS_TIME_UTC_-6'),
			"-5"    => Lang::txt('EVENTS_TIME_UTC_-5'),
			"-4"    => Lang::txt('EVENTS_TIME_UTC_-4'),
			"-4.5"  => Lang::txt('EVENTS_TIME_UTC_-430'),
			"-3.5"  => Lang::txt('EVENTS_TIME_UTC_-330'),
			"-3"    => Lang::txt('EVENTS_TIME_UTC_-3'),
			"-2"    => Lang::txt('EVENTS_TIME_UTC_-2'),
			"-1"    => Lang::txt('EVENTS_TIME_UTC_-1'),
			"0"     => Lang::txt('EVENTS_TIME_UTC_0'),
			"1"     => Lang::txt('EVENTS_TIME_UTC_1'),
			"2"     => Lang::txt('EVENTS_TIME_UTC_2'),
			"3"     => Lang::txt('EVENTS_TIME_UTC_3'),
			"3.5"   => Lang::txt('EVENTS_TIME_UTC_330'),
			"4"     => Lang::txt('EVENTS_TIME_UTC_4'),
			"4.5"   => Lang::txt('EVENTS_TIME_UTC_430'),
			"5"     => Lang::txt('EVENTS_TIME_UTC_5'),
			"5.5"   => Lang::txt('EVENTS_TIME_UTC_530'),
			"5.75"  => Lang::txt('EVENTS_TIME_UTC_545'),
			"6"     => Lang::txt('EVENTS_TIME_UTC_6'),
			"6.5"   => Lang::txt('EVENTS_TIME_UTC_630'),
			"7"     => Lang::txt('EVENTS_TIME_UTC_7'),
			"8"     => Lang::txt('EVENTS_TIME_UTC_8'),
			"8.75"  => Lang::txt('EVENTS_TIME_UTC_845'),
			"9"     => Lang::txt('EVENTS_TIME_UTC_9'),
			"9.5"   => Lang::txt('EVENTS_TIME_UTC_930'),
			"10"    => Lang::txt('EVENTS_TIME_UTC_10'),
			"10.5"  => Lang::txt('EVENTS_TIME_UTC_1030'),
			"11"    => Lang::txt('EVENTS_TIME_UTC_11'),
			"11.5"  => Lang::txt('EVENTS_TIME_UTC_1130'),
			"12"    => Lang::txt('EVENTS_TIME_UTC_12'),
			"12.75" => Lang::txt('EVENTS_TIME_UTC_1245'),
			"13"    => Lang::txt('EVENTS_TIME_UTC_13'),
			"14"    => Lang::txt('EVENTS_TIME_UTC_14')
		);

		if (array_key_exists($tz, $timezones))
		{
			return $timezones[$tz];
		}
		return('(timezone n/a)');
	}

	/**
	 * Build checkboxes for each day of the week
	 *
	 * @param      string $reccurweekdays Week day recursion
	 * @param      string $args           Arguments to add to element
	 * @return     string
	 */
	public static function buildWeekDaysCheck($reccurweekdays, $args)
	{
		$day_name = array(
			'<span style="color:red;">' . Lang::txt('EVENTS_CAL_LANG_SUNDAYSHORT') . '</span>',
			Lang::txt('EVENTS_CAL_LANG_MONDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_TUESDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_THURSDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_FRIDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_SATURDAYSHORT')
		);
		$tosend = '';
		if ($reccurweekdays == '')
		{
			$split = array();
			$countsplit = 0;
		}
		else
		{
			$split = explode('|', $reccurweekdays);
			$countsplit = count($split);
		}

		for ($a=0; $a<7; $a++)
		{
			$checked = '';
			for ($x = 0; $x < $countsplit; $x++)
			{
				if ($split[$x] == $a)
				{
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<label class="option"><input type="checkbox" id="cb_wd' . $a . '" name="reccurweekdays" value="' . $a . '" ' . $args . ' ' . $checked . '/>&nbsp;' . $day_name[$a] . '</label>' . "\n";
		}
		return $tosend;
	}

	/**
	 * Build checkboxes for each week of a month
	 *
	 * @param      string $reccurweeks Weekly recursion
	 * @param      string $args        Arguments to add to element
	 * @return     string
	 */
	public static function buildWeeksCheck($reccurweeks, $args)
	{
		$week_name = array(
			'',
			Lang::txt('EVENTS_CAL_LANG_REP_WEEK') . ' 1',
			Lang::txt('EVENTS_CAL_LANG_REP_WEEK') . ' 2',
			Lang::txt('EVENTS_CAL_LANG_REP_WEEK') . ' 3',
			Lang::txt('EVENTS_CAL_LANG_REP_WEEK') . ' 4',
			Lang::txt('EVENTS_CAL_LANG_REP_WEEK') . ' 5'
		);
		$tosend = '';
		$checked = '';

		if ($reccurweeks == '')
		{
			$split = array();
			$countsplit = 0;
		}
		else
		{
			$split = explode('|', $reccurweeks);
			$countsplit = count($split);
		}

		for ($a=1; $a<6; $a++)
		{
			$checked = '';
			if ($reccurweeks == '')
			{
				$checked = 'checked="checked"';
			}
			for ($x = 0; $x < $countsplit; $x++)
			{
				if ($split[$x] == $a)
				{
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<label class="option"><input class="option" type="checkbox" id="cb_wn' . $a . '" name="reccurweeks" value="' . $a . '" ' . $args . ' ' . $checked . '/>&nbsp;' . $week_name[$a] . '</label><br />' . "\n";
		}
		return $tosend;
	}

	/**
	 * Generate a mailto link for a user
	 *
	 * @param      integer $agid   Access group ID
	 * @param      integer $userid User ID
	 * @return     string
	 */
	public static function getUserMailtoLink($agid, $userid)
	{
		$agenda_viewmail = _CAL_CONF_MAILVIEW;
		if ($userid)
		{
			$user = User::getInstance($userid);

			if ($user)
			{
				if (($user->get('email')) && ($agenda_viewmail=='YES'))
				{
					$contactlink = '<a href="mailto:' . $user->get('email') . '">' . $user->get('name') . '</a>';
				}
				else
				{
					$contactlink = $user->get('username');
				}
			}
		}
		else
		{
			$database = \App::get('db');
			$database->setQuery("SELECT created_by_alias FROM #__events WHERE id='$agid'");
			$userdet = $database->loadResult();
			if ($userdet)
			{
				$contactlink = $userdet;
			}
			else
			{
				$contactlink = Lang::txt('EVENTS_CAL_LANG_ANONYME');
			}
		}

		return $contactlink;
	}

	/**
	 * Get the month name from the numerical value
	 *
	 * @param      string $month Numerical month value (01-12)
	 * @return     string
	 */
	public static function getMonthName($month)
	{
		$monthname = '';
		switch ($month)
		{
			case '01': $monthname = Lang::txt('EVENTS_CAL_LANG_JANUARY');   break;
			case '02': $monthname = Lang::txt('EVENTS_CAL_LANG_FEBRUARY');  break;
			case '03': $monthname = Lang::txt('EVENTS_CAL_LANG_MARCH');     break;
			case '04': $monthname = Lang::txt('EVENTS_CAL_LANG_APRIL');     break;
			case '05': $monthname = Lang::txt('EVENTS_CAL_LANG_MAY');       break;
			case '06': $monthname = Lang::txt('EVENTS_CAL_LANG_JUNE');      break;
			case '07': $monthname = Lang::txt('EVENTS_CAL_LANG_JULY');      break;
			case '08': $monthname = Lang::txt('EVENTS_CAL_LANG_AUGUST');    break;
			case '09': $monthname = Lang::txt('EVENTS_CAL_LANG_SEPTEMBER'); break;
			case '10': $monthname = Lang::txt('EVENTS_CAL_LANG_OCTOBER');   break;
			case '11': $monthname = Lang::txt('EVENTS_CAL_LANG_NOVEMBER');  break;
			case '12': $monthname = Lang::txt('EVENTS_CAL_LANG_DECEMBER');  break;
		}
		return $monthname;
	}

	/**
	 * Get the day name from the numerical value
	 *
	 * @param      string $daynb Numerical day value (0-6)
	 * @return     string
	 */
	public static function getLongDayName($daynb)
	{
		$dayname = '';
		switch ($daynb)
		{
			case '0': $dayname = Lang::txt('EVENTS_CAL_LANG_SUNDAY');    break;
			case '1': $dayname = Lang::txt('EVENTS_CAL_LANG_MONDAY');    break;
			case '2': $dayname = Lang::txt('EVENTS_CAL_LANG_TUESDAY');   break;
			case '3': $dayname = Lang::txt('EVENTS_CAL_LANG_WEDNESDAY'); break;
			case '4': $dayname = Lang::txt('EVENTS_CAL_LANG_THURSDAY');  break;
			case '5': $dayname = Lang::txt('EVENTS_CAL_LANG_FRIDAY');    break;
			case '6': $dayname = Lang::txt('EVENTS_CAL_LANG_SATURDAY');  break;
		}
		return $dayname;
	}

	/**
	 * Generate the date format
	 *
	 * @param      integer $year  Year
	 * @param      integer $month Month
	 * @param      integer $day   Day
	 * @param      string  $type  Format type
	 * @return     string
	 */
	public static function getDateFormat($year, $month, $day, $type)
	{
		if (empty($year))
		{
			$year = 0;
		}
		if (empty($month))
		{
			$month = 0;
		}
		if (empty($day))
		{
			$day = 1;
		}

		$format_type = _CAL_CONF_DATEFORMAT;
		$datestp     = (mktime(0, 0, 0, $month, $day, $year));
		$jour_fr     = date("j", $datestp);
		$numero_jour = date("w", $datestp);
		$mois_fr     = date("n", $datestp);
		$mois_0      = date("m", $datestp);
		$annee       = date("Y", $datestp);
		$newdate     = '';

		switch ($type)
		{
			case '0':
				if ($format_type == 0)
				{
					// Fr style : Monday 23 Juillet 2003
					$newdate = self::getLongDayName($numero_jour) . '&nbsp;' . $jour_fr . '&nbsp;' . self::getMonthName($mois_0) . '&nbsp;' . $annee;
				}
				else if ($format_type == 1)
				{
					// Us style : Monday, July 23 2003
					$newdate = self::getLongDayName($numero_jour) . ',&nbsp;' . self::getMonthName($mois_0) . '&nbsp;' . $jour_fr . '&nbsp;' . $annee;
				}
				else
				{
					// De style : Montag, 23 Juli 2003
					$newdate = self::getLongDayName($numero_jour) . ',&nbsp;' . $jour_fr . '.&nbsp;' . self::getMonthName($mois_0) . '&nbsp;' . $annee;
				}
			break;

			case '1':
				if ($format_type == 0)
				{
					// Fr style : 23 Juillet 2003
					$newdate = $jour_fr . '&nbsp;' . self::getMonthName($mois_0) . '&nbsp;' . $annee;
				}
				else if ($format_type == 1)
				{
					// Us style : July 23, 2003
					$newdate = self::getMonthName($mois_0) . '&nbsp;' . $jour_fr . ',&nbsp;' . $annee;
				}
				else
				{
					// De style : 23. Juli 2003
					$newdate = $jour_fr . '.&nbsp;' . self::getMonthName($mois_0) . '&nbsp;' . $annee;
				}
			break;

			case '2':
				if ($format_type == 0)
				{
					// Fr style : 23 Juillet
					$newdate = $jour_fr . '&nbsp;' . self::getMonthName($mois_0);
				}
				else if ($format_type == 1)
				{
					// Us style : Juillet, 23
					$newdate = self::getMonthName($mois_0) . ',&nbsp;' . $jour_fr;
				}
				else
				{
					// De style : 23. Juli
					$newdate = $jour_fr . '.&nbsp;' . self::getMonthName($mois_0);
				}
			break;

			case '3':
				if ($format_type == 0)
				{
					// Fr style : Juillet 2003
					$newdate = self::getMonthName($mois_0) . '&nbsp;' . $annee;
				}
				else if ($format_type == 1)
				{
					// Us style : Juillet 2003
					$newdate = self::getMonthName($mois_0) . '&nbsp;' . $annee;
				}
				else
				{
					// De style : Juli 2003
					$newdate = self::getMonthName($mois_0) . '&nbsp;' . $annee;
				}
			break;

			case '4':
				if ($format_type == 0)
				{
					// Fr style : 23/07/2003
					$newdate = $jour_fr . '/' . $mois_0 . '/' . $annee;
				}
				else if ($format_type == 1)
				{
					// Us style : 07/23/2003
					$newdate = $mois_0 . '/' . $jour_fr . '/' . $annee;
				}
				else
				{
					// De style : 23.07.2003
					$newdate = $jour_fr . '.' . $mois_0 . '.' . $annee;
				}
			break;

			case '5':
				if ($format_type == 0)
				{
					// Fr style : 23/07
					$newdate = $jour_fr . '/' . $mois_0;
				}
				else if ($format_type == 1)
				{
					// Us style : 07/23
					$newdate = $mois_0 . '/' . $jour_fr;
				}
				else
				{
					// De style : 23.07.
					$newdate = $jour_fr . '.' . $mois_0 . '.';
				}
			break;

			case '6':
				if ($format_type == 0)
				{
					// Fr style : 07/2003
					$newdate = $mois_0 . '/' . $annee;
				}
				else if ($format_type == 1)
				{
					// Us style : 07/2003
					$newdate = $mois_0 . '/' . $annee;
				}
				else
				{
					// De style : 07/2003
					$newdate = $mois_0 . '/' . $annee;
				}
			break;

			default:
			break;
		}
		return $newdate;
	}
}

