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

// No direct access
defined('_HZEXEC_') or die();

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
			'title' => Lang::txt('PLG_GROUPS_CALENDAR'),
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
		$user = User::getInstance();

		//get the group members
		$members = $group->get('members');

		// Set some variables so other functions have access
		$this->user       = $user;
		$this->authorized = $authorized;
		$this->members    = $members;
		$this->group      = $group;
		$this->option     = $option;
		$this->action     = $action;
		$this->access     = $access;
		$this->event = null;

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
					$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
					return $arr;
				}

				//check if guest and force login if plugin access is registered or members
				if (User::isGuest()
				 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
				{
					$url = Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&active='.$active, false, true);

					App::redirect(
						Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
						Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
						'warning'
					);
					return;
				}

				//check to see if user is member and plugin access requires members
				if (!in_array($user->get('id'), $members) && $group_plugin_acl == 'members')
				{
					$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
					return $arr;
				}
			}

			// load events lang file
			Lang::load('com_events') ||
			Lang::load('com_events', PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'site');

			//push styles to the view
			$this->css('calendar');
			$this->js('calendar');

			//get the request vars
			$this->month    = Request::getInt('month', Date::format("m") ,'get');
			$this->month    = (strlen($this->month) == 1) ? '0'.$this->month : $this->month;
			$this->year     = Request::getInt('year', Date::format("Y"), 'get');
			$this->calendar = Request::getInt('calendar', 0, 'get');

			// make sure month is always two digets
			if (strlen($this->month) == 1)
			{
				$this->month = 0 . $this->month;
			}

			//set vars for reuse purposes
			$this->database = App::get('db');

			//include needed event libs
			require __DIR__ . '/helper.php';
			require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'event.php');
			require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'calendar' . DS . 'archive.php');
			require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'tables' . DS . 'respondent.php');
			require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'html.php');

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
			$link = Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&active=calendar');
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
		$view = $this->view('display', 'calendar');

		//push the calendar content to view
		$view->month        = $this->month;
		$view->year         = $this->year;
		$view->calendar     = $this->calendar;
		$view->user        = $this->user;
		$view->authorized   = $this->authorized;
		$view->members      = $this->members;
		$view->option       = $this->option;
		$view->group        = $this->group;
		$view->params       = $this->params;

		//get calendars
		$eventsCalendarArchive = \Components\Events\Models\Calendar\Archive::getInstance();
		$view->calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber')
		));

		// event calendar model
		$eventsCalendar = \Components\Events\Models\Calendar::getInstance();

		//define our filters
		$view->filters = array(
			'scope'    => 'group',
			'scope_id' => $this->group->get('gidNumber'),
			'orderby'  => 'publish_up DESC'
		);

		// get events count
		$view->eventsCount = $eventsCalendar->events('count', $view->filters);

		// get events for no js
		$view->filters['limit'] = Request::getInt('limit', Config::get('list_limit'));
		$view->filters['start'] = Request::getInt('limitstart', 0);
		$view->events = $eventsCalendar->events('list', $view->filters);

		// add hub fancyselect lib
		$this->js('jquery.fancyselect.min', 'system');
		$this->css('jquery.fancyselect.css', 'system');

		// add full calendar lib
		$this->js('moment.min', 'system');
		$this->js('jquery.fullcalendar.min', 'system');
		$this->css('jquery.fullcalendar.css', 'system');
		$this->css('jquery.fullcalendar.print.css', 'system', array('media' => 'print'));

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
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
		$eventsCalendarArchive = \Components\Events\Models\Calendar\Archive::getInstance();
		$calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber')
		));

		// add each calendar to the sources
		foreach ($calendars as $calendar)
		{
			$source            = new stdClass;
			$source->title     = $calendar->get('title');
			$source->url       = Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&active=calendar&action=events&calender_id=' . $calendar->get('id'));
			$source->className = ($calendar->get('color')) ? 'fc-event-' . $calendar->get('color') : 'fc-event-default';
			array_push($sources, $source);
		}

		// add uncategorized source
		$source            = new stdClass;
		$source->title     = 'Uncategorized';
		$source->url       = Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&active=calendar&action=events&calender_id=0');
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
		$start      = Request::getVar('start');
		$end        = Request::getVar('end');
		$calendarId = Request::getInt('calender_id', 'null');

		// format date/times
		$start = Date::of($start . ' 00:00:00');
		$end   = Date::of($end . ' 00:00:00');
		$end->modify('-1 second');

		// get calendar events
		$eventsCalendar = \Components\Events\Models\Calendar::getInstance();
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
			$up   = Date::of($rawEvent->get('publish_up'));
			$down = Date::of($rawEvent->get('publish_down'));

			$event            = new stdClass;
			$event->id        = $rawEvent->get('id');
			$event->title     = $rawEvent->get('title');
			$event->allDay    = $rawEvent->get('allday') == 1;
			$event->url       = $rawEvent->link();
			$event->start     = Date::of($rawEvent->get('publish_up'))->toLocal('Y-m-d\TH:i:sO');
			$event->className = ($rawEvent->get('calendar_id')) ? 'calendar-'.$rawEvent->get('calendar_id') : 'calendar-0';
			if ($rawEvent->get('publish_down') != '0000-00-00 00:00:00')
			{
				$event->end = Date::of($rawEvent->get('publish_down'))->toLocal('Y-m-d\TH:i:sO');
			}

			// add start & end for displaying dates user clicked on
			// instead of actual event start & end
			if ($rawEvent->get('repeating_rule') != '')
			{
				$event->url .= '?start=' . $up->toUnix();
				if ($rawEvent->get('publish_down') != '0000-00-00 00:00:00')
				{
					$event->url .= '&end=' . $down->toUnix();
				}
			}

			// accounts for how humans keep time.
			if ($event->allDay)
			{
				//google events don't put a time.
				if (!isset($event->end))
				{
					$event->end = '0000-00-00 00:00:00';
				}

				// Kevin: Don't change this value. Everyone else is wrong.
				// Seriously this is the correct way to do all-day events.
				// Previous entries may need to be corrected, but future events will be correct.
				$end_day = strtotime($event->end . '+ 24 hours');
				$down = date('Y-m-d H:i:s', $end_day);
				$event->end = $down;
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
		if (!in_array($this->user->get('id'), $this->group->get('members')))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
				Lang::txt('Only group members are allowed to create & edit events.'),
				'warning'
			);
			return;
		}

		//create the view
		$view = $this->view('edit', 'calendar');

		//get the passed in event id
		$eventId = Request::getInt('event_id', 0, 'get');

		//load event data
		$view->event = new \Components\Events\Models\Event($eventId);

		//get calendars
		$eventsCalendarArchive = \Components\Events\Models\Calendar\Archive::getInstance();
		$view->calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber'),
			'readonly'  => 0
		));

		// do we have access to edit
		if ($view->event->get('id'))
		{
			//check to see if user has the correct permissions to edit
			if ($this->user->get('id') != $view->event->get('created_by') && $this->authorized != 'manager')
			{
				//do not have permission to edit the event
				App::redirect(
					Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
					Lang::txt('You do not have the correct permissions to edit this event.'),
					'error'
				);
				return;
			}

			// make sure this event is editable
			$eventCalendar = $view->event->calendar();
			if ($eventCalendar->isSubscription())
			{
				App::redirect(
					Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id='.$view->event->get('id')),
					Lang::txt('You cannot edit imported events from remote calendar subscriptions.'),
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
		$view->registrationFields = new \Hubzero\Html\Parameter(
			$view->event->get('params'),
			PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'events.xml'
		);

		//are we passing an events array back from save
		if (isset($this->event))
		{
			$view->event = $this->event;
		}

		//added need scripts and stylesheets
		$this->js('fileupload/jquery.fileupload', 'system');
		$this->js('fileupload/jquery.iframe-transport', 'system');
		$this->js('jquery.fancyselect.min', 'system');
		$this->js('jquery.timepicker', 'system');
		$this->js('toolbox', 'system');
		$this->css('jquery.datepicker.css', 'system');
		$this->css('jquery.timepicker.css', 'system');
		$this->css('jquery.fancyselect.css', 'system');
		$this->css('toolbox.css', 'system');

		//get any errors if there are any
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
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
		Request::checkToken();

		//get request vars
		$event              = Request::getVar('event', array(), 'post');
		$event['time_zone'] = Request::getVar('time_zone', -5);
		$event['params']    = Request::getVar('params', array());
		$event['content']   = Request::getVar('content', '', 'post', 'STRING', JREQUEST_ALLOWRAW);
		$registration       = Request::getVar('include-registration', 0);

		//set vars for saving
		$event['catid']       = '-1';
		$event['state']       = 1;
		$event['scope']       = 'group';
		$event['scope_id']    = $this->group->get('gidNumber');
		$event['modified']    = Date::toSql();
		$event['modified_by'] = $this->user->get('id');

		// repeating rule
		$event['repeating_rule'] = $this->_buildRepeatingRule();

		//if we are updating set modified time and actor
		if (!isset($event['id']) || $event['id'] == 0)
		{
			$event['created']    = Date::toSql();
			$event['created_by'] = $this->user->get('id');
		}

		// timezone
		$timezone = new DateTimezone(Config::get('offset'));

		// Handle all-day events, iCal is literal
		// Interpreted as <up>12:00am - <down>11:59pm
		$allday = (isset($event['allday']) && $event['allday'] == 1) ? true : false;
		if ($allday)
		{
			$event['publish_up'] = $event['publish_up'] . ' 12:00am';
			$event['publish_up'] = Date::of($event['publish_up'], $timezone)->format("Y-m-d H:i:s");

			$event['publish_down'] = $event['publish_down'] . '11:59pm';
			$event['publish_down'] = Date::of($event['publish_down'], $timezone)->format("Y-m-d H:i:s");
		}

		//parse publish up date/time
		if (isset($event['publish_up']) && $event['publish_up'] != '' && !$allday)
		{
			// combine date & time
			if (isset($event['publish_up_time']) && !$allday)
			{
				$event['publish_up'] = $event['publish_up'] . ' ' . $event['publish_up_time'];
			}
			$event['publish_up'] = Date::of($event['publish_up'], $timezone)->format("Y-m-d H:i:s");
			unset($event['publish_up_time']);
		}


		//parse publish down date/time
		if (isset($event['publish_down']) && $event['publish_down'] != '' && !$allday)
		{
			// combine date & time
			if (isset($event['publish_down_time']))
			{
				$event['publish_down'] = $event['publish_down'] . ' ' . $event['publish_down_time'];
			}
			$event['publish_down'] = Date::of($event['publish_down'], $timezone)->format("Y-m-d H:i:s");
			unset($event['publish_down_time']);
		}

		//parse register by date/time
		if (isset($event['registerby']) && $event['registerby'] != '')
		{
			//remove @ symbol
			$event['registerby'] = str_replace("@", "", $event['registerby']);
			$event['registerby'] = Date::of($event['registerby'], $timezone)->format("Y-m-d H:i:s");
		}

		//stringify params
		if (isset($event['params']) && count($event['params']) > 0)
		{
			$params = new \Hubzero\Config\Registry($event['params']);
			$event['params'] = $params->toString();
		}

		//did we want to turn off registration?
		if (!$registration)
		{
			$event['registerby'] = '0000-00-00 00:00:00';
		}

		//instantiate new event object
		$eventsModelEvent = new \Components\Events\Models\Event();

		// attempt to bind
		if (!$eventsModelEvent->bind($event))
		{
			$this->setError($eventsModelEvent->getError());
			$this->event = $eventsModelEvent;
			return $this->edit();
		}

		if (isset($event['content']) && $event['content'])
		{
			$event['content'] = \Hubzero\Utility\Sanitize::clean($event['content']);
		}

		if (isset($event['extra_info']) && $event['extra_info'] && ! \Hubzero\Utility\Validate::url($event['extra_info']))
		{
			$this->setError('Website entered does not appear to be a valid URL.');
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

		//check to make sure end time is greater than start time
		if (isset($event['publish_down']) && $event['publish_down'] != '0000-00-00 00:00:00' && $event['publish_down'] != '')
		{
			$up     = strtotime($event['publish_up']);
			$down   = strtotime($event['publish_down']);

			// make sure up greater than down when not all day
			// when all day event up can equal down
			if (($up >= $down && !$allday) || ($allday && $up > $down))
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
			Request::setVar('includeRegistration', 1);
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
		$year  = Date::of(strtotime($event['publish_up']))->format("Y");
		$month = Date::of(strtotime($event['publish_up']))->format("m");

		//build message
		$message = Lang::txt('You have successfully created a new group event.');
		if (isset($event['id']) && $event['id'] != 0)
		{
			$message = Lang::txt('You have successfully edited the group event.');
		}

		//inform user and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id=' . $eventsModelEvent->get('id')),
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
		$eventId = Request::getVar('event_id','','get');

		//load event data
		$eventsModelEvent = new \Components\Events\Models\Event($eventId);

		//for rediction purposes
		$publish_up = strtotime($eventsModelEvent->get('publish_up'));
		$year  = date('Y', $publish_up);
		$month = date('m', $publish_up);

		// check to see if user has the right permissions to delete
		if ($this->user->get('id') != $eventsModelEvent->get('created_by') && $this->authorized != 'manager')
		{
			// do not have permission to delete the event
			App::redirect(
				Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $year . '&month=' . $month),
				Lang::txt('You do not have the correct permissions to delete this event.'),
				'error'
			);
			return;
		}

		// make sure this event is editable
		$eventCalendar = $eventsModelEvent->calendar();
		if ($eventCalendar->isSubscription())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id='.$eventsModelEvent->get('id')),
				Lang::txt('You cannot delete imported events from remote calendar subscriptions.'),
				'error'
			);
			return;
		}

		//make as disabled
		$eventsModelEvent->set('state', 0);

		//save changes
		if (!$eventsModelEvent->store(true))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $year . '&month=' . $month),
				Lang::txt('An error occurred while trying to delete the event. Please try again.'),
				'error'
			);
			return;
		}

		//inform user and return
		App::redirect(
			Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $year . '&month=' . $month),
			Lang::txt('You have successfully deleted the event.'),
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
		$view = $this->view('details', 'calendar');

		//get request varse
		$eventId = Request::getVar('event_id','','get');

		//load event data
		$view->event = new \Components\Events\Models\Event($eventId);

		// make sure we have event
		if (!$view->event->get('id'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&year=' . $this->year . '&month=' . $this->month),
				Lang::txt('Event not found.'),
				'error'
			);
			return;
		}

		//get registrants count
		$eventsRespondent = new \Components\Events\Tables\Respondent(array('id' => $eventId));
		$view->registrants = $eventsRespondent->getCount();

		//get calendar
		$view->calendar = \Components\Events\Models\Calendar::getInstance($view->event->get('calendar_id'));

		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->user      = $this->user;

		//get any errors if there are any
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
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
		$eventId = Request::getVar('event_id','','get');

		// load & export event
		$eventsModelEvent = new \Components\Events\Models\Event($eventId);
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
			die(Lang::txt('Calendar subsciptions are currently turned off.'));
		}

		//force https protocol
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
		{
			App::redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die(Lang::txt('Calendar subscriptions only support the HTTPS (port 443) protocol.'));
		}

		//get the calendar plugin access
		$plugin_access = $this->access['calendar'];

		//is the plugin off
		if ($plugin_access == 'nobody')
		{
			header('HTTP/1.1 404 Not Found');
			die(Lang::txt('GROUPS_PLUGIN_OFF', 'Calendar'));
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
				die(Lang::txt('GROUPS_PLUGIN_REGISTERED', 'Calendar'));
			}

			//make sure we are a member
			if ($plugin_access == 'members' && !is_object($auth) && !in_array($auth->id, $this->group->get('members')))
			{
				header('HTTP/1.1 403 Unauthorized');
				die(Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', 'Calendar'));
			}
		}

		// load & subscribe to the calendar archive
		$eventsCalendarArchive = \Components\Events\Models\Calendar\Archive::getInstance();
		$subscriptionName = '[' . Config::get('sitename') . '] Group Calendar: ' . $this->group->get('description');
		$eventsCalendarArchive->subscribe($subscriptionName, 'group', $this->group->get('gidNumber'));
	}

	/**
	 * Authenticate Subscription Requests
	 *
	 * @return void
	 */
	private function authenticateSubscriptionRequest()
	{
		$realm = '[' . Config::get('sitename') . '] Group Calendar: ' . $this->group->get('description');
		if (empty($_SERVER['PHP_AUTH_USER']))
		{
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			echo Lang::txt('You are not authorized to view this calendar.');
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
			die(Lang::txt('You must enter a valid username and password.'));
		}

		//get the user based on username
		$sql = "SELECT u.id, u.username, up.passhash
		        FROM #__users AS u, #__users_password AS up
		        WHERE u.id=up.user_id
		        AND u.username=". $this->database->quote($httpBasicUsername);
		$this->database->setQuery($sql);
		$user = $this->database->loadObject();

		//make sure we found a user
		if (!is_object($user) || $user->id == '' || $user->id == 0)
		{
			App::get('log')->logger('auth')->info($httpBasicUsername . ' ' . $_SERVER['REMOTE_ADDR'] . ' invalid group calendar subscription auth for ' . $this->group->get('cn'));
			apache_note('auth','invalid');

			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			die(Lang::txt('You must enter a valid username and password.'));
		}

		//make sure password matches stored password
		if (!\Hubzero\User\Password::comparePasswords($user->passhash, $httpBasicPassword))
		{
			App::get('log')->logger('auth')->info($httpBasicUsername . ' ' . $_SERVER['REMOTE_ADDR'] . ' invalid group calendar subscription auth for ' . $this->group->get('cn'));
			apache_note('auth','invalid');

			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			die(Lang::txt('You must enter a valid username and password.'));
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
		require_once __DIR__ . DS . 'icalparser.php';

		//get incoming
		$file = Request::getVar('import', array(), 'files');

		// parse file & get first event
		$icalparser = new IcalParser($file['tmp_name']);
		$icalEvent = $icalparser->getFirstEvent();

		// get values from ical File
		$title       = (isset($icalEvent['SUMMARY'])) ? $icalEvent['SUMMARY'] : '';
		$description = (isset($icalEvent['DESCRIPTION'])) ? $icalEvent['DESCRIPTION'] : '';
		$location    = (isset($icalEvent['LOCATION'])) ? $icalEvent['LOCATION'] : '';
		$website     = (isset($icalEvent['URL'])) ? $icalEvent['URL'] : '';
		$start       = (isset($icalEvent['DTSTART']) && ($icalEvent['DTSTART'] instanceof DateTime)) ? $icalEvent['DTSTART'] : new DateTime();
		$end         = (isset($icalEvent['DTEND']) && ($icalEvent['DTEND'] instanceof DateTime)) ? $icalEvent['DTEND'] : new DateTime();
		$recurrence  = (isset($icalEvent['RRULE'])) ? $icalEvent['RRULE'] : array();

		// normalize until date
		if (isset($recurrence['UNTIL']))
		{
			$tz = Config::get('offset');
			$until = new DateTime($recurrence['UNTIL']);
			$until->setTimezone(new DateTimezone($tz));
			$recurrence['UNTIL'] = $until->format('m/d/Y');
		}

		//object to hold event data
		$event             = new stdClass;
		$event->title      = $title;
		$event->content    = stripslashes(str_replace('\n', "\n", $description));
		$event->start      = $start->format("m/d/Y @ g:i a");
		$event->end        = $end->format("m/d/Y @ g:i a");
		$event->location   = $location;
		$event->website    = $website;
		$event->recurrence = $recurrence;

		//return event details
		echo json_encode(array('event'=> $event));
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
		$view = $this->view('register', 'calendar');

		//get request varse
		$eventId = Request::getVar('event_id','');

		//load event data
		$view->event = new \Components\Events\Models\Event($eventId);

		//get registrants count
		$eventsRespondent = new \Components\Events\Tables\Respondent(array('id' => $eventId));
		$view->registrants = $eventsRespondent->getCount();

		//do we have a registration deadline
		if ($view->event->get('registerby') == '' || $view->event->get('registerby') == '0000-00-00 00:00:00')
		{
			App::redirect(
				Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id=' . $view->event->get('id')),
				Lang::txt('This event does not have registration.'),
				'warning'
			);
			return;
		}

		//make sure registration is open
		$now        = Date::toUnix();
		$registerby = Date::of($view->event->get('registerby'))->toUnix();

		if ($registerby >= $now)
		{
			//get the password
			$password = Request::getVar('passwrd', '', 'post');

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
		$view->user      = $this->user;

		$view->register   = (isset($this->register)) ? $this->register : null;
		$view->arrival    = (isset($this->arrival)) ? $this->arrival : null;
		$view->departure  = (isset($this->departure)) ? $this->departure : null;
		$view->dietary    = (isset($this->dietary)) ? $this->dietary : null;
		$view->dinner     = (isset($this->dinner)) ? $this->dinner : null;
		$view->disability = (isset($this->disability)) ? $this->disability : null;
		$view->race       = (isset($this->race)) ? $this->race : null;

		//add params to view
		$view->params = new \Hubzero\Config\Registry($view->event->get('params'));

		if (!$this->user->get('guest'))
		{
			$profile = \Hubzero\User\User::oneOrNew($this->user->get('id'));

			$view->register['first_name']  = $profile->get('givenName');
			$view->register['last_name']   = $profile->get('surname');
			$view->register['affiliation'] = $profile->get('organization');
			$view->register['email']       = $profile->get('email');
			$view->register['telephone']   = $profile->get('phone');
			$view->register['website']     = $profile->get('url');
		}

		//get any errors if there are any
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
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
		$register   = Request::getVar('register', NULL, 'post');
		$arrival    = Request::getVar('arrival', NULL, 'post');
		$departure  = Request::getVar('departure', NULL, 'post');
		$dietary    = Request::getVar('dietary', NULL, 'post');
		$dinner     = Request::getVar('dinner', NULL, 'post');
		$disability = Request::getVar('disability', NULL, 'post');
		$race       = Request::getVar('race', NULL, 'post');
		$event_id   = Request::getInt('event_id', NULL, 'post');

		//load event data
		$event = new \Components\Events\Models\Event($event_id);

		// get event params
		$params = new \Hubzero\Config\Registry($event->get('params'));

		//array to hold any errors
		$errors = array();

		//check for first name
		if (!isset($register['first_name']) || $register['first_name'] == '')
		{
			$errors[] = Lang::txt('Missing first name.');
		}

		//check for last name
		if (!isset($register['last_name']) || $register['last_name'] == '')
		{
			$errors[] = Lang::txt('Missing last name.');
		}

		//check for affiliation
		if (isset($register['affiliation']) && $register['affiliation'] == '')
		{
			$errors[] = Lang::txt('Missing affiliation.');
		}

		//check for email if email is supposed to be on
		if ($params->get('show_email', 1) == 1)
		{
			if (!isset($register['email']) || $register['email'] == '' || !filter_var($register['email'], FILTER_VALIDATE_EMAIL))
			{
				$errors[] = Lang::txt('Missing email address or email is not valid.');
			}

			// check to make sure this is the only time registering
			if (\Components\Events\Tables\Respondent::checkUniqueEmailForEvent($register['email'], $event_id) > 0)
			{
				$errors[] = Lang::txt('You have previously registered for this event.');
			}
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
			$this->setError(implode('<br />', $errors));
			return $this->register();
		}

		//set data for saving
		$eventsRespondent                       = new \Components\Events\Tables\Respondent(array());
		$eventsRespondent->event_id             = $event_id;
		$eventsRespondent->registered           = Date::toSql();
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
		$eventsRespondent->bind($register);

		//did we save properly
		if (!$eventsRespondent->save($eventsRespondent))
		{
			$this->setError($eventsRespondent->getError());
			return $this->register();
		}

		$r = $race;
		unset($r['nativetribe']);
		$r = (empty($r)) ? array() : $r;
		$sql = "INSERT INTO `#__events_respondent_race_rel` (respondent_id, race, tribal_affiliation)
		        VALUES (".$this->database->quote($eventsRespondent->id).", ".$this->database->quote(implode(',', $r)).", ".$this->database->quote($race['nativetribe']).")";
		$this->database->setQuery($sql);
		$this->database->query();

		//load event we are registering for
		$eventsEvent = new \Components\Events\Tables\Event($this->database);
		$eventsEvent->load($event_id);

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
			$email->params     = $params;
			$email->event      = $eventsEvent;
			$email->sitename   = Config::get('sitename');
			$email->register   = $register;
			$email->race       = $race;
			$email->dietary    = $dietary;
			$email->disability = $disability;
			$email->arrival    = $arrival;
			$email->departure  = $departure;
			$email->dinner     = $dinner;
			$message           = str_replace("\n", "\r\n", $email->loadTemplate());

			//declare subject
			$subject = Lang::txt("[" . $email->sitename . "] Group \"{$this->group->get('description')}\" Event Registration: " . $eventsEvent->title);

			//make from array
			$from = array(
				'email' => 'group-event-registration@' . $_SERVER['HTTP_HOST'],
				'name'  => $register['first_name'] . ' ' . $register['last_name']
			);

			// email from person
			if ($params->get('show_email', 1) == 1)
			{
				$from['email'] = $register['email'];
			}

			//send email
			$this->_sendEmail($eventsEvent->email, $from, $subject, $message);
		}

		// build message to send to event registerer
		// only send if show email is on
		if ($params->get('show_email', 1) == 1)
		{
			$email = $this->view('register_email_user', 'calendar');
			$email->option     = $this->option;
			$email->group      = $this->group;
			$email->params     = $params;
			$email->event      = $eventsEvent;
			$email->sitename   = Config::get('sitename');
			$email->siteurl    = Config::get('live_site');
			$email->register   = $register;
			$email->race       = $race;
			$email->dietary    = $dietary;
			$email->disability = $disability;
			$email->arrival    = $arrival;
			$email->departure  = $departure;
			$email->dinner     = $dinner;
			$message           = str_replace("\n", "\r\n", $email->loadTemplate());

			// build to, from, & subject
			$to      = User::get('email');
			$from    = array('email' => 'groups@' . $_SERVER['HTTP_HOST'], 'name'  => $email->sitename . ' Group Calendar: ' . $this->group->get('description'));
			$subject = Lang::txt('Thank you for Registering for the "%s" event', $eventsEvent->title);

			// send mail to user registering
			$this->_sendEmail($to, $from, $subject, $message);
		}

		// redirect back to the event
		App::redirect(
			Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=calendar&action=details&event_id=' . $event_id),
			Lang::txt('You have successfully registered for the event.'),
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
		$view = $this->view('registrants', 'calendar');

		//get request varse
		$eventId = Request::getVar('event_id','','get');

		//load event data
		$view->event = new \Components\Events\Tables\Event($this->database);
		$view->event->load($eventId);

		//get registrants count
		$eventsRespondent = new \Components\Events\Tables\Respondent(array('id' => $eventId));
		$view->registrants = $eventsRespondent->getRecords();

		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->user      = $this->user;

		//get any errors if there are any
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
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
		$eventId = Request::getVar('event_id','','get');

		//get registrants count
		$eventsRespondent = new \Components\Events\Tables\Respondent(array('id' => $eventId));
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
			        FROM `#__events_respondent_race_rel`
			        WHERE respondent_id=" . $this->database->quote($registrant->id);
			$this->database->setQuery($sql);
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
						$output .= $this->escapeCsv($race) . ',';
						break;
					case 'registered':
						$output .= $this->escapeCsv(Date::of($registrant->registered)->toLocal('Y-m-d H:i:s'));
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
	private function escapeCsv($value)
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
		$eventsCalendarArchive = \Components\Events\Models\Calendar\Archive::getInstance();
		$calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber')
		));

		//create the view
		$view = $this->view('display', 'calendars');

		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->user      = $this->user;
		$view->calendars  = $calendars;

		//get any errors if there are any
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
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
		$calendarId = Request::getVar('calendar_id','');

		//create the view
		$view = $this->view('edit', 'calendars');

		// get the calendar
		$view->calendar = \Components\Events\Models\Calendar::getInstance($calendarId);

		//push some vars to the view
		$view->month      = $this->month;
		$view->year       = $this->year;
		$view->group      = $this->group;
		$view->option     = $this->option;
		$view->authorized = $this->authorized;
		$view->user       = $this->user;

		//get any errors if there are any
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
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
		Request::checkToken();

		//get request vars
		$calendarInput = Request::getVar('calendar',array());

		// get the calendar
		$calendar = \Components\Events\Models\Calendar::getInstance($calendarInput['id']);

		//add scope and scope id to calendar array
		$calendarInput['scope']    = 'group';
		$calendarInput['scope_id'] = $this->group->get('gidNumber');
		$calendarInput['url']      = trim($calendarInput['url']);

		$colors = array('red','orange','yellow','green','blue','purple','brown');
		if (!in_array($calendarInput['color'], $colors))
		{
			$calendarInput['color'] = '';
		}

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
			$this->setError($calendar->getError());
			return $this->editCalendar();
		}

		// attempt to save
		if (!$calendar->store(true))
		{
			$this->setError($calendar->getError());
			return $this->editCalendar();
		}

		// should we refresh?
		if ($needsRefresh)
		{
			$calendar->refresh();
		}

		//inform and redirect
		App::redirect(
			Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=calendars'),
			Lang::txt('You have successfully added a new calendar.'),
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
		$calendarId   = Request::getVar('calendar_id','');
		$events       = Request::getVar('events','delete');
		$deleteEvents = ($events == 'delete') ? true : false;

		// get the calendar
		$calendar = \Components\Events\Models\Calendar::getInstance($calendarId);

		//delete the calendar
		$calendar->delete($deleteEvents);

		//inform and redirect
		App::redirect(
			Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=calendars'),
			Lang::txt('You have successfully deleted the calendar.'),
			'passed'
		);
	}

	/**
	 * Method to refresh Group Calendar
	 */
	private function refreshCalendar()
	{
		//get the passed in event id
		$calendarId = Request::getVar('calendar_id','');

		// get the calendar
		$calendar = \Components\Events\Models\Calendar::getInstance($calendarId);

		// refresh Calendar (force refresh even if we dont need to yet)
		if (!$calendar->refresh(true))
		{
			$this->setError(Lang::txt('Unable to sync the group calendar "%s". Please verify the calendar subscription URL is valid.', $calendar->getError()));
			return $this->calendars();
		}

		//inform and redirect
		App::redirect(
			Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=calendars'),
			Lang::txt('You have successfully refreshed the calendar.'),
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
		$eventsCalendarArchive = \Components\Events\Models\Calendar\Archive::getInstance();
		$calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => 'group',
			'scope_id'  => $this->group->get('gidNumber')
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
		$reccurance = Request::getVar('reccurance', array(), 'post');

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
				$timezone = new DateTimezone(Config::get('offset'));
				$date = Date::of($until, $timezone);

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
	 * @return  array
	 */
	private function _getAllFutureEvents()
	{
		$db = App::get('db');
		$sql = "SELECT COUNT(*)
				FROM #__events
				WHERE scope=" . $db->quote('group') . "
				AND scope_id=".$this->group->get('gidNumber')."
				AND state=1
				AND (publish_up >='".Date::toSql()."' OR publish_down >='".Date::toSql()."')";
		$db->setQuery($sql);
		return $db->loadResult();
	}


	/**
	 * Get all future events that start or finish this month
	 *
	 * @return  array
	 */
	private function _getFutureEventsThisMonth()
	{
		$db = App::get('db');
		$sql = "SELECT COUNT(*)
				FROM #__events
				WHERE scope=" . $db->quote('group') . "
				AND scope_id=".$this->group->get('gidNumber')."
				AND state=1
				AND (publish_up >= '".Date::toSql()."' OR publish_down >='".Date::toSql()."') AND publish_up <= '".Date::format("Y-m-t 23:59:59")."'";
		$db->setQuery($sql);
		return $db->loadResult();
	}


	/**
	 * Send an email
	 *
	 * @param   string   $to
	 * @param   array    $from
	 * @param   string   $subject
	 * @param   string   $body
	 * @return  boolean
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
