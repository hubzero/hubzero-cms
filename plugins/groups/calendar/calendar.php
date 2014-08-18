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

/**
 * Groups Plugin class for calendar
 */
class plgGroupsCalendar extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

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
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon' => 'f073'
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
		$juser = JFactory::getUser();

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
					$url = JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&active='.$active, false, true);

					$this->redirect(
						JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url)),
						JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
						'warning'
					);
					return;
				}

				//check to see if user is member and plugin access requires members
				if (!in_array($juser->get('id'), $members) && $group_plugin_acl == 'members')
				{
					$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
					return $arr;
				}
			}

			// load events lang file
			$lang = JFactory::getLanguage();
			$lang->load('com_events');

			//push styles to the view
			\Hubzero\Document\Assets::addPluginStylesheet('groups','calendar');
			\Hubzero\Document\Assets::addPluginScript('groups','calendar');

			//get the request vars
			$this->month    = JRequest::getInt('month', JFactory::getDate()->format("m") ,'get');
			$this->month    = (strlen($this->month) == 1) ? '0'.$this->month : $this->month;
			$this->year     = JRequest::getInt('year', JFactory::getDate()->format("Y"), 'get');
			$this->calendar = JRequest::getInt('calendar', 0, 'get');

			// make sure month is always two digets
			if (strlen($this->month) == 1)
			{
				$this->month = 0 . $this->month;
			}

			//set vars for reuse purposes
			$this->database = JFactory::getDBO();

			//include needed event libs
			require __DIR__ . '/helper.php';
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'event.php' );
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'calendar' . DS . 'archive.php' );
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
				case 'refreshcalendars': $this->refreshCalendars();                  break;
				case 'eventsources':     $this->eventSources();                      break;
				case 'events':           $this->events();                            break;
				default:                 $arr['html'] = $this->display();            break;
			}
		}

		//get count of all future group events
		$arr['metadata']['count'] = $this->_getAllFutureEvents();

		//get the upcoming events
		$upcoming_events = $this->_getFutureEventsThisMonth();
		if ($upcoming_events > 0)
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
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendar',
				'layout'  => 'display'
			)
		);

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

		//get calendars
		$eventsCalendarArchive = EventsModelCalendarArchive::getInstance();
		$view->calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber'),
			'published' => array(1)
		));

		$jconfig = JFactory::getConfig();

		// event calendar model
		$eventsCalendar = EventsModelCalendar::getInstance();

		//define our filters
		$view->filters = array(
			'scope'    => 'group',
			'scope_id' => $this->group->get('gidNumber'),
			'orderby'  => 'publish_up DESC'
		);

		// get events count
		$view->eventsCount = $eventsCalendar->events('count', $view->filters);

		// get events for no js
		$view->filters['limit'] = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$view->filters['start'] = JRequest::getInt('limitstart', 0);
		$view->events = $eventsCalendar->events('list', $view->filters);

		// add hub fancyselect lib
		\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect.min');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect.css');

		// add full calendar lib
		\Hubzero\Document\Assets::addSystemScript('jquery.fullcalendar.min');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fullcalendar.css');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fullcalendar.print.css', 'text/css', 'print');

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
	 * Output event sources. For caledar
	 *
	 * @return string
	 */
	private function eventSources()
	{
		// array to hold sources
		$sources = array();

		// get calendars
		$eventsCalendarArchive = EventsModelCalendarArchive::getInstance();
		$calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber'),
			'published' => array(1)
		));

		// add each calendar to the sources
		foreach ($calendars as $calendar)
		{
			$source            = new stdClass;
			$source->title     = $calendar->get('title');
			$source->url       = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&active=calendar&action=events&calender_id=' . $calendar->get('id'));
			$source->className = ($calendar->get('color')) ? 'fc-event-' . $calendar->get('color') : 'fc-event-default';
			array_push($sources, $source);
		}

		// add uncategorized source
		$source            = new stdClass;
		$source->title     = 'Uncategorized';
		$source->url       = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&active=calendar&action=events&calender_id=0');
		$source->className = 'fc-event-default';
		array_push($sources, $source);

		// output sources
		echo json_encode($sources);
		exit();
	}

	/**
	 * Returns events for a source.
	 * Ajax only and returns json.
	 *
	 * @return string
	 */
	public function events()
	{
		// array to hold events
		$events = array();

		// get request params
		$start      = JRequest::getInt('start', 0);
		$end        = JRequest::getInt('end', 0);
		$calendarId = JRequest::getInt('calender_id', 'null');

		// format date/times
		$start = JFactory::getDate($start);
		$end   = JFactory::getDate($end);
		$end->modify('-1 second');

		// get calendar events
		$eventsCalendar = EventsModelCalendar::getInstance();
		$rawEvents = $eventsCalendar->events('list', array(
			'scope'        => 'group',
			'scope_id'     => $this->group->get('gidNumber'),
			'calendar_id'  => $calendarId,
			'state'        => array(1),
			'publish_up'   => $start->format('Y-m-d H:i:s'),
			'publish_down' => $end->format('Y-m-d H:i:s')
		));

		// get repeating events
		$rawEventsRepeating = $eventsCalendar->events('repeating', array(
			'scope'        => 'group',
			'scope_id'     => $this->group->get('gidNumber'),
			'calendar_id'  => $calendarId,
			'state'        => array(1),
			'publish_up'   => $start->format('Y-m-d H:i:s'),
			'publish_down' => $end->format('Y-m-d H:i:s')
		));

		// merge events with repeating events
		$rawEvents = $rawEvents->merge($rawEventsRepeating);

		// loop through each event to return it
		foreach ($rawEvents as $rawEvent)
		{
			$event            = new stdClass;
			$event->id        = $rawEvent->get('id');
			$event->title     = $rawEvent->get('title');
			$event->allDay    = false;
			$event->url       = $rawEvent->link();
			$event->start     = JFactory::getDate($rawEvent->get('publish_up'))->toUnix();
			$event->className = ($rawEvent->get('calendar_id')) ? 'calendar-'.$rawEvent->get('calendar_id') : 'calendar-0';
			if ($rawEvent->get('publish_down') != '0000-00-00 00:00:00')
			{
				$event->end = JFactory::getDate($rawEvent->get('publish_down'))->toUnix();
			}

			// add start & end for displaying dates user clicked on
			// instead of actual event start & end
			if ($rawEvent->get('repeating_rule') != '')
			{
				$event->url .= '?start=' . $event->start;
				if ($rawEvent->get('publish_down') != '0000-00-00 00:00:00')
				{
					$event->url .= '&end=' . $event->end;
				}
			}

			array_push($events, $event);
		}

		// output events
		echo json_encode($events);
		exit();
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
		$view = new \Hubzero\Plugin\View(
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
		$view->event = new EventsModelEvent($eventId);

		//get calendars
		$eventsCalendarArchive = EventsModelCalendarArchive::getInstance();
		$view->calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber'),
			'published' => array(1),
			'readonly'  => 0
		));

		// do we have access to edit
		if ($view->event->get('id'))
		{
			//check to see if user has the correct permissions to edit
			if ($this->juser->get('id') != $view->event->get('created_by') && $this->authorized != 'manager')
			{
				//do not have permission to edit the event
				$this->redirect(
					JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
					JText::_('You do not have the correct permissions to edit this event.'),
					'error'
				);
				return;
			}

			// make sure this event is editable
			$eventCalendar = $view->event->calendar();
			if ($eventCalendar->isSubscription())
			{
				$this->redirect(
					JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id='.$view->event->get('id')),
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
			$view->event->get('params'),
			JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_events' . DS . 'events.xml'
		);

		//are we passing an events array back from save
		if (isset($this->event))
		{
			$view->event = $this->event;
		}

		//added need scripts and stylesheets
		\Hubzero\Document\Assets::addSystemScript('fileupload/jquery.fileupload');
		\Hubzero\Document\Assets::addSystemScript('fileupload/jquery.iframe-transport');
		\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect.min');
		\Hubzero\Document\Assets::addSystemScript('jquery.timepicker');
		\Hubzero\Document\Assets::addSystemScript('toolbox');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.datepicker.css');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.timepicker.css');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect.css');
		\Hubzero\Document\Assets::addSystemStylesheet('toolbox.css');

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
		$event['modified']    = JFactory::getDate()->toSql();
		$event['modified_by'] = $this->juser->get('id');

		// repeating rule
		$event['repeating_rule'] = $this->_buildRepeatingRule();

		//if we are updating set modified time and actor
		if (!isset($event['id']) || $event['id'] == 0)
		{
			$event['created']    = JFactory::getDate()->toSql();
			$event['created_by'] = $this->juser->get('id');
		}

		// timezone
		$timezone = new DateTimezone(JFactory::getConfig()->get('offset'));

		//parse publish up date/time
		if (isset($event['publish_up']) && $event['publish_up'] != '')
		{
			//remove @ symbol
			$event['publish_up'] = str_replace("@", "", $event['publish_up']);
			$event['publish_up'] = JFactory::getDate($event['publish_up'], $timezone)->format("Y-m-d H:i:s");
		}

		//parse publish down date/time
		if (isset($event['publish_down']) && $event['publish_down'] != '')
		{
			//remove @ symbol
			$event['publish_down'] = str_replace("@", "", $event['publish_down']);
			$event['publish_down'] = JFactory::getDate($event['publish_down'], $timezone)->format("Y-m-d H:i:s");
		}

		//parse register by date/time
		if (isset($event['registerby']) && $event['registerby'] != '')
		{
			//remove @ symbol
			$event['registerby'] = str_replace("@", "", $event['registerby']);
			$event['registerby'] = JFactory::getDate($event['registerby'], $timezone)->format("Y-m-d H:i:s");
		}

		//stringify params
		if (isset($event['params']) && count($event['params']) > 0)
		{
			$params = new JRegistry('');
			$params->loadArray( $event['params'] );
			$event['params'] = $params->toString();
		}

		//did we want to turn off registration?
		if (!$registration)
		{
			$event['registerby'] = '0000-00-00 00:00:00';
		}

		//instantiate new event object
		$eventsModelEvent = new EventsModelEvent();

		// attempt to bind
		if (!$eventsModelEvent->bind($event))
		{
			$this->setError($eventsModelEvent->getError());
			$this->event = $eventsModelEvent;
			return $this->edit();
		}

		//make sure we have both start and end time
		if ($event['publish_up'] == '')
		{
			$this->setError('You must enter an event start, an end date is optional.');
			$this->event = $eventsModelEvent;
			return $this->edit();
		}

		//check to make sure end time is greater then start time
		if (isset($event['publish_down']) && $event['publish_down'] != '0000-00-00 00:00:00' && $event['publish_down'] != '')
		{
			if (strtotime($event['publish_up']) >= strtotime($event['publish_down']))
			{
				$this->setError('You must an event end date greater than the start date.');
				$this->event = $eventsModelEvent;
				return $this->edit();
			}
		}

		//make sure registration email is valid
		if ($registration && isset($event['email']) && $event['email'] != '' && !filter_var($event['email'], FILTER_VALIDATE_EMAIL))
		{
			$this->setError('You must enter a valid email address for the events registration admin email.');
			$this->event = $eventsModelEvent;
			return $this->edit();
		}

		//make sure registration email is valid
		if ($registration && (!isset($event['registerby']) || $event['registerby'] == ''))
		{
			$this->setError('You must enter a valid event registration deadline to require registration.');
			JRequest::setVar('includeRegistration', 1);
			$this->event = $eventsModelEvent;
			return $this->edit();
		}

		//check to make sure we have valid info
		if (!$eventsModelEvent->store(true))
		{
			$this->setError('An error occurred when trying to edit the event. Please try again.');
			$this->event = $eventsModelEvent;
			return $this->edit();
		}

		//get the year and month for this event
		//so we can jump to that spot
		$year = JFactory::getDate(strtotime($event['publish_up']))->format("Y");
		$month = JFactory::getDate(strtotime($event['publish_up']))->format("m");

		//build message
		$message = JText::_('You have successfully created a new group event.');
		if (isset($event['id']) && $event['id'] != 0)
		{
			$message = JText::_('You have successfully edited the group event.');
		}

		//inform user and redirect
		$this->redirect(
			JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id=' . $eventsModelEvent->get('id')),
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
		$eventsModelEvent = new EventsModelEvent($eventId);

		// check to see if user has the right permissions to delete
		if ($this->juser->get('id') != $eventsModelEvent->get('created_by') && $this->authorized != 'manager')
		{
			// do not have permission to delete the event
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
				JText::_('You do not have the correct permissions to delete this event.'),
				'error'
			);
			return;
		}

		// make sure this event is editable
		$eventCalendar = $eventsModelEvent->calendar();
		if ($eventCalendar->isSubscription())
		{
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id='.$eventsModelEvent->get('id')),
				JText::_('You cannot delete imported events from remote calendar subscriptions.'),
				'error'
			);
			return;
		}

		//make as disabled
		$eventsModelEvent->set('state', 0);

		//save changes
		if (!$eventsModelEvent->store(true))
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
		$view = new \Hubzero\Plugin\View(
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
		$view->event = new EventsModelEvent( $eventId );

		// make sure we have event
		if (!$view->event->get('id'))
		{
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
				JText::_('Event not found.'),
				'error'
			);
			return;
		}

		//get registrants count
		$eventsRespondent = new EventsRespondent( array('id' => $eventId ) );
		$view->registrants = $eventsRespondent->getCount();

		//get calendar
		$view->calendar = EventsModelCalendar::getInstance($view->event->get('calendar_id'));

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
	 * @return void
	 */
	private function export()
	{
		// get request varse
		$eventId = JRequest::getVar('event_id','','get');

		// load & export event
		$eventsModelEvent = new EventsModelEvent( $eventId );
		$eventsModelEvent->export();
	}

	/**
	 * Subscribe to a calendar
	 *
	 * @return void
	 */
	private function subscribe()
	{
		//check to see if subscriptions are on
		if (!$this->params->get('allow_subscriptions', 1))
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
			if ($plugin_access == 'members' && !is_object($auth) && !in_array($auth->id, $this->group->get('members')))
			{
				header('HTTP/1.1 403 Unauthorized');
				die( JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', 'Calendar') );
			}
		}

		// load & subscribe to the calendar archive
		$eventsCalendarArchive = EventsModelCalendarArchive::getInstance();
		$subscriptionName = '[' . JFactory::getConfig()->getValue('sitename') . '] Group Calendar: ' . $this->group->get('description');
		$eventsCalendarArchive->subscribe($subscriptionName, 'group', $this->group->get('gidNumber'));
	}

	/**
	 * Authenticate Subscription Requests
	 *
	 * @return void
	 */
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
		if (!is_object($user) || $user->id == '' || $user->id == 0)
		{
			JFactory::getAuthLogger()->info($httpBasicUsername . ' ' . $_SERVER['REMOTE_ADDR'] . ' invalid group calendar subscription auth for ' . $this->group->get('cn'));
			apache_note('auth','invalid');

			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			die( JText::_('You must enter a valid username and password.') );
		}

		//make sure password matches stored password
		if (!\Hubzero\User\Password::comparePasswords($user->passhash, $httpBasicPassword))
		{
			JFactory::getAuthLogger()->info($httpBasicUsername . ' ' . $_SERVER['REMOTE_ADDR'] . ' invalid group calendar subscription auth for ' . $this->group->get('cn'));
			apache_note('auth','invalid');

			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			die( JText::_('You must enter a valid username and password.') );
		}

		return $user;
	}

	/**
	 * Import iCal File
	 *
	 * @return mixed
	 */
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

		// get values from ical File
		$title       = (isset($icalEvent['SUMMARY'])) ? $icalEvent['SUMMARY'] : '';
		$description = (isset($icalEvent['DESCRIPTION'])) ? $icalEvent['DESCRIPTION'] : '';
		$location    = (isset($icalEvent['LOCATION'])) ? $icalEvent['LOCATION'] : '';
		$website     = (isset($icalEvent['URL;VALUE=URI'])) ? $icalEvent['URL;VALUE=URI'] : '';

		//object to hold event data
		$event           = new stdClass;
		$event->title    = $title;
		$event->content  = stripslashes(str_replace('\n', "\n", $description));
		$event->start    = JFactory::getDate($start)->format("m/d/Y @ g:i a");
		$event->end      = JFactory::getDate($end)->format("m/d/Y @ g:i a");
		$event->location = $location;
		$event->website  = $website;

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
		$view = new \Hubzero\Plugin\View(
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
		$view->event = new EventsModelEvent( $eventId );

		//get registrants count
		$eventsRespondent = new EventsRespondent( array('id' => $eventId ) );
		$view->registrants = $eventsRespondent->getCount();

		//do we have a registration deadline
		if ($view->event->get('registerby') == '' || $view->event->get('registerby') == '0000-00-00 00:00:00')
		{
			$this->redirect(
				JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id=' . $view->event->get('id')),
				JText::_('This event does not have registration.'),
				'warning'
			);
			return;
		}

		//make sure registration is open
		$now        = JFactory::getDate()->toUnix();
		$registerby = JFactory::getDate($view->event->get('registerby'))->toUnix();

		if ($registerby >= $now)
		{
			//get the password
			$password = JRequest::getVar('passwrd', '', 'post');

			//is the event restricted
			if ($view->event->get('restricted') != '' && $view->event->get('restricted') != $password && !isset($this->register))
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
		$view->params = new JRegistry( $view->event->get('params') );

		if (!$this->juser->get('guest'))
		{
			$profile = new \Hubzero\User\Profile();
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

		// check to make sure this is the only time registering
		if (EventsRespondent::checkUniqueEmailForEvent($register['email'], $event_id) > 0)
		{
			$errors[] = JText::_('You have previously registered for this event.');
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
		$eventsRespondent->registered           = JFactory::getDate()->toSql();
		$eventsRespondent->arrival              = $arrival['day'] . ' ' . $arrival['time'];
		$eventsRespondent->departure            = $departure['day'] . ' ' . $departure['time'];

		$eventsRespondent->position_description = '';
		if (isset($register['position_other']) && $register['position_other'] != '')
		{
			$eventsRespondent->position_description = $register['position_other'];
		}
		else if (isset($register['position']))
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
		$r = (empty($r)) ? array() : $r;
		$sql = "INSERT INTO #__events_respondent_race_rel(respondent_id,race,tribal_affiliation)
		        VALUES(".$this->database->quote( $eventsRespondent->id ).", ".$this->database->quote( implode(',', $r) ).", ".$this->database->quote( $race['nativetribe'] ).")";
		$this->database->setQuery( $sql );
		$this->database->query();

		//load event we are registering for
		$eventsEvent = new EventsEvent( $this->database );
		$eventsEvent->load( $event_id );

		// send a copy to event admin
		if ($eventsEvent->email != '')
		{
			//build message to send to event admin
			$email = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'groups',
					'element' => 'calendar',
					'name'    => 'calendar',
					'layout'  => 'register_email_admin'
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

		//build message to send to event registerer
		$email = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendar',
				'layout'  => 'register_email_user'
			)
		);
		$email->option     = $this->option;
		$email->group      = $this->group;
		$email->event      = $eventsEvent;
		$email->sitename   = JFactory::getConfig()->getValue('config.sitename');
		$email->siteurl    = JFactory::getConfig()->getValue('config.live_site');
		$email->register   = $register;
		$email->race       = $race;
		$email->dietary    = $dietary;
		$email->disability = $disability;
		$email->arrival    = $arrival;
		$email->departure  = $departure;
		$email->dinner     = $dinner;
		$message           = str_replace("\n", "\r\n", $email->loadTemplate());

		// build to, from, & subject
		$to      = JFactory::getUser()->get('email');
		$from    = array('email' => 'groups@nanohub.org', 'name'  => $email->sitename . ' Group Calendar: ' . $this->group->get('description'));
		$subject = JText::sprintf('Thank you for Registering for the "%s" event', $eventsEvent->title);

		// send mail to user registering
		$this->_sendEmail($to, $from, $subject, $message);

		// redirect back to the event
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
		$view = new \Hubzero\Plugin\View(
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

		foreach ($registrants as $registrant)
		{
			$sql = "SELECT CONCAT(race, ',', tribal_affiliation) as race
			        FROM #__events_respondent_race_rel
			        WHERE respondent_id=" . $this->database->quote( $registrant->id);
			$this->database->setQuery( $sql );
			$race = $this->database->loadResult();

			foreach ($fields as $field)
			{
				switch ($field)
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
					case 'registered':
						$output .= $this->escapeCsv(JHTML::_('date', $registrant->registered, 'Y-m-d H:i:s'));
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
		//get calendars
		$eventsCalendarArchive = EventsModelCalendarArchive::getInstance();
		$calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber')
		));

		//create the view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendars',
				'layout'  => 'display'
			)
		);

		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->juser      = $this->juser;
		$view->calendars  = $calendars;

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
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'groups',
				'element' => 'calendar',
				'name'    => 'calendars',
				'layout'  => 'edit'
			)
		);

		// get the calendar
		$view->calendar = EventsModelcalendar::getInstance($calendarId);

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
		$calendarInput = JRequest::getVar('calendar',array());

		// get the calendar
		$calendar = EventsModelcalendar::getInstance($calendarInput['id']);

		//add scope and scope id to calendar array
		$calendarInput['scope']    = 'group';
		$calendarInput['scope_id'] = $this->group->get('gidNumber');
		$calendarInput['url']      = trim($calendarInput['url']);

		//is this a remote calendar url
		if ($calendarInput['url'] != '' && filter_var($calendarInput['url'], FILTER_VALIDATE_URL))
		{
			$calendarInput['readonly'] = 1;
			$needsRefresh = true;
		}
		else
		{
			$calendarInput['url'] = '';
			$calendarInput['readonly'] = 0;
			$needsRefresh = false;
		}

		// bind input
		if (!$calendar->bind($calendarInput))
		{
			$this->setError( $calendar->getError() );
			return $this->editCalendar();
		}

		// attempt to save
		if (!$calendar->store(true))
		{
			$this->setError( $calendar->getError() );
			return $this->editCalendar();
		}

		// should we refresh?
		if ($needsRefresh)
		{
			$calendar->refresh();
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
		$calendarId   = JRequest::getVar('calendar_id','');
		$events       = JRequest::getVar('events','delete');
		$deleteEvents = ($events == 'delete') ? true : false;

		// get the calendar
		$calendar = EventsModelcalendar::getInstance($calendarId);

		//delete the calendar
		$calendar->delete($deleteEvents);

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

		// get the calendar
		$calendar = EventsModelcalendar::getInstance($calendarId);

		// refresh Calendar (force refresh even if we dont need to yet)
		if (!$calendar->refresh(true))
		{
			$this->setError( JText::sprintf('Unable to sync the group calendar "%s". Please verify the calendar subscription URL is valid.', $calendar->getError()) );
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
	 * Refresh all Calendars
	 *
	 * Should only be called via ajax
	 *
	 * @return string
	 */
	private function refreshCalendars()
	{
		//get calendars
		$eventsCalendarArchive = EventsModelCalendarArchive::getInstance();
		$calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber'),
			'published' => array(1)
		));

		// array to hold refreshed cals
		$refreshed = array();

		// refresh each calendar
		// dont force refresh if we havent made it to the next refresh interval
		$calendars->map(function($calendar) use (&$refreshed) {
			// if we refreshed lets add it
			// to our array of refreshed
			if ($calendar->refresh(false))
			{
				$refreshed[] = $calendar->get('id');
			}
		});

		// return refreshed count
		echo json_encode(array('refreshed' => count($refreshed)));
		exit();
	}

	/**
	 * Build Repeating rule from input
	 *
	 * @return string
	 */
	private function _buildRepeatingRule()
	{
		$rules = array();

		// get reccurrance
		$reccurance = JRequest::getVar('reccurance', array(), 'post');

		// valid frequencies
		$validFreq = array('daily','weekly','monthly','yearly');

		// make sure we have a frequency and its a valid type
		if (!isset($reccurance['freq']) || !in_array($reccurance['freq'], $validFreq))
		{
			return '';
		}

		// frequency & interval
		$freq     = $reccurance['freq'];
		$interval = (isset($reccurance['interval'][$freq])) ? $reccurance['interval'][$freq] : 1;

		// add the frequency rule
		$rule[] = 'FREQ=' . strtoupper($freq);

		// make sure we have a valid interval
		if ($interval < 1 || $interval > 30)
		{
			$interval = 1;
		}

		// add interval rule
		$rule[] = 'INTERVAL=' . $interval;

		// valid end
		$validEnd = array('never','count','until');

		// do we need to add end rules?
		if (isset($reccurance['ends']['when']) && in_array($reccurance['ends']['when'], $validEnd))
		{
			// get the end type
			$end = $reccurance['ends']['when'];

			// if end is after a count or after date
			if ($end == 'count')
			{
				$count = (isset($reccurance['ends']['count'])) ? $reccurance['ends']['count'] : 1;
				$rule[] = 'COUNT=' . $count;
			}
			elseif ($end == 'until')
			{
				// create date object in local timezone
				$until    = (isset($reccurance['ends']['until'])) ? $reccurance['ends']['until'] : 1;

				// create date time object where timezoen is configured value
				// let php convert to UTC when formatting
				$timezone = new DateTimezone(JFactory::getConfig()->get('offset'));
				$date = JFactory::getDate($until, $timezone);

				// subtract by 1 second (iCal standard)
				$date->modify('-1 second');

				//set the rule
				$rule[] = 'UNTIL=' . $date->format('Ymd\THis\Z');
			}
		}

		// return the full rule
		return implode(';', $rule);
	}

	/**
	 * Get all future events for this group cal
	 *
	 * @return     array
	 */
	private function _getAllFutureEvents()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT COUNT(*)
				FROM #__events
				WHERE scope=" . $db->quote('group') . "
				AND scope_id=".$this->group->get('gidNumber')."
				AND state=1
				AND (publish_up >='".JFactory::getDate()->toSql()."' OR publish_down >='".JFactory::getDate()->toSql()."')";
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
		$db = JFactory::getDBO();
		$sql = "SELECT COUNT(*)
				FROM #__events
				WHERE scope=" . $db->quote('group') . "
				AND scope_id=".$this->group->get('gidNumber')."
				AND state=1
				AND (publish_up >= '".JFactory::getDate()->toSql()."' OR publish_down >='".JFactory::getDate()->toSql()."') AND publish_up <= '".JFactory::getDate()->format("Y-m-t 23:59:59")."'";
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
	private function _sendEmail($to, $from, $subject, $body)
	{
		// create message object
		$message = new \Hubzero\Mail\Message();

		// set message details and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($to)
				->addPart($body, 'text/plain')
				->addHeader('X-Component', 'com_groups')
				->addHeader('X-Component-Object', 'Group Calendar Event Registration')
				->send();

		// add good
		return true;
	}
}