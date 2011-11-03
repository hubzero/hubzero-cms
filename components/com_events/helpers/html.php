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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'EventsHtml'
 * 
 * Long description (if any) ...
 */
class EventsHtml
{

	/**
	 * Short description for 'autolink'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $matches Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function autolink($matches)
	{
		$href = $matches[0];

		if (substr($href, 0, 1) == '!') {
			return substr($href, 1);
		}

		$href = str_replace('"','',$href);
		$href = str_replace("'",'',$href);
		$href = str_replace('&#8221','',$href);

		$h = array('h','m','f','g','n');
		if (!in_array(substr($href,0,1), $h)) {
			$href = substr($href, 1);
		}
		$name = trim($href);
		if (substr($name, 0, 7) == 'mailto:') {
			$name = substr($name, 7, strlen($name));
			$name = Eventshtml::obfuscate($name);
			//$href = Eventshtml::obfuscate($href);
			$href = 'mailto:'.$name;
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
	 * Short description for 'obfuscate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $email Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function obfuscate( $email )
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++)
		{
			$obfuscatedEmail .= '&#'. ord($email[$i]) .';';
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
	public function buildRadioOption( $arr, $tag_name, $tag_attribs, $key, $text, $selected )
	{
		$html = '';
		for ($i=0, $n=count( $arr ); $i < $n; $i++ )
		{
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;

			$sel = '';
			if (is_array( $selected )) {
				foreach ($selected as $obj)
				{
					$k2 = $obj->$key;
					if ($k == $k2) {
						$sel = ' checked="checked"';
						break;
					}
				}
			} else {
				$sel = ($k == $selected ? ' checked="checked"' : '');
			}
			$html .= '<label class="option"><input class="option" name="'.$tag_name.'" id="'.$tag_name.$i.'" type="radio" value="'.$k.'"'.$sel.' '.$tag_attribs.'/>'.$t.'</label>'."\n";
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
	public function buildReccurDaySelect($reccurday, $tag_name, $args)
	{
		$day_name = array('<span style="color:red;">'.JText::_('EVENTS_CAL_LANG_SUNDAYSHORT').'</span>',
							JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_SATURDAYSHORT'));
		$daynamelist[] = JHTML::_('select.option', '-1', '&nbsp;'.JText::_('EVENTS_CAL_LANG_BYDAYNUMBER').'<br />', 'value', 'text');
		for ($a=0; $a<7; $a++)
		{
			$name_of_day = '&nbsp;'.$day_name[$a];
			$daynamelist[] = JHTML::_('select.option', $a, $name_of_day, 'value', 'text');
        }
		$tosend = EventsHtml::buildRadioOption( $daynamelist, $tag_name, $args, 'value', 'text', $reccurday );
		return $tosend;
    }

	/**
	 * Short description for 'buildMonthSelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $month Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function buildMonthSelect($month, $args)
	{
		for ($a=1; $a<13; $a++)
		{
			$mnh = $a;
			if ($mnh<="9"&ereg("(^[0-9]{1})",$mnh)) {
				$mnh="0".$mnh;
			}
			$name_of_month = EventsHtml::getMonthName($mnh);
			$monthslist[] = JHTML::_('select.option', $mnh, $name_of_month, 'value', 'text');
		}
		$tosend = JHTML::_('select.genericlist', $monthslist, 'month', $args, 'value', 'text', $month, false, false );
		return $tosend;
    }

	/**
	 * Short description for 'buildDaySelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $year Parameter description (if any) ...
	 * @param      unknown $month Parameter description (if any) ...
	 * @param      unknown $day Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function buildDaySelect($year, $month, $day, $args)
	{
		$nbdays = date("d",mktime(0,0,0,($month + 1),0,$year));
		for ($a=1; $a<=$nbdays; $a++)
		{
			$dys = $a;
			if ($dys<="9"&ereg("(^[1-9]{1})",$dys)) {
				$dys="0".$dys;
			}
			$dayslist[] = JHTML::_('select.option', $dys, $dys, 'value', 'text');
		}
		$tosend = JHTML::_('select.genericlist', $dayslist, 'day', $args, 'value', 'text', $day, false, false );
		return $tosend;
    }

	/**
	 * Short description for 'buildYearSelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $year Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function buildYearSelect($year, $args)
	{
		$y = date("Y");
		if ($year<$y-2) {
			$yearslist[] = JHTML::_('select.option', $year, $year, 'value', 'text');
        }
		for ($i=$y-2;$i<=$y+5;$i++)
		{
			$yearslist[] = JHTML::_('select.option', $i, $i, 'value', 'text');
        }
		if ($year>$y+5) {
			$yearslist[] = JHTML::_('select.option', $year, $year, 'value', 'text');
		}
		$tosend = JHTML::_('select.genericlist', $yearslist, 'year', $args, 'value', 'text', $year, false, false );
		return $tosend;
    }

	/**
	 * Short description for 'buildViewSelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $viewtype Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function buildViewSelect($viewtype, $args)
	{
		$viewlist[] = JHTML::_('select.option', 'view_week', JText::_('EVENTS_CAL_LANG_VIEWBYWEEK'), 'value', 'text');
		$viewlist[] = JHTML::_('select.option', 'view_month', JText::_('EVENTS_CAL_LANG_VIEWBYMONTH'), 'value', 'text');
		$viewlist[] = JHTML::_('select.option', 'view_year', JText::_('EVENTS_CAL_LANG_VIEWBYYEAR'), 'value', 'text');
		//$viewlist[] = JHTML::_('select.option', 'view_day', JText::_('EVENTS_CAL_LANG_VIEWBYDAY'), 'value', 'text');
		//$viewlist[] = JHTML::_('select.option', 'view_cat', JText::_('EVENTS_CAL_LANG_VIEWBYCAT'), 'value', 'text');
		//$viewlist[] = JHTML::_('select.option', 'view_search', JText::_('EVENTS_SEARCH_TITLE'), 'value', 'text');
		$tosend = JHTML::_('select.genericlist', $viewlist, 'task', $args, 'value', 'text', $viewtype, false, false );
		return $tosend;
	}

	/**
	 * Short description for 'buildHourSelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $start Parameter description (if any) ...
	 * @param      unknown $end Parameter description (if any) ...
	 * @param      unknown $inc Parameter description (if any) ...
	 * @param      unknown $tag_name Parameter description (if any) ...
	 * @param      unknown $tag_attribs Parameter description (if any) ...
	 * @param      unknown $selected Parameter description (if any) ...
	 * @param      string $format Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function buildHourSelect( $start, $end, $inc, $tag_name, $tag_attribs, $selected, $format='' )
	{
		$start = intval( $start );
		$end = intval( $end );
		$inc = intval( $inc );
		$arr = array();
		$tmpi = '';
		for ($i=$start; $i <= $end; $i+=$inc)
		{
			if (_CAL_CONF_DATEFORMAT == 1) { // US time
				if ($i > 11) {
					$tmpi = ($i-12).' pm';
				} else {
					$tmpi = $i.' am';
				}
			} else {
				$tmpi = $format ? sprintf( "$format", $i ) : "$i";
			}
			$fi = $format ? sprintf( "$format", $i ) : "$i";
			$arr[] = JHTML::_('select.option', $fi, $tmpi, 'value', 'text');
		}
		return JHTML::_('select.genericlist', $arr, $tag_name, $tag_attribs, 'value', 'text', $selected, false, false );
	}

	/**
	 * Short description for 'buildCategorySelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $catid Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @param      unknown $gid Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function buildCategorySelect($catid, $args, $gid, $option)
	{
		$database =& JFactory::getDBO();

		$catsql = "SELECT id AS value, name AS text FROM #__categories "
				. "WHERE section='$option' AND access<='$gid' AND published='1' ORDER BY ordering";

		$categories[] = JHTML::_('select.option', '0', JText::_('EVENTS_CAL_LANG_EVENT_CHOOSE_CATEG'), 'value', 'text');

		$database->setQuery($catsql);
		$categories = array_merge( $categories, $database->loadObjectList() );
		$clist = JHTML::_('select.genericlist', $categories, 'catid', $args, 'value', 'text', $catid, false, false );

		return $clist;
	}

	public function buildTimeZoneSelect($tzselected, $args)
	{
		$timezones[] = JHTML::_('select.option', 'est', JText::_('EVENTS_CAL_TIME_ZONE_EST'), 'value', 'text');
		$timezones[] = JHTML::_('select.option', 'edt', JText::_('EVENTS_CAL_TIME_ZONE_EDT'), 'value', 'text');
		$timezones[] = JHTML::_('select.option', 'cst', JText::_('EVENTS_CAL_TIME_ZONE_CST'), 'value', 'text');
		$timezones[] = JHTML::_('select.option', 'cdt', JText::_('EVENTS_CAL_TIME_ZONE_CDT'), 'value', 'text');
		$timezones[] = JHTML::_('select.option', 'mst', JText::_('EVENTS_CAL_TIME_ZONE_MST'), 'value', 'text');
		$timezones[] = JHTML::_('select.option', 'mdt', JText::_('EVENTS_CAL_TIME_ZONE_MDT'), 'value', 'text');
		$timezones[] = JHTML::_('select.option', 'pst', JText::_('EVENTS_CAL_TIME_ZONE_PST'), 'value', 'text');
		$timezones[] = JHTML::_('select.option', 'pdt', JText::_('EVENTS_CAL_TIME_ZONE_PDT'), 'value', 'text');

		$tzlist = JHTML::_('select.genericlist', $timezones, 'time_zone', $args, 'value', 'text', $tzselected, false, false );

		return $tzlist;
	}

	public function buildWeekDaysCheck($reccurweekdays, $args)
	{
		$day_name = array('<span style="color:red;">'.JText::_('EVENTS_CAL_LANG_SUNDAYSHORT').'</span>',
							JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_SATURDAYSHORT'));
		$tosend = '';
		if ($reccurweekdays == '') {
			$split = array();
			$countsplit = 0;
		} else {
			$split = explode("|", $reccurweekdays);
			$countsplit = count($split);
		}

		for ($a=0; $a<7; $a++)
		{
			$checked = '';
			for ($x = 0; $x < $countsplit; $x++)
			{
				if ($split[$x] == $a) {
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<label class="option"><input type="checkbox" id="cb_wd'.$a.'" name="reccurweekdays" value="'.$a.'" '.$args.' '.$checked.'/>&nbsp;'.$day_name[$a].'</label>'."\n";
		}
		return $tosend;
	}

	/**
	 * Short description for 'buildWeeksCheck'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $reccurweeks Parameter description (if any) ...
	 * @param      string $args Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildWeeksCheck($reccurweeks, $args)
	{
		$week_name = array('',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 1',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 2',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 3',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 4',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 5');
		$tosend = '';
		$checked = '';

		if ($reccurweeks == '') {
			$split = array();
			$countsplit = 0;
		} else {
			$split = explode("|", $reccurweeks);
			$countsplit = count($split);
		}

		for ($a=1; $a<6; $a++)
		{
			$checked = '';
			if ($reccurweeks == '') {
				$checked = 'checked="checked"';
			}
			for ($x = 0; $x < $countsplit; $x++)
			{
				if ($split[$x] == $a) {
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<label class="option"><input class="option" type="checkbox" id="cb_wn'.$a.'" name="reccurweeks" value="'.$a.'" '.$args.' '.$checked.'/>&nbsp;'.$week_name[$a].'</label><br />'."\n";
		}
		return $tosend;
	}

	/**
	 * Short description for 'getUserMailtoLink'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $agid Parameter description (if any) ...
	 * @param      unknown $userid Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
    public function getUserMailtoLink($agid, $userid)
	{
		$agenda_viewmail = _CAL_CONF_MAILVIEW;
		if ($userid) {
			$juser =& JUser::getInstance( $userid );

			if ($juser) {
				if (($juser->get('email')) && ($agenda_viewmail=='YES')) {
					$contactlink = '<a href="mailto:'.$juser->get('email').'">'.$juser->get('name').'</a>';
				} else {
					$contactlink = $juser->get('username');
				}
			}
		} else {
			$database =& JFactory::getDBO();
			$database->setQuery("SELECT created_by_alias FROM #__events WHERE id='$agid'");
			$userdet = $database->loadResult();
			if ($userdet) {
				$contactlink = $userdet;
			} else {
				$contactlink = JText::_('EVENTS_CAL_LANG_ANONYME');
			}
		}

		return $contactlink;
	}

	/**
	 * Short description for 'getMonthName'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $month Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getMonthName($month)
	{
		$monthname = '';
		switch ($month)
		{
			case '01': $monthname = JText::_('EVENTS_CAL_LANG_JANUARY');   break;
			case '02': $monthname = JText::_('EVENTS_CAL_LANG_FEBRUARY');  break;
			case '03': $monthname = JText::_('EVENTS_CAL_LANG_MARCH');     break;
			case '04': $monthname = JText::_('EVENTS_CAL_LANG_APRIL');     break;
			case '05': $monthname = JText::_('EVENTS_CAL_LANG_MAY');       break;
			case '06': $monthname = JText::_('EVENTS_CAL_LANG_JUNE');      break;
			case '07': $monthname = JText::_('EVENTS_CAL_LANG_JULY');      break;
			case '08': $monthname = JText::_('EVENTS_CAL_LANG_AUGUST');    break;
			case '09': $monthname = JText::_('EVENTS_CAL_LANG_SEPTEMBER'); break;
			case '10': $monthname = JText::_('EVENTS_CAL_LANG_OCTOBER');   break;
			case '11': $monthname = JText::_('EVENTS_CAL_LANG_NOVEMBER');  break;
			case '12': $monthname = JText::_('EVENTS_CAL_LANG_DECEMBER');  break;
        }
		return $monthname;
	}

	/**
	 * Short description for 'getLongDayName'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $daynb Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getLongDayName($daynb)
	{
		$dayname = '';
		switch ($daynb)
		{
			case '0': $dayname = JText::_('EVENTS_CAL_LANG_SUNDAY');    break;
			case '1': $dayname = JText::_('EVENTS_CAL_LANG_MONDAY');    break;
			case '2': $dayname = JText::_('EVENTS_CAL_LANG_TUESDAY');   break;
			case '3': $dayname = JText::_('EVENTS_CAL_LANG_WEDNESDAY'); break;
			case '4': $dayname = JText::_('EVENTS_CAL_LANG_THURSDAY');  break;
			case '5': $dayname = JText::_('EVENTS_CAL_LANG_FRIDAY');    break;
			case '6': $dayname = JText::_('EVENTS_CAL_LANG_SATURDAY');  break;
		}
		return $dayname;
	}

	/**
	 * Short description for 'getDateFormat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $year Parameter description (if any) ...
	 * @param      integer $month Parameter description (if any) ...
	 * @param      integer $day Parameter description (if any) ...
	 * @param      unknown $type Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getDateFormat($year,$month,$day, $type)
	{
		if (empty($year)) $year = 0;
		if (empty($month)) $month = 0;
		if (empty($day)) $day = 1;

		$format_type = _CAL_CONF_DATEFORMAT;
		$datestp     = (mktime(0,0,0,$month,$day,$year));
		$jour_fr     = date("j", $datestp);
		$numero_jour = date("w", $datestp);
		$mois_fr     = date("n", $datestp);
		$mois_0      = date("m", $datestp);
		$annee       = date("Y", $datestp);
		$newdate     = '';

		switch ($type)
		{
			case '0':
				if ($format_type == 0) {
					// Fr style : Monday 23 Juillet 2003
					$newdate = EventsHtml::getLongDayName($numero_jour).'&nbsp;'.$jour_fr.'&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				} else if ($format_type == 1) {
					// Us style : Monday, July 23 2003
					$newdate = EventsHtml::getLongDayName($numero_jour).',&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$jour_fr.'&nbsp;'.$annee;
				} else {
					// De style : Montag, 23 Juli 2003
					$newdate = EventsHtml::getLongDayName($numero_jour).',&nbsp;'.$jour_fr.'.&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				}
			break;

			case '1':
				if ($format_type == 0) {
					// Fr style : 23 Juillet 2003
					$newdate = $jour_fr.'&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				} else if ($format_type == 1) {
					// Us style : July 23, 2003
					$newdate = EventsHtml::getMonthName($mois_0).'&nbsp;'.$jour_fr.',&nbsp;'.$annee;
				} else {
					// De style : 23. Juli 2003
					$newdate = $jour_fr.'.&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				}
			break;

			case '2':
				if ($format_type == 0) {
					// Fr style : 23 Juillet
					$newdate = $jour_fr.'&nbsp;'.EventsHtml::getMonthName($mois_0);
				} else if ($format_type == 1) {
					// Us style : Juillet, 23
					$newdate = EventsHtml::getMonthName($mois_0).',&nbsp;'.$jour_fr;
				} else {
					// De style : 23. Juli
					$newdate = $jour_fr.'.&nbsp;'.EventsHtml::getMonthName($mois_0);
				}
			break;

			case '3':
				if ($format_type == 0) {
					// Fr style : Juillet 2003
					$newdate = EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				} else if ($format_type == 1) {
					// Us style : Juillet 2003
					$newdate = EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				} else {
					// De style : Juli 2003
					$newdate = EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				}
			break;

			case '4':
				if ($format_type == 0) {
					// Fr style : 23/07/2003
					$newdate = $jour_fr.'/'.$mois_0.'/'.$annee;
				} else if ($format_type == 1) {
					// Us style : 07/23/2003
					$newdate = $mois_0.'/'.$jour_fr.'/'.$annee;
				} else {
					// De style : 23.07.2003
					$newdate = $jour_fr.'.'.$mois_0.'.'.$annee;
				}
			break;

			case '5':
				if ($format_type == 0) {
					// Fr style : 23/07
					$newdate = $jour_fr.'/'.$mois_0;
				} else if ($format_type == 1){
					// Us style : 07/23
					$newdate = $mois_0.'/'.$jour_fr;
				} else {
					// De style : 23.07.
					$newdate = $jour_fr.'.'.$mois_0.'.';
				}
			break;

			case '6':
				if ($format_type == 0) {
					// Fr style : 07/2003
					$newdate = $mois_0.'/'.$annee;
				} else if ($format_type == 1) {
					// Us style : 07/2003
					$newdate = $mois_0.'/'.$annee;
				} else {
					// De style : 07/2003
					$newdate = $mois_0.'/'.$annee;
				}
			break;

			default:
			break;
		}
		return $newdate;
	}
}

