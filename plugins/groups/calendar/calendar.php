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
 * Groups Plugin class for calendar
 */
class plgGroupsCalendar extends Hubzero_Plugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgGroupsCalendar(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'calendar',
			'title' => JText::_('PLG_GROUPS_CALENDAR'),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1)
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 * 
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$returnhtml = true;
		$active = 'calendar';

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

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
		
		//get the group members
		$members = $group->get('members');
		
		// Set some variables so other functions have access
		$this->juser      = $juser;
		$this->authorized = $authorized;
		$this->members    = $members;
		$this->group      = $group;
		$this->option     = $option;
		$this->action     = $action;
		$this->access     = $access;
		
		//if we want to return content
		if ($returnhtml) 
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];
			
			//if were not trying to subscribe
			if ($this->action != 'subscribe')
			{
				//if set to nobody make sure cant access
				if ($group_plugin_acl == 'nobody') 
				{
					$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
					return $arr;
				}

				//check if guest and force login if plugin access is registered or members
				if ($juser->get('guest') 
				 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')) 
				{
					$url = JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&active='.$active);
					$message = JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active));
					$this->redirect( "/login?return=".base64_encode($url), $message, 'warning' );
					return;
				}

				//check to see if user is member and plugin access requires members
				if (!in_array($juser->get('id'), $members) && $group_plugin_acl == 'members') 
				{
					$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
					return $arr;
				}
			}
			
			//push styles to the view
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups','calendar');
			Hubzero_Document::addPluginScript('groups','calendar');
			
			//check to see if we need to include the mootools datepicker
			if (!JPluginHelper::isEnabled('system', 'jquery'))
			{
				$document =& JFactory::getDocument();
				if (is_file(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'js' . DS . 'calendar.rc4.js')) 
				{
					$document->addScript('components' . DS . 'com_events' . DS . 'js' . DS . 'calendar.rc4.js');
				}
			}
			
			//get the request vars
			$this->month    = JRequest::getVar('month',date("m") ,'get');
			$this->year     = JRequest::getVar('year', date("Y"), 'get');
			$this->calendar = JRequest::getInt('calendar', 0, 'get');
			
			//set vars for reuse purposes
			$this->database =& JFactory::getDBO();
			
			//include needed event libs
			ximport('Hubzero_Event_Helper');
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'tables' . DS . 'event.php' );
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'tables' . DS . 'calendar.php' );
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'tables' . DS . 'respondent.php' );
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'html.php' );
			
			//run task based on action
			switch ($this->action)
			{
				//managing events
				case 'add':              $arr['html'] = $this->add();                break;
				case 'edit':             $arr['html'] = $this->edit();               break;
				case 'save':             $arr['html'] = $this->save();               break;
				case 'delete':           $arr['html'] = $this->delete();             break;
				case 'details':          $arr['html'] = $this->details();            break;
				case 'export':           $arr['html'] = $this->export();             break;
				case 'subscribe':        $arr['html'] = $this->subscribe();          break;
				case 'import':           $arr['html'] = $this->import();             break;
				
				//event registration
				case 'register':         $arr['html'] = $this->register();           break;
				case 'doregister':       $arr['html'] = $this->doRegister();         break;
				case 'registrants':      $arr['html'] = $this->registrants();        break;
				case 'download':         $arr['html'] = $this->download();           break;
				
				//event calendars
				case 'calendars':        $arr['html'] = $this->calendars();          break;
				case 'addcalendar':      $arr['html'] = $this->addCalendar();        break;
				case 'editcalendar':     $arr['html'] = $this->editCalendar();       break;
				case 'savecalendar':     $arr['html'] = $this->saveCalendar();       break;
				case 'deletecalendar':   $arr['html'] = $this->deleteCalendar();     break;
				case 'refreshcalendar':  $arr['html'] = $this->refreshCalendar();    break;
				default:                 $arr['html'] = $this->display();            break;
			}
		}
		
		//get count of all future group events
		$arr['metadata']['count'] = $this->_getAllFutureEvents();
		
		//get the upcoming events
		$upcoming_events = $this->_getFutureEventsThisMonth();
		if($upcoming_events > 0)
		{
			$title = $this->group->get('description')." has {$upcoming_events} events this month.";
			$link = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&active=calendar');
			$arr['metadata']['alert'] = "<a class=\"alrt\" href=\"{$link}\"><span><h5>Calendar Alert</h5>{$title}</span></a>";
		}
		
		// Return the output
		return $arr;
	}
	
	
	/**
	 * Display a calendar
	 * 
	 * @return     string
	 */
	private function display()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendar',
				'layout'  => 'display'
			)
		);
		
		//refresh calendars
		$eventsCalendar = new EventsCalendar( $this->database );
		$eventsCalendar->refreshAll( $this->group );
		
		//get calendar sync errors
		$syncErrors = array_filter($eventsCalendar->getErrors());
		if (count($syncErrors) > 0 && $eventsCalendar->failed_attempts > 3)
		{
			//set message to display to user
			$this->setError( JText::_('Unable to sync the following group calendar(s). The group Managers have been notified. <br /> - ' . implode('<br /> - ', $syncErrors)));
			
			//build message sent to managers
			$subject  = JText::_('Group Calendar Subscription Sync Issue');
			$message  = 'There is an issue with the following group calendar subscriptions:' . "\n";
			$message .= '---------------------------------------------------------------------------------------------------' . "\n\n";
			$message .= " - " . implode( "\n - ", $syncErrors);
			$config =& JFactory::getConfig();
			$from['name'] = $this->group->get('description') . " Group on " . $config->getValue("fromname");
			$from['email'] = $config->getValue("mailfrom");
			
			// Send the message
			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('group_message', $subject, $message, $from, $this->group->get('managers'), 'com_groups'))) 
			{
				$this->setError(JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED'));
			}
		}
		
		// An array of the names of the days of the week
		$days_of_week = array(
			JText::_('PLG_GROUPS_CALENDAR_SUNDAY_SHORT'),
			JText::_('PLG_GROUPS_CALENDAR_MONDAY_SHORT'),
			JText::_('PLG_GROUPS_CALENDAR_TUESDAY_SHORT'),
			JText::_('PLG_GROUPS_CALENDAR_WEDNESDAY_SHORT'),
			JText::_('PLG_GROUPS_CALENDAR_THURSDAY_SHORT'),
			JText::_('PLG_GROUPS_CALENDAR_FRIDAY_SHORT'),
			JText::_('PLG_GROUPS_CALENDAR_SATURDAY_SHORT')
		);
		
		//class to determine if we allow double click to create
		$class = ($this->params->get('allow_quick_create', 1) && in_array($this->juser->get('id'), $this->group->get('members'))) ? 'quick-create' : 'no-quick-create';
		
		// Create Calendar
		$calendarHTML = "<div id=\"calendar\" class=\"{$class}\">";
		$calendarHTML .= "<table>"."\n";
		$calendarHTML .= "<thead>"."\n";
		$calendarHTML .= "<tr>"."\n";
		
		foreach ($days_of_week as $day_of_week) 
		{
			$calendarHTML .= "<th>".$day_of_week."</th>"."\n";
		}

		$calendarHTML .= "</tr>"."\n";
		$calendarHTML .= "</thead>"."\n";

		//create the calendar days
		$calendarHTML .= "<tbody>"."\n";
		$calendarHTML .= "<tr class=\"calendar-row\">"."\n";

		// Fix to fill in end days out of month correctly
		$running_day = date('w',mktime(0,0,0,$this->month,1,$this->year));
		$days_in_month = date('t',mktime(0,0,0,$this->month,1,$this->year));
		$today = date("Y-m-d");
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		for ($x = 0; $x < $running_day; $x++) 
		{
			$calendarHTML .= '<td class="no-date">&nbsp;</td>' ."\n";
			$days_in_this_week++;
		}

		for ($list_day = 1; $list_day <= $days_in_month; $list_day++) 
		{
			$day = mktime(0,0,0,$this->month,$list_day,$this->year);
			$weekend = date("D",$day);
			$class = ($weekend == 'Sat' || $weekend == 'Sun') ? ' weekend' : '';
			
			//check to see if today
			$class .= (date("Y-m-d", $day) == $today) ? ' today' : '';
			
			$dateString = $this->year . '-' . $this->month . '-' . date('d', $day) . ' 08:00:00';
			$calendarHTML .= "<td id=\"box-{$list_day}\" class=\"{$class}\" data-date=\"{$dateString}\">";
			$calendarHTML .= "<div class=\"day\">{$list_day}</div>";
			
			//get any group events
			$events = $this->_getEvents($list_day, $this->month, $this->year, $this->calendar);
			
			//display any events	
			$calendarHTML .= "<ul>";
			
			foreach ($events as $event) 
			{
				$event_url = JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id=' . $event->id);
				$calendarHTML .= "<li class=\"{$event->event_calendar_color}\">";
				$calendarHTML .= "<a class=\"event\" href=\"{$event_url}\">".$event->title."</a>";
				$calendarHTML .= "</li>";
			}
			
			$calendarHTML .= "</ul>";
			
		    $calendarHTML.= '</td>';
			if ($running_day == 6) 
			{
				$calendarHTML.= '</tr>';
				if (($day_counter+1) != $days_in_month) 
				{
					$calendarHTML.= '<tr class="calendar-row">';
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
				$calendarHTML.= '<td class="no-date"> </td>';
			}
		}

		//Finish off the table
		$calendarHTML .= "</tr>"."\n";
		$calendarHTML .= "</tbody>"."\n";
		$calendarHTML .= "</table>"."\n";
		$calendarHTML .= "</div>"."\n";

		//push the calendar content to view
		$view->month        = $this->month;
		$view->year         = $this->year;
		$view->calendar     = $this->calendar;
		$view->juser        = $this->juser;
		$view->authorized   = $this->authorized;
		$view->members      = $this->members;
		$view->option       = $this->option;
		$view->group        = $this->group;
		$view->params       = $this->params;
		$view->calendarHTML = $calendarHTML;
		
		//get calendars
		
		$view->calendars = $eventsCalendar->getCalendars( $this->group );
		
		//add ddslick lib
		Hubzero_Document::addSystemScript('jquery.fancyselect.min');
		Hubzero_Document::addSystemStylesheet('jquery.fancyselect.css');
		
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
		//if we are not a member we cant create events
		if (!in_array($this->juser->get('id'), $this->group->get('members')))
		{
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
				JText::_('Only group members are allowed to create & edit events.'),
				'warning'
			);
			return;
		}
		
		//create the view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendar',
				'layout'  => 'edit'
			)
		);

		//get the passed in event id
		$eventId = JRequest::getInt('event_id', 0, 'get');

		//load event data
		$view->event    = new EventsEvent( $this->database );
		$eventsCalendar = new EventsCalendar( $this->database );
		
		//get calendars
		$view->calendars = $eventsCalendar->getCalendars( $this->group, null, 0 );
		
		//if we have an event id we are in edit mode
		if (isset($eventId) && $eventId != '') 
		{
			//load the event obj based on event id passed in
			$view->event->load( $eventId );

			//check to see if user has the correct permissions to edit
			if ($this->juser->get('id') != $view->event->created_by && $this->authorized != 'manager') 
			{
				//do not have permission to edit the event
				$this->redirect(
					JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
					JText::_('You do not have the correct permissions to edit this event.'),
					'error'
				);
				return;
			}
			
			//is this a readonly event
			$cal = $eventsCalendar->getCalendars( $this->group, $view->event->calendar_id );
			if (isset($cal[0]) && $cal[0]->readonly)
			{
				$this->redirect(
					JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id='.$view->event->id),
					JText::_('You cannot edit imported events from remote calendar subscriptions.'),
					'error'
				);
				return;
			}
		}

		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->calendar   = $this->calendar;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->params     = $this->params;
		
		//load com_events params file for registration fields
		$view->registrationFields = new JParameter(
			$view->event->params, 
			JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_events' . DS . 'events.xml'
		);
		
		//are we passing an events array back from save
		if (isset($this->event))
		{
			$view->event = $this->event;
		}
		
		//added need scripts and stylesheets
		Hubzero_Document::addSystemScript('fileupload/jquery.fileupload');
		Hubzero_Document::addSystemScript('fileupload/jquery.iframe-transport');
		Hubzero_Document::addSystemScript('jquery.fancyselect.min');
		Hubzero_Document::addSystemScript('jquery.timepicker');
		Hubzero_Document::addSystemScript('toolbox');
		Hubzero_Document::addSystemStylesheet('jquery.datepicker.css');
		Hubzero_Document::addSystemStylesheet('jquery.timepicker.css');
		Hubzero_Document::addSystemStylesheet('jquery.fancyselect.css');
		Hubzero_Document::addSystemStylesheet('toolbox.css');
		
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
		//get request vars
		$event              = JRequest::getVar('event', array(), 'post');
		$event['time_zone'] = JRequest::getVar('time_zone', -5);
		$event['params']    = JRequest::getVar('params', array());
		$event['content']   = JRequest::getVar('content', '', 'post', 'STRING', JREQUEST_ALLOWRAW);
		$registration       = JRequest::getVar('include-registration', 0);

		//set vars for saving
		$event['catid']       = '-1';
		$event['state']       = 1;
		$event['scope']       = 'group';
		$event['scope_id']    = $this->group->get('gidNumber');
		$event['modified']    = date("Y-m-d H:i:s");
		$event['modified_by'] = $this->juser->get('id');

		//if we are updating set modified time and actor
		if (!isset($event['id']) || $event['id'] == 0) 
		{
			$event['created']    = date("Y-m-d H:i:s");
			$event['created_by'] = $this->juser->get('id');
		}
		
		//parse publish up date/time
		if (isset($event['publish_up']) && $event['publish_up'] != '')
		{
			//remove @ symbol
			$event['publish_up'] = str_replace("@", "", $event['publish_up']);
			$event['publish_up'] = date("Y-m-d H:i:s", strtotime($event['publish_up']));
		}

		//parse publish down date/time
		if (isset($event['publish_down']) && $event['publish_down'] != '')
		{
			//remove @ symbol
			$event['publish_down'] = str_replace("@", "", $event['publish_down']);
			$event['publish_down'] = date("Y-m-d H:i:s", strtotime($event['publish_down']));
		}

		//parse register by date/time
		if (isset($event['registerby']) && $event['registerby'] != '')
		{
			//remove @ symbol
			$event['registerby'] = str_replace("@", "", $event['registerby']);
			$event['registerby'] = date("Y-m-d H:i:s", strtotime($event['registerby']));
		}

		//stringify params
		if (isset($event['params']) && count($event['params']) > 0)
		{
			$paramsClass = (version_compare(JVERSION, '1.6', 'ge')) ? 'JRegistry' : 'JParameter';
			$params = new $paramsClass('');
			$params->loadArray( $event['params'] );
			$event['params'] = $params->toString();
		}

		//did we want to turn off registration?
		if (!$registration)
		{
			$event['registerby'] = '0000-00-00 00:00:00';
		}

		//instantiate new event object
		$eventsEvent = new EventsEvent( $this->database );
		$eventsEvent->bind( $event );
		
		//check to make sure we have valid info
		if (!$eventsEvent->check())
		{
			$this->setError($eventsEvent->getError());
			$this->event = $eventsEvent;
			return $this->edit();
		}

		//make sure we have both start and end time
		if ($event['publish_up'] == '')
		{
			$this->setError('You must enter an event start, an end date is optional.');
			$this->event = $eventsEvent;
			return $this->edit();
		}

		//check to make sure end time is greater then start time
		if (isset($event['publish_down']) && $event['publish_down'] != '0000-00-00 00:00:00' && $event['publish_down'] != '')
		{
			if (strtotime($event['publish_up']) >= strtotime($event['publish_down'])) 
			{
				$this->setError('You must an event end date greater than the start date.');
				$this->event = $eventsEvent;
				return $this->edit();
			}
		}

		//make sure registration email is valid
		if ($registration && isset($event['email']) && $event['email'] != '' && !filter_var($event['email'], FILTER_VALIDATE_EMAIL))
		{
			$this->setError('You must enter a valid email address for the events registration admin email.');
			$this->event = $eventsEvent;
			return $this->edit();
		}
		
		//make sure registration email is valid
		if ($registration && (!isset($event['registerby']) || $event['registerby'] == ''))
		{
			$this->setError('You must enter a valid event registration deadline to require registration.');
			JRequest::setVar('includeRegistration', 1);
			$this->event = $eventsEvent;
			return $this->edit();
		}

		//save event
		if (!$eventsEvent->save( $event ))
		{
			$this->setError('An error occurred when trying to edit the event. Please try again.');
			$this->event = $eventsEvent;
			return $this->edit();
		}

		//get the year and month for this event
		//so we can jump to that spot
		$year = date("Y", strtotime($event['publish_up']));
		$month = date("m", strtotime($event['publish_up']));
		
		//build message
		$message = JText::_('You have successfully created a new group event.');
		if (isset($event['id']) && $event['id'] != 0)
		{
			$message = JText::_('You have successfully edited the group event.');
		}
	
		//inform user and redirect
		$this->redirect(
			JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id=' . $eventsEvent->id),
			$message,
			'passed'
		);
	}
	
	
	/**
	 * Delete an event
	 * 
	 * @return     string
	 */
	private function delete()
	{
		//get the passed in event id
		$eventId = JRequest::getVar('event_id','','get');

		//load event data
		$eventsEvent = new EventsEvent( $this->database );
		$eventsEvent->load( $eventId );

		//check to see if user has the right permissions to delete
		if ($this->juser->get('id') != $eventsEvent->created_by && $this->authorized != 'manager') 
		{
			//do not have permission to edit the event
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
				JText::_('You do not have the correct permissions to delete this event.'),
				'error'
			);
			return;
		}
		
		//is this event deletable?
		$eventsCalendar = new EventsCalendar( $this->database );
		$cal = $eventsCalendar->getCalendars( $this->group, $eventsEvent->calendar_id );
		if (isset($cal[0]) && $cal[0]->readonly)
		{
			//do not have permission to edit the event
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id='.$eventsEvent->id),
				JText::_('You cannot delete imported events from remote calendar subscriptions.'),
				'error'
			);
			return;
		}
		
		//make as disabled
		$eventsEvent->state = 0;

		//save changes
		if (!$eventsEvent->save($eventsEvent))
		{
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
				JText::_('An error occurred while trying to delete the event. Please try again.'),
				'error'
			);
			return;
		}

		//inform user and return
		$this->redirect(
			JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
			JText::_('You have successfully deleted the event.'),
			'passed'
		);
	}
	
	
	/**
	 * Details View for Event
	 * 
	 * @return     string
	 */
	private function details()
	{
		//create the view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendar',
				'layout'  => 'details'
			)
		);

		//get request varse
		$eventId = JRequest::getVar('event_id','','get');

		//load event data
		$view->event = new EventsEvent( $this->database );
		$view->event->load( $eventId );

		//get registrants count
		$eventsRespondent = new EventsRespondent( array('id' => $eventId ) );
		$view->registrants = $eventsRespondent->getCount();

		//get calendars
		if (isset($view->event->calendar_id) && $view->event->calendar_id != '')
		{
			$eventsCalendar = new EventsCalendar( $this->database );
			$view->calendar = $eventsCalendar->getCalendars( $this->group, $view->event->calendar_id );
		}

		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->juser      = $this->juser;

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
	 * Export Event Details
	 * 
	 * @return     string
	 */
	private function export()
	{
		//get request varse
		$eventId = JRequest::getVar('event_id','','get');
		
		//load event
		$eventsEvent = new EventsEvent( $this->database );
		$eventsEvent->load( $eventId );
		
		//get current timezone offset
		$currentOffset   = date('O');
		$currentModifier = substr($currentOffset, 0, 1);
		$currentOffset   = trim(substr($currentOffset, 1), 0);
		
		//get event timezone 
		$timezone      = $eventsEvent->time_zone;
		$timezone      = (substr($eventsEvent->time_zone, 0, 1) == '-') ? $timezone : '+' . $timezone;
		
		//get the event offset
		$eventModifier = substr($timezone, 0, 1);
		$eventOffset   = substr($timezone, 1);
		
		//are we on daylight savings time
		if(date('I') == 1)
		{
			$eventOffset -= 1;
		}
		
		//calculate offset based on current timezone
		$realOffset = ($currentModifier.$currentOffset) - ($eventModifier.$eventOffset);
		$realOffset = (substr($realOffset,0,1) != '-') ? '+'.$realOffset : $realOffset;
		
		//get gmt time with timezone
		$publishUp   = strtotime($realOffset . ' HOURS', strtotime($eventsEvent->publish_up));
		$publishDown = strtotime($realOffset . ' HOURS', strtotime($eventsEvent->publish_down));
		
		//event vars
		$id       = $eventsEvent->id;
		$title    = $eventsEvent->title;
		$desc     = str_replace("\n", '\n', $eventsEvent->content);
		$url      = $eventsEvent->extra_info;
		$location = $eventsEvent->adresse_info;
		$now      = gmdate('Ymd') . 'T' . gmdate('His') . 'Z';
		$start    = gmdate('Ymd', $publishUp).'T'.gmdate('His', $publishUp).'Z';
		$end      = gmdate('Ymd', $publishDown).'T'.gmdate('His', $publishDown).'Z';
		$created  = gmdate('Ymd', strtotime($eventsEvent->created)) . 'T' . gmdate('His', strtotime($eventsEvent->created)) . 'Z';
		$modified = gmdate('Ymd', strtotime($eventsEvent->modified)) . 'T' . gmdate('His', strtotime($eventsEvent->modified)) . 'Z';
		
		//create ouput
		$output  = "BEGIN:VCALENDAR\r\n";
		$output .= "VERSION:2.0\r\n";
		$output .= "PRODID:PHP\r\n";
		$output .= "METHOD:PUBLISH\r\n";
		$output .= "BEGIN:VEVENT\r\n";
		$output .= "UID:{$id}\r\n";
		$output .= "DTSTAMP:{$now}\r\n";
		$output .= "DTSTART:{$start}\r\n";
		if($eventsEvent->publish_down != '' && $eventsEvent->publish_down != '0000-00-00 00:00:00')
		{
			$output .= "DTEND:{$end}\r\n";
		}
		else
		{
			$output .= "DTEND:{$start}\r\n";
		}
		$output .= "CREATED:{$created}\r\n";
		$output .= "LAST-MODIFIED:{$modified}\r\n";
		$output .= "SUMMARY:{$title}\r\n";
		$output .= "DESCRIPTION:{$desc}\r\n";
		if($url != '' && filter_var($url, FILTER_VALIDATE_URL))
		{
			$output .= "URL;VALUE=URI:{$url}\r\n";
		}
		if ($location != '')
		{
			$output .= "LOCATION:{$location}\r\n";
		}
		$output .= "END:VEVENT\r\n";
		$output .= "END:VCALENDAR\r\n";
		
		//set the headers for output
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', strtolower($title)) . '_export.ics');
		echo $output;
		exit();
	}
	
	
	private function subscribe()
	{
		//check to see if subscriptions are on
		if(!$this->params->get('allow_subscriptions', 1))
		{
			header('HTTP/1.1 404 Not Found');
			die( JText::_('Calendar subsciptions are currently turned off.') );
		}
		
		//force https protocol
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
		{
			JFactory::getApplication()->redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die( JText::_('Calendar subscriptions only support the HTTPS (port 443) protocol.') );
		}
		
		//get the calendar plugin access
		$plugin_access = $this->access['calendar'];
		
		//is the plugin off
		if ($plugin_access == 'nobody')
		{
			header('HTTP/1.1 404 Not Found');
			die( JText::sprintf('GROUPS_PLUGIN_OFF', 'Calendar') );
		}
		
		//is the plugin for registered or members only?
		if ($plugin_access == 'registered' || $plugin_access == 'members')
		{
			//authenticate user
			$auth = $this->authenticateSubscriptionRequest();
			
			//is it registered users only?
			if ($plugin_access == 'registered' && !is_object($auth))
			{
				header('HTTP/1.1 403 Not Authorized');
				die( JText::sprintf('GROUPS_PLUGIN_REGISTERED', 'Calendar') );
			}
			
			//make sure we are a member
			if($plugin_access == 'members' && !is_object($auth) && !in_array($auth->id, $this->group->get('members')))
			{
				header('HTTP/1.1 403 Unauthorized');
				die( JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', 'Calendar') );
			}
		}
		
		//get request varse
		$calendarIds = JRequest::getVar('calendar_id','','get');
		$calendarIds = array_map("intval", explode(',', $calendarIds));
		
		//array to hold events
		$events = array();
		
		//loop through and get each calendar
		foreach ($calendarIds as $k => $calendarId)
		{
			if ($calendarId != 0)
			{
				//load calendar object
				$eventsCalendar = new EventsCalendar( $this->database );
				$eventsCalendar->load( $calendarId );
				
				//make sure we have a valid calendar
				if (!is_object($eventsCalendar) || $eventsCalendar->id == '' || $eventsCalendar->scope_id == '')
				{
					unset($calendarIds[$k]);
				}
				
				//make sure the calender is published
				if (!$eventsCalendar->published)
				{
					unset($calendarIds[$k]);
				}
				
				//make its our groups calendar
				if ($eventsCalendar->scope_id != $this->group->get('gidNumber'))
				{
					unset($calendarIds[$k]);
				}
				
				//get events for this calendar
				$sql = "SELECT *
				        FROM #__events AS e
				        WHERE state=1
				        AND calendar_id=" . $this->database->quote( $calendarId ) . "
				        AND scope=" . $this->database->quote( 'group' ) . "
				        AND scope_id=" . $this->database->quote( $this->group->get('gidNumber') );
				$this->database->setQuery( $sql );
				$e = $this->database->loadObjectList();
				$events = array_merge($events, $e);
			}
			else
			{
				//get events for this calendar
				$sql = "SELECT *
				        FROM #__events AS e
				        WHERE state=1
				        AND (calendar_id=0 OR calendar_id IS NULL)
				        AND scope=" . $this->database->quote( 'group' ) . "
				        AND scope_id=" . $this->database->quote( $this->group->get('gidNumber') );
				$this->database->setQuery( $sql );
				$events = array_merge($events, $this->database->loadObjectList());
			}
		}
		
		//create output
		$output  = "BEGIN:VCALENDAR\r\n";
		$output .= "VERSION:2.0\r\n";
		$output .= "PRODID:PHP\r\n";
		$output .= "METHOD:PUBLISH\r\n";
		$output .= "X-WR-CALNAME:" . '[' . JFactory::getConfig()->getValue('sitename') . '] Group Calendar: ' . $this->group->get('description') . "\r\n";
		$output .= "X-PUBLISHED-TTL:PT15M\r\n";
		$output .= "X-ORIGINAL-URL:https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\r\n";
		$output .= "CALSCALE:GREGORIAN\r\n";
		
		//loop through events
		foreach ($events as $event)
		{
			$sequence = 0;
			$uid      = $event->id;
			$title    = $event->title;
			$content  = str_replace("\n", '\n', $event->content);
			$location = $event->adresse_info;
			$url      = $event->extra_info;
			
			//get current timezone offset
			$currentOffset   = date('O');
			$currentModifier = substr($currentOffset, 0, 1);
			$currentOffset   = trim(substr($currentOffset, 1), 0);
			
			//get event timezone 
			$timezone      = $event->time_zone;
			$timezone      = (substr($event->time_zone, 0, 1) == '-') ? $timezone : '+' . $timezone;
			
			//get the event offset
			$eventModifier = substr($timezone, 0, 1);
			$eventOffset   = substr($timezone, 1);
			
			//are we on daylight savings time
			if(date('I') == 1)
			{
				$eventOffset -= 1;
			}
			
			//calculate offset based on current timezone
			$realOffset = ($currentModifier.$currentOffset) - ($eventModifier.$eventOffset);
			$realOffset = (substr($realOffset,0,1) != '-') ? '+'.$realOffset : $realOffset;
			
			//get gmt time with timezone
			$publishUp   = strtotime($realOffset . ' HOURS', strtotime($event->publish_up));
			$publishDown = strtotime($realOffset . ' HOURS', strtotime($event->publish_down));
			
			$now      = gmdate('Ymd') . 'T' . gmdate('His') . 'Z';
			$start    = gmdate('Ymd', $publishUp) . 'T' . gmdate('His', $publishUp) . 'Z';
			$end      = gmdate('Ymd', $publishDown) . 'T' . gmdate('His', $publishDown) . 'Z';
			$created  = gmdate('Ymd', strtotime($event->created)) . 'T' . gmdate('His', strtotime($event->created)) . 'Z';
			$modified = gmdate('Ymd', strtotime($event->modified)) . 'T' . gmdate('His', strtotime($event->modified)) . 'Z';
			
			$output .= "BEGIN:VEVENT\r\n";
			$output .= "UID:{$uid}\r\n";
			$output .= "SEQUENCE:{$sequence}\r\n";
			$output .= "DTSTAMP:{$now}Z\r\n";
			$output .= "DTSTART:{$start}\r\n";
			if($event->publish_down != '' && $event->publish_down != '0000-00-00 00:00:00')
			{
				$output .= "DTEND:{$end}\r\n";
			}
			else
			{
				$output .= "DTEND:{$start}\r\n";
			}
			$output .= "CREATED:{$created}\r\n";
			$output .= "LAST-MODIFIED:{$modified}\r\n";
			$output .= "SUMMARY:{$title}\r\n";
			$output .= "DESCRIPTION:{$content}\r\n";
			//do we have extra info
			if($url != '' && filter_var($url, FILTER_VALIDATE_URL))
			{
				$output .= "URL;VALUE=URI:{$url}\r\n";
			}
			//do we have a location
			if ($location != '')
			{
				$output .= "LOCATION:{$location}\r\n";
			}
			$output .= "END:VEVENT\r\n";
		}
		
		//close calendar
		$output .= "END:VCALENDAR";
		
		//set headers and output
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename="'. '[' . JFactory::getConfig()->getValue('sitename') . '] Group Calendar: ' . $this->group->get('description') .'.ics"');
		//header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT');
		//header('Cache-Control: no-store, no-cache, must-revalidate');
		//header('Cache-Control: post-check=0, pre-check=0', false);
		//header('Pragma: no-cache');
		echo $output;
		exit();
	}
	
	private function authenticateSubscriptionRequest()
	{
		$realm = '[' . JFactory::getConfig()->getValue('sitename') . '] Group Calendar: ' . $this->group->get('description');
		if (empty($_SERVER['PHP_AUTH_USER']))
		{
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			echo JText::_('You are not authorized to view this calendar.');
			exit();
		}
		
		//get the username and password
		$httpBasicUsername = $_SERVER['PHP_AUTH_USER'];
		$httpBasicPassword = $_SERVER['PHP_AUTH_PW'];
		
		//make sure we have a username and password
		if (!isset($httpBasicUsername) || !isset($httpBasicPassword) || $httpBasicUsername == '' || $httpBasicPassword == '')
		{
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			die( JText::_('You must enter a valid username and password.') );
		}
		
		//get the user based on username
		$sql = "SELECT u.id, u.username, up.passhash 
		        FROM #__users AS u, #__users_password AS up
		        WHERE u.id=up.user_id
		        AND u.username=". $this->database->quote( $httpBasicUsername );
		$this->database->setQuery( $sql );
		$user = $this->database->loadObject();
		
		//make sure we found a user
		if(!is_object($user) || $user->id == '' || $user->id == 0)
		{
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			die( JText::_('You must enter a valid username and password.') );
		}
		
		//parse stored passhash
		preg_match('/({[^}]*})(.+)/', $user->passhash, $matches);
		$encryption     = preg_replace('/{|}/', '', strtolower($matches[1]));
		$storedPassword = $matches[2];
		
		//run hashing on password entered to see if it matches db
		$httpBasicPassword = base64_encode(pack('H*', $encryption($httpBasicPassword)));
		
		//make sure password matches stored password
		if ($storedPassword != $httpBasicPassword)
		{
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			die( JText::_('You must enter a valid username and password.') );
		}
		
		return $user;
	}
	
	
	private function import()
	{
		//include icalendar file reader
		require_once JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'calendar' . DS . 'ical.reader.php';
		
		//get incoming
		$file = JRequest::getVar('import', array(), 'files');
		
		//read calendar file
		$iCalReader = new iCalReader( $file['tmp_name'] );
		$icalEvent = $iCalReader->firstEvent();
		
		//get the start and end dates and parse to unix timestamp
		$start = $iCalReader->iCalDateToUnixTimestamp($icalEvent['DTSTART']);
		$end   = $iCalReader->iCalDateToUnixTimestamp($icalEvent['DTEND']);
		
		//object to hold event data
		$event           = new stdClass;
		$event->title    = $icalEvent['SUMMARY'];
		$event->content  = stripslashes(str_replace('\n', "\n", $icalEvent['DESCRIPTION']));
		$event->start    = date("m/d/Y @ g:i a", $start);
		$event->end      = date("m/d/Y @ g:i a", $end);
		$event->location = (isset($icalEvent['LOCATION'])) ? $icalEvent['LOCATION'] : '';
		$event->website  = (isset($icalEvent['URL;VALUE=URI'])) ? $icalEvent['URL;VALUE=URI'] : '';
		
		//return event details
		echo json_encode(array('event'=>$event));
		exit();
	}
	
	/**
	 * Register View for Event
	 * 
	 * @return     string
	 */
	private function register()
	{
		//create the view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendar',
				'layout'  => 'register'
			)
		);

		//get request varse
		$eventId = JRequest::getVar('event_id','');

		//load event data
		$view->event = new EventsEvent( $this->database );
		$view->event->load( $eventId );

		//get registrants count
		$eventsRespondent = new EventsRespondent( array('id' => $eventId ) );
		$view->registrants = $eventsRespondent->getCount();

		//do we have a registration deadline
		if (!isset($view->event->registerby) || $view->event->registerby == '' || $view->event->registerby == '0000-00-00 00:00:00')
		{
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id=' . $view->event->id),
				JText::_('This event does not have registration.'),
				'warning'
			);
			return;
		}

		//make sure registration is open
		$now = time();
		if (strtotime($view->event->registerby) >= $now)
		{
			//get the password
			$password = JRequest::getVar('passwrd', '', 'post');

			//is the event restricted
			if (isset($view->event->restricted) && $view->event->restricted != '' && $view->event->restricted != $password && !isset($this->register))
			{
				//if we entered a password and it was bad lets tell the user
				if (isset($password) && $password != '')
				{
					$this->setError('The password entered is incorrect.');
				}
				$view->setLayout('register_restricted');
			}
		}
		else
		{
			$view->setLayout('register_closed');
		}

		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->juser      = $this->juser;

		$view->register   = (isset($this->register)) ? $this->register : null;
		$view->arrival    = (isset($this->arrival)) ? $this->arrival : null;
		$view->departure  = (isset($this->departure)) ? $this->departure : null;
		$view->dietary    = (isset($this->dietary)) ? $this->dietary : null;
		$view->dinner     = (isset($this->dinner)) ? $this->dinner : null;
		$view->disability = (isset($this->disability)) ? $this->disability : null;
		$view->race       = (isset($this->race)) ? $this->race : null;

		//add params to view
		$paramsClass = (version_compare(JVERSION, '1.6', 'ge')) ? 'JRegistry' : 'JParameter';
		$view->params = new $paramsClass( $view->event->params );

		if (!$this->juser->get('guest')) 
		{
			$profile = new Hubzero_User_Profile();
			$profile->load($this->juser->get('id'));

			$view->register['first_name']  = $profile->get('givenName');
			$view->register['last_name']   = $profile->get('surname');
			$view->register['affiliation'] = $profile->get('organization');
			$view->register['email']       = $profile->get('email');
			$view->register['telephone']   = $profile->get('phone');
			$view->register['website']     = $profile->get('url');
		}

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
	 * Process Registration
	 * 
	 * @return     string
	 */
	private function doRegister()
	{
		//get request vars
		$register   = JRequest::getVar('register', NULL, 'post');
		$arrival    = JRequest::getVar('arrival', NULL, 'post');
		$departure  = JRequest::getVar('departure', NULL, 'post');
		$dietary    = JRequest::getVar('dietary', NULL, 'post');
		$dinner     = JRequest::getVar('dinner', NULL, 'post');
		$disability = JRequest::getVar('disability', NULL, 'post');
		$race       = JRequest::getVar('race', NULL, 'post');
		$event_id   = JRequest::getInt('event_id', NULL, 'post');
		
		//array to hold any errors
		$errors = array();

		//check for first name
		if (!isset($register['first_name']) || $register['first_name'] == '')
		{
			$errors[] = JText::_('Missing first name.');
		}

		//check for last name
		if (!isset($register['last_name']) || $register['last_name'] == '')
		{
			$errors[] = JText::_('Missing last name.');
		}

		//check for affiliation
		if (isset($register['affiliation']) && $register['affiliation'] == '')
		{
			$errors[] = JText::_('Missing affiliation.');
		}

		//check for email
		if (!isset($register['email']) || $register['email'] == '' || !filter_var($register['email'], FILTER_VALIDATE_EMAIL))
		{
			$errors[] = JText::_('Missing email address or email is not valid.');
		}

		//if we have any errors we must return
		if (count($errors) > 0)
		{
			$this->register = $register;
			$this->arrival = $arrival;
			$this->departure = $departure;
			$this->dietary = $dietary;
			$this->dinner = $dinner;
			$this->disability = $disability;
			$this->race = $race;
			$this->setError( implode('<br />', $errors));
			return $this->register();
		}

		//set data for saving
		$eventsRespondent                       = new EventsRespondent( array() );
		$eventsRespondent->event_id             = $event_id;
		$eventsRespondent->registered           = date("Y-m-d H:i:s");
		$eventsRespondent->arrival              = $arrival['day'] . ' ' . $arrival['time'];
		$eventsRespondent->departure            = $departure['day'] . ' ' . $departure['time'];

		$eventsRespondent->position_description = '';
		if (isset($register['position_other']) && $register['position_other'] != '')
		{
			$eventsRespondent->position_description = $register['position_other'];
		}
		else if(isset($register['position']))
		{
			$eventsRespondent->position_description = $register['position'];
		}

		$eventsRespondent->highest_degree       = (isset($register['degree'])) ? $register['degree'] : '';
		$eventsRespondent->gender               = (isset($register['sex'])) ? $register['sex'] : '';
		$eventsRespondent->disability_needs     = (isset($disability) && strtolower($disability) == 'yes') ? 1 : null;
		$eventsRespondent->dietary_needs        = (isset($dietary['needs']) && strtolower($dietary['needs']) == 'yes') ? $dietary['specific'] : null;
		$eventsRespondent->attending_dinner     = (isset($dinner) && $dinner == 'yes') ? 1 : 0;
		$eventsRespondent->bind( $register );
		
		//did we save properly
		if (!$eventsRespondent->save($eventsRespondent))
		{
			$this->setError( $eventsRespondent->getError() );
			return $this->register();
		}
		
		$r = $race;
		unset($r['nativetribe']);
		$sql = "INSERT INTO #__events_respondent_race_rel(respondent_id,race,tribal_affiliation) 
		        VALUES(".$this->database->quote( $eventsRespondent->id ).", ".$this->database->quote( implode(',', $r) ).", ".$this->database->quote( $race['nativetribe'] ).")";
		$this->database->setQuery( $sql );
		$this->database->query();

		//load event we are registering for
		$eventsEvent = new EventsEvent( $this->database );
		$eventsEvent->load( $event_id );

		//send a copy to event admin
		if ($eventsEvent->email != '')
		{
			//build message to send to event admin
			ximport('Hubzero_Plugin_View');
			$email = new Hubzero_Plugin_View(
				array(
					'folder'  => 'groups',
					'element' => 'calendar',
					'name'    => 'calendar',
					'layout'  => 'register_email'
				)
			);
			$email->option     = $this->option;
			$email->group      = $this->group;
			$email->event      = $eventsEvent;
			$email->sitename   = JFactory::getConfig()->getValue('config.sitename');
			$email->register   = $register;
			$email->race       = $race;
			$email->dietary    = $dietary;
			$email->disability = $disability;
			$email->arrival    = $arrival;
			$email->departure  = $departure;
			$email->dinner     = $dinner;
			$message           = str_replace("\n", "\r\n", $email->loadTemplate());

			//declare subject
			$subject = JText::_( "[" . $email->sitename . "] Group \"{$this->group->get('description')}\" Event Registration: " . $eventsEvent->title);

			//make from array
			$from = array(
				'email' => $register['email'],
				'name' => $register['first_name'] . ' ' . $register['last_name']
			);

			//send email
			$this->_sendEmail($eventsEvent->email, $from, $subject, $message);
		}

		$this->redirect(
			JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id=' . $event_id),
			JText::_('You have successfully registered for the event.'),
			'passed'
		);
	}
	
	
	/**
	 * View Event Registrants
	 * 
	 * @return     string
	 */
	private function registrants()
	{
		//create the view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendar',
				'layout'  => 'registrants'
			)
		);

		//get request varse
		$eventId = JRequest::getVar('event_id','','get');

		//load event data
		$view->event = new EventsEvent( $this->database );
		$view->event->load( $eventId );

		//get registrants count
		$eventsRespondent = new EventsRespondent( array('id' => $eventId ) );
		$view->registrants = $eventsRespondent->getRecords();

		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->juser      = $this->juser;

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
	 * Download Registrants CSV
	 * 
	 * @return     string
	 */
	private function download()
	{
		//get request varse
		$eventId = JRequest::getVar('event_id','','get');

		//get registrants count
		$eventsRespondent = new EventsRespondent( array('id' => $eventId ) );
		$registrants = $eventsRespondent->getRecords();

		//var to hold output
		$output = 'First Name,Last Name,Title,Affiliation,Email,Website,Telephone,Fax,City,State,Zip,Country,Current Position,Highest Degree Earned,Gender,Race,Arrival Info,Departure Info,Disability Needs,Dietary Needs,Attending Dinner,Abstract,Comments,Register Date' . "\n";

		$fields = array(
			'first_name',
			'last_name',
			'title',
			'affiliation',
			'email',
			'website',
			'telephone',
			'fax',
			'city',
			'state',
			'zip',
			'country',
			'position_description',
			'highest_degree',
			'gender',
			'race',
			'arrival',
			'departure',
			'disability_needs',
			'dietary_needs',
			'attending_dinner',
			'abstract',
			'comment',
			'registered'
		);
		
		foreach($registrants as $registrant)
		{
			$sql = "SELECT CONCAT(race, ',', tribal_affiliation) as race 
			        FROM #__events_respondent_race_rel 
			        WHERE respondent_id=" . $this->database->quote( $registrant->id);
			$this->database->setQuery( $sql );
			$race = $this->database->loadResult();
			
			foreach($fields as $field)
			{
				switch($field)
				{
					case 'disability_needs':
						$output .= ($registrant->disability_needs == 1) ? 'Yes,' : 'No,';
						break;
					case 'attending_dinner':
						$output .= ($registrant->attending_dinner == 1) ? 'Yes,' : 'No,';
						break;
					case 'race':
						$output .= $this->escapeCsv( $race ) . ',';
						break;
					default:
						$output .= $this->escapeCsv($registrant->$field) . ',';
				}
			}
			$output .= "\n";
		}
		
		//set the headers for output
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=event_rsvp.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $output;
		exit();
	}
	
	
	/**
	 * Escape string for csv output
	 * 
	 * @return     string
	 */
	private function escapeCsv( $value )
	{
		// First off escape all " and make them ""
		$value = str_replace('"', '""', $value);
		
		// Check if I have any commas or new lines
		if (preg_match('/,/', $value) || preg_match("/\n/", $value) || preg_match('/"/', $value)) 
		{
			// If I have new lines or commas escape them
			return '"'.$value.'"';
		}
		else
		{
			// If no new lines or commas just return the value
			return $value;
		}
	}
	
	
	/**
	 * View Group Calendars
	 * 
	 * @return     string
	 */
	private function calendars()
	{
		//create the view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendars',
				'layout'  => 'display'
			)
		);
		
		//get calendars
		$eventsCalendar = new EventsCalendar( $this->database );
		$view->calendars = $eventsCalendar->getCalendars( $this->group );
		
		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->juser      = $this->juser;

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
	 * Add Group Calendar
	 * 
	 * @return     string
	 */
	private function addCalendar()
	{
		return $this->editCalendar();
	}
	
	
	/**
	 * Edit Group Calendar
	 * 
	 * @return     string
	 */
	private function editCalendar()
	{
		//get request vars
		$calendarId = JRequest::getVar('calendar_id','');
		
		//create the view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendars',
				'layout'  => 'edit'
			)
		);
		
		//get calendars
		$view->calendar            = new stdClass;
		$view->calendar->id        = null;
		$view->calendar->title     = null;
		$view->calendar->url       = null;
		$view->calendar->color     = null;
		$view->calendar->published = 1;
		if (isset($calendarId) && $calendarId != '')
		{
			$eventsCalendar = new EventsCalendar( $this->database );
			$calendars = $eventsCalendar->getCalendars( $this->group, $calendarId );
			$view->calendar = $calendars[0];
		}
		
		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->juser      = $this->juser;

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
	 * Save Group Calendar
	 * 
	 * @return     string
	 */
	private function saveCalendar()
	{
		//get request vars
		$calendar = JRequest::getVar('calendar','');
		
		//add scope and scope id to calendar array
		$calendar['scope']    = 'group';
		$calendar['scope_id'] = $this->group->get('gidNumber');
		
		//is this a remote calendar url
		if ($calendar['url'] != '' && filter_var($calendar['url'], FILTER_VALIDATE_URL))
		{
			$calendar['readonly'] = 1;
		}
		else
		{
			$calendar['url'] = '';
		}
		
		//new events calendar object
		$eventsCalendar = new EventsCalendar( $this->database );
		
		//save calendar
		if (!$eventsCalendar->save( $calendar ))
		{
			$this->setError( $eventsCalendar->getError() );
			return $this->editCalendar();
		}
		
		//inform and redirect
		$this->redirect(
			JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=calendars'),
			JText::_('You have successfully added a new calendar.'),
			'passed'
		);
	}
	
	
	/**
	 * Delete Group Calendar
	 * 
	 * @return     string
	 */
	private function deleteCalendar()
	{
		//get the passed in event id
		$calendarId = JRequest::getVar('calendar_id','');
		
		//make sure we have a calendar id
		if (!$calendarId || $calendarId == 0 || $calendarId == '')
		{
			return $this->calendars();
		}
		
		//get calendars
		$eventsCalendar = new EventsCalendar( $this->database );
		$calendars = $eventsCalendar->getCalendars( $this->group, $calendarId );
		$calendar = $calendars[0];
		
		//make sure we have a calendar
		if (!is_object($calendar) || $calendar->id == '')
		{
			return $this->calendars();
		}
		
		//delete calendar
		$eventsCalendar->delete( $calendar->id );
		
		//inform and redirect
		$this->redirect(
			JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=calendars'),
			JText::_('You have successfully deleted the calendar.'),
			'passed'
		);
	}
	
	/**
	 * Method to refresh Group Calendar
	 */
	private function refreshCalendar()
	{
		//get the passed in event id
		$calendarId = JRequest::getVar('calendar_id','');
		
		//make sure we have a calendar id
		if (!$calendarId || $calendarId == 0 || $calendarId == '')
		{
			return $this->calendars();
		}
		
		//load event calendar
		$eventsCalendar = new EventsCalendar( $this->database );
		$eventsCalendar->load( $calendarId );
		
		//make sure we have a valid calendar url
		if ($eventsCalendar->url == '' || !filter_var($eventsCalendar->url, FILTER_VALIDATE_URL))
		{
			return $this->calendars();
		}
		
		//refresh Calendar if we can
		if (!$eventsCalendar->refresh( $this->group, $calendarId ))
		{
			$this->setError( JText::_('Unable to sync the group calendar "' . $eventsCalendar->getError() . '". Please verify the calendar subscription URL is valid.') );
			return $this->calendars();
		}
		
		//inform and redirect
		$this->redirect(
			JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=calendars'),
			JText::_('You have successfully refreshed the calendar.'),
			'passed'
		);
	}
	
	/**
	 * Get events for a specific date
	 * 
	 * @param      integer $day   Day to display for
	 * @param      integer $month Month to display for
	 * @param      integer $year  Year to display for
	 * @return     array
	 */
	private function _getEvents($day, $month, $year, $calendar = 0)
	{
		$start = date("Y-m-d H:i:s", mktime(0,0,0,$month,$day,$year));
		$end = date("Y-m-d H:i:s", mktime(23,59,59,$month,$day,$year));

		$database =& JFactory::getDBO();
		$sql = "SELECT e.*, ec.title AS event_calendar_title, ec.color AS event_calendar_color
				FROM #__events AS e
				LEFT JOIN #__events_calendars AS ec
				ON e.calendar_id = ec.id
				WHERE (e.publish_up >= " . $database->Quote( $start )." AND e.publish_up <=" . $database->Quote( $end ) . " 
					OR e.publish_down >= " . $database->Quote( $start )." AND e.publish_down <=" . $database->Quote( $end ) . " 
					OR e.publish_up <= " . $database->Quote( $start )." AND e.publish_down >= " . $database->Quote( $end ) . ")
				AND e.scope=" . $database->quote( 'group' ) . " 
				AND e.scope_id=" . $database->Quote( $this->group->get('gidNumber') ) . " 
				AND e.state=1";
		
		if (is_numeric($calendar) && $calendar != '' && $calendar != 0)
		{
			$sql .= " AND ec.id=" . $database->quote( $calendar );
		}
		
		$database->setQuery($sql);
		return $database->loadObjectList();
	}
	
	
	/**
	 * Get all future events for this group cal
	 * 
	 * @return     array
	 */
	private function _getAllFutureEvents()
	{
		$db =& JFactory::getDBO();
		$sql = "SELECT COUNT(*) 
				FROM #__events 
				WHERE scope=" . $db->quote('group') . " 
				AND scope_id=".$this->group->get('gidNumber')." 
				AND state=1 
				AND (publish_up >='".date("Y-m-d H:i:s")."' OR publish_up >='".date("Y-m-d H:i:s")."')";
		$db->setQuery($sql);
		return $db->loadResult();
	}
	
	
	/**
	 * Get all future events that start or finish this month
	 * 
	 * @return     array
	 */
	private function _getFutureEventsThisMonth()
	{
		$db =& JFactory::getDBO();
		$sql = "SELECT COUNT(*) 
				FROM #__events 
				WHERE scope=" . $db->quote('group') . "
				AND scope_id=".$this->group->get('gidNumber')." 
				AND state=1 
				AND (publish_up >= '".date("Y-m-d H:i:s")."' OR publish_down >='".date("Y-m-d H:i:s")."') AND publish_up <= '".date("Y-m-t 23:59:59")."'";
		$db->setQuery($sql);
		return $db->loadResult();
	}
	
	
	/**
	 * Send an email
	 * 
	 * @param      array &$hub Parameter description (if any) ...
	 * @param      unknown $email Parameter description (if any) ...
	 * @param      unknown $subject Parameter description (if any) ...
	 * @param      unknown $message Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	private function _sendEmail($to, $from, $subject, $message)
	{
		$contact_email = $from['email'];
		$contact_name  = $from['name'];

		$args     = '-f hubmail-bounces@' . $_SERVER['HTTP_HOST'];
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
		$headers .= 'From: ' . $contact_name .' <' . $contact_email . ">\n";
		$headers .= 'Reply-To: ' . $contact_name .' <' . $contact_email . ">\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: Normal\n";
		$headers .= 'X-Mailer: PHP/' . phpversion() ."\n";
		$headers .= "X-Component: com_groups \n";
		$headers .= "X-Component-Object: Group Calendar Event Registration \n";
		if (mail($to, $subject, $message, $headers, $args)) 
		{
			return(1);
		}
		return(0);
	}
}