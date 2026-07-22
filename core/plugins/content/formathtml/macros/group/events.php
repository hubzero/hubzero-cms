<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros\Group;

require_once dirname(__DIR__) . DS . 'group.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'calendar.php';

use Plugins\Content\Formathtml\Macros\GroupMacro;
use Components\Events\Models\Calendar;
use Date;

/**
 * Group events Macro
 */
class Events extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		$txt = array();
		$txt['html']  = '<p>Displays group events.</p>';
		$txt['html'] .= '<p>Arguments:</p>
							<ul>
								<li><code>from</code> - The start date of the events to display. Default is "today". Can be either in YYYY-MM-DD format, or one of "today", "yesterday", or "tomorrow.</li>
								<li><code>to</code> - The end date of the events to display. Default is one year after "from" date. Can be either in YYYY-MM-DD format, or one of "today", "yesterday", or "tomorrow.</li>
								<li><code>for</code> - The duration of events to display, starting after "from" date (inclusive). Default is "1 year". Can use number of days, weeks, months, or years. This argument is ignored if the "to" argument is specified.</li>
								<li><code>order</code> - The order in which to display the events. Can be "forward" or "backward" in time. Default is "forward." This is most helpful when displaying past events (see example below). </li>
							</ul>';
		$txt['html'] .= '<p>Examples:</p>
							<ul>
								<li><code>[[Group.Events()]]</code> - Displays all events from today to 1 year from today</li>
								<li><code>[[Group.Events(from=today)]]</code> - Another way to display all events from today to 1 year from today</li>
								<li><code>[[Group.Events(from=today, for=3 months)]]</code> - Displays 3 months of events from today</li>
								<li><code>[[Group.Events(from=today, to=today)]]</code> - Displays events for today only</li>
								<li><code>[[Group.Events(from=0000-01-01, to=yesterday, order=backward)]]</code> - Displays events up to and including yesterday (i.e. all past events), with most recent events shown first.</li>
								<li><code>[[Group.Events(from=2021-04-15)]]</code> - Displays events from 2021-04-15 to 2022-04-14 (1 year default)</li>
								<li><code>[[Group.Events(from=2021-04-15, to=2025-01-01)]]</code> - Displays events from 2021-04-15 to 2025-01-01</li>
							</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		// Check if we can render
		if (!parent::canRender())
		{
			return \Lang::txt('[This macro is designed for Groups only]');
		}

		// Get args
		$args = $this->getArgs();

		// Array of filters
		$filters = array();

		// Get arguments
		if (($filters['from'] = $this->_getFrom($args)) === false) {
			return "<p style='color:red;'>Error in Group.Events macro: Invalid 'from' argument. Please use either one of 'today', 'tomorrow', or 'yesterday', or a proper date of the form YYYY-MM-DD.</p>";
		}
		if (($filters['to'] = $this->_getTo($args)) === false) {
			return "<p style='color:red;'>Error in Group.Events macro: Invalid 'to' argument. Please use either one of 'today', 'tomorrow', or 'yesterday', or a proper date of the form YYYY-MM-DD.</p>";
		}
		if (!$filters['to']) { // Only get 'for' if 'to' is not specified
			if (($filters['for'] = $this->_getFor($args)) === false) {
				return "<p style='color:red;'>Error in Group.Events macro: Invalid 'for' argument. Please use a number, whitespace, and then 'days', 'weeks', 'months', or 'years'.</p>";
			}
			$filters['to'] = Date::of($filters['from'])->add($filters['for'])->format('Y-m-d 23:59:59');
		}
		if (($filters['order'] = $this->_getOrder($args)) === false) {
			return "<p style='color:red;'>Error in Group.Events macro: Invalid 'order' argument. Please use either 'forward' or 'backward'.</p>";
		}

		// From date should be before to date
		if ($filters['from'] && $filters['to'] && strtotime($filters['from']) > strtotime($filters['to'])) {
			return "<p style='color:red;'>Error in Group.Events macro: 'from' date must be before 'to' date.</p>";
		}

		// Get group events
		$events =  $this->getGroupEvents($this->group, $filters);

		// Create the html container
		$html  = '<div class="upcoming_events">';

		// Render the events
		$html .= $this->renderEvents($this->group, $events);

		// Close the container
		$html .= '</div>';

		// Return rendered events
		return $html;
	}

	/**
	 * Get a list of events for a group
	 *
	 * @param   object  $group
	 * @param   array   $filters
	 * @return  array
	 */
	private function getGroupEvents($group, $filters = array())
	{
		// Pulled from core/plugins/groups/calendar/calendar.php::events()

		// array to hold events
		$events = array();

		// get request params ($from and $to should already exist)
		$start = $filters['from'];
		$end = $filters['to'];
		$order = (in_array($filters['order'], array('forward', 'forwards')) ? 1 : -1);
		$calendarId = isset($filters['calendar_id']) ? $filters['calendar_id'] : 0;

		// get calendar events
		$eventsCalendar = Calendar::getInstance();
		$rawEvents = $eventsCalendar->events('list', array(
			'scope'        => 'group',
			'scope_id'     => $this->group->get('gidNumber'),
			'calendar_id'  => $calendarId,
			'state'        => array(1),
			'publish_up'   => $start,
			'publish_down' => $end,
			'non_repeating'    => true
		));

		// get repeating events
		$rawEventsRepeating = $eventsCalendar->events('repeating', array(
			'scope'        => 'group',
			'scope_id'     => $this->group->get('gidNumber'),
			'calendar_id'  => $calendarId,
			'state'        => array(1),
			'publish_up'   => $start,
			'publish_down' => $end,
			'until'		   => $end
		));

		// merge events with repeating events
		$rawEvents = $rawEvents->merge($rawEventsRepeating);

		// loop through each event to return it
		foreach ($rawEvents as $rawEvent)
		{
			$up   = Date::of($rawEvent->get('publish_up'));
			$down = Date::of($rawEvent->get('publish_down'));
			$params = new \Hubzero\Config\Registry($rawEvent->get('params'));
			$ignoreDst = false;
			$ignoreDst = $params->get('ignore_dst') == 1 ? true : false;

			$event            = new \stdClass;
			$event->title     = $rawEvent->get('title');

			// Create URL to event
			$event->url       = $rawEvent->link();
			// add start & end for displaying dates user clicked on
			// instead of actual event start & end
			if ($rawEvent->get('repeating_rule') != '')
			{
				$event->url .= '?start=' . $up->toUnix();
				if ($rawEvent->get('publish_down') && $rawEvent->get('publish_down') != '0000-00-00 00:00:00')
				{
					$event->url .= '&end=' . $down->toUnix();
				}
			}
			$event->location = $rawEvent->get('adresse_info');
			$event->about = nl2br($rawEvent->get('content'));
			$strcap = 255;
			if (strlen($event->about) > $strcap)
			{
				$event->about = \Hubzero\Utility\Str::truncate($event->about, $strcap, array('html' => true)) . ' <a href="' . $event->url . '">[more]</a>';
			}

			$event->allDay    = $rawEvent->get('allday') == 1;
			$event->className = ($rawEvent->get('calendar_id')) ? 'calendar-' . $rawEvent->get('calendar_id') : 'calendar-0';
			$event->start_month = $up->format('M');
			$event->start_day = $up->format('d');
			$event->iso_8601 = $up->format('c');
			$event->year = $up->format('Y');
			$event->up = $up; // Used for sorting below
			if (!$event->allDay) {
				// Is this an open-ended event?
				if ($down <= Date::of('0000-00-00 00:00:00')) {
					// Open-ended event
					$event->start = $up->toLocal('g:i A T', $ignoreDst);
					$event->end = "(heat death of the universe)";
				} else {
					if ($up->format('Y-m-d') == $down->format('Y-m-d')) {
						// Single-day event
						$event->start = $up->toLocal('g:i A', $ignoreDst);
						$event->end = $down->toLocal('g:i A T', $ignoreDst);
					} else {
						// Multi-day event
						$event->start = $up->toLocal('M j g:i A T', $ignoreDst);
						$event->end = $down->toLocal('M j g:i A T', $ignoreDst);
					}
				}
			} else {
				// Adjustment since hub stores down of all-day events as next day at midnight
				$down = $down->subtract('24 hours');

				// Is this an open-ended event?
				if ($down <= Date::of('0000-00-00 00:00:00') ||
				    $down->format('Y-m-d') != $up->format('Y-m-d')) {
					$event->start = $up->format('M j');
					$event->end = $down->format('M j');
				} else {
					$event->start = 'All day';
					$event->end = '';
				}
			}

			array_push($events, $event);
		}

		// Put in order of start datetime
		uasort($events, function($a, $b) use ($order) {
			return $order*($a->up->toUnix() - $b->up->toUnix());
		});

		return $events;
	}

	/**
	 * Render the events
	 *
	 * @param  array  $group   Array of groups
	 * @param  array  $events  Array of group events
	 * @return string
	 */
	private function renderEvents($group, $events)
	{
		\Document::addStyleSheet(rtrim(str_replace(PATH_ROOT, '', __DIR__)) . DS . '../macro-assets' . DS . 'events' . DS . 'events.css');

		$content = '';
		$curr_year = '';
		if (count($events) > 0)
		{
			foreach ($events as $event)
			{
				if ($curr_year == '') {
					$content .= '<div class="item-list">';
					$content .= '<h3 class="year">' . $event->year . '</h3>';
					$content .= '<ul class="list-unstyled">';
					$curr_year = $event->year;
				} elseif ($curr_year != $event->year) {
					$content .= '</ul>';
					$content .= '</div>';
					$content .= '<div class="item-list">';
					$content .= '<h3 class="year">' . $event->year . '</h3>';
					$content .= '<ul class="list-unstyled">';
					$curr_year = $event->year;
				}
				$content .= '<li class="mb-4">';
				$content .= '  <div>';
				$content .= '    <div class="event-card_wrap">';
				$content .= '      <div class="event-card_left">';
				$content .= '        <div>';
				$content .= '          <time class="event-date_stacked" datetime="' . $event->iso_8601 . '">';
				$content .= '            <div class="event-date_stacked_month">' . $event->start_month . '</div>';
				$content .= '            <div class="event-date_stacked_day">' . $event->start_day . '</div>';
				$content .= '          </time>';
				$content .= '        </div>';
				$content .= '      </div>';
				$content .= '      <div class="event-card_body">';
				$content .= '        <div><h5><a href="' . $event->url . '">' . $event->title . '</a></h5></div>';
				$content .= '        <div class="event-time event-time_teaser">';
				$content .= '          <span>' . $event->start . '</span>';
				if ($event->end)
				{
					$content .= '          <span>&nbsp;to&nbsp; </span><span>' . $event->end . '</span>';
				}
				$content .= '        </div>';
				if ($event->location) {
					$content .= '        <div class="event-location location">';
					if (preg_match_all('/(http|ftp|https):\/\/([\w-]+(?:(?:\.[\w_-]+)+))([\w.,@?()<>;^=%&:\/~+#-]*[\w@?(<^=%&\/~+#-])?/', $event->location)) {
						$content .= '		  <a href="' . $event->location . '" target="_blank">' . $event->location . '</a>';
					} else {
						$content .= '		  <span>' . $event->location . '</span>';
					}
					$content .= '        </div>';
				}
				$content .= '        <div>' . $event->about . '</div>';
				$content .= '      </div>';
				$content .= '    </div>';
				$content .= '  </div>';
				$content .= '</li>';
			}
			$content .= '</div>';
			$content .= '</ul>';
		}
		else
		{
			$content .= '<p>Currently there are no upcoming group events. Add an event by <a href="' . \Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&action=add') . '">clicking here.</a></p>';
		}

		return $content;
	}

	/**
	 * Get for argument
	 *
	 * @param   array  $args  Macro Arguments
	 * @return  mixed
	 */
	private function _getFor(&$args)
	{
		foreach ($args as $k => $arg)
		{
			// Match any word characters, digits, or spaces
			if (preg_match('/for[\s]*=[\s]*([\w\d\s]*)/', $arg, $matches))
			{
				$for = (isset($matches[1])) ? $matches[1] : '';
				unset($args[$k]);

				// Validation - Check if from is a number, space, then word
				if (!preg_match('/([\d]*)\s([\w]*)/', $for, $matches) ||
				    !in_array($matches[2], array('day', 'days', 'week', 'weeks', 'month', 'months', 'year', 'years'))) {
					$for = false; // Invalid
				}

				return $for;
			}
		}

		// Default to 1 year
		$for = "1 year";
		return $for;
	}

	/**
	 * Get from argument
	 *
	 * @param   array  $args  Macro Arguments
	 * @return  mixed
	 */
	private function _getFrom(&$args)
	{
		foreach ($args as $k => $arg)
		{
			// Match any word characters, digits, or spaces
			if (preg_match('/from[\s]*=[\s]*([\w\d-]*)/', $arg, $matches))
			{
				$from = (isset($matches[1])) ? $matches[1] : '';
				unset($args[$k]);

				// Validation - Check if from is a number, space, then word, or the word "today"
				if (!(preg_match('/([\d]{4}[-\/][\d]{2}[-\/][\d]{2})/', $from, $matches) && (strtotime($matches[1]) !== false)) &&
					!in_array($from, array("today", "yesterday", "tomorrow"))) {
					$from = false; // Invalid
				} elseif ($from == "today") {
					$from = Date::of('now')->format('Y-m-d 00:00:00');
				} elseif ($from == "yesterday") {
					$from = Date::of('yesterday')->format('Y-m-d 00:00:00');
				} elseif ($from == "tomorrow") {
					$from = Date::of('tomorrow')->format('Y-m-d 00:00:00');
				}

				return $from;
			}
		}

		// Default to today
		$from = Date::of('now')->format('Y-m-d 00:00:00');
		return $from;
	}

	/**
	 * Get to argument
	 *
	 * @param   array  $args  Macro Arguments
	 * @return  mixed
	 */
	private function _getTo(&$args)
	{
		foreach ($args as $k => $arg)
		{
			// Match any word characters, digits, or spaces
			if (preg_match('/to[\s]*=[\s]*([\w\d-]*)/', $arg, $matches))
			{
				$to = (isset($matches[1])) ? $matches[1] : '';
				unset($args[$k]);

				// Validation - Check if to is a number, space, then word, or the word "today"
				if (!(preg_match('/([\d]{4}[-\/][\d]{2}[-\/][\d]{2})/', $to, $matches) && (strtotime($matches[1]) !== false)) &&
				    !in_array($to, array("today", "yesterday", "tomorrow"))) {
					$to = false; // Invalid
				} elseif ($to == "today") {
					$to = Date::of('now')->format('Y-m-d 23:59:59');
				} elseif ($to == "yesterday") {
					$to = Date::of('yesterday')->format('Y-m-d 23:59:59');
				} elseif ($to == "tomorrow") {
					$to = Date::of('tomorrow')->format('Y-m-d 23:59:59');
				}

				return $to;
			}
		}
	}

	/**
	 * Get order argument
	 *
	 * @param   array  $args  Macro Arguments
	 * @return  mixed
	 */
	private function _getOrder(&$args)
	{
		foreach ($args as $k => $arg)
		{
			// Match any word characters, digits, or spaces
			if (preg_match('/order[\s]*=[\s]*([a-z]*)/', $arg, $matches))
			{
				$order = (isset($matches[1])) ? $matches[1] : '';
				unset($args[$k]);

				// Validation - Check if order is either "forward" or "backward"
				if (!in_array($order, array("forward", "forwards", "backward", "backwards"))) {
					$order = false; // Invalid
				}

				return $order;
			}
		}

		return "forward";
	}
}
