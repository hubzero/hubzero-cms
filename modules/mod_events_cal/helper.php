<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
defined('_JEXEC') or die('Restricted access');

/**
 * Short description for 'modEventsCalendar'
 * 
 * Long description (if any) ...
 */
class modEventsCalendar
{

	/**
	 * Description for '_attributes'
	 * 
	 * @var array
	 */
	private $_attributes = array();
	/**
	 * Description for 'attributes'
	 * 
	 * @var array
	 */

	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_attributes[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->_attributes[$property]))
		{
			return $this->_attributes[$property];
		}
	}

	/**
	 * Short description for '__isset'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function display()
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet($this->module->module);

		$juser =& JFactory::getUser();

		if (!$juser->get('guest') && intval($this->params->get('cache', 0)))
		{
			$cache =& JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . date('Y-m-d H:i:s', time()) . ' -->';
			return;
		}

		$this->run();
	}

	/**
	 * Short description for 'run'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function run()
	{
		$lang =& JFactory::getLanguage();

		// Check the events component
		if (file_exists(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'html.php'))
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'html.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'date.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'repeat.php');
		} else {
			$this->error = JText::_('MOD_EVENTS_LATEST_COMPONENT_REQUIRED');
			return;
		}

		// Display last month?
		$displayLastMonth = $this->params->get('display_last_month');
		switch ($displayLastMonth)
		{
			case 'YES_stop':
				$disp_lastMonthDays = abs(intval($this->params->get('display_last_month_days')));
				$disp_lastMonth = 1;
				break;
			case 'YES_stop_events':
				$disp_lastMonthDays = abs(intval($this->params->get('display_last_month_days')));
				$disp_lastMonth = 2;
				break;
			case 'ALWAYS':
				$disp_lastMonthDays = 0;
				$disp_lastMonth = 1;
				break;
			case 'ALWAYS_events':
				$disp_lastMonthDays = 0;
				$disp_lastMonth = 2;
				break;
			case 'NO':
			default:
				$disp_lastMonthDays = 0;
				$disp_lastMonth = 0;
				break;
		}

		// Display next month?
		$displayNextMonth = $this->params->get('display_next_month');
		switch ($displayNextMonth)
		{
			case 'YES_stop':
				$disp_nextMonthDays = abs(intval($this->params->get('display_next_month_days')));
				$disp_nextMonth = 1;
				break;
			case 'YES_stop_events':
				$disp_nextMonthDays = abs(intval($this->params->get('display_next_month_days')));
				$disp_nextMonth = 2;
				break;
			case 'ALWAYS':
				$disp_nextMonthDays = 0;
				$disp_nextMonth = 1;
				break;
			case 'ALWAYS_events':
				$disp_nextMonthDays = 0;
				$disp_nextMonth = 2;
				break;
			case 'NO':
			default:
				$disp_nextMonthDays = 0;
				$disp_nextMonth = 0;
				break;
		}

		// Get the time with offset
		$config = JFactory::getConfig();
		$timeWithOffset = time() + ($config->getValue('config.offset')*60*60);

		// Get the start day
		$startday = $this->params->get('start_day');
		if (!defined('_CAL_CONF_STARDAY'))
		{

	/**
	 * Description for ''_CAL_CONF_STARDAY''
	 */
			define('_CAL_CONF_STARDAY', $startday);
		}
		//define('_CAL_CONF_DATEFORMAT',1);
		//define('_CAL_CONF_MAILVIEW','YES');
		if ((!$startday) || ($startday > 1))
		{
			$startday = 0;
		}

		// An array of the names of the days of the week
		$day_name = array(
			JText::_('EVENTS_CAL_LANG_SUNDAYSHORT'),
			JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
			JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
			JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
			JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
			JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
			JText::_('EVENTS_CAL_LANG_SATURDAYSHORT')
		);

		$this->content = '';

		// Display a calendar. Want to show 1,2, or 3 calendars optionally
		// depending upon module parameters. (IE. Last Month, This Month, or Next Month)
		$thisDayOfMonth = date("j", $timeWithOffset);
		$daysLeftInMonth = date("t", $timeWithOffset) - date("j", $timeWithOffset) + 1;

		// Display last month?
		if ($disp_lastMonth && (!$disp_lastMonthDays || $thisDayOfMonth <= $disp_lastMonthDays))
		{
			// Build last month calendar
			$this->content .= $this->_calendar($timeWithOffset, $startday, mktime(0, 0, 0, date("n") - 1, 1, date("Y")), JText::_('_CAL_LANG_LAST_MONTH'), $day_name, $disp_lastMonth == 2);
		}

		// Build this month
		$this->content .= $this->_calendar($timeWithOffset, $startday, mktime(0, 0, 0, date("n"), 1, date("Y")), JText::_('EVENTS_CAL_LANG_THIS_MONTH'), $day_name);

		// Display next month?
		if ($disp_nextMonth && (!$disp_nextMonthDays || $daysLeftInMonth <= $disp_nextMonthDays))
		{
			// Build next month calendar
			$this->content .= $this->_calendar($timeWithOffset, $startday, mktime(0, 0, 0, date("n") + 1, 1, date("Y")), JText::_('_CAL_LANG_NEXT_MONTH'), $day_name, $disp_nextMonth == 2);
		}

		require(JModuleHelper::getLayoutPath($this->module->module));
	}

	/**
	 * Short description for '_calendar'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $timeWithOffset Parameter description (if any) ...
	 * @param      number $startday Parameter description (if any) ...
	 * @param      unknown $time Parameter description (if any) ...
	 * @param      unknown $linkString Parameter description (if any) ...
	 * @param      array &$day_name Parameter description (if any) ...
	 * @param      boolean $monthMustHaveEvent Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _calendar($timeWithOffset, $startday, $time, $linkString, &$day_name, $monthMustHaveEvent=false)
	{
		$database =& JFactory::getDBO();

		$juser =& JFactory::getUser();
		//$gid = $juser->get('gid');

		$cal_year  = date("Y", $time);
		$cal_month = date("m", $time);
		$calmonth  = date("n", $time);
		$to_day    = date("Y-m-d", $timeWithOffset);

		// Start building the table
		$content  = '<table class="mod_events_calendar" summary="'.JText::_('TABLE_SUMMARY').'">'."\n";
		$content .= ' <caption>'."\n";
		if ($this->params->get('show_nav_prev_month'))
		{
			$content .= ' <a class="prev month" href="'.JRoute::_('index.php?option=com_events&amp;year='.($cal_month == 1 ? $cal_year - 1 : $cal_year).'&amp;month='.($cal_month == 1 ? 12 : $cal_month - 1)).'">'.EventsHtml::getMonthName(($cal_month == 1 ? 12 : $cal_month - 1)).'</a>'."\n";
		}
		$content .= ' <a class="current month" href="'.JRoute::_('index.php?option=com_events&amp;year='.$cal_year.'&amp;month='.$cal_month).'">'.EventsHtml::getMonthName($cal_month).'</a>'."\n";
		if ($this->params->get('show_nav_next_month'))
		{
			$content .= ' <a class="next month" href="'.JRoute::_('index.php?option=com_events&amp;year='.($cal_month == 12 ? $cal_year + 1 : $cal_year).'&amp;month='.($cal_month == 12 ? 1 : $cal_month + 1)).'">'.EventsHtml::getMonthName(($cal_month == 12 ? 1 : $cal_month + 1)).'</a>'."\n";
		}
		$content .= ' </caption>'."\n";
		$content .= ' <thead>'."\n";
	    $content .= '  <tr>'."\n";
		// Days name rows
		for ($i=0;$i<7;$i++)
		{
			$content.='   <th>'.$day_name[($i+$startday)%7].'</th>'."\n";
		}
		$content .= '  </tr>'."\n";
		$content .= ' </thead>'."\n";
		$content .= ' <tbody>'."\n";
		$content .= '  <tr>'."\n";

		// Fix to fill in end days out of month correctly
		$dayOfWeek = $startday;
		$start = (date("w", mktime(0, 0, 0, $cal_month, 1, $cal_year))-$startday+7)%7;
		$d = date("t", mktime(0, 0, 0, $cal_month, 0, $cal_year))-$start + 1;
		$kownt = 0;
		for ($a=$start; $a>0; $a--)
		{
			$content .= '   <td class="daylink">&nbsp;</td>'."\n";
			$dayOfWeek++;
			$kownt++;
		}

		$monthHasEvent = false;
		$eventCheck = new EventsRepeat;
		$lastDayOfMonth = date("t", mktime(0, 0, 0, $cal_month, 1, $cal_year));
		$rd = 0;
		for ($d=1;$d<=$lastDayOfMonth;$d++)
		{
			$do = ($d<10) ? "0$d" : "$d";
			$selected_date = "$cal_year-$cal_month-$do";

			$sql = "SELECT #__events.* FROM #__events, #__categories as b"
				. "\n WHERE #__events.catid = b.id " // AND b.access <= $gid AND #__events.access <= $gid"
				. "\n AND ((publish_up >= '$selected_date 00:00:00' AND publish_up <= '$selected_date 23:59:59')"
				. "\n OR (publish_down >= '$selected_date 00:00:00' AND publish_down <= '$selected_date 23:59:59')"
				. "\n OR (publish_up <= '$selected_date 00:00:00' AND publish_down >= '$selected_date 23:59:59')) AND state='1'"
				. "\n   AND (#__events.scope IS NULL OR #__events.scope=" . $database->quote('event') . ")"
				. "\n ORDER BY publish_up ASC";

			$database->setQuery($sql);
			$rows = $database->loadObjectList();
			$mark_bold = '';
			$mark_close_bold = '';
			$class = ($selected_date == $to_day) ? 'todaynoevents' : 'daynoevents';

			for ($r = 0; $r < count($rows); $r++)
			{
				if ($eventCheck->EventsRepeat($rows[$r], $cal_year, $cal_month, $do))
				{
					$monthHasEvent = true;
					$mark_bold = '<b>';
					$mark_close_bold = '</b>';
					$class = ($selected_date == $to_day) ? 'todaywithevents' : 'daywithevents';
					break;
				}
			}

			// Only adds link if event scheduled that day
			$content .= '   <td class="'.$class.'">';
			if ($class == 'todaywithevents' || $class == 'daywithevents')
			{
				$content .= '<a class="mod_events_daylink" href="'.JRoute::_('index.php?option=com_events&year='.$cal_year.'&month='.$cal_month.'&day='.$do).'">'.$d.'</a>';
			}
			else
			{
				$content .= "$d";
			}
	        $content .= '</td>'."\n";
			$rd++;

			// Check if Next week row
			if ((1 + $dayOfWeek++)%7 == $startday)
			{
				$content .= '  </tr>'."\n".'  <tr>'."\n";
				$rd = ($rd >= 7) ? 0 : $rd;
			}
		}

		// Fill in any blank days for the rest of the row
		for ($d=$rd;$d<=6;$d++)
		{
			$content .= '   <td>&nbsp;</td>'."\n";
		}

		// Finish off the table
		$content .= '  </tr>'."\n";
		$content .= ' </tbody>'."\n";
		$content .= '</table>'."\n";

		// Now check to see if this month needs to have at least 1 event in order to display
		if (!$monthMustHaveEvent || $monthHasEvent)
		{
			return $content;
		}
		else
		{
			return '';
		}
	}
}

