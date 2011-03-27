<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

class modMyTickets
{
	private $attributes = array();

	//-----------

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------

	private function _convertTime($stime)
	{
		// Convert YYYY-MM-DD HH:MM:SS time to unix time stamp
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------
	
	private function _calculateTime($timestamp)
	{
		// Store the current time
		$current_time = time();
		
		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;
		
		// Set the periods of time
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		
		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
		
		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);
		
		// Ensure the script has found a match
		if ($val < 0) $val = 0;
		
		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);
		
		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1) $periods[$val] .= "s";
		
		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);
		
		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)){
			$text .= $this->_calculateTime($new_time);
		}
		
		return $text;
	}
	
	//-----------
	
	public function timeAgo($timestamp) 
	{
		// Convert YYYY-MM-DD HH:MM:SS time to unix time stamp
		$timestamp = $this->_convertTime($timestamp);
		// Find out how long ago that was as a human readable string
		$text = $this->_calculateTime($timestamp);
		
		// Return only the first portions of the string
		// e.g. return '2 months' rather than '2 months, 3 weeks, 5 days, 4 hours, 2 minutes, 12 seconds'
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		return $text;
	}

	//-----------
	
	public function display() 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$params =& $this->params;
		$this->moduleclass = $params->get( 'moduleclass' );
		$limit = intval( $params->get( 'limit' ) );
		$limit = ($limit) ? $limit : 10;
		
		// Check for the existence of required tables that should be
		// installed with the com_support component
		$database->setQuery("SHOW TABLES");
		$tables = $database->loadResultArray();
		
		if ($tables && array_search($database->_table_prefix.'support_tickets', $tables)===false) {
			// Support tickets table not found!
			echo 'Required database table not found.';
			return false;
	    }

		// Find the user's most recent support tickets
		$database->setQuery( "SELECT id, summary, category, status, severity, owner, created, login, name, "
			. " (SELECT COUNT(*) FROM #__support_comments as sc WHERE sc.ticket=st.id AND sc.access=0) as comments"
			. " FROM #__support_tickets as st WHERE st.login='".$juser->get('username')."' AND (st.status=0 OR st.status=1) AND type=0"
			. " ORDER BY created DESC"
			. " LIMIT ".$limit
			);
		$this->rows1 = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		// Find the user's most recent support tickets
		$database->setQuery( "SELECT id, summary, category, status, severity, owner, created, login, name, "
			. " (SELECT COUNT(*) FROM #__support_comments as sc WHERE sc.ticket=st.id AND sc.access=0) as comments"
			. " FROM #__support_tickets as st WHERE st.owner='".$juser->get('username')."' AND (st.status=0 OR st.status=1) AND type=0"
			. " ORDER BY created DESC"
			. " LIMIT ".$limit
			);
		$this->rows2 = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		ximport('Hubzero_User_Helper');
		$xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'members');
		$groups = '';
		if ($xgroups) {
			$g = array();
			foreach ($xgroups as $xgroup) 
			{
				$g[] = $xgroup->cn;
			}
			$groups = implode("','",$g);
		}
		
		if ($groups) {
			// Find support tickets on the user's contributions
			$database->setQuery( "SELECT id, summary, category, status, severity, owner, created, login, name, 
				 (SELECT COUNT(*) FROM #__support_comments as sc WHERE sc.ticket=st.id AND sc.access=0) as comments
				 FROM #__support_tickets as st WHERE (st.status=0 OR st.status=1) AND type=0 AND st.group IN ('$groups')
				 ORDER BY created DESC
				 LIMIT $limit"
				);
			$this->rows3 = $database->loadObjectList();
			if ($database->getErrorNum()) {
				echo $database->stderr();
				return false;
			}
		} else {
			$this->rows3 = null;
		}
		
		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet('mod_mytickets');
	}
}
