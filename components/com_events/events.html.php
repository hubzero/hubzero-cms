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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("r","\r");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class EventsHtml
{
	public function encode_html($str, $quotes=1)
	{
		$a = array(
			'&' => '&#38;'
			//'<' => '&#60;',
			//'>' => '&#62;',
		);
		if ($quotes) $a = $a + array(
			"'" => '&#39;',
			'"' => '&#34;',
		);

		return strtr($str, $a);
	}
	
	//-----------
	
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------
	
	public function alert( $msg )
	{
		return "<script> alert('".$msg."'); window.history.go(-1); </script>";
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>';
		$html .= ($txt != '') ? n.$txt.n : '';
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
		return $html;
	}
	
	//-----------
	
	public function hed( $level=2, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>'.n;
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
			$text = $text.' ...';
		}
		if ($text == '') {
			$text = '...';
		}
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	//-----------

	public function starter($option, $authorized, $year, $month, $day) 
	{
		$title = JText::_('EVENTS');
		$title .= ($year) ? ': '.$year : '';
		$title .= ($month) ? '/'.$month : '';
		$title .= ($day) ? '/'.$day : '';
		
		if ($authorized) {
			$html  = EventsHtml::div( EventsHtml::hed( 2, $title ), '', 'content-header').n;
			$html .= '<div id="content-header-extra">'.n;
			$html .= t.'<ul id="useroptions"><li class="last"><a href="'.JRoute::_('index.php?option='.$option.a.'task=add').'">'.JText::_('ADD_EVENT').'</a></li></ul>'.n;
			$html .= '</div><!-- / #content-header-extra -->'.n;
		} else {
			$html  = EventsHtml::div( EventsHtml::hed( 2, $title ), 'full', 'content-header').n;
		}
		return $html;
	}
	
	//-----------

	public function categoryForm($category, $categories, $option, $year, $month, $day, $active='month') 
	{
		$base = 'index.php?option='.$option;
		
		switch ($active) 
		{
			case 'year':
				$sef = JRoute::_($base.a.'year='.$year);
				break;
			case 'month':
				$sef = JRoute::_($base.a.'year='.$year.a.'month='.$month);
				break;
			case 'week':
				$sef = JRoute::_($base.a.'year='.$year.a.'month='.$month.a.'day='.$day.a.'task=week');
				break;
			case 'day':
				$sef = JRoute::_($base.a.'year='.$year.a.'month='.$month.a.'day='.$day);
				break;
		}
		
		$html  = '<form action="'.$sef.'" method="get" id="event-categories">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<select name="category">'.n;
		$html .= t.t.t.'<option value="">'.JText::_('ALL_CATEGORIES').'</option>'.n;
		foreach ($categories as $id=>$title) 
		{
			$selected = ($category == $id) ? ' selected="selected"' : '';
			
			$html .= t.t.t.'<option value="'.$id.'"'.$selected.'>'.stripslashes($title).'</option>'.n;
		}
		$html .= t.t.'</select>'.n;
		$html .= t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.'</fieldset>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}

	//-----------

	public function subnav($option, $year, $month, $day, $active='month') 
	{		
		$base = 'index.php?option='.$option;
		
		$html  = '<ul>'.n;
		$html .= t.'<li';
		if ($active == 'year') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_($base.a.'year='.$year).'"><span>'.JText::_('EVENTS_CAL_LANG_REP_YEAR').'</span></a></li>'.n;
		$html .= t.'<li';
		if ($active == 'month') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_($base.a.'year='.$year.a.'month='.$month).'"><span>'.JText::_('EVENTS_CAL_LANG_REP_MONTH').'</span></a></li>'.n;
		$html .= t.'<li';
		if ($active == 'week') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_($base.a.'year='.$year.a.'month='.$month.a.'day='.$day.a.'task=week').'"><span>'.JText::_('EVENTS_CAL_LANG_REP_WEEK').'</span></a></li>'.n;
		$html .= t.'<li';
		if ($active == 'day') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_($base.a.'year='.$year.a.'month='.$month.a.'day='.$day).'"><span>'.JText::_('EVENTS_CAL_LANG_REP_DAY').'</span></a></li>'.n;
		$html .= '</ul>'.n;
		$html .= EventsHtml::div('', 'clear');
		
		return EventsHtml::div($html, '', 'sub-menu');
	}

	//-----------

	public function byYear($rows, $option, $year, $month, $day, $authorized, $fields, $category, $categories) 
	{
		if (count($rows) > 0) {
			$html  = '<ul class="events">'.n;
			foreach ($rows as $row) 
			{
				$event_up = new EventsDate( $row->publish_up );
				$event_up->day   = sprintf( "%02d", $event_up->day);
				$event_up->month = sprintf( "%02d", $event_up->month);
				$event_up->year  = sprintf( "%4d",  $event_up->year);
				
				$html .= EventsHtml::eventRow($row, $event_up, $option, $fields, $categories);
			}
			$html .= '</ul>'.n;
		} else {
			$html  = EventsHtml::warning( JText::_('EVENTS_CAL_LANG_NO_EVENTFOR').' <strong>'.$year.'</strong>' ).n;
		}

		$cal  = EventsHtml::categoryForm( $category, $categories, $option, $year, $month, $day, 'year' ).n;
		$cal .= EventsHtml::viewYear( $option, 'year', $year, 0, 0 );
		
		$out  = EventsHtml::starter( $option, $authorized, $year, 0, 0 );
		$out .= EventsHtml::subnav( $option, $year, $month, $day, 'year' ).n;
		$out .= EventsHtml::div( 
					EventsHtml::div($cal,'aside') .
					EventsHtml::div($html,'subject'), 
					'main section'
				);
		$out .= EventsHtml::div( '', 'clear' );
		
		return $out;
	}

	//-----------

	public function byMonth($rows, $option, $year, $month, $day, $offset, $authorized, $fields, $category, $categories)
	{
		$html = '';
		if (count($rows) > 0) {
			$html .= '<ul class="events">'.n;
			foreach ($rows as $row) 
			{
				$event_up = new EventsDate( $row->publish_up );
				$event_up->day   = sprintf( "%02d", $event_up->day);
				$event_up->month = sprintf( "%02d", $event_up->month);
				$event_up->year  = sprintf( "%4d",  $event_up->year);
				
				$html .= EventsHtml::eventRow($row, $event_up, $option, $fields, $categories);
			}
			$html .= '</ul>'.n;
		} else {
			$html .= EventsHtml::warning( JText::_('EVENTS_CAL_LANG_NO_EVENTFOR').' <strong>'.EventsHtml::getDateFormat($year,$month,'',3).'</strong>' ).n;
		}
		
		$cal  = EventsHtml::categoryForm( $category, $categories, $option, $year, $month, $day, 'month' ).n;
		$cal .= EventsHtml::viewYear( $option, 'month', $year, $month, 0 );
		$cal .= EventsHtml::div( EventsHtml::viewCalendar( 'month', $offset, $option, $year, $month, $day ), 'calendarwrap');
		
		$out  = EventsHtml::starter( $option, $authorized, $year, $month, 0 );
		$out .= EventsHtml::subnav( $option, $year, $month, $day, 'month' ).n;
		$out .= EventsHtml::div( 
					EventsHtml::div($cal,'aside') .
					EventsHtml::div($html,'subject'), 
					'main section'
				);
		$out .= EventsHtml::div( '', 'clear' );
		
		return $out;
	}

	//-----------

	public function byWeek($offset, $option, $year, $month, $day, $startdate, $enddate, $events, $authorized, $category, $categories) 
	{
		$html  = '<ul class="events">'.n;
		$html .= $events;
		$html .= '</ul>'.n;
		
		$cal  = EventsHtml::categoryForm( $category, $categories, $option, $year, $month, $day, 'week' ).n;
		$cal .= EventsHtml::viewYear( $option, 'week', $year, $month, $day );
		$cal .= EventsHtml::div( EventsHtml::viewCalendar( 'week', $offset, $option, $year, $month, $day ), 'calendarwrap');
		$cal .= EventsHtml::viewWeek( $option, 'week', $year, $month, $day, $startdate, $enddate );
		
		$out  = EventsHtml::starter( $option, $authorized, JText::_('Week of').' '.$year, $month, $day );
		$out .= EventsHtml::subnav( $option, $year, $month, $day, 'week' ).n;
		$out .= EventsHtml::div( 
					EventsHtml::div($cal,'aside') .
					EventsHtml::div($html,'subject'), 
					'main section'
				);
		$out .= EventsHtml::div( '', 'clear' );
		
		return $out;
	}

	//-----------

	public function forWeek($rows, $offset, $option, $week, $fields, $categories) 
	{
		if ($week['month'] == strftime( "%m", time()+($offset*60*60) ) 
			&& $week['year'] == strftime( "%Y", time()+($offset*60*60) )
			&& $week['day'] == strftime( "%d", time()+($offset*60*60) )) {
			$cls = ' class="today"';
		} else {
			$cls = '';
		}
		
		$countprint = 0;
		
		$html  = t.'<li'.$cls.'>'.n;
		$html .= t.t.'<dl class="event-details">'.n;
		//$html .= t.t.t.'<dt><a href="index.php?option='.$option.a.'year='.$week['year'].a.'month='.$week['month'].a.'day='.$week['day'].'">'.EventsHtml::getDateFormat($week['year'],$week['month'],$week['day'],2).'</a></dt>'.n;
		//$html .= t.t.t.'<dt>'.EventsHtml::getDateFormat($week['year'],$week['month'],$week['day'],1).'</dt>'.n;
		$html .= t.t.t.'<dt>'.JHTML::_('date',$week['year'].'-'.$week['month'].'-'.$week['day'].' 00:00:00', '%d %b, %Y').'</dt>'.n;
		$html .= t.t.'</dl>'.n;
		$html .= t.t.'<div class="ewrap">'.n;
		if (count($rows) > 0) {
			$e = array();
			foreach ($rows as $row) 
			{
				$event_up = new EventsDate( $row->publish_up );
				$event_up->day   = sprintf( "%02d", $event_up->day);
				$event_up->month = sprintf( "%02d", $event_up->month);
				$event_up->year  = sprintf( "%4d",  $event_up->year);
        
				$checkprint = new EventsRepeat($row, $week['year'], $week['month'], $week['day']);
				if ($checkprint->viewable == true) {
					$e[] = EventsHtml::eventRow($row, $event_up, $option, $fields, $categories, 0);
					$countprint++;
				}
			}
			if ($countprint == 0) {
				$html .= t.t.t.'<p>'.JText::_('EVENTS_CAL_LANG_NO_EVENTS').'</p>'.n;
			} else {
				$html .= t.t.t.'<ul class="events">'.n;
				$html .= implode("",$e);
				$html .= t.t.t.'</ul>'.n;
			}
			
    	} else {
			$html .= '<p>'.JText::_('EVENTS_CAL_LANG_NO_EVENTS').'</p>'.n;
		}
		$html .= t.t.'</div>'.n;
		$html .= t.'</li>'.n;
		
		return $html;
	}

	//-----------
	
	public function byDay($rows, $offset, $option, $year, $month, $day, $authorized, $fields, $categories) 
	{
		//$html  = EventsHtml::hed( 2, 'Events' );
		//$html .= '<p id="tagline"><a href="'.JRoute::_('index.php?option='.$option.a.'task=add').'">Add an events</a></p>'.n;
		//$html .= EventsHtml::subnav( $option, $year, $month, $day, 'day' ).n;
		if (count($rows) > 0) {
			$html  = '<ul class="events">'.n;
			$html .= t.'<li>'.n;
			$html .= t.t.'<dl class="event-details">'.n;
			//$html .= t.t.t.'<dt><a href="'.JRoute::_('index.php?option='.$option.a.'year='.$year.a.'month='.$month.a.'day='.$day).'">'.EventsHtml::getDateFormat($year,$month,$day,0).'</a></dt>'.n;
			$html .= t.t.t.'<dt>'.JHTML::_('date',$year.'-'.$month.'-'.$day.' 00:00:00', '%d %b, %Y').'</dt>'.n;
			$html .= t.t.'</dl>'.n;
			$html .= t.t.'<div class="ewrap">'.n;
			$html .= t.t.t.'<ul class="events">'.n;
			foreach ($rows as $row) 
			{
				$event_up = new EventsDate( $row->publish_up );
				$event_up->day   = sprintf( "%02d", $event_up->day);
				$event_up->month = sprintf( "%02d", $event_up->month);
				$event_up->year  = sprintf( "%4d",  $event_up->year);
				
				$html .= EventsHtml::eventRow($row, $event_up, $option, $fields, $categories, 0);
			}
			$html .= t.t.t.'</ul>'.n;
			$html .= t.t.'</div>'.n;
			$html .= t.'</li>'.n;
			$html .= '</ul>'.n;
		} else {
			$html  = EventsHtml::warning( JText::_('EVENTS_CAL_LANG_NO_EVENTFOR').' '.EventsHtml::getDateFormat($year,$month,$day,0) ).n;
		}

		$cal  = EventsHtml::viewYear( $option, 'day', $year, $month, $day );
		$cal .= EventsHtml::div( EventsHtml::viewCalendar( 'day', $offset, $option, $year, $month, $day ), 'calendarwrap');
		$cal .= EventsHtml::viewDay( $option, 'day', $year, $month, $day );
		
		$out  = EventsHtml::starter( $option, $authorized, $year, $month, $day );
		$out .= EventsHtml::subnav( $option, $year, $month, $day, 'day' ).n;
		$out .= EventsHtml::div( 
					EventsHtml::div($cal,'aside') .
					EventsHtml::div($html,'subject'), 
					'main section'
				);
		$out .= EventsHtml::div( '', 'clear' );
		
		return $out;
	}
	
	//-----------

	public function eventRow($row, $event_up, $option, $fields, $categories, $showdate=1)
	{		
		$row->content = stripslashes($row->content);
		$row->content = str_replace('<br />','',$row->content);
		//$row->content = EventsHtml::encode_html($row->content);
		
		//$fields = $config->getCfg('fields');
		if (!empty($fields)) {
			for ($i=0, $n=count( $fields ); $i < $n; $i++) 
			{
				// explore the text and pull out all matches
				array_push($fields[$i], EventsController::parseTag($row->content, $fields[$i][0]));
				// clean the original text of any matches
				$row->content = str_replace('<ef:'.$fields[$i][0].'>'.end($fields[$i]).'</ef:'.$fields[$i][0].'>','',$row->content);
			}
			$row->content = trim($row->content);
		}
		
		$event_up = new EventsDate( $row->publish_up );
		$row->start_date = EventsHtml::getDateFormat($event_up->year,$event_up->month,$event_up->day,0);
		$row->start_time = (defined('_CAL_USE_STD_TIME') && _CAL_USE_STD_TIME == 'YES') 
						 ? $event_up->get12hrTime() 
						 : $event_up->get24hrTime();
		
		$event_down = new EventsDate( $row->publish_down );
		$row->stop_date = EventsHtml::getDateFormat($event_down->year,$event_down->month,$event_down->day,0);
		$row->stop_time = (defined('_CAL_USE_STD_TIME') && _CAL_USE_STD_TIME == 'YES') 
						? $event_down->get12hrTime() 
						: $event_down->get24hrTime();
		
		$html  = t.'<li id="event'.$row->id.'">'.n;
		$html .= t.t.'<dl class="event-details">'.n;
		if ($row->start_date == $row->stop_date) {
			if ($showdate) {
				$html .= t.t.t.'<dt>'.JHTML::_('date',$row->publish_up, '%d %b, %Y').'</dt>'.n;
			}
			$html .= t.t.t.'<dd class="starttime">'.JHTML::_('date',$row->publish_up, '%I:%M %p').'</dd>'.n;
			$html .= t.t.t.'<dd class="endtime">'.strtolower(JText::_('EVENTS_CAL_LANG_TO')).' '.JHTML::_('date',$row->publish_down, '%I:%M %p').'</dd>'.n;
		} else {
			if ($showdate) {
				$html .= t.t.t.'<dt class="starttime">'.JHTML::_('date',$row->publish_up, '%d %b, %Y').'</dt>'.n;
			}
			$html .= t.t.t.'<dd class="starttime">'.JHTML::_('date',$row->publish_up, '%I:%M %p').'</dd>'.n;
			if ($showdate) {
				$html .= t.t.t.'<dt class="endtime">'.strtolower(JText::_('EVENTS_CAL_LANG_TO')).' '.JHTML::_('date',$row->publish_down, '%d %b, %Y').'</dt>'.n;
			}
			$html .= t.t.t.'<dd class="endtime">'.JHTML::_('date',$row->publish_down, '%I:%M %p').'</dd>'.n;
		}
		$html .= t.t.'</dl><div class="ewrap">'.n;
		$html .= t.t.'<p class="title"><a href="'. JRoute::_('index.php?option='.$option.a.'task=details'.a.'id='.$row->id) .'">'. stripslashes($row->title) .'</a></p>'.n;
		$html .= t.t.'<p class="category"><strong>Category:</strong> '. stripslashes($categories[$row->catid]) .'</p>'.n;
		$info = '';
		foreach ($fields as $field) 
		{
			if ($field[4] == 1 && end($field) != '') {
				$info .= t.t.'<p class="'.$field[0].'"><strong>'. $field[1] .':</strong> '. end($field) .'</p>'.n;
			}
		}
		$html .= $info;
		if (!$info) {
			$html .= t.t.'<p class="description">'. EventsHtml::shortenText($row->content,300,0) .'</p>'.n;
		}
		$html .= t.'</div></li>'.n;
		
		return $html;
	}

	//-----------

	public function autolink($matches) 
	{
		$href = $matches[0];

		if (substr($href, 0, 1) == '!') {
			return substr($href, 1);
		}
		
		$href = str_replace('"','',$href);
		$href = str_replace("'",'',$href);
		$href = str_replace('&#8221','',$href);
		
		$h = array('h','m','f','g','n');
		if (!in_array(substr($href,0,1), $h)) {
			$href = substr($href, 1);
		}
		$name = trim($href);
		if (substr($name, 0, 7) == 'mailto:') {
			$name = substr($name, 7, strlen($name));
			$name = Eventshtml::obfuscate($name);
			//$href = Eventshtml::obfuscate($href);
			$href = 'mailto:'.$name;
		}
		$l = sprintf(
			' <a class="ext-link" href="%s"%s>%s</a>',
			$href,
			' rel="external"',
			$name
		);
		return $l;
	}
	
	//-----------
	
	public function obfuscate( $email )
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++) 
		{
			$obfuscatedEmail .= '&#'. ord($email[$i]) .';';
		}
		
		return $obfuscatedEmail;
	}
	
	//-----------

	public function details($row, $offset, $option, $year, $month, $day, $authorized, $fields, $config, $categories, $tags, $auth) 
	{
		$juser =& JFactory::getUser();
		
		$html = '';
		if ($row) {
			//ximport('xuser');
			//$xuser = XUser::getInstance( $row->created_by );
			$xuser =& JUser::getInstance( $row->created_by );
			
			if (is_object($xuser)) {
				$name = $xuser->get('name');
			} else {
				$name = JText::_('EVENTS_CAL_LANG_UNKOWN');
			}
			
			//$row->content = stripslashes($row->content);
			$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
			$row->content = preg_replace_callback("/$UrlPtrn/", array('EventsHtml','autolink'), $row->content);
			
			$html .= '<table id="event-info" summary="'.JText::_('EVENTS_DETAILS_TABLE_SUMMARY').'">'.n;
			$html .= ' <thead>'.n;
			$html .= '  <tr>'.n;
			$html .= '   <th colspan="2">'. stripslashes($row->title);
			if ($authorized || $row->created_by == $juser->get('id')) {
				$html .= '&nbsp;&nbsp;';
				$html .= '<a href="'. JRoute::_('index.php?option='.$option.a.'task=edit'.a.'id='.$row->id) .'" title="'.JText::_('EDIT').'">[ '.strtolower(JText::_('Edit')).' ]</a>'.n;
				$html .= '&nbsp;&nbsp;'.n;
				$html .= '<a href="'. JRoute::_('index.php?option='.$option.a.'task=delete'.a.'id='.$row->id) .'" title="'.JText::_('DELETE').'">[ '.strtolower(JText::_('Delete')).' ]</a>'.n;
			}
			$html .= '</th>'.n;
			$html .= '  </tr>'.n;
			$html .= ' </thead>'.n;
			$html .= ' <tbody>'.n;
			$html .= '  <tr>'.n;
			$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY').':</th>'.n;
			$html .= '   <td>'. stripslashes($categories[$row->catid]) .'</td>'.n;
			$html .= '  </tr>'.n;
			$html .= '  <tr>'.n;
			$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_DESCRIPTION').':</th>'.n;
			$html .= '   <td>'. $row->content .'</td>'.n;
			$html .= '  </tr>'.n;
			$html .= '  <tr>'.n;
			$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_WHEN').':</th>'.n;
			$html .= '   <td>'.n;
			
			$ts = explode(':', $row->start_time);
			if (intval($ts[0]) > 12) {
				$ts[0] = ($ts[0] - 12);
				$row->start_time = implode(':',$ts);
				$row->start_time .= ' <small>PM</small>';
			} else {
				$row->start_time .= (intval($ts[0]) == 12) ? ' <small>Noon</small>' : ' <small>AM</small>';
			}
			$te = explode(':', $row->stop_time);
			if (intval($te[0]) > 12) {
				$te[0] = ($te[0] - 12);
				$row->stop_time = implode(':',$te);
				$row->stop_time .= ' <small>PM</small>';
			} else {
				$row->stop_time .= (intval($te[0]) == 12) ? ' <small>Noon</small>' : ' <small>AM</small>';
			}
			//if ($config->getCfg('repeatview') == 'YES') {
				if ($row->start_date == $row->stop_date) {
					$html .= $row->start_date .', '.$row->start_time.'&nbsp;-&nbsp;'.$row->stop_time.'<br />';
				} else {
					$html .= JText::_('EVENTS_CAL_LANG_FROM').' '.$row->start_date.'&nbsp;-&nbsp;'.$row->start_time.'<br />'.
						JText::_('EVENTS_CAL_LANG_TO').' '.$row->stop_date.'&nbsp;-&nbsp;'.$row->stop_time.'<br />';
				}
			/*} else {
				$html .= $row->start_date .', '.$row->start_time.'&nbsp;-&nbsp;'.$row->stop_time.'<br />';
			}*/
			$html .= '   </td>'.n;
			$html .= '  </tr>'.n;
			if (trim($row->contact_info)) {
				$html .= '  <tr>'.n;
				$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_CONTACT').':</th>'.n;
				$html .= '   <td>'. htmlentities($row->contact_info) .'</td>'.n;		
				$html .= '  </tr>'.n;
			}
			if (trim($row->adresse_info)) {
				$html .= '  <tr>'.n;
				$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_ADRESSE').':</th>'.n;
				$html .= '   <td>'. $row->adresse_info .'</td>'.n;
				$html .= '  </tr>'.n;
			}
			if (trim($row->extra_info)) {
				$html .= '  <tr>'.n;
				$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_EXTRA').':</th>'.n;
				$html .= '   <td><a href="'. htmlentities($row->extra_info) .'">'. htmlentities($row->extra_info) .'</a></td>'.n;		
				$html .= '  </tr>'.n;
			}
			foreach ($fields as $field) 
			{
				if (end($field) != NULL) {
					if (end($field) == '1') {
						$html .= '  <tr>'.n;
						$html .= '   <th scope="row">'.$field[1].':</th>'.n;
						$html .= '   <td>'.JText::_('YES').'</td>'.n;
						$html .= '  </tr>'.n;
					} else {
						$html .= '  <tr>'.n;
						$html .= '   <th scope="row">'.$field[1].':</th>'.n;
						$html .= '   <td>'.end($field).'</td>'.n;
						$html .= '  </tr>'.n;
					}
				}
			}

			if ($config->getCfg('byview') == 'YES') {
				$html .= '  <tr>'.n;
				$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_AUTHOR_ALIAS').':</th>'.n;
				$html .= '   <td>' . $name . '</td>'.n;
				$html .= '  </tr>'.n;
			}
			if ($tags) {
				$html .= '  <tr>'.n;
				$html .= '   <th scope="row">'.JText::_('EVENTS_CAL_LANG_EVENT_TAGS').':</th>'.n;
				$html .= '   <td>' . $tags . '</td>'.n;
				$html .= '  </tr>'.n;
			}
			$html .= ' </tbody>'.n;
			$html .= '</table>'.n;
		} else {
			$html .= EventsHtml::warning( JText::_('EVENTS_CAL_LANG_REP_NOEVENTSELECTED') ).n;
		}
		
		$cal  = EventsHtml::div( EventsHtml::viewCalendar( 'day', $offset, $option, $year, $month, $day, 0 ), 'calendarwrap');
		
		$out  = EventsHtml::starter( $option, $auth, $year, $month, $day );
		$out .= EventsHtml::subnav( $option, $year, $month, $day, '' ).n;
		$out .= EventsHtml::div( 
					EventsHtml::div($cal,'aside') .
					EventsHtml::div($html,'subject'), 
					'main section'
				);
		
		return $out;
	}

	//-----------
	
	public function viewYear($option, $task, $year, $month=0, $day=0) 
	{
		$this_date = new EventsDate();
		$this_date->setDate( $year, $month, $day );

		$prev_year = clone($this_date);
		$prev_year->addMonths( -12 );
		$next_year = clone($this_date);
		$next_year->addMonths( +12 );
		
		$prev = 'index.php?option='.$option.a. $prev_year->toDateURL($task);
		$next = 'index.php?option='.$option.a. $next_year->toDateURL($task);
		
		$html  = '<p class="datenav">'.n;
		$html .= t.'<a class="prv" href="'.JRoute::_($prev).'" title="'.JText::_('EVENTS_CAL_LANG_PREVIOUSYEAR').'">&lsaquo;</a> '.n;
		$html .= t.'<a class="nxt" href="'.JRoute::_($next).'" title="'.JText::_('EVENTS_CAL_LANG_NEXTYEAR').'">&rsaquo;</a> '.n;
		$html .= t.$year.n;
		$html .= '</p>'.n;
		
		return EventsHtml::div($html,'calendarwrap');
	}
	
	//-----------
	
	public function viewWeek($option, $task, $year, $month, $day, $startdate, $enddate) 
	{
		$this_date = new EventsDate();
		$this_date->setDate( $year, $month, $day );

		$prev_week = clone($this_date);
		$prev_week->addDays( -7 );
		$next_week = clone($this_date);
		$next_week->addDays( +7 );
		
		$prev = 'index.php?option='.$option.a. $prev_week->toDateURL($task);
		$next = 'index.php?option='.$option.a. $next_week->toDateURL($task);
		
		$html  = '<p class="datenav">'.n;
		$html .= t.'<a class="prv" href="'.JRoute::_($prev).'" title="'.JText::_('EVENTS_CAL_LANG_PREVIOUSWEEK').'">&lsaquo;</a> '.n;
		$html .= t.'<a class="nxt" href="'.JRoute::_($next).'" title="'.JText::_('EVENTS_CAL_LANG_NEXTWEEK').'">&rsaquo;</a> '.n;
		$html .= t.$startdate.' to '.$enddate.n;
		$html .= '</p>'.n;
		
		return EventsHtml::div($html,'calendarwrap');
	}

	//-----------
	
	public function viewDay($option, $task, $year, $month, $day) 
	{
		$this_date = new EventsDate();
		$this_date->setDate( $year, $month, $day );

		$prev_day = clone($this_date);
		$prev_day->addDays( -1 );
		$next_day = clone($this_date);
		$next_day->addDays( +1 );
		
		$prev = 'index.php?option='.$option.a. $prev_day->toDateURL($task);
		$next = 'index.php?option='.$option.a. $next_day->toDateURL($task);
		
		$t = mktime(0,0,0,$month,$day,$year);
		$d = date("l", $t);
		
		$html  = '<p class="datenav">'.n;
		$html .= t.'<a class="prv" href="'.JRoute::_($prev).'" title="'.JText::_('EVENTS_CAL_LANG_PREVIOUSDAY').'">&lsaquo;</a> '.n;
		$html .= t.'<a class="nxt" href="'.JRoute::_($next).'" title="'.JText::_('EVENTS_CAL_LANG_NEXTDAY').'">&rsaquo;</a> '.n;
		$html .= t.$d.n;
		$html .= '</p>'.n;
		
		return EventsHtml::div($html,'calendarwrap');
	}

	//-----------

	public function viewCalendar($task, $offset, $option, $year, $month, $day, $shownav=1)
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		$gid = $juser->get('gid');
		
		$startday = ((!_CAL_CONF_STARDAY) || (_CAL_CONF_STARDAY > 1)) ? 0 : _CAL_CONF_STARDAY;
		$timeWithOffset = time() + ($offset*60*60);

		$to_day = date("Y-m-d", $timeWithOffset);
		
		$day_name = array(JText::_('EVENTS_CAL_LANG_SUNDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_SATURDAYSHORT')
			);
		
		$time  = mktime(0, 0, 0, $month, 1, intval($year) );
		$ptime = mktime(0, 0, 0, ($month-1), 1, intval($year) );
		$ntime = mktime(0, 0, 0, ($month+1), 1, intval($year) );
		
		// This month
		$cal_year  = date("Y",$time);
		$cal_month = date("m",$time);
		$calmonth  = date("n",$time);
		
		$this_date = new EventsDate();
		$this_date->setDate( $year, $month, $day );

		$prev_month = clone($this_date);
		$prev_month->addMonths( -1 );
		$next_month = clone($this_date);
		$next_month->addMonths( +1 );
		$prev = JRoute::_( 'index.php?option='.$option.a. $prev_month->toDateURL($task) );
		$next = JRoute::_( 'index.php?option='.$option.a. $next_month->toDateURL($task) );
		
		$content  = '<table class="ecalendar" summary="'.JText::_('EVENTS_CAL_TABLE_SUMMARY').'">'.n;
		$content .= ' <caption>';
		if ($shownav) {
			$content .= '<a class="prv" href="'.$prev.'" title="'.JText::_('EVENTS_CAL_LANG_PREVIOUSMONTH').'">&lsaquo;</a> <a class="nxt" href="'.$next.'" title="'.JText::_('EVENTS_CAL_LANG_NEXTMONTH').'">&rsaquo;</a> ';
		}
		$content .= EventsHtml::getMonthName($cal_month).'</caption>'.n;
		$content .= ' <thead>'."\n";
	    $content .= '  <tr>'."\n";
		for ($i=0;$i<7;$i++) 
		{
			$content.='   <th scope="col">'.$day_name[($i+$startday)%7].'</th>'.n;
		}
		$content .= '  </tr>'.n;
		$content .= ' </thead>'.n;
		$content .= ' <tbody>'.n;
		$content .= '  <tr>'.n;

		// dmcd May 7/04 fix to fill in end days out of month correctly
		$dayOfWeek = $startday;
		$start = (date("w",mktime(0,0,0,$cal_month,1,$cal_year))-$startday+7)%7;
		$d = date("t",mktime(0,0,0,$cal_month,0,$cal_year))-$start + 1;
		$kownt = 0;

		for ($a=$start; $a>0; $a--) 
		{
			$content .= '   <td';
			if ($a == $start) {
				$content .= ' class="weekend"';
			}
			$content .= '>&nbsp;</td>'.n;
			$dayOfWeek++;
			$kownt++;
		}

		$monthHasEvent = false;
		$eventCheck = new EventsRepeat;
		$lastDayOfMonth = date("t",mktime(0,0,0,$cal_month,1,$cal_year));
		$rd = 0;
		for ($d=1;$d<=$lastDayOfMonth;$d++) 
		{ 
			$do = ($d<10) ? "0$d" : "$d";
			$selected_date = "$cal_year-$cal_month-$do";

			$sql = "SELECT #__events.* FROM #__events, #__categories as b"
				. "\n WHERE #__events.catid = b.id AND b.access <= $gid AND #__events.access <= $gid"
				. "\n AND ((publish_up >= '$selected_date 00:00:00' AND publish_up <= '$selected_date 23:59:59')"
				. "\n OR (publish_down >= '$selected_date 00:00:00' AND publish_down <= '$selected_date 23:59:59')"
				. "\n OR (publish_up <= '$selected_date 00:00:00' AND publish_down >= '$selected_date 23:59:59')) AND state='1'"
				. "\n ORDER BY publish_up ASC";

			$database->setQuery($sql);
			$rows = $database->loadObjectList();

			$class = ($selected_date == $to_day) ? 'today' : '';       
			if ($d == $day) {
				$class .= ' selected';
			}
			$hasevents = false;
			for ($r = 0; $r < count($rows); $r++) 
			{
				if ($eventCheck->EventsRepeat($rows[$r], $cal_year, $cal_month, $do)) {
					$hasevents = true;
					//$class = ($selected_date == $to_day) ? 'today' : 'withevents';
					break;
				}
			}
			if ((($dayOfWeek)%7 == $startday) || ((1 + $dayOfWeek)%7 == $startday)) {
				$class .= ' weekend';
			}
			// Only adds link if event scheduled that day
			$content .= '   <td';
			$content .= ($class) ? ' class="'.$class.'">' : '>';
			if ($hasevents) {
				$content .= '<a class="mod_events_daylink" href="'.JRoute::_('index.php?option='.$option.a.'year='.$cal_year.a.'month='.$cal_month.a.'day='.$do).'">'.$d.'</a>';
			} else {
				$content .= $d;
			}
	        $content .= '</td>'.n;
			$rd++;
			
			// Check if Next week row
			if ((1 + $dayOfWeek++)%7 == $startday) {
				$content .= '  </tr>'.n;
				$content .= '  <tr>'.n;
				$rd = ($rd >= 7) ? 0 : $rd;
			}
		}

		for ($d=$rd;$d<=6;$d++) 
		{
			$content .= '   <td';
			if ($d == 6) {
				$content .= ' class="weekend"';
			}
			$content .= '>&nbsp;</td>'.n;
		}

		$content .= '  </tr>'.n;
		$content .= ' </tbody>'.n;
		$content .= '</table>'.n;

	    return $content;
	}

	//-----------

	public function edit( $row, $fields, $times, $lists, $option, $gid, $task, $config, $err='' ) 
	{
		$editor =& JFactory::getEditor();
		?>
<div id="content-header" class="full">
    <h2><?php echo JText::_('EVENTS'); ?></h2>
</div>
<div class="main section">
	<form action="index.php" method="post" id="hubForm">
		<?php
		if ($err) {
			echo EventsHtml::error( $err );
		}
		?>
		<div class="explaination">
			<p><?php echo JText::_('EVENTS_CAL_LANG_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo ($row->id) ? JText::_('UPDATE_EVENT') : JText::_('NEW_EVENT');?></h3>
			
			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?>: <span class="required"><?php echo JText::_('EVENTS_CAL_LANG_REQUIRED'); ?></span>
				<?php echo EventsHtml::buildCategorySelect($row->catid, '', $gid, $option); ?>
			</label>
			<!-- <input type="hidden" name="catid" value="1" /> -->

			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_TITLE'); ?>: <span class="required"><?php echo JText::_('EVENTS_CAL_LANG_REQUIRED'); ?></span>
				<input type="text" name="title" maxlength="250" value="<?php echo htmlentities(stripslashes($row->title)); ?>" />
			</label>

			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?>: <span class="required"><?php echo JText::_('EVENTS_CAL_LANG_REQUIRED'); ?></span>
				<?php 
				//echo $editor->display('econtent', htmlentities(stripslashes($row->content)), '100%', '200px', '45', '10', false); 
				echo '<textarea name="econtent" id="econtent" style="width:100%;height:200px;" rows="45" cols="10">'.htmlentities(stripslashes($row->content)).'</textarea>';
				?>
			</label>

			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_ADRESSE'); ?>
				<input type="text" name="adresse_info" maxlength="120" value="<?php echo htmlentities(stripslashes($row->adresse_info)); ?>" />
			</label>

			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_EXTRA'); ?>
				<input type="text" name="extra_info" maxlength="240" value="<?php echo htmlentities(stripslashes($row->extra_info)); ?>" />
			</label>
<?php
		foreach($fields as $field) 
		{
			?>
			<label>
				<?php echo $field[1]; ?>: <?php echo ($field[3]) ? '<span class="required">required</span>' : ''; ?>
				<?php 
				if ($field[2] == 'checkbox') {
					echo '<input class="option" type="checkbox" name="fields['. $field[0] .']" value="1"';
					if (stripslashes(end($field)) == 1) {
						echo ' checked="checked"';
					}
					echo ' />';
				} else {
					echo '<input type="text" name="fields['. $field[0] .']" size="45" maxlength="255" value="'. stripslashes(end($field)) .'" />';
				}
				?>
			</label>
<?php 
		}
?>
			<label>
				<?php echo JText::_('EVENTS_E_TAGS'); ?>
<?php
			JPluginHelper::importPlugin( 'tageditor' );
			$dispatcher =& JDispatcher::getInstance();
			$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$lists['tags'],'')) );
			if (count($tf) > 0) {
				echo $tf[0];
			} else {
				echo t.t.t.'<input type="text" name="tags" value="'. $lists['tags'] .'" size="38" />'.n;
			}
?>
			</label>
			<fieldset>
				<legend><?php echo JText::_('EVENTS_CAL_LANG_EVENT_STARTDATE').' '.a.' '.JText::_('EVENTS_CAL_LANG_EVENT_STARTTIME'); ?></legend>
				<p>
					<?php //echo JHTML::_('calendar', $start_publish, 'publish_up', 'publish_up', '%Y-%m-%d', array('class'=>'option inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
                    <input class="option" type="text" name="publish_up" id="publish_up" size="10" maxlength="10" value="<?php echo $times['start_publish']; ?>" />
					<input class="option" type="text" name="start_time" id="start_time" size="5" maxlength="6" value="<?php echo $times['start_time']; ?>" />
					<?php if ($config->getCfg('calUseStdTime') =='YES') { ?>
					<input class="option" id="start_pm0" name="start_pm" type="radio"  value="0" <?php if (!$times['start_pm']) echo 'checked="checked"'; ?> /><small>AM</small>
					<input class="option" id="start_pm1" name="start_pm" type="radio"  value="1" <?php if ($times['start_pm']) echo 'checked="checked"'; ?> /><small>PM</small>
					<?php } ?>
				</p>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ENDDATE').' '.a.' '.JText::_('EVENTS_CAL_LANG_EVENT_ENDTIME'); ?></legend>
				<p>
					<?php //echo JHTML::_('calendar', $stop_publish, 'publish_down', 'publish_down', '%Y-%m-%d', array('class'=>'option inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
					<input class="option" type="text" name="publish_down" id="publish_down" size="10" maxlength="10" value="<?php echo $times['stop_publish']; ?>" />
					<input class="option" type="text" name="end_time" id="end_time" size="5" maxlength="6" value="<?php echo $times['end_time']; ?>" />
					<?php if ($config->getCfg('calUseStdTime') =='YES') { ?>
					<input class="option" id="end_pm0" name="end_pm" type="radio"  value="0" <?php if (!$times['end_pm']) echo 'checked="checked"'; ?> /><small>AM</small>
					<input class="option" id="end_pm1" name="end_pm" type="radio"  value="1" <?php if ($times['end_pm']) echo 'checked="checked"'; ?> /><small>PM</small>
					<?php } ?>
				</p>
			</fieldset>
			<?php if ($row->id) { ?>
			<label>
				<?php echo JText::_('EVENTS_E_PUBLISHING'); ?>
				<?php echo $lists['state']; ?>
			</label>
			<?php } else { ?>
			<input type="hidden" name="state" value="<?php echo $row->state; ?>" />
			<?php } ?>
		</fieldset><div class="clear"></div>
<?php if ($config->getCfg('calSimpleEventForm') != 'YES') { ?>
		<div class="explaination">
			<p><?php echo JText::_('EVENTS_CAL_LANG_EVENT_REPEAT_INFO'); ?></p>
		</div>
		<fieldset>
    		<h3><?php echo JText::_('EVENTS_CAL_LANG_EVENT_REPEATTYPE'); ?></h3>

			<table>
				<tbody>
					<tr>
						<th><?php echo JText::_('EVENTS_CAL_LANG_REP_DAY'); ?></th>
						<td colspan="2" class="frm_td_bydays">
							<label class="option"><input class="option" id="reccurtype0" name="reccurtype" type="radio" value="0" /><?php echo JText::_('EVENTS_CAL_LANG_ALLDAYS'); ?></label>
						</td>
					</tr>
					<tr>
						<th rowspan="3"><?php echo JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></th>
						<td class="frm_td_byweeks">
							<label class="option"><input class="option" id="reccurtype1" name="reccurtype" type="radio" value="1" <?php if ($row->reccurtype == 1) { echo 'checked="checked"'; } ?> /> 1 * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></label>
						</td>
						<td class="frm_td_byweeks">
							<?php 
							if ($row->reccurtype == 1 || $row->reccurtype == 2) {
								$arg = '';
							} else {
								$arg = ' disabled="disabled"';
							}
							echo EventsHtml::buildReccurDaySelect($row->reccurday,'reccurday_week',$arg); ?>
						</td>
					</tr>
					<tr>
						<td class="frm_td_byweeks">
							<label class="option"><input class="option" id="reccurtype2" name="reccurtype" type="radio" value="2" <?php if ($row->reccurtype == 2) { echo 'checked="checked"'; } ?> /> n * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></label>
						</td>
						<td class="frm_td_byweeks">
							<?php 
							if ($row->reccurtype == 1 || $row->reccurtype == 2) {
								$arg = '';
							} else {
								$arg = ' disabled="disabled"';
							}
							echo EventsHtml::buildWeekDaysCheck($row->reccurweekdays, 'class="option"'.$arg); ?>
						</td>
					</tr>
					<tr>
						<td class="frm_td_byweeks"><em><?php echo JText::_('EVENTS_CAL_LANG_EVENT_WEEKOPT'); ?></em></td>
						<td class="frm_td_byweeks">
							<?php echo EventsHtml::buildWeeksCheck($row->reccurweeks, $arg); ?>
							<label class="option"><input class="option" id="cb_wn6" name="reccurweekss" type="radio" value="pair" <?php if ($row->reccurweeks == 'pair') { echo 'checked="checked"'; } else { echo 'disabled="disabled"'; } ?> /><?php echo JText::_('EVENTS_CAL_LANG_REP_WEEKPAIR'); ?></label><br />
							<label class="option"><input class="option" id="cb_wn7" name="reccurweekss" type="radio" value="impair" <?php if ($row->reccurweeks == 'impair') { echo 'checked="checked"'; } else { if ($row->reccurtype != 1 && $row->reccurtype != 2) { echo 'disabled="disabled"'; } } ?> /><?php echo JText::_('EVENTS_CAL_LANG_REP_WEEKIMPAIR'); ?></label>
						</td>
					</tr>
					<tr>
						<th rowspan="2"><?php echo JText::_('EVENTS_CAL_LANG_REP_MONTH'); ?></th>
						<td class="frm_td_bymonth">
							<label class="option"><input class="option" id="reccurtype3" name="reccurtype" type="radio" value="3" <?php if ($row->reccurtype == 3) { echo 'checked="checked"'; } ?> /> 1 * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_MONTH'); ?></label>
						</td>
						<td class="frm_td_bymonth">
							<?php 
							if ($row->reccurtype == 3) {
								$arg = '';
							} else {
								$arg = ' disabled="disabled"';
							}
							echo EventsHtml::buildReccurDaySelect($row->reccurday_month,'reccurday_month',$arg); ?>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="frm_td_bymonth">
							<label class="option"><input class="option" id="reccurtype4" name="reccurtype" type="radio" value="4" <?php if ($row->reccurtype == 4) { echo 'checked="checked"'; } ?> /><?php echo JText::_('EVENTS_CAL_LANG_EACH').' '.JText::_('EVENTS_CAL_LANG_ENDMONTH'); ?></label>
						</td>
					</tr>
					<tr>
						<th rowspan="2"><?php echo JText::_('EVENTS_CAL_LANG_REP_YEAR'); ?></th>
						<td class="frm_td_byyear">
							<label class="option"><input class="option" id="reccurtype5" name="reccurtype" type="radio" value="5" <?php if ($row->reccurtype == 5) { echo 'checked="checked"'; } ?> /> 1 * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_YEAR'); ?></label>
						</td>
						<td class="frm_td_byyear">
							<?php 
							if ($row->reccurtype == 5) {
								$arg = '';
							} else {
								$arg = ' disabled="disabled"';
							}
							echo EventsHtml::buildReccurDaySelect($row->reccurday_year,'reccurday_year',$arg); ?>
						</td>
					</tr>
				</tbody>
            </table>
		</fieldset><div class="clear"></div>
<?php } ?>
		<p class="submit"><input type="submit" value="<?php echo JText::_('Save'); ?> Event" /></p>
      
		<input type="hidden" name="created_by" value="<?php echo $row->created_by; ?>" />
		<input type="hidden" name="created_by_alias" value="<?php echo $row->created_by_alias; ?>" />
		<!-- <input type="hidden" name="reccurtype" value="<?php //echo $row->reccurtype; ?>" /> -->
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	</form>
</div>
<?php
	}

	//-----------

	public function buildRadioOption( $arr, $tag_name, $tag_attribs, $key, $text, $selected ) 
	{  
		$html = '';
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) 
		{
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			
			$sel = '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) 
				{
					$k2 = $obj->$key;
					if ($k == $k2) {
						$sel = ' checked="checked"';
						break;
					}
				}
			} else {
				$sel = ($k == $selected ? ' checked="checked"' : '');
			}
			$html .= '<label class="option"><input class="option" name="'.$tag_name.'" id="'.$tag_name.$i.'" type="radio" value="'.$k.'"'.$sel.' '.$tag_attribs.'/>'.$t.'</label>'.n;
		}
		return $html;
	}

	//-----------

	public function buildReccurDaySelect($reccurday, $tag_name, $args) 
	{
		$day_name = array('<span style="color:red;">'.JText::_('EVENTS_CAL_LANG_SUNDAYSHORT').'</span>',
							JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_SATURDAYSHORT'));        
		$daynamelist[] = JHTML::_('select.option', '-1', '&nbsp;'.JText::_('EVENTS_CAL_LANG_BYDAYNUMBER').'<br />', 'value', 'text');
		for ($a=0; $a<7; $a++) 
		{
			$name_of_day = '&nbsp;'.$day_name[$a];
			$daynamelist[] = JHTML::_('select.option', $a, $name_of_day, 'value', 'text');
        }
		$tosend = EventsHtml::buildRadioOption( $daynamelist, $tag_name, $args, 'value', 'text', $reccurday );
		return $tosend;
    }

	//-----------

	public function buildMonthSelect($month, $args) 
	{
		for ($a=1; $a<13; $a++) 
		{
			$mnh = $a;
			if ($mnh<="9"&ereg("(^[0-9]{1})",$mnh)) {
				$mnh="0".$mnh;
			}
			$name_of_month = EventsHtml::getMonthName($mnh);
			$monthslist[] = JHTML::_('select.option', $mnh, $name_of_month, 'value', 'text');
		}
		$tosend = JHTML::_('select.genericlist', $monthslist, 'month', $args, 'value', 'text', $month, false, false );
		return $tosend;		        
    }

	//-----------

	public function buildDaySelect($year, $month, $day, $args) 
	{
		$nbdays = date("d",mktime(0,0,0,($month + 1),0,$year));
		for ($a=1; $a<=$nbdays; $a++) 
		{
			$dys = $a;
			if ($dys<="9"&ereg("(^[1-9]{1})",$dys)) {
				$dys="0".$dys;
			}
			$dayslist[] = JHTML::_('select.option', $dys, $dys, 'value', 'text');
		}
		$tosend = JHTML::_('select.genericlist', $dayslist, 'day', $args, 'value', 'text', $day, false, false );
		return $tosend;
    }

	//-----------

	public function buildYearSelect($year, $args) 
	{
		$y = date("Y");
		if ($year<$y-2) {
			$yearslist[] = JHTML::_('select.option', $year, $year, 'value', 'text');
        }
		for ($i=$y-2;$i<=$y+5;$i++) 
		{           	    				
			$yearslist[] = JHTML::_('select.option', $i, $i, 'value', 'text');
        }
		if ($year>$y+5) {
			$yearslist[] = JHTML::_('select.option', $year, $year, 'value', 'text');
		}
		$tosend = JHTML::_('select.genericlist', $yearslist, 'year', $args, 'value', 'text', $year, false, false );
		return $tosend;
    }

	//-----------

	public function buildViewSelect($viewtype, $args) 
	{
		$viewlist[] = JHTML::_('select.option', 'view_week', JText::_('EVENTS_CAL_LANG_VIEWBYWEEK'), 'value', 'text');
		$viewlist[] = JHTML::_('select.option', 'view_month', JText::_('EVENTS_CAL_LANG_VIEWBYMONTH'), 'value', 'text');
		$viewlist[] = JHTML::_('select.option', 'view_year', JText::_('EVENTS_CAL_LANG_VIEWBYYEAR'), 'value', 'text');
		//$viewlist[] = JHTML::_('select.option', 'view_day', JText::_('EVENTS_CAL_LANG_VIEWBYDAY'), 'value', 'text');
		//$viewlist[] = JHTML::_('select.option', 'view_cat', JText::_('EVENTS_CAL_LANG_VIEWBYCAT'), 'value', 'text');
		//$viewlist[] = JHTML::_('select.option', 'view_search', JText::_('EVENTS_SEARCH_TITLE'), 'value', 'text');
		$tosend = JHTML::_('select.genericlist', $viewlist, 'task', $args, 'value', 'text', $viewtype, false, false );
		return $tosend;
	}

	//-----------

	public function buildHourSelect( $start, $end, $inc, $tag_name, $tag_attribs, $selected, $format='' ) 
	{
		$start = intval( $start );
		$end = intval( $end );
		$inc = intval( $inc );
		$arr = array();
		$tmpi = '';
		for ($i=$start; $i <= $end; $i+=$inc) 
		{
			if (_CAL_CONF_DATEFORMAT == 1) { // US time
				if ($i > 11) {
					$tmpi = ($i-12).' pm';
				} else {
					$tmpi = $i.' am';
				}
			} else {
				$tmpi = $format ? sprintf( "$format", $i ) : "$i";
			}
			$fi = $format ? sprintf( "$format", $i ) : "$i";
			$arr[] = JHTML::_('select.option', $fi, $tmpi, 'value', 'text');
		}
		return JHTML::_('select.genericlist', $arr, $tag_name, $tag_attribs, 'value', 'text', $selected, false, false );
	}

	//-----------

	public function buildCategorySelect($catid, $args, $gid, $option)
	{
		$database =& JFactory::getDBO();

		$catsql = "SELECT id AS value, name AS text FROM #__categories "
				. "WHERE section='$option' AND access<='$gid' AND published='1' ORDER BY ordering";	

		$categories[] = JHTML::_('select.option', '0', JText::_('EVENTS_CAL_LANG_EVENT_CHOOSE_CATEG'), 'value', 'text');

		$database->setQuery($catsql);
		$categories = array_merge( $categories, $database->loadObjectList() );
		$clist = JHTML::_('select.genericlist', $categories, 'catid', $args, 'value', 'text', $catid, false, false );
		
		return $clist;
	}

	//-----------

	public function buildWeekDaysCheck($reccurweekdays, $args) 
	{
		$day_name = array('<span style="color:red;">'.JText::_('EVENTS_CAL_LANG_SUNDAYSHORT').'</span>',
							JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_SATURDAYSHORT'));    
		$tosend = '';
		if ($reccurweekdays == '') {
			$split = array();
			$countsplit = 0;
		} else {
			$split = explode("|", $reccurweekdays);
			$countsplit = count($split);
		}
        
		for ($a=0; $a<7; $a++) 
		{
			$checked = '';
			for ($x = 0; $x < $countsplit; $x++) 
			{
				if ($split[$x] == $a) {
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<label class="option"><input type="checkbox" id="cb_wd'.$a.'" name="reccurweekdays" value="'.$a.'" '.$args.' '.$checked.'/>&nbsp;'.$day_name[$a].'</label>'.n;
		}
		return $tosend;
	}

	//-----------

	public function buildWeeksCheck($reccurweeks, $args) 
	{
		$week_name = array('',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 1',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 2',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 3',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 4',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 5');        
		$tosend = '';
		$checked = '';
    
		if ($reccurweeks == '') {
			$split = array();
			$countsplit = 0;
		} else {
			$split = explode("|", $reccurweeks);
			$countsplit = count($split);
		}
        
		for ($a=1; $a<6; $a++) 
		{
			$checked = '';
			if ($reccurweeks == '') { 
				$checked = 'checked="checked"';
			}
			for ($x = 0; $x < $countsplit; $x++) 
			{
				if ($split[$x] == $a) {
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<label class="option"><input class="option" type="checkbox" id="cb_wn'.$a.'" name="reccurweeks" value="'.$a.'" '.$args.' '.$checked.'/>&nbsp;'.$week_name[$a].'</label><br />'.n;     
		}
		return $tosend;
	}

	//-----------

	/*public function getCategoryName($catid) 
	{
		global $database, $gid, $option;

		$catsql = "SELECT id, name FROM #__categories WHERE id='$catid'";
		$database->setQuery($catsql);
		if ($categories = $database->loadObjectList()) {
			$categories = $categories[0];
			if ($categories) {
				return $categories->name;
			}
		}
		return '';
	}

	//-----------
    // New funtion added by Swaroop to get image 
    
	public function getCategoryImage($catid)
	{
		global $database, $gid, $option;

		$catsql = "SELECT image FROM #__categories WHERE id='$catid'";
		$database->setQuery($catsql);
		if ($categories = $database->loadObjectList()) {
			$categories = $categories[0];
			if ($categories) {
				return $categories->image;
			}
		}
		return '';
	}*/

	//-----------

    public function getUserMailtoLink($agid, $userid)
	{
		$agenda_viewmail = _CAL_CONF_MAILVIEW;
		if ($userid) {
			//ximport('xuser');
			//$xuser =& XUser::getInstance( $userid );
			$xuser =& JUser::getInstance( $userid );
			
			if ($xuser) {
				if (($xuser->get('email')) && ($agenda_viewmail=='YES')) {
					$contactlink = '<a href="mailto:'.$xuser->get('email').'">'.$xuser->get('name').'</a>';
				} else {
					$contactlink = $xuser->get('username');
				}
			}
		} else {
			$database =& JFactory::getDBO();
			$database->setQuery("SELECT created_by_alias FROM #__events WHERE id='$agid'");
			$userdet = $database->loadResult();
			if ($userdet) {
				$contactlink = $userdet;
			} else {
				$contactlink = JText::_('EVENTS_CAL_LANG_ANONYME');
			}
		}
		
		return $contactlink;
	}

	//-----------

	public function getMonthName($month) 
	{        
		$monthname = '';
		switch ($month)
		{
			case '01': $monthname = JText::_('EVENTS_CAL_LANG_JANUARY');   break;
			case '02': $monthname = JText::_('EVENTS_CAL_LANG_FEBRUARY');  break;
			case '03': $monthname = JText::_('EVENTS_CAL_LANG_MARCH');     break;
			case '04': $monthname = JText::_('EVENTS_CAL_LANG_APRIL');     break;
			case '05': $monthname = JText::_('EVENTS_CAL_LANG_MAY');       break;
			case '06': $monthname = JText::_('EVENTS_CAL_LANG_JUNE');      break;
			case '07': $monthname = JText::_('EVENTS_CAL_LANG_JULY');      break;
			case '08': $monthname = JText::_('EVENTS_CAL_LANG_AUGUST');    break;
			case '09': $monthname = JText::_('EVENTS_CAL_LANG_SEPTEMBER'); break;
			case '10': $monthname = JText::_('EVENTS_CAL_LANG_OCTOBER');   break;
			case '11': $monthname = JText::_('EVENTS_CAL_LANG_NOVEMBER');  break;
			case '12': $monthname = JText::_('EVENTS_CAL_LANG_DECEMBER');  break;
        }
		return $monthname;
	}

	//-----------

	public function getLongDayName($daynb) 
	{
		$dayname = '';
		switch ($daynb) 
		{
			case '0': $dayname = JText::_('EVENTS_CAL_LANG_SUNDAY');    break;
			case '1': $dayname = JText::_('EVENTS_CAL_LANG_MONDAY');    break;
			case '2': $dayname = JText::_('EVENTS_CAL_LANG_TUESDAY');   break;
			case '3': $dayname = JText::_('EVENTS_CAL_LANG_WEDNESDAY'); break;
			case '4': $dayname = JText::_('EVENTS_CAL_LANG_THURSDAY');  break;
			case '5': $dayname = JText::_('EVENTS_CAL_LANG_FRIDAY');    break;
			case '6': $dayname = JText::_('EVENTS_CAL_LANG_SATURDAY');  break;
		}
		return $dayname;
	}

	//-----------

	public function getDateFormat($year,$month,$day, $type)
	{
		if (empty($year)) $year = 0;
		if (empty($month)) $month = 0;
		if (empty($day)) $day = 1;
        
		$format_type = _CAL_CONF_DATEFORMAT;
		$datestp     = (mktime(0,0,0,$month,$day,$year));
		$jour_fr     = date("j", $datestp);
		$numero_jour = date("w", $datestp);
		$mois_fr     = date("n", $datestp);
		$mois_0      = date("m", $datestp);
		$annee       = date("Y", $datestp);
		$newdate     = '';
		
		switch ($type)
		{
			case '0':
				if ($format_type == 0) {
					// Fr style : Monday 23 Juillet 2003
					$newdate = EventsHtml::getLongDayName($numero_jour).'&nbsp;'.$jour_fr.'&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				} else if ($format_type == 1) {
					// Us style : Monday, July 23 2003
					$newdate = EventsHtml::getLongDayName($numero_jour).',&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$jour_fr.'&nbsp;'.$annee; 
				} else {
					// De style : Montag, 23 Juli 2003
					$newdate = EventsHtml::getLongDayName($numero_jour).',&nbsp;'.$jour_fr.'.&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				}
			break;

			case '1':
				if ($format_type == 0) {
					// Fr style : 23 Juillet 2003
					$newdate = $jour_fr.'&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				} else if ($format_type == 1) {
					// Us style : July 23, 2003
					$newdate = EventsHtml::getMonthName($mois_0).'&nbsp;'.$jour_fr.',&nbsp;'.$annee;
				} else {
					// De style : 23. Juli 2003
					$newdate = $jour_fr.'.&nbsp;'.EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				}
			break;

			case '2':
				if ($format_type == 0) {
					// Fr style : 23 Juillet
					$newdate = $jour_fr.'&nbsp;'.EventsHtml::getMonthName($mois_0);
				} else if ($format_type == 1) {
					// Us style : Juillet, 23
					$newdate = EventsHtml::getMonthName($mois_0).',&nbsp;'.$jour_fr;
				} else {
					// De style : 23. Juli
					$newdate = $jour_fr.'.&nbsp;'.EventsHtml::getMonthName($mois_0);
				}
			break;

			case '3':
				if ($format_type == 0) {
					// Fr style : Juillet 2003
					$newdate = EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				} else if ($format_type == 1) {
					// Us style : Juillet 2003
					$newdate = EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				} else {
					// De style : Juli 2003
					$newdate = EventsHtml::getMonthName($mois_0).'&nbsp;'.$annee;
				}
			break;

			case '4':
				if ($format_type == 0) {
					// Fr style : 23/07/2003
					$newdate = $jour_fr.'/'.$mois_0.'/'.$annee;
				} else if ($format_type == 1) {
					// Us style : 07/23/2003
					$newdate = $mois_0.'/'.$jour_fr.'/'.$annee;
				} else {
					// De style : 23.07.2003
					$newdate = $jour_fr.'.'.$mois_0.'.'.$annee;
				}
			break;

			case '5':
				if ($format_type == 0) {
					// Fr style : 23/07
					$newdate = $jour_fr.'/'.$mois_0;
				} else if ($format_type == 1){
					// Us style : 07/23
					$newdate = $mois_0.'/'.$jour_fr;
				} else {
					// De style : 23.07.
					$newdate = $jour_fr.'.'.$mois_0.'.';
				}
			break;
      
			case '6':
				if ($format_type == 0) {
					// Fr style : 07/2003
					$newdate = $mois_0.'/'.$annee; 
				} else if ($format_type == 1) {
					// Us style : 07/2003
					$newdate = $mois_0.'/'.$annee; 
				} else {
					// De style : 07/2003
					$newdate = $mois_0.'/'.$annee; 
				}
			break;

			default:
			break;
		}
		return $newdate;
	}
}
?>
