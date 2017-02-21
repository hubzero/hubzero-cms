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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Models;

use Components\Events\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;
use DateInterval;
use DateTimezone;
use Config;
use Lang;
use Date;
use User;

// include tables
require_once dirname(__DIR__) . DS . 'tables' . DS . 'calendar.php';

// include icalendar file reader
require_once PATH_CORE . DS . 'plugins' . DS . 'groups' . DS . 'calendar' . DS . 'icalparser.php';

/**
 * Event calendar model
 */
class Calendar extends Model
{
	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Events\\Tables\\Calendar';

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_events = null;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_events_repeating = null;

	/**
	 * Events Count
	 *
	 * @var int
	 */
	private $_events_count = null;

	/**
	 * Constructor
	 *
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct($oid = null)
	{
		// create needed objects
		$this->_db = \App::get('db');

		// load page jtable
		$this->_tbl = new $this->_tbl_name($this->_db);

		// load object
		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Get Instance this Model
	 *
	 * @param   $key   Instance Key
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($key);
		}

		return $instances[$key];
	}

	/**
	 * Get a list of group pages
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function events($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'count':
				if (!$this->_events_count || $clear)
				{
					$tbl = new Tables\Event($this->_db);
					$this->_events_count = $tbl->count($filters);
				}
				return $this->_events_count;
			break;

			case 'repeating':
				if (!($this->_events_repeating instanceof ItemList) || $clear)
				{
					// var to hold repeating data
					$repeats = array();

					// add repeating filters
					$filters['repeating'] = true;

					// capture publish up/down
					// remove for now as we want all events that have a repeating rule
					$start = Date::of($filters['publish_up']);
					$end   = Date::of($filters['publish_down']);
					unset($filters['publish_up']);
					unset($filters['publish_down']);

					// find any events that match our filters
					$tbl = new Tables\Event($this->_db);
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$start = Date::of($result->publish_up);

							// get the repeating & pass start date
							$rule = new \Recurr\Rule($result->repeating_rule, $start);

							// create transformmer & generate occurances
							$transformer = new \Recurr\Transformer\ArrayTransformer();
							$occurrences = $transformer->transform($rule, null);

							// calculate diff so we can create down
							$diff = new DateInterval('P0Y0DT0H0M');
							if ($result->publish_down != '0000-00-00 00:00:00')
							{
								$diff = date_diff(Date::of($result->publish_up), Date::of($result->publish_down));
							}

							// create new event for each reoccurrence
							foreach ($occurrences as $occurrence)
							{
								$event               = clone($result);
								$event->publish_up   = $occurrence->getStart()->format('Y-m-d H:i:s');
								$event->publish_down = $occurrence->getStart()->add($diff)->format('Y-m-d H:i:s');
								$repeats[]           = new Event($event);
							}
						}
					}
					$this->_events_repeating = new ItemList($repeats);
				}
				return $this->_events_repeating;
			break;

			case 'list':
			default:
				if (!($this->_events instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Event($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Event($result);
						}
					}
					$this->_events = new ItemList($results);
				}
				return $this->_events;
			break;
		}
	}

	/**
	 * Is Calendar a subscription
	 *
	 * @return bool
	 */
	public function isSubscription()
	{
		return $this->get('readonly') && filter_var($this->get('url'), FILTER_VALIDATE_URL);
	}

	/**
	 * Refresh a Specific Group Calendar
	 *
	 * @param  bool  $force  Force refresh calendar?
	 */
	public function refresh($force = false)
	{
		// only refresh subscriptions
		if (!$this->isSubscription())
		{
			$this->setError($this->get('title'));
			return false;
		}

		// get refresh interval
		$interval = \Plugin::params('calendar', 'groups')->get('import_subscription_interval', 60);

		// get datetimes needed to refresh
		$now             = Date::of('now');
		$lastRefreshed   = Date::of($this->get('last_fetched_attempt'));
		$refreshInterval = new DateInterval("PT{$interval}M");

		// Assumes minutes
		// add refresh interval to last refreshed
		$lastRefreshed->add($refreshInterval->i);

		// if we havent passed our need to refresh date stop
		if ($now < $lastRefreshed && !$force)
		{
			return false;
		}

		// get current events
		$currentEvents = $this->events('list', array(
			'scope'       => $this->get('scope'),
			'scope_id'    => $this->get('scope_id'),
			'calendar_id' => $this->get('id'),
			'state'       => array(1)
		));

		//build calendar url
		$calendarUrl = str_replace('webcal', 'http', $this->get('url'));

		//test to see if this calendar is valid
		$calendarHeaders = get_headers($calendarUrl, 1);
		$statusCode      = (isset($calendarHeaders[0])) ? $calendarHeaders[0] : '';

		// if we got a 301, lets update the location
		if (stristr($statusCode, '301 Moved Permanently'))
		{
			if (isset($calendarHeaders['Location']))
			{
				$this->set('url', $calendarHeaders['Location']);
				$this->store(true);
				$this->refresh();
			}
		}

		//make sure the calendar url is valid
		if (!strstr($statusCode, '200 OK'))
		{
			$this->set('failed_attempts', (int)$this->get('failed_attempts', 0) + 1);
			$this->set('last_fetched_attempt', Date::toSql());
			$this->store(true);
			$this->setError($this->get('title'));
			return false;
		}

		//read calendar file
		$icalparser = new \icalparser($calendarUrl);
		$incomingEvents = $icalparser->getEvents();

		// check to make sure we have events
		if (count($incomingEvents) < 1)
		{
			$this->setError($this->get('title'));
			return false;
		}

		//make uid keys for array
		//makes it easier to diff later on
		foreach ($incomingEvents as $k => $incomingEvent)
		{
			//get old and new key
			$oldKey = $k;
			$newKey = (isset($incomingEvent['UID'])) ? $incomingEvent['UID'] : '';

			//set keys to be the uid
			if ($newKey != '')
			{
				$incomingEvents[$newKey] = $incomingEvent;
				unset($incomingEvents[$oldKey]);
			}
		}

		//get events we need to delete
		$eventsToDelete = array_diff($currentEvents->lists('ical_uid'), array_keys($incomingEvents));

		//delete each event we dont have in the incoming events
		foreach ($eventsToDelete as $eventDelete)
		{
			$e = $currentEvents->fetch('ical_uid', $eventDelete);
			$e->delete();
		}

		//create new events for each event we pull
		foreach ($incomingEvents as $uid => $incomingEvent)
		{
			// fetch event from our current events by uid
			$event = $currentEvents->fetch('ical_uid', $uid);

			// create blank event if we dont have one
			if (!$event)
			{
				$event = new Event();
			}

			// set the timezone
			$tz = new DateTimezone(Config::get('offset'));

			// start already datetime objects
			$start = $incomingEvent['DTSTART'];
			$start->setTimezone($tz);

			// set publish up/down
			$publish_up   = $start->toSql();
			$publish_down = '0000-00-00 00:00:00';
			$allday       = (isset($incomingEvent['ALLDAY']) && $incomingEvent['ALLDAY'] == 1) ? 1 : 0;
			$rrule        = null;

			// handle end
			if (isset($incomingEvent['DTEND']))
			{
				$end = $incomingEvent['DTEND'];
				$end->setTimezone($tz);
				$publish_down = $end->toSql();
			}

			// handle rrule
			if (isset($incomingEvent['RRULE']))
			{
				// add frequency
				$rrule = 'FREQ=' . $incomingEvent['RRULE']['FREQ'];

				// add interval
				if (!isset($incomingEvent['RRULE']['INTERVAL']))
				{
					$incomingEvent['RRULE']['INTERVAL'] = 1;
				}
				$rrule .= ';INTERVAL=' . $incomingEvent['RRULE']['INTERVAL'];

				// count
				if (isset($incomingEvent['RRULE']['COUNT']))
				{
					$rrule .= ';COUNT=' . $incomingEvent['RRULE']['COUNT'];
				}

				// until
				if (isset($incomingEvent['RRULE']['UNTIL']))
				{
					if (strlen($incomingEvent['RRULE']['UNTIL']) == 8)
					{
						$incomingEvent['RRULE']['UNTIL'] .= 'T000000Z';
					}
					$until = Date::of($incomingEvent['RRULE']['UNTIL']);
					$rrule .= ';UNTIL=' . $until->format('Ymd\THis\Z');
				}

				//by day
				if (isset($incomingEvent['RRULE']['BYDAY']))
				{
					$rrule .= ';BYDAY=' . $incomingEvent['RRULE']['BYDAY'];
				}
			}

			// handle all day events
			if ($start->add(new DateInterval('P1D')) == $end)
			{
				$publish_down = '0000-00-00 00:00:00';
			}

			// set event details
			$event->set('title', isset($incomingEvent['SUMMARY']) ? $incomingEvent['SUMMARY'] : '');
			$event->set('content', isset($incomingEvent['DESCRIPTION']) ? $incomingEvent['DESCRIPTION'] : '');
			$event->set('content', stripslashes(str_replace('\n', "\n", $event->get('content'))));
			$event->set('adresse_info', isset($incomingEvent['LOCATION']) ? $incomingEvent['LOCATION'] : '');
			$event->set('extra_info', isset($incomingEvent['URL']) ? $incomingEvent['URL'] : '');
			$event->set('modified', Date::toSql());
			$event->set('modified_by', User::get('id'));
			$event->set('publish_up', $publish_up);
			$event->set('publish_down', $publish_down);
			$event->set('allday', $allday);
			$event->set('repeating_rule', $rrule);

			// new event
			if (!$event->get('id'))
			{
				$event->set('catid', -1);
				$event->set('calendar_id', $this->get('id'));
				$event->set('ical_uid', isset($incomingEvent['UID']) ? $incomingEvent['UID'] : '');
				$event->set('scope', $this->get('scope'));
				$event->set('scope_id', $this->get('scope_id'));
				$event->set('state', 1);
				$event->set('created', Date::toSql());
				$event->set('created_by', User::get('id'));
				$event->set('time_zone', -5);
				$event->set('registerby', '0000-00-00 00:00:00');
				$event->set('params', '');
			}

			// save event
			$event->store(true);
		}

		// mark as fetched
		// clear failed attempts
		$this->set('last_fetched', Date::toSql());
		$this->set('last_fetched_attempt', Date::toSql());
		$this->set('failed_attempts', 0);
		$this->store(true);
		return true;
	}

	/**
	 * Delete Calendar
	 *
	 * @return [type] [description]
	 */
	public function delete($deleteEvents = false)
	{
		// if subscription delete events
		if ($this->isSubscription() || $deleteEvents)
		{
			// delete events
			$sql = "DELETE FROM `#__events` WHERE `calendar_id`=" . $this->_db->quote($this->get('id'));
			$this->_db->setQuery($sql);
			$this->_db->query();
		}
		else
		{
			// update all events, resetting their calendar
			$sql = "UPDATE `#__events` SET `calendar_id`=0 WHERE `calendar_id`=" . $this->_db->quote($this->get('id'));
			$this->_db->setQuery($sql);
			$this->_db->query();
		}

		// delete calendar
		parent::delete();
	}

	/**
	 * Delete a calendars events
	 *
	 * @param  boolean $force Force delete events (event if not subscription)
	 * @return void
	 */
	public function deleteEvents($force = false)
	{
		// if were not a subscription and not force deleting
		if (!$this->isSubscription() && !$force)
		{
			return false;
		}

		// delete events
		$sql = "DELETE FROM `#__events` WHERE `calendar_id`=" . $this->_db->quote($this->id);
		$this->_db->setQuery($sql);
		$this->_db->query();

		// all good
		return true;
	}
}
