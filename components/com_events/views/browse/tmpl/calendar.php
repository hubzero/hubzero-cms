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

$database =& JFactory::getDBO();
$juser =& JFactory::getUser();
$gid = $juser->get('gid');
$gid = (intval($gid)) ? $gid : 0;

$startday = ((!_CAL_CONF_STARDAY) || (_CAL_CONF_STARDAY > 1)) ? 0 : _CAL_CONF_STARDAY;
$timeWithOffset = time() + ($this->offset*60*60);

$to_day = date("Y-m-d", $timeWithOffset);

$day_name = array(
	JText::_('EVENTS_CAL_LANG_SUNDAYSHORT'),
	JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
	JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
	JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
	JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
	JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
	JText::_('EVENTS_CAL_LANG_SATURDAYSHORT')
);

$time  = mktime(0, 0, 0, intval($this->month), 1, intval($this->year) );
$ptime = mktime(0, 0, 0, ($this->month-1), 1, intval($this->year) );
$ntime = mktime(0, 0, 0, ($this->month+1), 1, intval($this->year) );

// This month
$cal_year  = date("Y",$time);
$cal_month = date("m",$time);
$calmonth  = date("n",$time);

$this_date = new EventsDate();
$this_date->setDate( $this->year, $this->month, $this->day );

$prev_month = clone($this_date);
$prev_month->addMonths( -1 );
$next_month = clone($this_date);
$next_month->addMonths( +1 );
$prev = JRoute::_( 'index.php?option='.$this->option.'&'. $prev_month->toDateURL($this->task) );
$next = JRoute::_( 'index.php?option='.$this->option.'&'. $next_month->toDateURL($this->task) );

$content  = '<table class="ecalendar" summary="'.JText::_('EVENTS_CAL_TABLE_SUMMARY').'">'."\n";
$content .= ' <caption>';
if ($this->shownav) {
	$content .= '<a class="prv" href="'.$prev.'" title="'.JText::_('EVENTS_CAL_LANG_PREVIOUSMONTH').'">&lsaquo;</a> <a class="nxt" href="'.$next.'" title="'.JText::_('EVENTS_CAL_LANG_NEXTMONTH').'">&rsaquo;</a> ';
}
$content .= EventsHtml::getMonthName($cal_month).'</caption>'."\n";
$content .= ' <thead>'."\n";
$content .= '  <tr>'."\n";
for ($i=0;$i<7;$i++)
{
	$content.='   <th scope="col">'.$day_name[($i+$startday)%7].'</th>'."\n";
}
$content .= '  </tr>'."\n";
$content .= ' </thead>'."\n";
$content .= ' <tbody>'."\n";
$content .= '  <tr>'."\n";

// dmcd May 7/04 fix to fill in end days out of month correctly
$dayOfWeek = $startday;
$start = (date("w",mktime(0,0,0,$cal_month,1,$cal_year))-$startday+7)%7;
$d = date("t",mktime(0,0,0,$cal_month,0,$cal_year))-$start + 1;
$kownt = 0;

for ($a=$start; $a>0; $a--)
{
	$content .= '   <td';
	if ($a == $start) {
		$content .= ' class="weekend"';
	}
	$content .= '>&nbsp;</td>'."\n";
	$dayOfWeek++;
	$kownt++;
}

$monthHasEvent = false;
$eventCheck = new EventsRepeat;
$lastDayOfMonth = date("t",mktime(0,0,0,$cal_month,1,$cal_year));
$rd = 0;
for ($d=1;$d<=$lastDayOfMonth;$d++)
{
	$do = ($d<10) ? "0$d" : "$d";
	$selected_date = "$cal_year-$cal_month-$do";

	$sql = "SELECT #__events.* FROM #__events, #__categories as b"
		. "\n WHERE #__events.catid = b.id AND b.access <= $gid AND #__events.access <= $gid"
		. "\n AND ((publish_up >= '$selected_date 00:00:00' AND publish_up <= '$selected_date 23:59:59')"
		. "\n OR (publish_down >= '$selected_date 00:00:00' AND publish_down <= '$selected_date 23:59:59')"
		. "\n OR (publish_up <= '$selected_date 00:00:00' AND publish_down >= '$selected_date 23:59:59')) AND state='1'"
		. "\n ORDER BY publish_up ASC";

	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	$class = ($selected_date == $to_day) ? 'today' : '';
	if ($d == $this->day) {
		//$class .= ' selected';
	}
	$hasevents = false;
	for ($r = 0; $r < count($rows); $r++)
	{
		if ($eventCheck->EventsRepeat($rows[$r], $cal_year, $cal_month, $do)) {
			$hasevents = true;
			//$class = ($selected_date == $to_day) ? 'today' : 'withevents';
			break;
		}
	}
	if ((($dayOfWeek)%7 == $startday) || ((1 + $dayOfWeek)%7 == $startday)) {
		$class .= ' weekend';
	}
	// Only adds link if event scheduled that day
	$content .= '   <td';
	$content .= ($class) ? ' class="'.$class.'">' : '>';
	if ($hasevents) {
		$content .= '<a class="mod_events_daylink" href="'.JRoute::_('index.php?option='.$this->option.'&year='.$cal_year.'&month='.$cal_month.'&day='.$do).'">'.$d.'</a>';
	} else {
		$content .= $d;
	}
    $content .= '</td>'."\n";
	$rd++;

	// Check if Next week row
	if ((1 + $dayOfWeek++)%7 == $startday) {
		$content .= '  </tr>'."\n";
		$content .= '  <tr>'."\n";
		$rd = ($rd >= 7) ? 0 : $rd;
	}
}

for ($d=$rd;$d<=6;$d++)
{
	$content .= '   <td';
	if ($d == 6) {
		$content .= ' class="weekend"';
	}
	$content .= '>&nbsp;</td>'."\n";
}

$content .= '  </tr>'."\n";
$content .= ' </tbody>'."\n";
$content .= '</table>'."\n";

echo $content;
