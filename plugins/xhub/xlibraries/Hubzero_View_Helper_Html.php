<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

class Hubzero_View_Helper_Html 
{
	public function error($msg, $tag='p')
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------
	
	public function warning($msg, $tag='p')
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'."\n";
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>';
		$html .= ($txt != '') ? "\n".$txt."\n" : '';
		$html .= '</div><!-- / ';
		if ($id) 
		{
			$html .= '#'.$id;
		}
		if ($cls) 
		{
			$html .= '.'.$cls;
		}
		$html .= ' -->'."\n";
		return $html;
	}

	//-----------

	public function hed( $level, $words, $class='' ) 
	{
		$html  = '<h'.$level;
		$html .= ($class) ? ' class="'.$class.'"' : '';
		$html .= '>'.$words.'</h'.$level.'>'."\n";
		return $html;
	}
	
	//-----------
	
	public function xhtml( $text ) 
	{
		$text = stripslashes($text);
		$text = strip_tags($text);
		$text = str_replace('&amp;', '&', $text);
		$text = str_replace('&', '&amp;', $text);
		$text = str_replace('&amp;quot;', '&quot;', $text);
		$text = str_replace('&amp;lt;', '&lt;', $text);
		$text = str_replace('&amp;gt;', '&gt;', $text);
		
		return $text;
	}
	
	//-----------

	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) 
		{
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' &#8230;';
		}
		if ($text == '') 
		{
			$text = '&#8230;';
		}
		if ($p) 
		{
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}
	
	//-----------

	public function purifyText( &$text ) 
	{
		$text = stripslashes($text);
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = str_replace("\n",' ',$text);
		$text = str_replace("\r",' ',$text);
		$text = str_replace("\t",' ',$text);
		$text = strip_tags( $text );
		return $text;
	}
	
	//-----------
	
	public function str_highlight($text, $needles)
	{
		$highlight = '<span class="highlight">\1</span>';
	
		$pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#i';
		$sl_pattern = '#<a\s(?:.*?)>(%s)</a>#i';

		foreach ($needles as $needle) 
		{
			$needle = preg_quote($needle);
			$regex = sprintf($pattern, $needle);
			$text = preg_replace($regex, $highlight, $text);
		}
		return $text;
	}
	
	//-----------
	
	public function ampReplace( $text )
	{
		$text = str_replace( '&&', '*--*', $text );
		$text = str_replace( '&#', '*-*', $text );
		$text = str_replace( '&amp;', '&', $text );
		$text = preg_replace( '|&(?![\w]+;)|', '&amp;', $text );
		$text = str_replace( '*-*', '&#', $text );
		$text = str_replace( '*--*', '&&', $text );

		return $text;
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
			$text .= Hubzero_View_Helper_Html::timeAgoo($new_time);
		}

		return $text;
	}

	//-----------

	public function timeAgo($timestamp) 
	{
		$text = Hubzero_View_Helper_Html::timeAgoo($timestamp);

		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
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
}