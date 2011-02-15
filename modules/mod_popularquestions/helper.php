<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class modPopularQuestions
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

	public function mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------
	
	public function timeAgoo($timestamp)
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
		if ($number != 1) $periods[$val].= "s";
		
		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);
		
		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)){
			$text .= $this->timeAgoo($new_time);
		}
		
		return $text;
	}
	
	//-----------
	
	public function timeAgo($timestamp) 
	{
		$text = $this->timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}
	
	//-----------

	public function display()
	{
		$database =& JFactory::getDBO();
		
		$params =& $this->params;
		$state = $params->get( 'state' );
		$limit = intval( $params->get( 'limit' ) );
		
		switch ($state) 
		{
			case 'open': $st = "a.state=0"; break;
			case 'closed': $st = "a.state=1"; break;
			case 'both':
			default: $st = ""; break;
		}
		
		$tag = JRequest::getVar( 'tag', '', 'get' );
		$modpopularquestions->style = JRequest::getVar( 'style', '', 'get' );
		
		if ($tag) {
			$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous "
				."\n FROM #__answers_questions AS a, #__tags_object AS t, #__tags AS tg, #__answers_responses AS r"
				."\n WHERE r.qid=a.id AND a.id=t.objectid AND tg.id=t.tagid AND t.tbl='answers' AND (tg.tag='".$tag."' OR tg.raw_tag='".$tag."' OR tg.alias='".$tag."')";
		} else {
			$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous "
				."\n FROM #__answers_questions AS a, #__answers_responses AS r"
				."\n WHERE r.qid=a.id";
		}
		if ($st) {
			$query .= " AND ".$st;
		}
		$query .= "\n GROUP BY id ORDER BY helpful DESC";
		$query .= ($limit) ? "\n LIMIT ".$limit : "";
		
		$database->setQuery( $query );
		$this->rows = $database->loadObjectList();
		
		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet('mod_popularquestions');
	}
}