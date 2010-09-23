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

class modFeaturedquestion
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

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}

	//-----------

	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' &#8230;';
		}

		if ($text == '') {
			$text = '&#8230;';
		}

		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	//-----------

	public function encode_html($str, $quotes=1)
	{
		$str = $this->ampersands($str);

		$a = array(
			//'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);
		if ($quotes) $a = $a + array(
			"'" => '&#39;',
			'"' => '&#34;',
		);

		return strtr($str, $a);
	}

	//-----------

	public function ampersands( $str ) 
	{
		$str = stripslashes($str);
		$str = str_replace('&#','*-*', $str);
		$str = str_replace('&amp;','&',$str);
		$str = str_replace('&','&amp;',$str);
		$str = str_replace('*-*','&#', $str);
		return $str;
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
		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');

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
		if ($number != 1) $periods[$val].= 's';

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)) {
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
		require_once( JPATH_ROOT.DS.'components'.DS.'com_features'.DS.'tables'.DS.'history.php' );
		
		$this->error = false;
		if (!class_exists('FeaturesHistory')) {
			$this->error = true;
			return false;
		}
		
		$database =& JFactory::getDBO();

		$params =& $this->params;
		
		$filters = array();
		$filters['limit'] = 1;
		
		$this->cls = trim($params->get( 'moduleclass_sfx' ));
		$this->txt_length = trim($params->get( 'txt_length' ));
		
		$start = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 00:00:00";
		$end = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 23:59:59";
		
		$row = null;
		
		$fh = new FeaturesHistory( $database );

		// Load some needed libraries
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'question.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'response.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'log.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'questionslog.php' );
		
		// Check the feature history for today's feature
		$fh->loadActive($start, 'answers');
		
		// Did we find a feature for today?
		if ($fh->id && $fh->tbl == 'answers') {
			// Yes - load the member profile
			$row = new AnswersQuestion( $database );
			$row->load( $fh->objectid );
			
			$ar = new AnswersResponse( $database );
			$row->rcount = count($ar->getIds( $row->id ));
		} else {
			// No - so we need to randomly choose one
			$filters['start'] = 0;
			$filters['sortby'] = 'random';
			$filters['tag'] = '';
			$filters['filterby'] = 'open';
			$filters['created_before'] = date('Y-m-d', mktime(0,0,0,date('m'),(date('d')+7), date('Y')))." 00:00:00";
			
			$mp = new AnswersQuestion( $database );

			$rows = $mp->getResults( $filters );
			if (count($rows) > 0) {
				$row = $rows[0];
			}
		}

		// Did we have a result to display?
		if ($row) {
			$this->row = $row;
			
			$config =& JComponentHelper::getParams( 'com_answers' );
				
			// Check if this has been saved in the feature history
			if (!$fh->id) {
				$fh->featured = $start;
				$fh->objectid = $row->id;
				$fh->tbl = 'answers';
				$fh->store();
			}
			
			$this->thumb = trim($params->get( 'defaultpic' ));
		} else {
			$this->row = null;
		}
	}
}
