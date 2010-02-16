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

class EventsController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	//-----------
	
	private function getTask()
	{
		$task = JRequest::getString('task', $this->config->getCfg('startview'));
		$task = ($this->_task) ? $this->_task : $task;
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		$this->setup();
		
		$this->getStyles();

		switch ($this->getTask()) 
		{
			case 'delete':   $this->delete();   break;
			case 'add':      $this->edit();     break;
			case 'edit':     $this->edit();     break;
			case 'save':     $this->save();     break;
			case 'details':  $this->details();  break;
			case 'day':      $this->day();      break;
			case 'week':     $this->week();     break;
			case 'month':    $this->month();    break;
			case 'year':     $this->year();     break;
			case 'register': $this->register(); break;
			case 'process':  $this->process();  break;

			default: $this->month(); break;
		}
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}

	//-----------
	
	private function getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'js'.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.'js'.DS.'calendar.rc4.js');
			$document->addScript('components'.DS.$this->_option.DS.'js'.DS.$this->_name.'.js');
		}
	}

	//----------------------------------------------------------
	// Checks (private)
	//----------------------------------------------------------

	private function setup()
	{
		$database =& JFactory::getDBO();
		
		// Load the events configuration
		$config = new EventsConfigs( $database );
		$config->load();
		
		$this->config = $config;
		
		// Set some defines
		define( '_CAL_CONF_STARDAY', $config->getCfg('starday'));
		define( '_CAL_CONF_DATEFORMAT', $config->getCfg('dateformat') );
		
		$jconfig =& JFactory::getConfig();
		$this->offset = $jconfig->getValue('config.offset');
		
		// Incoming
		$year  = JRequest::getVar( 'year',  strftime("%Y", time()+($this->offset*60*60)) );
		$month = JRequest::getVar( 'month', strftime("%m", time()+($this->offset*60*60)) );
		$day   = JRequest::getVar( 'day',   strftime("%d", time()+($this->offset*60*60)) );
		
		$category = JRequest::getInt( 'category', 0 );
		
		if ($day<="9"&ereg("(^[1-9]{1})",$day)) {
			$day = "0$day";
		}
		if ($month<="9"&ereg("(^[1-9]{1})",$month)) {
			$month = "0$month";
		}
		
		$ee = new EventsEvent( $database );
		
		// Find the date of the first event
		$row = $ee->getFirst();
		if ($row) {
			$pyear = substr($row,0,4);
			$pmonth = substr($row,4,2);
			if ($year < $pyear) {
				$year = $pyear;
			}
			if ($month < $pmonth) {
				//$month = $pmonth;
			}
		}

		// Find the date of the last event
		$row = $ee->getLast();
		if ($row) {
			$thisyear = strftime("%Y", time()+($this->offset*60*60));
			$fyear = substr($row,0,4);
			$fmonth = substr($row,4,2);
			if ($year > $fyear && $year > $thisyear) {
				$year = ($fyear > $thisyear) ? $fyear : $thisyear;
			}
			if ($month > $fmonth) {
				//$month = $fmonth;
			}
		}

		$this->year  = $year;
		$this->month = $month;
		$this->day   = $day;
		
		$this->category = $category;

		$juser =& JFactory::getUser();
		$this->gid = intval( $juser->get('gid') );
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function year() 
	{
		$database =& JFactory::getDBO();
		
		// Get some needed info
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$offset = $this->offset;
		$option = $this->_option;
		$gid    = $this->gid;
        
		/*
		// Get configuration
		$jconfig =& JFactory::getConfig();
		
		$filters['limit'] = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'), 'int');
		$filters['start'] = JRequest::getInt('limitstart', 0);
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
		*/
		
		// Set some filters
		$filters = array();
		$filters['gid'] = $gid;
		$filters['year'] = $year;
		$filters['category'] = $this->category;

		// Retrieve records
		$ee = new EventsEvent( $database );
		$rows = $ee->getEvents( 'year', $filters );

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if ($this->config->getCfg('adminlevel')) {
			$authorized = $this->authorize();
		}
		
		// Get a list of categories
		$categories = $this->getCategories();
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$year );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( $year, 'index.php?option='.$this->_option.a.'year='.$year );
		
		// Output HMTL
		echo EventsHtml::byYear($rows, $option, $year, $month, $day, $authorized, $this->config->getCfg('fields'), $this->category, $categories);
	}

	//-----------

	protected function month() 
	{
		$database =& JFactory::getDBO();
		
		// Get some needed info
		$offset = $this->offset;
		$option = $this->_option;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;		
		$gid    = $this->gid;

		// Set some dates
		$select_date = $year.'-'.$month.'-01 00:00:00';
		$select_date_fin = $year.'-'.$month.'-'.date("t",mktime(0,0,0,($month+1),0,$year)).' 23:59:59';
		
		// Set some filters
		$filters = array();
		$filters['gid'] = $gid;
		$filters['select_date'] = $select_date;
		$filters['select_date_fin'] = $select_date_fin;
		$filters['category'] = $this->category;
		
		// Retrieve records
		$ee = new EventsEvent( $database );
		$rows = $ee->getEvents( 'month', $filters );

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if ($this->config->getCfg('adminlevel')) {
			$authorized = $this->authorize();
		}
		
		// Get a list of categories
		$categories = $this->getCategories();

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$year.'/'.$month );

		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( $year, 'index.php?option='.$this->_option.a.'year='.$year );
		$pathway->addItem( $month, 'index.php?option='.$this->_option.a.'year='.$year.a.'month='.$month );

		// Output HTML
		echo EventsHtml::byMonth($rows, $option, $year, $month, $day, $offset, $authorized, $this->config->getCfg('fields'), $this->category, $categories);
	}

	//-----------

	protected function week() 
	{
		$database =& JFactory::getDBO();
		
		// Get some needed info
		$offset = $this->offset;
		$option = $this->_option;
		$year   = intval($this->year);
		$month  = intval($this->month);
		$day    = intval($this->day);
       
		/* 
		$startday = $this->config->getCfg('starday');
		$numday = ((date("w",mktime(0,0,0,$month,$day,$year))-$startday)%7);
		if ($numday == -1) {
			$numday = 6;
		}
		$week_start = mktime(0, 0, 0, $month, ($day - $numday), $year );
		$week_end   = $week_start + ( 3600 * 24 * 6 );
		$startdate  = date( "Y-m-d 00:00:00", $week_start );
		$enddate    = date( "Y-m-d 23:59:59", $week_end );
		
		$filters['startdate'] = $startdate;
		$filters['enddate'] = $enddate;
		
		$ee->getEvents( 'week', $filters );
		*/
		
		$startday = _CAL_CONF_STARDAY;
		$numday = ((date("w",mktime(0,0,0,$month,$day,$year))-$startday)%7);
		if ($numday == -1) {
			$numday = 6;
		} 
		$week_start = mktime(0, 0, 0, $month, ($day - $numday), $year );

		$this_date = new EventsDate();
		$this_date->setDate(strftime("%Y", $week_start ), strftime("%m", $week_start ), strftime("%d", $week_start ));
		$this_enddate = clone($this_date);
		$this_enddate->addDays( +6 );
		
		$sdt = JHTML::_('date', $this_date->year.'-'.$this_date->month.'-'.$this_date->day.' 00:00:00', '%d %b');
		$edt = JHTML::_('date', $this_enddate->year.'-'.$this_enddate->month.'-'.$this_enddate->day.' 00:00:00', '%d %b');
		
		$this_currentdate = $this_date;
		
		$categories = $this->getCategories();
		
		$filters = array();
		$filters['gid'] = $this->gid;
		$filters['category'] = $this->category;
		
		$ee = new EventsEvent( $database );
		
		$html = '';
		for ($d = 0; $d < 7; $d++) 
		{
			if ($d > 0) {
				$this_currentdate->addDays( +1 );
			}
			$week = array();
			$week['day']   = sprintf("%02d", $this_currentdate->day);
			$week['month'] = sprintf("%02d", $this_currentdate->month);
			$week['year']  = sprintf("%4d",  $this_currentdate->year);

			$filters['select_date'] = sprintf( "%4d-%02d-%02d", $week['year'], $week['month'], $week['day'] );
			
			$rows = $ee->getEvents( 'day', $filters );
			
			$html .= EventsHtml::forWeek($rows, $offset, $option, $week, $this->config->getCfg('fields'), $categories);
		}

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if ($this->config->getCfg('adminlevel')) {
			$authorized = $this->authorize();
		}
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)).': '.$year.'/'.$month.'/'.$day );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( $year, 'index.php?option='.$this->_option.a.'year='.$year );
		$pathway->addItem( $month, 'index.php?option='.$this->_option.a.'year='.$year.a.'month='.$month );
		$pathway->addItem( JText::_('Week of').' '.$day, 'index.php?option='.$this->_option.a.'year='.$year.a.'month='.$month.a.'day='.$day.a.'task=week' );
		
		// Output HTML
		echo EventsHtml::byWeek($offset, $option, $year, $month, $day, $sdt, $edt, $html, $authorized, $this->category, $categories);
	}

	//-----------

	protected function day() 
	{
		$database =& JFactory::getDBO();
		
		// Get some needed info
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$offset = $this->offset;
		$option = $this->_option;
		
		// Get the events for this day
		//$rows = $this->getByDay(sprintf( "%4d-%02d-%02d", $year, $month, $day ));
		$filters = array();
		$filters['gid'] = $this->gid;
		$filters['select_date'] = sprintf( "%4d-%02d-%02d", $year, $month, $day );
		$filters['category'] = $this->category;
		
		$ee = new EventsEvent( $database );
		$rows = $ee->getEvents( 'day', $filters );

		// Go through each event and ensure it should be displayed
		$events = array();
		if (count($rows) > 0) {
			foreach ($rows as $row) 
			{
				$checkprint = new EventsRepeat($row, $year, $month, $day);
				if ($checkprint->viewable == true) { 
					$events[] = $row;
				}
			}
		}
		
		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if ($this->config->getCfg('adminlevel')) {
			$authorized = $this->authorize();
		}
		
		// Get a list of categories
		$categories = $this->getCategories();

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$year.'/'.$month.'/'.$day );

		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( $year, 'index.php?option='.$this->_option.a.'year='.$year );
		$pathway->addItem( $month, 'index.php?option='.$this->_option.a.'year='.$year.a.'month='.$month );
		$pathway->addItem( $day, 'index.php?option='.$this->_option.a.'year='.$year.a.'month='.$month.a.'day='.$day );

		// Output HTML
		echo EventsHtml::byDay($events, $offset, $option, $year, $month, $day, $authorized, $this->config->getCfg('fields'), $categories);
	}

	//-----------

	protected function details() 
	{
		$database =& JFactory::getDBO();
		$document =& JFactory::getDocument();
		
		// Get some needed info
		$offset = $this->offset;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$option = $this->_option;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		// Load event
		$row = new EventsEvent( $database );
		$row->load( $id );
		
		if ($row) {
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
			
			// Kludge for overnight events, advance the displayed stop_date by 1 day when an overnighter is detected
			if ($row->stop_time < $row->start_time) {
				$event_down->addDays(1);
			}
			
			// Parse http and mailto
			$alphadigit = "([a-z]|[A-Z]|[0-9])";

			// Adresse
			$row->adresse_info = preg_replace("/(mailto:\/\/)?((-|$alphadigit|\.)+)@((-|$alphadigit|\.)+)(\.$alphadigit+)/i","<a href=\"mailto:$2@$5$8\">$2@$5$8</a>", $row->adresse_info);
			$row->adresse_info = preg_replace("/(http:\/\/)((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"http://$2$5$8\">$1$2$5$8</a>", $row->adresse_info); 
		
			// Contact
			$row->contact_info = preg_replace("/(mailto:\/\/)?((-|$alphadigit|\.)+)@((-|$alphadigit|\.)+)(\.$alphadigit+)/i","<a href=\"mailto:$2@$5$8\">$2@$5$8</a>", $row->contact_info);
			$row->contact_info = preg_replace("/(http:\/\/)((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"http://$2$5$8\">$1$2$5$8</a>", $row->contact_info); 

			// Images - replace the {mosimage} plugins in both text areas
			if ($row->images) {
				$row->images = explode("\n", $row->images);
				$images = array();
				
				foreach ($row->images as $img) 
				{
					$temp = explode( '|', trim( $img ) );
					if (!isset($temp[1]))
						$temp[1] = "left";

					if (!isset($temp[2]))
						$temp[2] = "Image";

					if (!isset($temp[3]))
						$temp[3] = "0";

					$images[] = '<img src="./images/stories/'.$temp[0].'" style="float:'.$temp[1].';" alt="'.$temp[2].'" />';
				}

				$text = explode( '{mosimage}', $row->content );

				$row->content = $text[0];

				for ($i=0, $n=count( $text )-1; $i < $n; $i++) 
				{
					if (isset( $images[$i] )) {
						$row->content .= $images[$i];
					}
					if (isset( $text[$i+1] )) {
						$row->content .= $text[$i+1];
					}
				}
				unset( $text );
			} 
			
			$row->content = nl2br(trim($row->content));
			$row->content = str_replace("[[BR]]",'<br />',$row->content);
			$row->content = str_replace(" * ",'<br />&nbsp;&bull;&nbsp;',$row->content);
			$row->content = stripslashes($row->content);
			
			$fields = $this->config->getCfg('fields');
			if (!empty($fields)) {
				for ($i=0, $n=count( $fields ); $i < $n; $i++) 
				{
					// Explore the text and pull out all matches
					array_push($fields[$i], $this->parseTag($row->content, $fields[$i][0]));
					
					// Clean the original text of any matches
					$row->content = str_replace('<ef:'.$fields[$i][0].'>'.end($fields[$i]).'</ef:'.$fields[$i][0].'>','',$row->content);
				}
				$row->content = trim($row->content);
			}
			
			$bits = explode('-',$row->publish_up);
			$eyear = $bits[0];
			$emonth = $bits[1];
			$edbits = explode(' ',$bits[2]);
			$eday = $edbits[0];
			
			// Everyone has access unless restricted to admins in the configuration
			//$authorized = true;
			//if ($this->config->getCfg('adminlevel')) {
				$authorized = $this->authorize($row->created_by_alias);
			//}
			
			$auth = true;
			if ($this->config->getCfg('adminlevel')) {
				$auth = $this->authorize();
			}

			// Get a list of categories
			$categories = $this->getCategories();
			
			// Get tags on this event
			$rt = new EventsTags( $database );
			$tags = $rt->get_tag_cloud(0, 0, $row->id);
			
			// Set the page title
			$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)).': '.$row->title );
			
			// Set the pathway
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
			}
			$pathway->addItem( $eyear, 'index.php?option='.$this->_option.a.'year='.$eyear );
			$pathway->addItem( $emonth, 'index.php?option='.$this->_option.a.'year='.$eyear.a.'month='.$emonth );
			$pathway->addItem( $eday, 'index.php?option='.$this->_option.a.'year='.$eyear.a.'month='.$emonth.a.'day='.$eday );
			$pathway->addItem( stripslashes($row->title), 'index.php?option='.$this->_option.a.'task=details'.a.'id='.$row->id );
			
			//------
			
			// Incoming
			$alias = JRequest::getVar( 'page', '' );
			
			// Load the current page
			$page = new EventsPage( $database );
			if ($alias) {
				$page->loadFromAlias( $alias, $row->id );
			}

			// Get the pages for this workshop
			$pages = $page->loadPages( $row->id );
			
			if ($alias) {
				$pathway->addItem(stripslashes($page->title),'index.php?option='.$this->_option.a.'task=details'.a.'id='.$row->id.a.'page='.$page->alias);
			}
			//------
			
			// Build the HTML
			$html = EventsHtml::details($row, $offset, $option, $eyear, $emonth, $eday, $authorized, $fields, $this->config, $categories, $tags, $auth, $page, $pages);
		} else {
			// Set the page title
			$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) );
			
			// Warning message
			$html = EventsHtml::warning( JText::_('EVENTS_CAL_LANG_NO_EVENTFOR').' '.JText::_('EVENTS_CAL_LANG_THIS_DAY') );
		}
		
		// Output HTML
		echo $html;
	}

	//-----------

	protected function register()
	{
		$database =& JFactory::getDBO();
		$document =& JFactory::getDocument();
		
		// Get some needed info
		$offset = $this->offset;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$option = $this->_option;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		// Ensure we have an ID
		if (!$id) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		// Load event
		$event = new EventsEvent( $database );
		$event->load( $id );
		
		// Ensure we have an event
		if (!$event->title || $event->registerby == '0000-00-00 00:00:00') {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		$auth = true;
		if ($this->config->getCfg('adminlevel')) {
			$auth = $this->authorize();
		}
		
		$bits = explode('-',$event->publish_up);
		$eyear = $bits[0];
		$emonth = $bits[1];
		$edbits = explode(' ',$bits[2]);
		$eday = $edbits[0];
		
		// Push some styles to the template
		$this->getStyles();
		
		// Set the page title
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)).': '.stripslashes($event->title) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( $eyear, 'index.php?option='.$this->_option.a.'year='.$eyear );
		$pathway->addItem( $emonth, 'index.php?option='.$this->_option.a.'year='.$eyear.a.'month='.$emonth );
		$pathway->addItem( $eday, 'index.php?option='.$this->_option.a.'year='.$eyear.a.'month='.$emonth.a.'day='.$eday );
		$pathway->addItem( stripslashes($event->title), 'index.php?option='.$this->_option.a.'task=details'.a.'id='.$event->id );
		$pathway->addItem( JText::_('Register'),'index.php?option='.$this->_option.a.'task=details'.a.'id='.$event->id.a.'page=register');
		
		$page = new EventsPage( $database );
		$page->alias = $this->_task;
		
		// Get the pages for this workshop
		$pages = $page->loadPages( $event->id );
		
		// Check if registration is still open
		$registerby = $this->_mkt($event->registerby);
		$now = time();
		
		$register = array();
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			$profile = new XProfile();
			$profile->load( $juser->get('id') );
			
			$register['firstname'] = $profile->get('givenName');
			$register['lastname'] = $profile->get('surname');
			$register['affiliation'] = $profile->get('organization');
			$register['email'] = $profile->get('email');
			$register['telephone'] = $profile->get('phone');
			$register['website'] = $profile->get('url');
		}
		
		if ($registerby >= $now) {
			// Is the registration restricted?
			if ($event->restricted) {
				$passwrd = JRequest::getVar('passwrd', '', 'post');
				
				if ($event->restricted == $passwrd) {
					EventsHtml::register( $this->_option, $event, $year, $month, $day, $page, $pages, $register, null, null, $auth );
				} else {
					echo EventsHtml::restricted( $this->_option, $event, $year, $month, $day, $page, $pages, $auth );
				}
			} else {
				EventsHtml::register( $this->_option, $event, $year, $month, $day, $page, $pages, $register, null, null, $auth );
			}
		} else {
			echo EventsHtml::closed( $this->_option, $event, $year, $month, $day, $page, $pages, $auth );
		}
	}
	//-----------
	
	private function _mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}

	//-----------

	protected function process() 
	{
		$database =& JFactory::getDBO();
		$document =& JFactory::getDocument();
		
		// Get some needed info
		$offset = $this->offset;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$option = $this->_option;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'post' );
		
		// Ensure we have an ID
		if (!$id) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		// Load event
		$event = new EventsEvent( $database );
		$event->load( $id );
		$this->event = $event;
		
		// Ensure we have an event
		if (!$event->title) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		$auth = true;
		if ($this->config->getCfg('adminlevel')) {
			$auth = $this->authorize();
		}
		
		$bits = explode('-',$event->publish_up);
		$eyear = $bits[0];
		$emonth = $bits[1];
		$edbits = explode(' ',$bits[2]);
		$eday = $edbits[0];
		
		$page = new EventsPage( $database );
		$page->alias = $this->_task;
		
		// Get the pages for this workshop
		$pages = $page->loadPages( $event->id );
		
		// Push some styles to the template
		$this->getStyles();
		
		// Set the page title
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)).': '.stripslashes($event->title) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( $eyear, 'index.php?option='.$this->_option.a.'year='.$eyear );
		$pathway->addItem( $emonth, 'index.php?option='.$this->_option.a.'year='.$eyear.a.'month='.$emonth );
		$pathway->addItem( $eday, 'index.php?option='.$this->_option.a.'year='.$eyear.a.'month='.$emonth.a.'day='.$eday );
		$pathway->addItem( stripslashes($event->title), 'index.php?option='.$this->_option.a.'task=details'.a.'id='.$event->id );
		$pathway->addItem( JText::_('Register'),'index.php?option='.$this->_option.a.'task=details'.a.'id='.$event->id.a.'page=register');
		
		// Incoming
		$register   = JRequest::getVar('register', NULL, 'post');
		$arrival    = JRequest::getVar('arrival', NULL, 'post');
		$departure  = JRequest::getVar('departure', NULL, 'post');
		$dietary    = JRequest::getVar('dietary', NULL, 'post');
		$bos        = JRequest::getVar('bos', NULL, 'post');
		$dinner     = JRequest::getVar('dinner', NULL, 'post');
		$disability = JRequest::getVar('disability', NULL, 'post');
		$race       = JRequest::getVar('race', NULL, 'post');

		if ($register) {
			$register = array_map('trim', $register);
			$register = array_map(array('EventsController','purifyText'), $register);
			
			$validemail = $this->check_validEmail($register['email']);
		}
		if ($arrival) {
			$arrival = array_map('trim', $arrival);
			$arrival = array_map(array('EventsController','purifyText'), $arrival);
		}
		if ($departure) {
			$departure = array_map('trim', $departure);
			$departure = array_map(array('EventsController','purifyText'), $departure);
		}
		if ($dietary) {
			$dietary = array_map('trim', $dietary);
			$dietary = array_map(array('EventsController','purifyText'), $dietary);
		}

		$xhub =& XFactory::getHub();

		$email   = $event->email;
		$subject = 'Event Registration ('.$event->id.')';
		$from    = $xhub->getCfg('hubShortName').' Event Registration';
		$hub     = array('email' => $register['email'], 'name' => $from);
	
		if ($register['firstname'] && $register['lastname'] && $register['affiliation'] && ($validemail == 1)) {

			$message  = 'Name: '. $register['firstname'].' '.$register['lastname'] .r.n;
			$message .= 'Title: '. $register['title'] .r.n;
			$message .= 'Affiliation: '. $register['affiliation'] .r.n;
			$message .= 'Email: '. $register['email'] .r.n;
			$message .= 'Website: '. $register['website'] .r.n;
			$message .= 'Telephone: '. $register['telephone'] .r.n;
			$message .= 'Fax: '. $register['fax'] .r.n.r.n;
		
			$message .= 'City: '. $register['city'] .r.n;
			$message .= 'State/Province: '. $register['state'] .r.n;
			$message .= 'Zip/Postal Code: '. $register['postalcode'] .r.n;
			$message .= 'Country: '. $register['country'] .r.n.r.n;
			
			if (isset($register['position']) || isset($register['position_other'])) {
				$message .= 'Current position: ';
				$message .= ($register['position']) ? $register['position'] : $register['position_other'];
				$message .= r.n.r.n;
			}
			if (isset($register['degree'])) {
				$message .= 'Highest degree earned: '. $register['degree'] .r.n.r.n;
			}
			if (isset($register['sex'])) {
				$message .= 'Gender: '. $register['sex'] .r.n.r.n;
			}
			if ($race) {
				//$message .= 'Race: '.implode(', ',$race) .r.n.r.n;
				$message .= 'Race: ';
				foreach ($race as $r=>$t) 
				{
					$message .= ($r != 'nativetribe') ? $r.', ' : '';
				}
				if ($race['nativetribe'] != '') {
					$message .= $race['nativetribe'];
				}
				$message .= r.n.r.n;
			}
			
			if ($disability) {
				$message .= '[X] I have auxiliary aids or services due to a disability. Please contact me.'.r.n.r.n;
			} else {
				$message .= '[ ] I have auxiliary aids or services due to a disability. Please contact me.'.r.n.r.n;
			}
			if (isset($dietary['needs']) || (isset($dietary['specific']) && $dietary['specific'] != '')) {
				$message .= '[X] I have specific dietary needs.'.r.n.r.n;
				$message .= '    Specific: '.$dietary['specific'].r.n.r.n;
			} else {
				$message .= '[ ] I have specific dietary needs.'.r.n.r.n;
			}
			
			if ($arrival) {
				$message .= '=== Arrival ==='.r.n;
				$message .= 'Arrival Day: '. $arrival['day'] .r.n;
				$message .= 'Arrival Time: '. $arrival['time'] .r.n.r.n;
			}
			if ($departure) {
				$message .= '=== Departure ==='.r.n;
				$message .= 'Departure Day: '. $departure['day'] .r.n;
				$message .= 'Departure Time: '. $departure['time'] .r.n.r.n;
			}
			
			/*if (!empty($bos)) {
				$message .= 'Break Out Session(s): '.r.n;
				for ($i=0, $n=count( $bos ); $i < $n; $i++) 
				{
					$message .= '  '.$bos[$i].r.n;
				}
				$message .= r.n;
			}*/
			if ($dinner) {
				$message .= '[x] Attending dinner.'.r.n.r.n;
			} else {
				$message .= '[ ] Attending dinner.'.r.n.r.n;
			}
			
			if (isset($register['additional'])) {
				$message .= 'Additional: '. $register['additional'] .r.n.r.n;
			}
			
			if (isset($register['comments'])) {
				$message .= 'Comments: '. $register['comments'] .r.n.r.n;
			}
				
			$this->send_email($hub, $email, $subject, $message);

			$this->log($register);

			echo EventsHtml::thanks( $this->_option, $event, $year, $month, $day, $auth, $page, $pages );
		} else {
			echo EventsHtml::register( $this->_option, $event, $year, $month, $day, $page, $pages, $register, $arrival, $departure, $auth, true);
		}
	}
	
	//-----------
	
	private function log($reg)
	{
		$dbh =& JFactory::getDBO();
		$dbh->setQuery(
			'INSERT INTO #__events_respondents(
				event_id,
				first_name, last_name, affiliation, title, city, state, zip, country, telephone, fax, email,
				website, position_description, highest_degree, gender, arrival, departure, disability_needs, 
				dietary_needs, attending_dinner, abstract, comment
			)
			VALUES ('.
				$this->event->id . ', '.
				$this->getValueString($dbh, $reg, array(
					'firstname', 'lastname', 'affiliation', 'title', 'city', 'state', 'postalcode', 'country', 'telephone', 'fax', 'email',
					'website', 'position', 'degree', 'gender', 'arrival', 'departure', 'disability', 
					'dietary', 'dinner', 'additional', 'comments'
				)).
			')'
		);
		$dbh->query();
		$races = JRequest::getVar('race', NULL, 'post');
		if (!is_null($races) && (!isset($races['refused']) || !$races['refused']))
		{
			$resp_id = $dbh->insertid();
			foreach (array('nativeamerican', 'asian', 'black', 'hawaiian', 'white', 'hispanic') as $race)
				if (array_key_exists($race, $races) && $races[$race])
					$dbh->execute(
						'INSERT INTO #__events_respondent_race_rel(respondent_id, race, tribal_affiliation) 
						VALUES ('.$resp_id.', \''.$race.'\', '.($race == 'nativeamerican' ? $dbh->quote($races['nativetribe']) : 'NULL').')'
					);
		}
	}

	//-----------

	private function getValueString(&$dbh, $reg, $values)
	{
		$rv = array();
		foreach ($values as $val)
		{
			switch ($val)
			{
				case 'position':
					$rv[] = ((isset($reg['position']) || isset($reg['position_other'])) && ($reg['position'] || $reg['position_other'])) 
						? $dbh->quote(
							$reg['position'] ? $reg['position'] : $reg['position_other']
						) 
						: 'NULL';
				break;
				case 'gender':
					$rv[] = (isset($reg['sex']) && ($reg['sex'] == 'male' || $reg['sex'] == 'female')) 
						? '\''.substr($reg['sex'], 0, 1).'\'' 
						: 'NULL';
				break;
				case 'dinner':
					$dinner = JRequest::getVar('dinner', NULL, 'post');
					$rv[] = is_null($dinner) ? 'NULL' : $dinner ? '1' : '0';
				break;
				case 'dietary':
					$rv[] = (($dietary = JRequest::getVar('dietary', NULL, 'post'))) 
						? $dbh->quote($dietary['specific']) 
						: 'NULL';
				break;
				case 'arrival': case 'departure':
					$rv[] = ($date = JRequest::getVar($val, NULL, 'post')) 
						? $dbh->quote($date['day'] . ' ' . $date['time'])
						: 'NULL';
				break;
				case 'disability':
					$disability = JRequest::getVar('disability', NULL, 'post');
					$rv[] = ($disability) ? '1' : '0';
				break;
				default:
					$rv[] = array_key_exists($val, $reg) && isset($reg[$val]) ? $dbh->quote($reg[$val]) : 'NULL';
			}
		}
		return implode($rv, ',');
	}

	//-----------

	private function send_email(&$hub, $email, $subject, $message) 
	{
		if ($hub) {
		     $xhub = &XFactory::getHub();
			$contact_email = $hub['email'];
			$contact_name  = $hub['name'];

			$args = "-f '" . $contact_email . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
			$headers .= 'From: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= 'Reply-To: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '.  $xhub->getCfg('hubShortName') .n;
			if (mail($email, $subject, $message, $headers, $args)) {
				return(1);
			}
		}
		return(0);
	}

	//-----------

	private function check_validEmail($email) 
	{
		if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return(1);
		} else {
			return(0);
		}
	}

	//-----------
	
	private function purifyText( &$text ) 
	{
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = strip_tags( $text );

		return $text;
	}

	//-----------

	private function login()
	{
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('EVENTS_CAL_LANG_CAL_TITLE').': '.JText::_('EVENTS_CAL_LANG_ADD_TITLE') );
		
		echo EventsHtml::div( EventsHtml::hed( 2, JText::_('EVENTS_CAL_LANG_CAL_TITLE') ), 'full', 'content-header');
		echo '<div class="main section">'.n;
		ximport('xmodule');
		XModuleHelper::displayModules('force_mod');
		echo '</div><!-- / .main section -->'.n;
	}

	//-----------

	protected function edit($row=NULL)
	{		
		$juser =& JFactory::getUser();
		
		// Check if they are logged in
		if ($juser->get('guest')) {
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
			}
			$pathway->addItem( JText::_('EVENTS_CAL_LANG_ADD_TITLE'), 'index.php?option='.$this->_option.a.'task=add' );
			
			$this->login();
			return;
		}
	
		// Check if they have edit access
		/*if ($this->config->getCfg('adminlevel') && !$this->authorize()) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			$this->_message = JText::_('EVENTS_CAL_LANG_NOPERMISSION');
			return;
		}*/
		
		// Get the database connection
		$database =& JFactory::getDBO();
		
		// Push some styles to the tmeplate
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'calendar.css');
		
		// Push some scripts to the template
		$this->getScripts();
		
		// We need at least one category before we can proceed
		$cat = new EventsCategory( $database );
		if ($cat->getCategoryCount( $this->_option ) < 1) {
			echo EventsHtml::error( JText::_('EVENTS_LANG_NEED_CATEGORY') );
			return;
		}
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		// Load event object
		if (!is_object($row)) {
			$row = new EventsEvent( $database );
			$row->load( $id );
		}
		
		// Do we have an ID?
		if ($row->id) {
			// Yes - edit mode
			
			// Are they authorized to make edits?
			if (!$this->authorize($row->created_by)) {
				// Not authorized - redirect
				$this->_redirect = JRoute::_('index.php?option='.$this->_option);
				$this->_message = JText::_('EVENTS_CAL_LANG_NOPERMISSION');
				return;
			}
			
			$event_up = new EventsDate( $row->publish_up );
			$start_publish = sprintf( "%4d-%02d-%02d",$event_up->year,$event_up->month,$event_up->day);
			$start_time = $event_up->hour .':'. $event_up->minute;
	
			$event_down = new EventsDate( $row->publish_down );
			$stop_publish = sprintf( "%4d-%02d-%02d",$event_down->year,$event_down->month,$event_down->day);
			$end_time = $event_down->hour .':'. $event_down->minute;
			
			$event_registerby = new EventsDate( $row->registerby );
			$registerby_date = sprintf( "%4d-%02d-%02d",$event_registerby->year,$event_registerby->month,$event_registerby->day);
			$registerby_time = $event_registerby->hour .':'. $event_registerby->minute;
        
			$row->reccurday_month = 99;
			$row->reccurday_week = 99;
			$row->reccurday_year = 99;
			
			if ($row->reccurday <> '') {
				if ($row->reccurtype == 1) {
					$row->reccurday_week = $row->reccurday;
				} elseif ($row->reccurtype == 3) {
					$row->reccurday_month = $row->reccurday;
				} elseif ($row->reccurtype == 5) {
					$row->reccurday_year = $row->reccurday;
				}
			}
			$arr = array(
				JHTML::_('select.option', 0, JText::_( 'no' ), 'value', 'text'),
				JHTML::_('select.option', 1, JText::_( 'yes' ), 'value', 'text'),
			);

			$lists['state'] = JHTML::_('select.genericlist', $arr, 'state', '', 'value', 'text', $row->state, false, false );
		} else {
			// No ID - we're creating a new event
			
			$year  = $this->year;
			$month = $this->month;
			$day   = $this->day;
			
			if ($year && $month && $day) {
				$start_publish = $year.'-'.$month.'-'.$day;
				$stop_publish = $year.'-'.$month.'-'.$day;
				$registerby_date = $year.'-'.$month.'-'.$day;
			} else {
				$offset = $this->offset;
				
				$start_publish = strftime( "%Y-%m-%d", time()+($offset*60*60) ); //date( "Y-m-d" );
				$stop_publish = strftime( "%Y-%m-%d", time()+($offset*60*60) );  //date( "Y-m-d" );
				$registerby_date = strftime( "%Y-%m-%d", time()+($offset*60*60) );  //date( "Y-m-d" );
			}
			//$row = new EventsEvent( $database );
			
			// If user hits refresh, try to maintain event form state
			$row->bind( $_POST );
			$row->reccurday_month = -1;
			$row->reccurday_week = -1;
			$row->reccurday_year = -1;
			$row->created_by_alias = $juser->get('username');
			$row->created_by = $juser->get('id');
			$row->reccurtype = 0;
			
			$start_time = "08:00";
			$end_time = "17:00";
			$registerby_time = "08:00";
			
			$lists = '';
		}
		
		// Get custom fields
		$fields = $this->config->getCfg('fields');
		if (!empty($fields)) {
			for ($i=0, $n=count( $fields ); $i < $n; $i++) 
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], $this->parseTag($row->content, $fields[$i][0]));
				
				// Clean the original text of any matches
				$row->content = str_replace('<ef:'.$fields[$i][0].'>'.end($fields[$i]).'</ef:'.$fields[$i][0].'>','',$row->content);
			}
			$row->content = trim($row->content);
		}
		
		list($start_hrs, $start_mins) = explode(':',$start_time);
		list($end_hrs, $end_mins) = explode(':',$end_time);
		list($registerby_hrs, $registerby_mins) = explode(':',$registerby_time);
		$start_pm = false;
		$end_pm = false;
		$registerby_pm = false;
		if ($this->config->getCfg('calUseStdTime') == 'YES') { 
			$start_hrs = intval($start_hrs);
			if ($start_hrs >= 12) $start_pm = true;
			if ($start_hrs > 12) $start_hrs -= 12;
			else if ($start_hrs == 0) $start_hrs = 12;
			if (strlen($start_mins) == 1) $start_mins = '0'.$start_mins;
			$start_time = $start_hrs .":". $start_mins;
			
			$end_hrs = intval($end_hrs);
			if ($end_hrs >= 12) $end_pm = true;
			if ($end_hrs > 12) $end_hrs -= 12;
			else if ($end_hrs == 0) $end_hrs = 12;
			
			$registerby_hrs = intval($registerby_hrs);
			if ($registerby_hrs >= 12) $registerby_pm = true;
			if ($registerby_hrs > 12) $registerby_hrs -= 12;
			else if ($registerby_hrs == 0) $registerby_hrs = 12;
			if (strlen($registerby_mins) == 1) $registerby_mins = '0'.$registerby_mins;
			$registerby_time = $registerby_hrs .":". $registerby_mins;
		}
		if (strlen($start_mins) == 1) $start_mins = '0'.$start_mins;
		if (strlen($start_hrs) == 1) $start_hrs = '0'.$start_hrs;
		$start_time = $start_hrs .':'. $start_mins;
		
		if (strlen($end_mins) == 1) $end_mins = '0'.$end_mins;
		if (strlen($end_hrs) == 1) $end_hrs = '0'.$end_hrs;
		$end_time = $end_hrs .':'. $end_mins;
		
		if (strlen($registerby_mins) == 1) $registerby_mins = '0'.$registerby_mins;
		if (strlen($registerby_hrs) == 1) $registerby_hrs = '0'.$registerby_hrs;
		$registerby_time = $registerby_hrs .':'. $registerby_mins;
		
		$times = array();
		$times['start_publish'] = $start_publish;
		$times['start_time'] = $start_time;
		$times['start_pm'] = $start_pm;
		
		$times['stop_publish'] = $stop_publish;
		$times['end_time'] = $end_time;
		$times['end_pm'] = $end_pm;
		
		$times['registerby_date'] = $registerby_date;
		$times['registerby_time'] = $registerby_time;
		$times['registerby_pm'] = $registerby_pm;
		
		// Get tags on this event
		$rt = new EventsTags( $database );
		$lists['tags'] = $rt->get_tag_string($row->id, 0, 0, NULL, 0, 1);
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$p = 'index.php?option='.$this->_option.a.'task='.$this->_task;
		if ($row->id) {
			$p .= a.'id='.$row->id;
		}
		$pathway->addItem( JText::_(strtoupper($this->_task)), $p );
		if ($row->id) {
			$pathway->addItem( stripslashes($row->title), 'index.php?option='.$this->_option.a.'task=details'.a.'id='.$row->id );
		}
		
		$admin = $this->authorize();
		
		// Output HTML
		EventsHtml::edit( $row, $fields, $times, $lists, $this->_option, $this->gid, $this->_task, $this->config, $admin, $this->_error );
	}

	//----------------------------------------------------------
	// Actions
	//----------------------------------------------------------
	
	protected function delete()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		// Ensure we have an ID to work with
		if (!$id) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		// Load event object
		$event = new EventsEvent( $database );
		$event->load( $id );
		
		if (!$this->authorize($event->created_by)) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			$this->_message = JText::_('EVENTS_CAL_LANG_NOPERMISSION');
			return;
		}
		
		// Delete the event
		$event->delete( $id );
		
		// Delete any associated pages 
		$ep = new EventsPage( $database );
		$ep->deletePages( $id );
		
		// Delete any associated respondents
		$ep = new EventsRespondent( array() );
		$er->deleteRespondents( $id );
		
		// Delete tags on this event
		$rt = new EventsTags( $database );
		$rt->remove_all_tags($id);
		
		// Load the event's category and update the count
		$category = new EventsCategory( $database );
		$category->updateCount( $event->catid );
		
		// Get the HUB configuration
		$xhub =& XFactory::getHub();
		$jconfig =& JFactory::getConfig();
		
		// E-mail subject line
		$subject  = '['.$jconfig->getValue('config.sitename').' '.JText::_('EVENTS').'] - '.JText::_('EVENT_DELETED');
		
		// Build the message to be e-mailed
		$message  = ''.r.n;
		$message .= JText::sprintf('EVENTS_CAL_LANG_ACT_DELETED_BY', $juser->get('name'), $juser->get('login'));
		$message .= ''.r.n;
		$message .= ''.r.n;
		$message .= JText::_('EVENTS_CAL_LANG_EVENT_TITLE').': '.stripslashes($event->title).r.n;
		$message .= JText::_('EVENTS_CAL_LANG_EVENT_DESCRIPTION').': '.stripslashes($event->content).r.n;
		$message .= ''.r.n;
		
		// Send the e-mail
		$this->sendMail($jconfig->getValue('config.sitename'), $xhub->getCfg('hubMonitorEmail'), $subject, $message);
		
		// Go back to the default front page
		$this->_redirect = JRoute::_('index.php?option='.$this->_option);
		$this->_message = JText::_('EVENTS_CAL_LANG_ACT_DELETED');
	}

	//-----------

	protected function save() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		//$access = $this->access;
		//$is_event_editor = $this->is_event_editor;
		$offset = $this->offset;
		
		// Incoming
		$start_time = JRequest::getVar( 'start_time', '08:00', 'post' );
		$start_pm   = JRequest::getInt( 'start_pm', 0, 'post' );
		$end_time   = JRequest::getVar( 'end_time', '17:00', 'post' );
		$end_pm     = JRequest::getInt( 'end_pm', 0, 'post' );
		//$registerby_time = JRequest::getVar( 'registerby_time', '00:00', 'post' );
		//$registerby_pm   = JRequest::getInt( 'registerby_pm', 0, 'post' );
		
		$reccurweekdays = JRequest::getVar( 'reccurweekdays', array(), 'post' );
		$reccurweeks    = JRequest::getVar( 'reccurweeks', array(), 'post' );
		$reccurday_week = JRequest::getVar( 'reccurday_week', '', 'post' );
		$reccurday_year = JRequest::getVar( 'reccurday_year', '', 'post' );

		// Bind the posted data to an event object
		$row = new EventsEvent( $database );
		if (!$row->bind( $_POST )) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}

		// New entry or existing?
		if ($row->id) {
			// Existing - update modified info
			$row->modified = strftime( "%Y-%m-%d %H:%M:%S", time()+($offset*60*60));
			if ($juser->get('id')) {
				$row->modified_by = $juser->get('id');
			}
		} else {
			// New - set created info
			$row->created = strftime( "%Y-%m-%d %H:%M:%S", time()+($offset*60*60));
			if ($juser->get('id')) {
				$row->created_by = $juser->get('id');
			}
		}
		
		// Set some fields and do some cleanup work
		if (is_null($row->useCatColor)) {
			$row->useCatColor = 0;
		}
		if ($row->catid) {
			$row->catid = intval( $row->catid );
		}
		
		$row->title = htmlentities($row->title);

		//$row->content = JRequest::getVar( 'econtent', '', 'post' );
		$row->content = $_POST['econtent'];
		$row->content = $this->clean($row->content);
	
		// Get the custom fields defined in the events configuration
		//$fields = JRequest::getVar( 'fields', array(), 'post' );
		if (isset($_POST['fields'])) {
			$fields = $_POST['fields'];
			$fields = array_map('trim',$fields);

			// Wrap up the content of the field and attach it to the event content
			$fs = $this->config->fields;
			foreach ($fields as $param=>$value)
			{
				if (trim($value) != '') {
					$row->content .= '<ef:'.$param.'>'.$this->clean($value).'</ef:'.$param.'>';
				} else {
					foreach ($fs as $f) 
					{
						if ($f[0] == $param && end($f) == 1) {
							echo EventsHtml::alert( JText::sprintf('EVENTS_REQUIRED_FIELD_CHECK', $f[1]) );
							exit();
						}
					}
				}
			}
		}
	
		// Clean adresse
		$row->adresse_info = $this->clean($row->adresse_info);
	
		// Clean contact
		$row->contact_info = $this->clean($row->contact_info);
	
		// Clean extra
		$row->extra_info = $this->clean($row->extra_info);
	
		// Prepend http:// to URLs without it
		if ($row->extra_info != NULL) {
			if ( (substr($row->extra_info,0,7) != 'http://') && (substr($row->extra_info,0,8) != 'https://')) {
				$row->extra_info = 'http://'.$row->extra_info;
			}
		}
		
		$row->created_by_alias = htmlentities($row->created_by_alias);

		// Reformat the time into 24hr format if necessary
		if ($this->config->getCfg('calUseStdTime') =='YES') {
			list($hrs,$mins) = explode(':', $start_time);
			$hrs = intval($hrs);
			$mins = intval($mins);
			if ($hrs != 12 && $start_pm) $hrs += 12;
			else if ($hrs == 12 && !$start_pm) $hrs = 0;
			if ($hrs < 10) $hrs = '0'.$hrs;
			if ($mins < 10) $mins = '0'.$mins;
			$start_time = $hrs.':'.$mins;
		
			list($hrs,$mins) = explode(':', $end_time);
			$hrs = intval($hrs);
			$mins = intval($mins);
			if ($hrs!= 12 && $end_pm) $hrs += 12;
			else if ($hrs == 12 && !$end_pm) $hrs = 0;
			if ($hrs < 10) $hrs = '0'.$hrs;
			if ($mins < 10) $mins = '0'.$mins;
			$end_time = $hrs.':'.$mins;
			
			/*list($hrs,$mins) = explode(':', $registerby_time);
			$hrs = intval($hrs);
			$mins = intval($mins);
			if ($hrs!= 12 && $registerby_pm) $hrs += 12;
			else if ($hrs == 12 && !$registerby_pm) $hrs = 0;
			if ($hrs < 10) $hrs = '0'.$hrs;
			if ($mins < 10) $mins = '0'.$mins;
			$registerby_time = $hrs.':'.$mins;*/
		}
		
		if ($row->publish_up) {
			$publishtime = $row->publish_up.' '.$start_time.':00';
			$row->publish_up = strftime("%Y-%m-%d %H:%M:%S",strtotime($publishtime));
		} else {
			$row->publish_up = strftime( "%Y-%m-%d 00:00:00", time()+($offset*60*60));
		}
	
		if ($row->publish_down) {
			$publishtime = $row->publish_down.' '.$end_time.':00';
			$row->publish_down = strftime("%Y-%m-%d %H:%M:%S",strtotime($publishtime));
		} else {
			$row->publish_down = strftime( "%Y-%m-%d 23:59:59", time()+($offset*60*60));
		}
		
		/*if ($row->registerby) {
			$publishtime = $row->registerby.' '.$registerby_time.':00';
			$row->registerby = strftime("%Y-%m-%d %H:%M:%S",strtotime($publishtime));
		} else {
			$row->registerby = strftime( "%Y-%m-%d 23:59:59", time()+($offset*60*60));
		}*/
        
		if ($row->publish_up <> $row->publish_down) {
			$row->reccurtype = intval( $row->reccurtype );
		} else {
			$row->reccurtype = 0;
		}
	
		if ($row->reccurtype == 0) {
			$row->reccurday = '';
		} elseif ($row->reccurtype == 1) {
			$row->reccurday =  $reccurday_week;
		} elseif ($row->reccurtype == 2) {
			$row->reccurday = '';
		} elseif ($row->reccurtype == 3) {
			$row->reccurday = $reccurday_month;
		} elseif ($row->reccurtype == 4) {
			$row->reccurday = '';
		} elseif ($row->reccurtype == 5) {
			$row->reccurday = $reccurday_year;
		}	
		
		// Reccur week days
		if (empty($reccurweekdays)) {		
			$weekdays = '';
		} else {
			$weekdays = implode( '|', $reccurweekdays );
		}
		$row->reccurweekdays = $weekdays;
        
		// Reccur viewable weeks
		$reccurweekss = JRequest::getVar( 'reccurweekss', '', 'post' );
		$reccurweeks = array();
		if ($reccurweekss) {
			$reccurweeks[] = $reccurweekss;
		}
		if (empty($reccurweeks)) {
			$weekweeks = '';
		} else {
			$weekweeks = implode( '|', $reccurweeks );
		}
		$row->reccurweeks = $weekweeks;
	
		// Always unpublish if no Publisher otherwise publish automatically
		if ($this->config->getCfg('adminlevel')) {
			$row->state = 0;
		} else {
			$row->state = 1;
		}

		$row->state = 1;
		$row->mask = 0;
	
		if (!$row->check()) {
			// Set the error message
			$this->_error = $row->getError();
			// Fall through to the edit view
			$this->edit($row);
			return;
		}
		if (!$row->store()) {
			// Set the error message
			$this->_error = $row->getError();
			// Fall through to the edit view
			$this->edit($row);
			return;
		}
		$row->checkin();
		
		// Incoming tags
		$tags = JRequest::getVar( 'tags', '', 'post' );
		
		// Save the tags
		$rt = new EventsTags( $database );
		$rt->tag_object($juser->get('id'), $row->id, $tags, 1, 0);
		
		$xhub =& XFactory::getHub();
		
		$jconfig =& JFactory::getConfig();
		
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$this->_option.'&task=details&id='.$row->id);
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		$sef = $juri->base().$sef;
		
		// Build the message to be e-mailed
		if ($state == 'add') {
			$msg = JText::_('EVENTS_CAL_LANG_ACT_ADDED');

			$subject  = '['.$jconfig->getValue('config.sitename').' '.JText::_('EVENTS_CAL_LANG_CAL_TITLE').'] - '.JText::_('EVENTS_CAL_LANG_MAIL_ADDED');
			$message  = ''.r.n;
			$message .= JText::sprintf('EVENTS_CAL_LANG_ACT_ADDED_BY', $juser->get('name'), $juser->get('username'));
		} else {
			$msg = JText::_('EVENTS_CAL_LANG_ACT_MODIFIED');
		
			$subject  = '['.$jconfig->getValue('config.sitename').' '.JText::_('EVENTS_CAL_LANG_CAL_TITLE').'] - '.JText::_('EVENTS_CAL_LANG_MAIL_ADDED');
			$message  = ''.r.n;
			$message .= JText::sprintf('EVENTS_CAL_LANG_ACT_MODIFIED_BY', $juser->get('name'), $juser->get('username'));
		}
		$message .= ''.r.n;
		$message .= ''.r.n;
		$message .= JText::_('EVENTS_CAL_LANG_EVENT_TITLE').': '.stripslashes($row->title).r.n.r.n;
		$message .= JText::_('EVENTS_CAL_LANG_EVENT_DESCRIPTION').': '.stripslashes($row->content).r.n;
		$message .= ''.r.n;
		$message .= $sef;
		
		// Send the e-mail
		$this->sendMail($jconfig->getValue('config.sitename'), $xhub->getCfg('hubMonitorEmail'), $subject, $message);
		
		// Redirect to the details page for the event we just created
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=details&id='.$row->id);
		$this->_message = $msg;
	}
	
	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	private function getCategories() 
	{
		$database =& JFactory::getDBO();
		
		$sql = "SELECT * FROM #__categories WHERE section='".$this->_option."' AND published = '1' ORDER BY title DESC";

		$database->setQuery($sql);
		$cats = $database->loadObjectList();
		
		$c = array();
		foreach ($cats as $cat) 
		{
			$c[$cat->id] = $cat->title;
		}
		
		return $c;
	}
	
	//-----------

	public function parseTag($text, $tag)
	{
		preg_match("#<ef:".$tag.">(.*?)</ef:".$tag.">#s", $text, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<ef:'.$tag.'>','',$match);
			$match = str_replace('</ef:'.$tag.'>','',$match);
		} else {
			$match = '';
		}
		return $match;
	}
	
	//-----------

	private function clean($string) 
	{
		if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);
		}
		
		// strip out any KL_PHP, script, style, HTML comments
		$string = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $string );
		$string = preg_replace( "'<head[^>]*?>.*?</head>'si", '', $string);
		$string = preg_replace( "'<body[^>]*?>.*?</body>'si", '', $string);
		$string = preg_replace( "'<style[^>]*>.*?</style>'si", '', $string );
		$string = preg_replace( "'<script[^>]*>.*?</script>'si", '', $string );
		$string = preg_replace( '/<!--.+?-->/', '', $string );
		
		$string = str_replace(array("&amp;","&lt;","&gt;"),array("&amp;amp;","&amp;lt;","&amp;gt;",),$string);
		// fix &entitiy\n;
		
		$string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u',"$1;",$string);
		$string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu',"$1$2;",$string);
		$string = html_entity_decode($string, ENT_COMPAT, "UTF-8");
		
		// remove any attribute starting with "on" or xmlns
		$string = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu',"$1>",$string);
		// remove javascript: and vbscript: protocol
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu','$1=$2nojavascript...',$string);
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu','$1=$2novbscript...',$string);
		//<span style="width: expression(alert('Ping!'));"></span> 
		// only works in ie...
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU',"$1>",$string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU',"$1>",$string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu',"$1>",$string);
		//remove namespaced elements (we do not need them...)
		$string = preg_replace('#</*\w+:\w[^>]*>#i',"",$string);
		//remove really unwanted tags
		do {
			$oldstring = $string;
			$string = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$string);
		} while ($oldstring != $string);
	
		return $string;
	}

	//-----------

	private function sendMail($name, $email, $subject, $message) 
	{
		$name .= ' '.JText::_('ADMINISTRATOR');
		
		$headers  = "";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "From: ".$name." <".$email.">\r\n";
		$headers .= "Reply-To: <".$email.">\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-MSMail-Priority: Low\r\n";
		$headers .= "X-Mailer: Joomla 1.5\r\n";

	    @mail($email, $subject, $message, $headers);
	}
	
	//-----------

	private function authorize($id='')
	{
		$juser =& JFactory::getUser();
		
		// Check if they are logged in
		if ($juser->get('guest')) {
			return false;
		}
	
		// Check if they're a site admin from Joomla
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}
	
		// Check against events configuration
		if (!$this->config->getCfg('adminlevel')) {
			if ($id && $id == $juser->get('id')) {
				return true;
			}
		}	
		
		return false;
	}
}
?>
