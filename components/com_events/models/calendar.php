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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// include tables
require_once JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'tables' . DS . 'calendar.php';

//include icalendar file reader
require_once JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'calendar' . DS . 'ical.reader.php';

class EventsModelCalendar extends \Hubzero\Base\Model
{
	/**
	 * JTable
	 * 
	 * @var string
	 */
	protected $_tbl = null;
	
	/**
	 * Table name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'EventsCalendar';
	
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
	public function __construct( $oid = null )
	{
		// create needed objects
		$this->_db = JFactory::getDBO();
		
		// load page jtable
		$this->_tbl = new $this->_tbl_name($this->_db);
		
		// load object 
		if (is_numeric($oid))
		{
			$this->_tbl->load( $oid );
		}
		else if(is_object($oid) || is_array($oid))
		{
			$this->bind( $oid );
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
	public function events( $rtrn = 'list', $filters = array(), $clear = false )
	{
		switch (strtolower($rtrn))
		{
			case 'count':
				if (!$this->_events_count || $clear)
				{
					$tbl = new EventsEvent($this->_db);
					$this->_events_count = $tbl->count( $filters );
				}
				return $this->_events_count;
				break;
			case 'repeating':
				if (!($this->_events_repeating instanceof \Hubzero\Base\Model\ItemList) || $clear)
				{
					// var to hold repeating data
					$repeats = array();

					// add repeating filters
					$filters['repeating'] = true;

					// capture publish up/down 
					// remove for now as we want all events that have a repeating rule
					$start = JFactory::getDate($filters['publish_up']);
					$end   = JFactory::getDate($filters['publish_down']);
					unset($filters['publish_up']);
					unset($filters['publish_down']);
					
					// find any events that match our filters
					$tbl = new EventsEvent($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{	
							// get repeating occurrences
							// wrap in try/catch to prevent 500 error
							$r = new When\When(); 
							try
							{
								$r->startDate(JFactory::getDate($result->publish_up))
								  ->until($end)
								  ->rrule($result->repeating_rule)
								  ->generateOccurrences();
							}
							catch (Exception $e)
							{}

							// calculate diff so we can create down
							$diff = new DateInterval('P0Y0DT0H0M');
							if ($result->publish_down != '0000-00-00 00:00:00')
							{
								$diff = date_diff(JFactory::getDate($result->publish_up), JFactory::getDate($result->publish_down));
							}

							// create new event for each reoccurrence
							foreach ($r->occurrences as $occurrence)
							{
								// dont include the original event
								if ($occurrence == JFactory::getDate($result->publish_up))
								{
									continue;
								}
								
								$event               = clone($result);
								$event->publish_up   = $occurrence->format('Y-m-d H:i:s');
								$event->publish_down = $occurrence->add($diff)->format('Y-m-d H:i:s');
								$repeats[]           = new EventsModelEvent($event);
							}
						}
					}
					$this->_events_repeating = new \Hubzero\Base\Model\ItemList($repeats);
				}
				return $this->_events_repeating;
				break;
			case 'list':
			default:
				if (!($this->_events instanceof \Hubzero\Base\Model\ItemList) || $clear)
				{
					$tbl = new EventsEvent($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new EventsModelEvent($result);
						}
					}
					$this->_events = new \Hubzero\Base\Model\ItemList($results);
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
		$params = \Hubzero\Plugin\Plugin::getParams('calendar','groups');
		$interval = $params->get('import_subscription_interval', 60);
		
		// get datetimes needed to refresh
		$now             = JFactory::getDate();
		$lastRefreshed   = JFactory::getDate($this->get('last_fetched_attempt'));
		$refreshInterval = new DateInterval("PT{$interval}M");

		// add refresh interval to last refreshed
		$lastRefreshed->add($refreshInterval);
		
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
			$this->set('failed_attempts', $this->failed_attempts + 1);
			$this->set('last_fetched_attempt', JFactory::getDate()->toSql());
			$this->store(true);
			$this->setError($this->get('title'));
			return false;
		}
		
		//read calendar file
		$iCalReader = new iCalReader( $calendarUrl );
		$incomingEvents = $iCalReader->events();

		// check to make sure we have events
		if (count($incomingEvents) < 1)
		{
			$this->setError($this->get('title'));
			return false;
		}

		//make uid keys for array
		//makes it easier to diff later on
		foreach($incomingEvents as $k => $incomingEvent)
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
		foreach($incomingEvents as $uid => $incomingEvent)
		{
			// fetch event from our current events by uid
			$event = $currentEvents->fetch('ical_uid', $uid);
			
			// create blank event if we dont have one
			if (!$event)
			{
				$event = new EventsModelEvent();
			}

			// make sure we handle all day events from Google
			if (strlen($incomingEvent['DTSTART']) == 8)
			{
				$incomingEvent['DTSTART'] .= 'T05000Z';
			}
			if (strlen($incomingEvent['DTEND']) == 8)
			{
				$incomingEvent['DTEND'] .= 'T050000Z';
			}

			//get the start and end dates and parse to unix timestamp
			$start = JFactory::getDate($incomingEvent['DTSTART']);
			$end   = JFactory::getDate($incomingEvent['DTEND']);
			
			// set the timezone
			$tz = new DateTimezone(JFactory::getConfig()->get('offset'));
			$start->setTimezone($tz);
			$end->setTimezone($tz);

			// set publish up/down
			$publish_up   = $start->toSql();
			$publish_down = $end->toSql();

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
			$event->set('extra_info', isset($incomingEvent['URL;VALUE=URI']) ? $incomingEvent['URL;VALUE=URI'] : '');
			$event->set('modified', JFactory::getDate()->toSql());
			$event->set('modified_by', JFactory::getUser()->get('id'));
			$event->set('publish_up', $publish_up);
			$event->set('publish_down', $publish_down);

			// new event
			if (!$event->get('id'))
			{
				$event->set('catid', -1);
				$event->set('calendar_id', $this->get('id'));
				$event->set('ical_uid', isset($incomingEvent['UID']) ? $incomingEvent['UID'] : '');
				$event->set('scope', $this->get('scope'));
				$event->set('scope_id', $this->get('scope_id'));
				$event->set('state', 1);
				$event->set('created', JFactory::getDate()->toSql());
				$event->set('created_by', JFactory::getUser()->get('id'));
				$event->set('time_zone', -5);
				$event->set('registerby', '0000-00-00 00:00:00');
				$event->set('params', '');
			}

			// save event
			$event->store(true);
		}
		
		// mark as fetched
		// clear failed attempts
		$this->set('last_fetched', JFactory::getDate()->toSql());
		$this->set('last_fetched_attempt', JFactory::getDate()->toSql());
		$this->set('failed_attempts', 0);
		$this->store(true);
		return true;
	}

	/**
	 * Delete Calendar
	 * 
	 * @return [type] [description]
	 */
	public function delete()
	{
		// if subscription delete events
		if ($this->isSubscription())
		{
			// delete events
			$sql = "DELETE FROM `#__events` WHERE `calendar_id`=" . $this->_db->quote($this->get('id'));
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