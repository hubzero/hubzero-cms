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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Courses Plugin class for calendar
 */
class plgCoursesCalendar extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgCoursesCalendar(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onCourseAreas()
	{
		$area = array(
			'name' => 'calendar',
			'title' => JText::_('PLG_COURSES_CALENDAR'),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => true
		);
		return $area;
	}

	/**
	 * Return data on a course view (this will be some form of HTML)
	 * 
	 * @param      object  $course      Current course
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onCourse($course, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$returnhtml = true;
		$active = 'calendar';

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		//get this area details
		$this_area = $this->onCourseAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit) 
		{
			if (!in_array($this_area['name'], $areas)) 
			{
				$returnhtml = false;
			}
		}
		
		//Create user object
		$juser =& JFactory::getUser();

		//get the course members
		$members = $course->get('members');

		// Set some variables so other functions have access
		$this->juser = $juser;
		$this->authorized = $authorized;
		$this->members = $members;
		$this->course = $course;
		$this->option = $option;
		$this->action = $action;
		
		//if we want to return content
		if ($returnhtml) 
		{
			//set course members plugin access level
			$course_plugin_acl = $access[$active];
			
			//if set to nobody make sure cant access
			if ($course_plugin_acl == 'nobody') 
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('COURSES_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest') 
			 && ($course_plugin_acl == 'registered' || $course_plugin_acl == 'members')) 
			{
				ximport('Hubzero_Module_Helper');
				$arr['html']  = '<p class="info">' . JText::sprintf('COURSES_PLUGIN_REGISTERED', ucfirst($active)) . '</p>';
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
				return $arr;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array($juser->get('id'), $members) 
			 && $course_plugin_acl == 'members' 
			 && $authorized != 'admin') 
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('COURSES_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			//push styles to the view
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('courses','calendar');
			Hubzero_Document::addPluginScript('courses','calendar');
			
			//check to see if we need to include the mootools datepicker
			if (!JPluginHelper::isEnabled('system', 'jquery'))
			{
				$document =& JFactory::getDocument();
				if (is_file(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'js' . DS . 'calendar.rc4.js')) 
				{
					$document->addScript('components' . DS . 'com_events' . DS . 'js' . DS . 'calendar.rc4.js');
				}
			}

			//get the month and year posted through month/year picker
			$goto_month = JRequest::getVar('month','','get');
			$goto_year = JRequest::getVar('year','','get');

			//set month/year for all functions and used in display
			$this->month = ($goto_month) ? $goto_month : date("m");
			$this->year = ($goto_year) ? $goto_year : date("Y");

			//include the course event table 
			require_once(JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . 'calendar' . DS . 'tables' . DS . 'course.event.php');

			//run task based on action
			switch ($this->action)
			{
				case 'add':     $arr['html'] = $this->add();    break;
				case 'edit':    $arr['html'] = $this->edit();   break;
				case 'delete':  $arr['html'] = $this->delete(); break;
				case 'save':    $arr['html'] = $this->save();   break;
				case 'display':
				default:        $arr['html'] = $this->display($this->month, $this->year); break;
			}
		}
		
		//get count of all future course events
		$arr['metadata']['count'] = $this->getAllFutureEvents();
		
		//get the upcoming events
		$upcoming_events = $this->getUpcomingEvents();
		if($upcoming_events > 0)
		{
			$title = $this->course->get('description')." has {$upcoming_events} events this month.";
			$link = JRoute::_('index.php?option=com_courses&gid='.$this->course->get('cn').'&active=calendar');
			$arr['metadata']['alert'] = "<a class=\"alrt\" href=\"{$link}\"><span><h5>Calendar Alert</h5>{$title}</span></a>";
		}
		
		// Return the output
		return $arr;
	}

	/**
	 * Actions to perform when deleting a course
	 * 
	 * @param      object $course Current course
	 * @return     void
	 */
	public function onCourseDeleteCount($course)
	{
		//return JText::_('Calendar Events').': '.count($this->getCalendarEvents($course));
	}

	/**
	 * Generate a date selector
	 * 
	 * @param      integer $month Month to display for
	 * @param      integer $year  Year to display for
	 * @return     string
	 */
	private function _generateCalendarPicker($month, $year)
	{
		$year_start = date("Y");
		$year_end = $year_start + 15;
		$picker = '';

		$picker .= "<select name=\"month\" onchange=\"document.goto_date.submit();\">";
		for ($i=1; $i<13; $i++) 
		{
			$sel = ($i == $month) ? 'selected' : '';
			$picker .= "<option {$sel} value=\"{$i}\">" . date("F",mktime(0,0,0,$i,1,2020)) . "</option>";
		}
		$picker .= "</select>";

		$picker .= "<select name=\"year\" onchange=\"document.goto_date.submit();\">";
		for ($i=($year_start-1); $i<$year_end; $i++) 
		{
			$sel = ($i == $year) ? 'selected' : '';
			$picker .= "<option {$sel} value=\"{$i}\">" . date("Y",mktime(0,0,0,1,1,$i)) . "</option>";
		}
		$picker .= "</select>";

		return $picker;
	}

	/**
	 * Get events for a specific date
	 * 
	 * @param      integer $day   Day to display for
	 * @param      integer $month Month to display for
	 * @param      integer $year  Year to display for
	 * @return     array
	 */
	private function getEvents($day, $month, $year)
	{
		$events = array();

		$start = date("Y-m-d H:i:s", mktime(0,0,0,$month,$day,$year));
		$end = date("Y-m-d H:i:s", mktime(23,59,59,$month,$day,$year));

		$db =& JFactory::getDBO();
		$sql = "SELECT * FROM #__courses_events 
				WHERE (start >= " . $db->Quote($start)." AND start <=" . $db->Quote($end) . " 
					OR end >= " . $db->Quote($start)." AND end <=" . $db->Quote($end) . " 
					OR start <= " . $db->Quote($start)." AND end >= " . $db->Quote($end) . ")
				AND gidNumber=" . $db->Quote($this->course->get('gidNumber')) . " 
				AND active=1";
		$db->setQuery($sql);
		$events = $db->loadAssocList();

		return $events;
	}
	
	
	private function getAllFutureEvents()
	{
		$db =& JFactory::getDBO();
		$sql = "SELECT COUNT(*) FROM #__courses_events WHERE gidNumber=".$this->course->get('gidNumber')." AND active=1 AND (start >='".date("Y-m-d H:i:s")."' OR end >='".date("Y-m-d H:i:s")."')";
		$db->setQuery($sql);
		return $db->loadResult();
	}
	
	private function getUpcomingEvents()
	{
		$db =& JFactory::getDBO();
		$sql = "SELECT COUNT(*) FROM #__courses_events WHERE gidNumber=".$this->course->get('gidNumber')." AND active=1 AND start >= '".date("Y-m-01 00:00:00")."' AND start <= '".date("Y-m-t 23:59:59")."'";
		$db->setQuery($sql);
		return $db->loadResult();
	}

	/**
	 * Display a calendar
	 * 
	 * @param      integer $month Month to display for
	 * @param      integer $year  Year to display for
	 * @return     string
	 */
	private function display($month, $year)
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => 'calendar',
				'name'    => 'browse'
			)
		);

		// An array of the names of the days of the week
		$days_of_week = array(
			JText::_('PLG_COURSES_CALENDAR_SUNDAY_SHORT'),
			JText::_('PLG_COURSES_CALENDAR_MONDAY_SHORT'),
			JText::_('PLG_COURSES_CALENDAR_TUESDAY_SHORT'),
			JText::_('PLG_COURSES_CALENDAR_WEDNESDAY_SHORT'),
			JText::_('PLG_COURSES_CALENDAR_THURSDAY_SHORT'),
			JText::_('PLG_COURSES_CALENDAR_FRIDAY_SHORT'),
			JText::_('PLG_COURSES_CALENDAR_SATURDAY_SHORT')
		);

		//Create Calendar Navigation
		$calendar = "";

		$calendar .= "<div id=\"calendar-nav\">";
		$calendar .= "<span class=\"date\">".date("F Y", mktime(0,0,0,$month,1,$year))."</span>";
		$calendar .= "<form id=\"goto_date\" name=\"goto_date\" action=\"\" method=\"get\">";
		$calendar .= $this->_generateCalendarPicker($month, $year);
		$calendar .= "<noscript><input type=\"submit\" name=\"goto-submit\" id=\"goto-submit\" value=\"Go\" /></noscript>";
		$calendar .= "</form>";
		if (in_array($this->juser->get('id'),$this->members) || $this->authorized == 'manager' || $this->authorized == 'admin') 
		{
			$calendar .= "<a class=\"add-event\" title=\"Add New Event\" href=\"".JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->cn.'&active=calendar&task=add')."\">".JText::_('PLG_COURSES_CALENDAR_ADD_NEW_LINK_TEXT')."</a>";
		}
		$calendar .= "</div>";

		// Create Calendar headings
		$calendar .= "<div id=\"calendar\">";
		$calendar .= "<table>"."\n";
		$calendar .= "<thead>"."\n";
	   	$calendar .= "<tr>"."\n";

		foreach ($days_of_week as $day_of_week) 
		{
			$calendar .= "<th>".$day_of_week."</th>"."\n";
		}

		$calendar .= "</tr>"."\n";
		$calendar .= "</thead>"."\n";

		//create the calendar days
		$calendar .= "<tbody>"."\n";
		$calendar .= "<tr class=\"calendar-row\">"."\n";

		// Fix to fill in end days out of month correctly
		$running_day = date('w',mktime(0,0,0,$month,1,$year));
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		$today = date("Y-m-d");
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		for ($x = 0; $x < $running_day; $x++) 
		{
			$calendar .= '<td class="no-date">&nbsp;</td>' ."\n";
			$days_in_this_week++;
		}

		for ($list_day = 1; $list_day <= $days_in_month; $list_day++) 
		{
			$day = mktime(0,0,0,$month,$list_day,$year);
			$weekend = date("D",$day);
			$class = ($weekend == 'Sat' || $weekend == 'Sun') ? ' weekend' : '';

			//check to see if today
			$class .= (date("Y-m-d", $day) == $today) ? ' today' : '';

			$calendar .= "<td id=\"box-{$list_day}\">";
			$calendar .= "<div class=\"day{$class}\">{$list_day}</div>";

		    //get any course events
			$events = $this->getEvents($list_day,$month,$year);

			//display any events	
		    $calendar .= "<ul>";

			foreach ($events as $event) 
			{
				$calendar .= "<li><a class=\"event\" href=\"#\">".$event['title']."</a>";

					$calendar .= "<ul>";
					$calendar .= "<li>";
						$calendar .= "<span class=\"title\">".$event['title']."</span>";
					$calendar .= "</li>";
					$calendar .= "<li>";
						$str_to_time_start = strtotime($event['start']);
						$str_to_time_end = strtotime($event['end']);
						if (date("d",$str_to_time_start) == date("d",$str_to_time_end)) 
						{
							$calendar .= "<span class=\"date\">".date("F d, Y", $str_to_time_start)."</span>";
						} 
						else 
						{
							$calendar .= "<span class=\"date\">".date("F<br> d", $str_to_time_start)." - ".date("d, Y", $str_to_time_end)."</span>";
						}
						$calendar .= "<span class=\"time\">".date("g:ia",strtotime($event['start']))."<br>to ".date("g:ia",strtotime($event['end']))."</span>";
						$calendar .= "<br class=\"clear\" />";
					$calendar .= "</li>";
					$calendar .= "<li>";
						$calendar .= "<span class=\"details\">".nl2br($event['details'])."</span>";
					$calendar .= "</li>";

					if ($this->authorized == 'admin' || $this->authorized == 'manager' || $event['actorid'] == $this->juser->get('id')) 
					{
						$calendar .= "<li>";
						$calendar .= "<a class=\"edit\" href=\"".JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn').'&active=calendar&task=edit&id='.$event['id'])."\">Edit</a>";
						$calendar .= "<a class=\"delete\" href=\"".JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn').'&active=calendar&task=delete&id='.$event['id'])."\">Delete</a>";
						$calendar .= "<br class=\"clear\" />";
						$calendar .= "</li>";
					}
					$calendar .= "</ul>";

				$calendar .= "</li>";
			}

			$calendar .= "</ul>";

		    $calendar.= '</td>';
			if ($running_day == 6) 
			{
				$calendar.= '</tr>';
				if (($day_counter+1) != $days_in_month) 
				{
					$calendar.= '<tr class="calendar-row">';
				}
				$running_day = -1;
				$days_in_this_week = 0;
			}

			$days_in_this_week++;
			$running_day++;
			$day_counter++;
		}

		/* finish the rest of the days in the week */
		if ($days_in_this_week < 8) 
		{
			for ($x = 1; $x <= (8 - $days_in_this_week); $x++) 
			{
				$calendar.= '<td class="no-date"> </td>';
			}
		}

		//Finish off the table
		$calendar .= "</tr>"."\n";
		$calendar .= "</tbody>"."\n";
		$calendar .= "</table>"."\n";
		$calendar .= "</div>"."\n";

		//push the calendar content to view
		$view->calendar = $calendar;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		return $view->loadTemplate();
	}

	/**
	 * Show a form for adding an entry
	 * 
	 * @return     string
	 */
	private function add()
	{
		return $this->edit();
	}

	/**
	 * Show a form for editing en entry
	 * 
	 * @return     string
	 */
	private function edit()
	{
		//create the view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => 'calendar',
				'name'    => 'edit'
			)
		);

		//get the passed in event id
		$eventid = JRequest::getVar('id','','get');

		//get the database object
		$db =& JFactory::getDBO();

		//get the course event object
		$event = new CourseEvent($db);

		//if we have an event id we are in edit mode
		if ($eventid) 
		{
			//load the event obj based on event id passed in
			$event->load($eventid);

			//check to see if user has the correct permissions to edit
			if ($this->juser->get('id') != $event->actorid && $this->authorized != 'manager' && $this->authorized != 'admin') 
			{
				//do not have permission to edit the event
				$this->setError('You do not have the correct permissions to edit this event');
				return $this->display($this->month,$this->year);
			}
		}

		//push some vars to the view
		$view->month = $this->month;
		$view->year  = $this->year;
		$view->course = $this->course;
		$view->event = $event;

		//get any errors if there are any
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}

		//load the view
		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 * 
	 * @return    string
	 */
	private function save()
	{
		//get the edit form posted vars
		$event = JRequest::getVar('event',array(),'post');

		//set some other needed vars
		$event['gidNumber'] = $this->course->get('gidNumber');
		$event['actorid']   = $this->juser->get('id');
		$event['type']      = 'general';
		$event['active']    = 1;
		$event['created']   = date("Y-m-d H:i:s");

		//build the start date
		$start = explode("/", $event['start_date']);
		$start_time = explode(":", $event['start_time']);
		$event['start'] = $start[2] . "-" . $start[0] . "-" . $start[1] . " " . $start_time[0] . ":" . $start_time[1] . ":00";

		//build the end date
		$end = explode("/",$event['end_date']);
		$end_time = explode(":",$event['end_time']);
		$event['end'] = $end[2] . "-" . $end[0] . "-" . $end[1] . " " . $end_time[0] . ":" . $end_time[1] . ":00";

		//check to make sure all required fields are set
		if ($event['title'] == '' || $event['start'] == '' || $event['end'] == '') 
		{
			$this->setError('You must enter all required fields.');
			return $this->edit();
		}

		//check to make sure end time is greater then start time
		if (strtotime($event['end']) <= strtotime($event['start'])) 
		{
			$this->setError('You must an event end time greater than the start time.');
			return $this->edit();
		}

		//instantiate database and course event objects
		$db =& JFactory::getDBO();
		$GEvent = new CourseEvent($db);

		//save event and if error display error
		if (!$GEvent->save($event)) 
		{
			$this->setError('An error occured when trying to edit the event. Please try again.');
			return $this->display($start[0], $start[2]);
		}

		//return to the calendar
		return $this->display($start[0], $start[2]);
	}

	/**
	 * Delete an event
	 * 
	 * @return     string
	 */
	private function delete()
	{
		//get the passed in event id
		$eventid = JRequest::getVar('id','','get');

		//get the database object
		$db =& JFactory::getDBO();

		//get the course event object
		$event = new CourseEvent($db);

		//load the event obj based on event id passed in
		$event->load($eventid);

		//check to see if user has the right permissions to delete
		if ($this->juser->get('id') == $event->actorid || $this->authorized == 'manager' || $this->authorized == 'admin') 
		{
			//delete the event
			if (!$event->delete($eventid)) 
			{
				$this->setError($event->getError());
			}

			//display the calendar
			return $this->display($this->month,$this->year);
		} 
		else 
		{
			$this->setError('You do not have the correct permissions to delete this event');
			return $this->display($this->month,$this->year);
		}
	}
}

