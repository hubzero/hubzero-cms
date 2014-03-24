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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2) 
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for event pages
 */
class EventsCalendar extends JTable
{
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $id             = NULL;
	
	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $scope          = NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $scope_id       = NULL;
	
	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $title          = NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $url            = NULL;
	
	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $color          = NULL;
	
	/**
	 * int(11)
	 * 
	 * @var string
	 */
	var $published      = NULL;
	
	/**
	 * tinyint
	 * 
	 * @var string
	 */
	var $readonly       = NULL;
	
	/**
	 * datetime
	 * 
	 * @var string
	 */
	var $last_fetched   = NULL;
	
	/**
	 * datetime
	 * 
	 * @var string
	 */
	var $last_fetched_attempt = NULL;
	
	/**
	 * datetime
	 * 
	 * @var string
	 */
	var $failed_attempts     = NULL;
	
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__events_calendars', 'id', $db);
	}
	
	
	/**
	 * Get Calendar Objects
	 *
	 * @param      object    $group     Group Object
	 * @param      int       $id        Calendar ID
	 * @return     array
	 */
	public function getCalendars( $group, $id = null, $readonly = null )
	{
		$sql = "SELECT * FROM {$this->_tbl} 
				WHERE scope=" . $this->_db->quote('group') . "
				AND scope_id=" . $this->_db->quote( $group->get('gidNumber') );
		
		if (isset($id) && $id != '' && $id != null)
		{
			$sql .= " AND id=" . $this->_db->quote( $id );
		}
		
		if ($readonly !== null)
		{
			$sql .= " AND readonly=" . $this->_db->quote( $readonly );
		}
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	
	/**
	 * Check Method for saving
	 */
	public function check()
	{
		if (!isset($this->title) || $this->title == '')
		{
			$this->setError(JText::_('Calendar must have a title.'));
			return false;
		}
		return true;
	}
	
	/**
	 * Refresh all Group Calendars
	 *
	 */
	public function refreshAll( $group )
	{
		//get refresh interval
		ximport('Hubzero_Plugin');
		$params = Hubzero_Plugin::getParams('calendar','groups');
		$refreshInterval = $params->get('import_subscription_interval', 60);
		
		//get all group calendars
		$calendars = $this->getCalendars( $group );
		
		//loop through each calendar to see if we need to refresh it
		foreach ($calendars as $calendar)
		{
			//if we dont have a url or its not valid move on
			if ($calendar->url == '' || !filter_var($calendar->url, FILTER_VALIDATE_URL))
			{
				continue;
			}
			
			//build our refresh after date
			$now           = time();
			$lastRefreshed = strtotime($calendar->last_fetched_attempt);
			$needToRefresh = strtotime('+'.$refreshInterval.' MINUTES', $lastRefreshed);
			
			//is it time to refresh?
			if ($now >= $needToRefresh)
			{
				$this->refresh( $group, $calendar->id );
			}
		}
	}
	
	/**
	 * Refresh a Specific Group Calendar
	 */
	public function refresh( $group, $calendarId )
	{
		//get user object
		$juser = JFactory::getUser();
		
		//load calendar
		$this->load( $calendarId );
		
		//get current events
		$sql = "SELECT *
		        FROM `#__events` 
		        WHERE `calendar_id`=".$this->_db->quote( $this->id )."
		        AND `scope`=".$this->_db->quote( 'group' )." 
		        AND `scope_id`=".$this->_db->quote( $group->get('gidNumber') );
		$this->_db->setQuery( $sql );
		$currentEvents = $this->_db->loadObjectList( 'ical_uid' );
		
		//include icalendar file reader
		require_once JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'calendar' . DS . 'ical.reader.php';
		
		//build calendar url
		$calendarUrl = str_replace('webcal', 'http', $this->url);
		
		//test to see if this calendar is valid
		$calendarHeaders = get_headers($calendarUrl, 1);
		$statusCode      = (isset($calendarHeaders[0])) ? $calendarHeaders[0] : '';
		
		//check to make sure we have a 200 or 404 otherwise continue
		if (!stristr($statusCode, '404 Not Found') && !stristr($statusCode, '200 Ok'))
		{
			return false;
		}
		
		//make sure the calendar url is valid
		if (!strstr($calendarHeaders[0], '200 OK'))
		{
			$this->failed_attempts      = $this->failed_attempts + 1;
			$this->last_fetched_attempt = JFactory::getDate()->toSql();
			$this->save( $this );
			$this->setError( $this->title );
			return false;
		}
		
		//read calendar file
		$iCalReader = new iCalReader( $calendarUrl );
		$incomingEvents = $iCalReader->events();

		// check to make sure we have events
		if (count($incomingEvents) < 1)
		{
			$this->setError( $this->title );
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
		$eventsToDelete = array_diff(array_keys($currentEvents), array_keys($incomingEvents));
		
		//delete each event we dont have in the incoming events
		foreach ($eventsToDelete as $eventDelete)
		{
			$e = $currentEvents[$eventDelete];
			$eventsEvent = new EventsEvent( $this->_db );
			$eventsEvent->delete( $e->id );
		}
		
		//create new events for each event we pull
		foreach($incomingEvents as $uid => $incomingEvent)
		{
			//get the current event if we have one
			$currentEvent   = (isset($currentEvents[$uid])) ? $currentEvents[$uid] : new stdClass;
			$currentEventId = (isset($currentEvent->id)) ? $currentEvent->id : null;
			
			//create event object
			$eventsEvent = new EventsEvent( $this->_db );
			
			//if we have event ide load event
			if ($currentEventId != null)
			{
				$eventsEvent->load( $currentEventId );
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

			//set event vars
			$eventsEvent->title        = (isset($incomingEvent['SUMMARY'])) ? $incomingEvent['SUMMARY'] : '';
			$eventsEvent->content      = (isset($incomingEvent['DESCRIPTION'])) ? $incomingEvent['DESCRIPTION'] : '';
			$eventsEvent->content      = stripslashes(str_replace('\n', "\n", $eventsEvent->content));
			$eventsEvent->adresse_info = (isset($incomingEvent['LOCATION'])) ? $incomingEvent['LOCATION'] : '';
			$eventsEvent->extra_info   = (isset($incomingEvent['URL;VALUE=URI'])) ? $incomingEvent['URL;VALUE=URI'] : '';
			$eventsEvent->modified     = JFactory::getDate()->toSql();
			$eventsEvent->modified_by  = $juser->get('id');
			$eventsEvent->publish_up   = $publish_up;
			$eventsEvent->publish_down = $publish_down;
			
			//this a new event
			if ($currentEventId == null)
			{
				$eventsEvent->catid        = -1;
				$eventsEvent->calendar_id  = $this->id;
				$eventsEvent->ical_uid     = (isset($incomingEvent['UID'])) ? $incomingEvent['UID'] : '';
				$eventsEvent->scope        = 'group';
				$eventsEvent->scope_id     = $group->get('gidNumber');
				$eventsEvent->state        = 1;
				$eventsEvent->created      = JFactory::getDate()->toSql();
				$eventsEvent->created_by   = $juser->get('id');
				$eventsEvent->time_zone    = -5;
				$eventsEvent->registerby   = '0000-00-00 00:00:00';
				$eventsEvent->params       = '';
			}
			
			//save event
			$eventsEvent->save($eventsEvent);
		}
		
		//mark as fetched
		$this->last_fetched         = JFactory::getDate()->toSql();
		$this->last_fetched_attempt = JFactory::getDate()->toSql();
		$this->failed_attempts      = 0;
		$this->save( $this );
		return true;
	}
}