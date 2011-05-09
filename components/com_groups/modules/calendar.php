<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

Class CalendarModule
{
	
	function __construct( $group )
	{
		//group object
		$this->group = $group;
		
	}
	
	//-----
	
	function onManageModules()
	{
		$mod = array(
			'name' => 'calendar',
			'title' => 'Group Mini Calendar',
			'description' => 'Calendar Module works with group calendar. Displays events in mini calendar format. (Requires Group Calendar Plugin Installed)',
			'input_title' => '',
			'input' => 'There is nothing you can edit for this group module. Clicking the update button below or the back above will take you back to the manage pages dashboard.'
		);
		
		return $mod;
	}
	
	//-----
	
	function render()
	{
		//var to hold content being returned
		$content  = '';
		
		//get the user
		$juser =& JFactory::getUser();
		
		//get the group
		$group = Hubzero_Group::getInstance($this->group->get('gidNumber'));
		
		//get the group members
		$members = $group->get('members');
		
		//get the calendar plugins access level
		$calendar_plugin_preference = $group->getPluginAccess('calendar');
		
		//if there isnt a preference set or there calendar plugin is set to hidden return nothing
		if($calendar_plugin_preference == 'nobody') {
			return;
		}
		
		//if calendar plugin access is limited to registered users and user is not logged in, show nothing
		if($calendar_plugin_preference == 'registered' && $juser->get('guest') == true) {
			return;
		}
		
		//if calendar access level is members and user is not a group member show nothing
		if($calendar_plugin_preference == 'members' && !in_array($juser->get('id'),$members)) {
			return;
		}
		
		//otherwise build the calendar
		$content .= $this->buildCalendar( $group );
		
		//return the content
		return $content;
	}
	
	//-----
	
	function buildCalendar( $group )
	{
		$month = date("m");
		$year = date("Y");
		$calendar  = '';
		
		//build calendar table
		$calendar .= '<table cellpadding="0" cellspacing="0" class="group_module_calendar">';
		$calendar .= '<caption>'.date("F", mktime(0,0,0,$month,1,$year)).'</caption>';
		
		 /* table headings */ 
		$headings = array('S','M','T','W','T','F','S');
		$calendar.= '<thead><tr><td>'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr></thead>';
		
		/* days and weeks vars now ... */ 
		$running_day = date('w',mktime(0,0,0,$month,1,$year));
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();
		
		/* row for week one */ 
		$calendar.= '<tbody><tr>';
		
		/* print "blank" days until the first of the current week */ 
		for($x = 0; $x < $running_day; $x++):
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			$days_in_this_week++;
		endfor;
		
		/* keep going with days.... */ 
		for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		
			if($year == date("Y") && $month == date("m") && $list_day == date("d")){
				$calendar .= '<td class="calendar-day-today">';
			} elseif($running_day == 0 || $running_day == 6) {
				$calendar .= '<td class="calendar-day-weekend">';
			} else {
				$calendar.= '<td class="calendar-day">';
			}
			
			/* add in the day number */ 
			$calendar.= '<div class="day-number">';
			
			//check for events if event display link otherwise just number
			$calendar.= $this->checkForEvent($group, $month, $list_day, $year);
			
			$calendar.= '</div>';
			
			//$calendar.= str_repeat('<p>&nbsp;</p>',2);
			$calendar.= '</td>';
			
			if($running_day == 6):
				$calendar.= '</tr>';
				
				if(($day_counter+1) != $days_in_month):
					$calendar.= '<tr>';
				endif;
				
				$running_day = -1;
				$days_in_this_week = 0;
			endif;
			
			$days_in_this_week++; $running_day++; $day_counter++;
			
		endfor;
		
		/* finish the rest of the days in the week */
		if($days_in_this_week < 8):
			for($x = 1; $x <= (8 - $days_in_this_week); $x++):
				$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			endfor;
		endif;
		
		/* final row */ 
		$calendar.= '</tr></tbody>';
		
		/* end the table */ 
		$calendar.= '</table>';
		
		/* all done, return result */
		return $calendar;
	}
	
	//------
	
	function checkForEvent( $group, $month, $day, $year)
	{
		//set to no event
		$event = false;
		
		//get the user
		$juser =& JFactory::getUser();
		
		//get members
		$members = $group->get('members');
		
		//get group id
		$gid = $group->get('gidNumber');
		
		//is there an event
		/*
			Check if there is an event
		*/
		
		//if user is a member 
		if(in_array($juser->get('id'), $members)) { 
			//if there is an event
			if($event == true) {
				return "<a href=\"index.php?option=com_groups&gid={$gid}&active=calendar\">{$day}</a>";
			} else {
				return $day;
			}
		} else {
			return $day;
		}
	}
	
	//-----
}

?>