<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Calendar\Site\Controllers;

require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'event.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'calendar' . DS . 'archive.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'tags.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'tables' . DS . 'respondent.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'tables' . DS . 'category.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'html.php');

use Hubzero\Component\SiteController;
use Hubzero\Component\View;
use DateTimezone;
use DateTime;
use Exception;
use Components\Events\Models\Tags;
use Components\Events\Tables\Event;
use Components\Events\Tables\Category;

/**
 * @todo replace models and tables, right now it's just "working".
 */

/**
 * Controller class for events
 */
class Calendar extends SiteController
{

	/**
	 * Runs before any other methods, used to set global variables.
	 * @return null
	 */
	public function execute()
	{
		//get the request vars
		$this->month    = Request::getInt('month', Date::format("m") ,'get');
		$this->month    = (strlen($this->month) == 1) ? '0'.$this->month : $this->month;
		$this->year     = Request::getInt('year', Date::format("Y"), 'get');
		$this->calendar = Request::getInt('calendar', 0, 'get');

		parent::execute();
	}

	/**
	 * displayTask() default view for the calendar component.
	 * @return void
	 */
	public function displayTask()
	{
		//push the calendar content to view
		$this->view->month        = $this->month;
		$this->view->year         = $this->year;
		$this->view->calendar     = $this->calendar;
		$this->view->authorized   = $this->authorized;
		$this->view->members      = $this->members;
		$this->view->option       = $this->option;
		$this->view->group        = $this->group;
		$this->view->params       = $this->params;

		//get calendars
		$eventsCalendarArchive = \Components\Events\Models\Calendar\Archive::getInstance();
		$this->view->calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => '',
		));

		// event calendar model
		$eventsCalendar = \Components\Events\Models\Calendar::getInstance();

		//define our filters
		$this->view->filters = array(
			'scope'    => '',
			'orderby'  => 'publish_up DESC'
		);

		// get events count
		$this->view->eventsCount = $eventsCalendar->events('count', $this->view->filters);

		// get events for no js
		$this->view->filters['limit'] = Request::getInt('limit', Config::get('list_limit'));
		$this->view->filters['start'] = Request::getInt('limitstart', 0);
		$this->view->events = $eventsCalendar->events('list', $this->view->filters);

		// add hub fancyselect lib
		\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect.min');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect.css');

		// add full calendar lib
		\Hubzero\Document\Assets::addSystemScript('moment.min');
		\Hubzero\Document\Assets::addSystemScript('jquery.fullcalendar.min');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fullcalendar.css');
		\Hubzero\Document\Assets::addSystemStylesheet('jquery.fullcalendar.print.css', 'text/css', 'print');

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Method for displaying the details view of an event.
	 * @return the details view
	 */
	public function detailsTask()
	{
		$id = \Request::getInt('event');
		$this->view->row = new Event($this->database);
		$this->view->row->load($id);

		// get category
		$catid = $this->view->row->catid;
		$category = new Category($this->database);
		$category->load($catid);
		$this->view->row->category = $category->title;

		$rt = new Tags($id);
		$this->view->row->tags = $rt->render();

		$this->view->display();

	}

	/**
	 * eventsourcesTask returns a list of event sources or calendars
	 * @return json a json-encoded list of calendars associated with this site scope.
	 */
	public function eventsourcesTask()
	{
		// array to hold sources
		$sources = array();

		// get calendars
		$eventsCalendarArchive = \Components\Events\Models\Calendar\Archive::getInstance();
		$calendars = $eventsCalendarArchive->calendars('list', array(
			'scope'     => '',
		));

		// add each calendar to the sources
		foreach ($calendars as $calendar)
		{
			$source            = new stdClass;
			$source->title     = $calendar->get('title');
			$source->url       = Route::url('index.php?option=com_calendar&task=events&no_html=1');
			$source->className = ($calendar->get('color')) ? 'fc-event-' . $calendar->get('color') : 'fc-event-default';
			array_push($sources, $source);
		}

		// add uncategorized source
		$source            = new \stdClass;
		$source->title     = 'Uncategorized';
		$source->url       = Route::url('index.php?option=com_calendar&task=events&no_html=1');
		$source->className = 'fc-event-default';
		array_push($sources, $source);

		// output sources
		echo json_encode($sources);
		exit();
	}

	/**
	 * eventsTask() used by the fullcalendar.js to return json set of events
	 * @return json set of events and their details.
	 */
	public function eventsTask()
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
			'calendar_id'  => $calendarId,
			'state'        => array(1),
			'publish_up'   => $start->format('Y-m-d H:i:s'),
			'publish_down' => $end->format('Y-m-d H:i:s')
		));

		// get repeating events
		$rawEventsRepeating = $eventsCalendar->events('repeating', array(
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
			$event            = new \stdClass;
			$event->id        = $rawEvent->get('id');
			$event->title     = $rawEvent->get('title');
			$event->allDay    = $rawEvent->get('allday') == 1;
			$event->url 	  = Route::url('index.php?option=com_calendar&task=details&event='.$event->id, false);
			$event->start     = Date::of($rawEvent->get('publish_up'))->toLocal('Y-m-d\TH:i:sO');
			$event->className = ($rawEvent->get('calendar_id')) ? 'calendar-'.$rawEvent->get('calendar_id') : 'calendar-0';

			$serverTZ = date_default_timezone_get(); //get the default timezone

			// convert UTC to local time zone
			date_default_timezone_set('UTC'); // set up DateTime to use UTC
			$start = new DateTime($rawEvent->get('publish_up')); // make the DateTime object
			$end = new DateTime($rawEvent->get('publish_down')); // make the DateTime object
			$registerby = new DateTime($rawEvent->get('registerby')); //make the DateTime object

			$local = new DateTimeZone($serverTZ); // create the local timezone
			$start->setTimezone($local); // convert the time to the local timezone
			$end->setTimezone($local);
			$registerby->setTimezone($local);

			$event->friendlyStart = date_format($start, 'F d, Y g:ia T');
			$event->friendlyEnd = date_format($end, 'F d, Y g:ia T');
			$event->location = $rawEvent->get('adresse_info');

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

				$end_day = strtotime($event->end . '+ 48 hours');
				$down = date('Y-m-d H:i:s', $end_day);
				$event->end = $down;
			}


			//daylight compensation
			$dstCreated = date('I' , strtotime($rawEvent->get('created')));

			if (!$dstCreated && date('I'))
			{
				if ((int) date('I', strtotime($event->end)))
				{
					$end = strtotime($event->end . '- 1 hour');
					$event->end = date('Y-m-d H:i:s', $end);
				}

				if ((int) date('I', strtotime($event->start)))
				{
					$start = strtotime($event->start . '- 1 hour');
					$event->start = date('Y-m-d H:i:s', $start);
				}
			}
				array_push($events, $event);
		}
		// output events
		echo json_encode($events);
		exit();
	}
}

