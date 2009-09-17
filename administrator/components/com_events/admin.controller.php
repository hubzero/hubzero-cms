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

class EventsController
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
		if(isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	//-----------
	
	private function getTask()
	{
		$task = JRequest::getString('task', '');
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		$database =& JFactory::getDBO();
		$config = new EventsConfigs( $database );
		$config->load();
		$this->config = $config;
		
		$tables = $database->getTableList();
		$table = $database->_table_prefix.'respondent_race_rel';
		if (!in_array($table,$tables)) {
			$database->setQuery( "CREATE TABLE `#__events_respondent_race_rel` (
			  `respondent_id` int(11) default NULL,
			  `race` varchar(255) default NULL,
			  `tribal_affiliation` varchar(255) default NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
		}
		
		define( '_CAL_CONF_STARDAY', $config->getCfg('starday'));
		define( '_CAL_CONF_DEFCOLOR', $config->getCfg('navbarcolor'));
		
		switch ( $this->getTask() ) 
		{
			// Category management
			case 'cats':         $this->cats();         break;
			case 'newcat':       $this->editcat();      break;
			case 'editcat':      $this->editcat();      break;
			case 'savecat':      $this->savecat();      break;
			case 'removecat':    $this->removecat();    break;
			case 'cancelcat':    $this->cancelcat();    break;
			case 'publishcat':   $this->publishcat();   break;
			case 'unpublishcat': $this->unpublishcat(); break;
			case 'orderup':      $this->orderup();      break;
			case 'orderdown':    $this->orderdown();    break;
			
			case 'make_announcement': $this->setType(); break;
			case 'make_event':   $this->setType();      break;
			
			// Event management
			case 'remove':       $this->remove();       break;
			case 'add':          $this->edit();         break;
			case 'edit':         $this->edit();         break;
			case 'save':         $this->save();         break;
			case 'publish':      $this->publish();      break;
			case 'unpublish':    $this->unpublish();    break;
			case 'cancel':       $this->cancel();       break;
			case 'events':       $this->events();       break;
			
			// Configuration
			case 'configure':    $this->configure();    break;
			case 'saveconfig':   $this->saveconfig();   break;
			
			// Respondents
			case 'viewrespondent':    $this->viewrespondent();    break;
			case 'viewlist':   $this->viewlist();   break;
			case 'downloadlist':   $this->downloadlist();   break;
			case 'removerespondent':   $this->removerespondent();   break;
			
			// Pages
			case 'pages':    $this->pages();    break;
			case 'addpage':   $this->addpage();   break;
			case 'editpage':   $this->editpage();   break;
			case 'savepage':   $this->savepage();   break;
			case 'removepage':   $this->removepage();   break;
			case 'orderuppage':   $this->orderuppage();   break;
			case 'orderdownpage':   $this->orderdownpage();   break;
			case 'reorderpage':   $this->reorderpage();   break;
			case 'cancelpage':   $this->cancelpage();   break;

			default: $this->events(); break;
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
	// Views
	//----------------------------------------------------------
	
	protected function events() 
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();

		// Get configuration
		$config = JFactory::getConfig();
		
		// Incoming
		$filters = array();
		$filters['limit']  = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start']  = JRequest::getVar('limitstart', 0, '', 'int');
		$filters['search'] = urldecode(JRequest::getString('search'));
		$filters['catid']  = JRequest::getVar('catid', 0, '', 'int');
		
		$ee = new EventsEvent( $database );
		
		// Get a record count
		$total = $ee->getCount( $filters );
		
		// Get records
		$rows = $ee->getRecords( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Get list of categories
		$categories[] = JHTML::_('select.option', '0', '- '.JText::_('EVENTS_CAL_LANG_EVENT_ALLCAT'), 'value', 'text' );
		$database->setQuery( "SELECT id AS value, title AS text FROM #__categories WHERE section='$this->_option' ORDER BY ordering" );
		$categories = array_merge( $categories, $database->loadObjectList() );

		$clist = JHTML::_('select.genericlist', $categories, 'catid', 'class="inputbox"','value', 'text', $filters['catid'], false, false );
		
		// Output HTML
		EventsHtml::events( $rows, $clist, $filters['search'], $pageNav, $this->_option );
	}
	
	//-----------
	
	protected function edit() 
	{
		$database =& JFactory::getDBO();
		
		$config = JFactory::getConfig();
		$offset = $config->getValue('config.offset');

	    $juser =& JFactory::getUser();
		
		// We need at least one category before we can proceed
		$cat = new EventsCategory( $database );
		if ($cat->getCategoryCount( $this->_option ) < 1) {
			echo EventsHtml::error( JText::_('EVENTS_LANG_NEED_CATEGORY') );
			return;
		}
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		// Load the event object
		$row = new EventsEvent( $database );
		$row->load( $id );

		// Fail if checked out not by 'me'
		if ($row->checked_out && $row->checked_out <> $juser->get('id')) {
			$this->_redirect = 'index.php?option='.$option;
			$this->_message = JText::_('EVENTS_CAL_LANG_WARN_CHECKEDOUT');
		}

		$document =& JFactory::getDocument();
		$document->addStyleSheet('..'.DS.'components'.DS.$this->_option.DS.'calendar.css');

		if ($row->id) {
			$row->checkout( $juser->get('id') );

			if (trim( $row->images )) {
				$row->images = explode( "\n", $row->images );
			} else {
				$row->images = array();
			}

			if (trim( $row->publish_down ) == '0000-00-00 00:00:00') {
				$row->publish_down = JText::_('EVENTS_CAL_LANG_NEVER');
			}
			
			$event_up = new EventsDate( $row->publish_up );
			$start_publish = sprintf( "%4d-%02d-%02d",$event_up->year,$event_up->month,$event_up->day);
			$start_time = $event_up->hour .':'. $event_up->minute;
			
			$event_down = new EventsDate( $row->publish_down );
			$stop_publish = sprintf( "%4d-%02d-%02d",$event_down->year,$event_down->month,$event_down->day);
			$end_time = $event_down->hour .':'. $event_down->minute;

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
		} else {
			$row->state = 0;
			$row->images = array();
			$start_publish = strftime( "%Y-%m-%d", time()+($offset*60*60) );
			$stop_publish = strftime( "%Y-%m-%d", time()+($offset*60*60) );
			$start_time = "08:00";
	        $end_time = "17:00";
			$row->color_bar = EventsHtml::getColorBar(null,'');

			$row->reccurday_month = -1;
			$row->reccurday_week = -1;
			$row->reccurday_year = -1;
		}

		// Get list of groups
		$database->setQuery( "SELECT id AS value, name AS text FROM #__groups ORDER BY id" );
		$groups = $database->loadObjectList();

		// Build the html select list
		$glist = JHTML::_('select.genericlist', $groups, 'access', 'class="inputbox"','value', 'text', intval( $row->access ), false, false );

		$fields = $this->config->getCfg('fields');
		if (!empty($fields)) {
			for ($i=0, $n=count( $fields ); $i < $n; $i++) 
			{
				// explore the text and pull out all matches
				array_push($fields[$i], $this->parseTag($row->content, $fields[$i][0]));
				// clean the original text of any matches
				$row->content = str_replace('<ef:'.$fields[$i][0].'>'.end($fields[$i]).'</ef:'.$fields[$i][0].'>','',$row->content);
			}
			$row->content = trim($row->content);
		}

		list($start_hrs, $start_mins) = explode(':',$start_time);
		list($end_hrs, $end_mins) = explode(':',$end_time);
		$start_pm = false;
		$end_pm = false;
		if ($this->config->getCfg('calUseStdTime') == 'YES') { 
			$start_hrs = intval($start_hrs);
			if ($start_hrs >= 12) $start_pm = true;
			if ($start_hrs > 12) $start_hrs -= 12;
			else if ($start_hrs == 0) $start_hrs = 12;

			$end_hrs = intval($end_hrs);
			if ($end_hrs >= 12) $end_pm = true;
			if ($end_hrs > 12) $end_hrs -= 12;
			else if ($end_hrs == 0) $end_hrs = 12;
		}
		if (strlen($start_mins) == 1) $start_mins = '0'.$start_mins;
		if (strlen($start_hrs) == 1) $start_hrs = '0'.$start_hrs;
		$start_time = $start_hrs .':'. $start_mins;
		if (strlen($end_mins) == 1) $end_mins = '0'.$end_mins;
		if (strlen($end_hrs) == 1) $end_hrs = '0'.$end_hrs;
		$end_time = $end_hrs .':'. $end_mins;

		$times = array();
		$times['start_publish'] = $start_publish;
		$times['start_time'] = $start_time;
		$times['start_pm'] = $start_pm;
		$times['stop_publish'] = $stop_publish;
		$times['end_time'] = $end_time;
		$times['end_pm'] = $end_pm;
		
		// Get tags on this event
		$rt = new EventsTags( $database );
		$tags = $rt->get_tag_string($row->id, 0, 0, NULL, 0, 1);
		
		// Output HTML
		EventsHtml::edit( $row, $this->config, $fields, $glist, $times, $juser->get('id'), $this->_option, $tags );
	}
	
	//-----------
	
	private function parseTag($text, $tag)
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
	
	protected function cancel() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Check in the event
		$event = new EventsEvent( $database );
		$event->load( $id );
		$event->checkin();
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}
	
	//-----------
	
	protected function save() 
	{
		$database =& JFactory::getDBO();
		
		$config = JFactory::getConfig();
		$offset = $config->getValue('config.offset');

		$juser =& JFactory::getUser();
		
		// Incoming
		$start_time = JRequest::getVar( 'start_time', '08:00', 'post' );
		$start_pm   = JRequest::getInt( 'start_pm', 0, 'post' );
		$end_time   = JRequest::getVar( 'end_time', '17:00', 'post' );
		$end_pm     = JRequest::getInt( 'end_pm', 0, 'post' );

		$reccurweekdays  = JRequest::getVar( 'reccurweekdays', array(), 'post' );
		$reccurweeks     = JRequest::getVar( 'reccurweeks', array(), 'post' );
		$reccurday_week  = JRequest::getVar( 'reccurday_week', '', 'post' );
		$reccurday_month = JRequest::getVar( 'reccurday_month', '', 'post' );
		$reccurday_year  = JRequest::getVar( 'reccurday_year', '', 'post' );

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
			if (!$juser->get('guest')) {
				$row->modified_by = $juser->get('id');
			}
		} else {
			// New - set created info
			$row->created = strftime( "%Y-%m-%d %H:%M:%S", time()+($offset*60*60));
			if (!$juser->get('guest')) {
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
		$fields = JRequest::getVar( 'fields', array(), 'post' );
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

		// reformat the time into 24hr format if necessary
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
		}

		if ($row->publish_up) {
			$publishtime = $row->publish_up." ".$start_time.":00";
			$row->publish_up = strftime("%Y-%m-%d %H:%M:%S",strtotime($publishtime));
		} else {
			$row->publish_up = strftime( "%Y-%m-%d 00:00:00", time()+($offset*60*60));
		}
		
		if ($row->publish_down) {
			$publishtime = $row->publish_down." ".$end_time.":00";
			$row->publish_down = strftime("%Y-%m-%d %H:%M:%S",strtotime($publishtime));
		} else {
			$row->publish_down = strftime( "%Y-%m-%d 23:59:59", time()+($offset*60*60));
		}
		
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
		if (empty($reccurweekdays) == '') {		
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

		// If this is a new event, publish it, otherwise retain its state
		if (!$row->id) {
			$row->state = 1;
		}

		$row->mask = 0;

		// Get parameters
		$params = JRequest::getVar( 'params', '', 'post' );
		if (is_array( $params )) {
			$txt = array();
			foreach ( $params as $k=>$v) 
			{
				$txt[] = "$k=$v";
			}
			$row->params = implode( "\n", $txt );
		}

		if (!$row->check()) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}
		if (!$row->store()) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}
		$row->checkin();
		
		// Incoming tags
		$tags = JRequest::getVar( 'tags', '', 'post' );
		
		// Save the tags
		$rt = new EventsTags( $database );
		$rt->tag_object($juser->get('id'), $row->id, $tags, 1, 0);
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('EVENTS_CAL_LANG_SAVED');
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
	
	protected function publish() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}
		
		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Instantiate an event object
		$event = new EventsEvent( $database );
		
		// Loop through the IDs and publish the event
		foreach ($ids as $id) 
		{	
			$event->publish( $id );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('EVENTS_CAL_LANG_PUBLISHED');
	}
	
	//-----------
	
	protected function unpublish() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}
		
		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Instantiate an event object
		$event = new EventsEvent( $database );
		
		// Loop through the IDs and unpublish the event
		foreach ($ids as $id) 
		{	
			$event->unpublish( $id );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('EVENTS_CAL_LANG_UNPUBLISHED');
	}

	//-----------
	
	protected function setType() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		switch ($this->_task) 
		{
			case 'make_announcement':
				$v = 1;
			break;
			case 'make_event':
			default:
				$v = 0;
			break;
		}
		
		// Loop through the IDs and publish the event
		foreach ($ids as $id) 
		{	
			// Instantiate an event object
			$event = new EventsEvent( $database );
			$event->load( $id );
			$event->announcement = $v;
			if (!$event->store()) {
				echo 'Error: '.$event->getError();
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------
	
	protected function remove() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}
		
		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Instantiate an event object
		$event = new EventsEvent( $database );
		
		// Instantiate an event tags object
		$rt = new EventsTags( $database );
		
		// Instantiate a page object
		$ep = new EventsPage( $database );
		
		// Instantiate a respondent object
		$er = new EventsRespondent( array() );
		
		
		// Loop through the IDs and unpublish the event
		foreach ($ids as $id) 
		{
			// Delete tags on this event
			$rt->remove_all_tags($id);
			
			// Delete the event
			$event->delete( $id );
			
			// Delete any associated pages 
			$ep->deletePages( $id );
			
			// Delete any associated respondents
			$er->deleteRespondents( $id );
		}
	
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('EVENTS_CAL_LANG_REMOVED');
	}
	
	//----------------------------------------------------------
	// Configuration
	//----------------------------------------------------------
	
	protected function configure() 
	{
		// Output HTML
		EventsHtml::configure($this->_option, $this->config);
	}
	
	//-----------
	
	protected function saveconfig() 
	{
		$database =& JFactory::getDBO();
		
		// Get the configuration
		$config = JRequest::getVar('config', array(), 'post');
		foreach ($config as $n=>$v) 
		{
			$box = array();
			$box['param'] = $n;
			$box['value'] = $v;
			
			$row = new EventsConfig( $database );
			if (!$row->bind( $box )) {
				echo EventsHtml::alert( $row->getError() );
				exit();
			}
			// Check content
			if (!$row->check()) {
				echo EventsHtml::alert( $row->getError() );
				exit();
			}
			// Store content
			if (!$row->store()) {
				echo EventsHtml::alert( $row->getError().' '.$row->param.' '.$row->value );
				exit();
			}
		}
		
		// Get the custom fields
		$fields = JRequest::getVar('fields', array(), 'post');

		$box = array();
		$box['param'] = 'fields';
		$box['value'] = '';

		if (is_array($fields)) {
			$txta = array();
			foreach ($fields as $val)
			{
				if ($val['title']) {
					$k = $this->normalize(trim($val['title']));
					$t = str_replace('=','-',$val['title']);
					$j = (isset($val['type'])) ? $val['type'] : 'text';
					$x = (isset($val['required'])) ? $val['required'] : '0';
					$z = (isset($val['show'])) ? $val['show'] : '0';
					$txta[] = $k.'='.$t.'='.$j.'='.$x.'='.$z;
				}
			}
			$field = implode( "\n", $txta );
		}
		$box['value'] = $field;
		
		$row = new EventsConfig( $database );
		if (!$row->bind( $box )) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}
		// Check content
		if (!$row->check()) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}
		// Store content
		if (!$row->store()) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('EVENTS_CAL_LANG_CONFIG_SAVED');
	}
	
	//-----------
	
	private function normalize($txt) 
	{
		// Strip any non-alphanumeric characters
		$normalized_valid_chars = 'a-zA-Z0-9';
		$normalized = preg_replace("/[^$normalized_valid_chars]/", "", $txt);
		return strtolower($normalized);
	}
	
	//----------------------------------------------------------
	// Categories
	//----------------------------------------------------------

	protected function cats() 
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		$section = $this->_option;

		$config = JFactory::getConfig();
		
		// Incoming
		$limit = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		$section_name = '';
		if (intval( $section ) > 0) {
			$table = 'content';

			$database->setQuery( "SELECT name FROM #__sections WHERE id='$section'" );
			$section_name = $database->loadResult();
			echo $database->getErrorMsg();
			$section_name .= ' Section';
		} else if (strpos( $section, 'com_' ) === 0) {
			$table = substr( $section, 4 );

			$database->setQuery( "SELECT name FROM #__components WHERE link='option=$section'" );
			$section_name = $database->loadResult();
			echo $database->getErrorMsg();
		} else {
			$table = $section;
		}

		// Get the total number of records
		$database->setQuery( "SELECT count(*) FROM #__categories WHERE section='$section'" );
		$total = $database->loadResult();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}

		// dmcd may 22/04  added #__events_categories table to fetch category color property
		$database->setQuery( "SELECT  c.*, g.name AS groupname, u.name AS editor, cc.color AS color, "
		. "COUNT(DISTINCT s2.checked_out) AS checked_out, COUNT(DISTINCT s1.id) AS num"
		. "\nFROM #__categories AS c"
		. "\nLEFT JOIN #__users AS u ON u.id = c.checked_out"
		. "\nLEFT JOIN #__groups AS g ON g.id = c.access"
		. "\nLEFT JOIN #__$table AS s1 ON s1.catid = c.id"
		. "\nLEFT JOIN #__$table AS s2 ON s2.catid = c.id AND s2.checked_out > 0"
		. "\nLEFT JOIN #__${table}_categories AS cc ON cc.id = c.id"
		. "\nWHERE section='$section'"
		. "\nGROUP BY c.id"
		. "\nORDER BY c.ordering, c.name"
		. "\nLIMIT $limitstart,$limit"
		);

		// Execute query
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit );
		
		// Output HTML
		EventsHtml::cats( $this->_option, $rows, $section, $section_name, $juser->get('id'), $pageNav );
	}

	//-----------

	protected function editcat() 
	{
		$database =& JFactory::getDBO();
	    $juser =& JFactory::getUser();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Load the category
		$row = new EventsCategory( $database );
		$row->load( $id );

		// Fail if checked out not by 'me'
		if ($row->checked_out && $row->checked_out <> $juser->get('id')) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
			$this->_message = JText::_('EVENTS_CAL_LANG_CATEGORY_CHECKEDOUT');
			return;
		}

		if ($row->id) {
			// Existing record
			$row->checkout( $juser->get('id') );
		} else {
			// New record
			$row->section = $this->_option;
			$row->color = '';
		}

		// Make order list
		$order = array();
		
		$max = intval( $row->getCategoryCount() ) + 1;
		for ($i=1; $i < $max; $i++) 
		{
			$order[] = JHTML::_('select.option', $i, $i, 'value', 'text' );
		}

		$ipos[] = JHTML::_('select.option', 'left', JText::_('left'), 'value', 'text' );
		$ipos[] = JHTML::_('select.option', 'right', JText::_('right'), 'value', 'text' );

		$iposlist = JHTML::_('select.genericlist', $ipos, 'image_position', 'class="inputbox" size="1"','value', 'text', $row->image_position ? $row->image_position : 'left', false, false );

		$imgFiles = $this->readDirectory( JPATH_ROOT.DS.'images'.DS.'stories' );
		$images = array( JHTML::_('select.option', '', JText::_('Select Image'), 'value', 'text') );
		foreach ($imgFiles as $file) 
		{
			if (eregi( "bmp|gif|jpg|png", $file )) {
				$images[] = JHTML::_('select.option', $file, $file, 'value', 'text' );
			}
		}

		$imagelist = JHTML::_('select.genericlist', $images, 'image', 'class="inputbox" size="1"'
		. " onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='../images/stories/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/M_images/blank.png'}\"",
		'value', 'text', $row->image, false, false );

		$orderlist = JHTML::_('select.genericlist', $order, 'ordering', 'class="inputbox" size="1"','value', 'text', $row->ordering, false, false );

		// Get list of groups
		$database->setQuery( "SELECT id AS value, name AS text FROM #__groups ORDER BY id" );
		$groups = $database->loadObjectList();

		$glist = JHTML::_('select.genericlist', $groups, 'access', 'class="inputbox" size="1"','value', 'text', intval( $row->access ), false, false );

		// Output HTML
		EventsHtml::editcat( $this->_option, $row, $imagelist, $iposlist, $orderlist, $glist, $this->_option );
	}

	//-----------

	private function readDirectory( $path, $filter='.', $recurse=false, $fullpath=false  )
	{
		$arr = array(null);

		// Get the files and folders
		jimport('joomla.filesystem.folder');
		$files		= JFolder::files($path, $filter, $recurse, $fullpath);
		$folders	= JFolder::folders($path, $filter, $recurse, $fullpath);
		// Merge files and folders into one array
		$arr = array_merge($files, $folders);
		// Sort them all
		asort($arr);
		return $arr;
	}

	//-----------

	protected function savecat() 
	{
		$database =& JFactory::getDBO();

		$row = new EventsCategory( $database );
		if (!$row->bind( $_POST )) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}
		if (!$row->check()) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}

		if (!$row->store()) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}
		$row->checkin();
		//$row->updateOrder( "section='$row->section'" );

		if ($oldtitle = JRequest::getVar('oldtitle', null, 'post')) {
			if ($oldtitle != $row->title) {
				$database->setQuery( "UPDATE #__menu SET name='$row->title' WHERE name='$oldtitle' AND type='content_category'" );
				$database->query();
			}
		}

		// Update Section Count
		if ($row->section != 'com_weblinks') {
			$database->setQuery( "UPDATE #__sections SET count=count+1 WHERE id = '$row->section'");
		}

		if (!$database->query()) {
			echo EventsHtml::alert( $database->getErrorMsg() );
			exit();
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
	}
	
	//-----------

	protected function cancelcat() 
	{
		$database =& JFactory::getDBO();

		// Checkin the category
		$row = new EventsCategory( $database );
		$row->bind( $_POST );
		$row->checkin();
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
	}
	
	//-----------
	
	protected function publishcat() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
			return;
		}
		
		// Instantiate a category object
		$event = new EventsCategory( $database );
		
		// Loop through the IDs and publish the category
		foreach ($ids as $id) 
		{	
			$event->publish( $id );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
		$this->_message = JText::_('EVENTS_CAL_LANG_CATEGORY_PUBLISHED');
	}
	
	//-----------
	
	protected function unpublishcat() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}
		
		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
			return;
		}
		
		// Instantiate a category object
		$event = new EventsCategory( $database );
		
		// Loop through the IDs and unpublish the category
		foreach ($ids as $id) 
		{	
			$event->unpublish( $id );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
		$this->_message = JText::_('EVENTS_CAL_LANG_CATEGORY_UNPUBLISHED');
	}
	
	//-----------
	
	protected function orderup() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Load the category, reorder, save
		$row = new EventsCategory( $database );
		$row->load( $id );
		$row->move( -1, "section='$row->section'" );
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
	}
	
	//-----------
	
	protected function orderdown() 
	{
		$database =& JFactory::getDBO();

		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Load the category, reorder, save
		$row = new EventsCategory( $database );
		$row->load( $id );
		$row->move( 1, "section='$row->section'" );
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
	}
	
	//-----------
	
	protected function removecat() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}
		
		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
			return;
		}

		$cids = array();
		if (count( $ids ) > 0) {
			// Loop through each category ID
			foreach ($ids as $id) 
			{
				// Load the category
				$cat = new EventsCategory( $database );
				$cat->load( $id );
				// Check its count of items in it
				if ($cat->count > 0) {
					// Category is NOT empty
					$cids[] = $cat->name;
				} else {
					// Empty category, go ahead and delete
					$cat->delete( $id );
				}
			}
		}

		if (count( $cids )) {
			$cids = implode( "\', \'", $cids );
			$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
			$this->_message = JText::sprintf('EVENTS_CAL_LANG_CATEGORY_NOTEMPTY', $cids);
			return;
		}
	
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
		$this->_message = JText::_('EVENTS_CAL_LANG_CATEGORY_REMOVED');
	}
	
	//----------------------------------------------------------
	//  Respondents
	//----------------------------------------------------------
	
	protected function viewrespondent()
	{
		EventsHtml::viewrespondent(new EventsRespondent(array('respondent_id' => JRequest::getInt('id', 0))));
	}
	
	//-----------

	protected function viewlist()
	{
		EventsHtml::viewlist($this->getRespondents(), $this->_option);
	}
	
	//-----------
	
	private function getRespondents()
	{
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		$sorting = JRequest::getVar('sortby', 'registered DESC');
		$filters = array(
			'search' => urldecode(JRequest::getString('search')),
			'id'     => JRequest::getVar('id', array()),
			'sortby' => $sorting == 'registerby DESC' ? 'registered DESC' : $sorting,
			'limit'  => $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int'),
			'offset' => JRequest::getInt('limitstart', 0)
		);
		if (!$filters['limit']) $filters['limit'] = 30;
		return new EventsRespondent($filters);	
	}
	
	//-----------

	protected function downloadlist()
	{
		EventsHtml::downloadlist($this->getRespondents(), $this->_option);
	}
	
	
	//-----------

	protected function removerespondent() 
	{
		// Incoming
		$workshop = JRequest::getInt( 'workshop', 0 );
		$ids = JRequest::getVar( 'rid', array() );

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}
		
		// Do we have any IDs?
		if (!empty($ids)) {
			$database =& JFactory::getDBO();
		
			$r = new EventsRespondent( array() );
			
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id) 
			{
				// Remove the profile
				$r->delete( $id );
			}
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=viewList&id[]='.$workshop;
		$this->_message = JText::_('EVENTS_RESPONDENT_REMOVED');
	}
	
	//----------------------------------------------------------
	//  Pages
	//----------------------------------------------------------

	protected function pages()
	{
		$ids = JRequest::getVar( 'id', array(0) );
		if (count($ids) < 1) {
			$this->cancel();
			return;
		}

		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();

		// Get filters
		$filters = array();
		$filters['event_id'] = $ids[0];
		$filters['search'] = urldecode(JRequest::getString('search'));

		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.pages.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['limit'] = ($filters['limit']) ? $filters['limit'] : 25;
		$filters['start'] = JRequest::getInt('limitstart', 0);

		$obj = new EventsPage( $database );
		
		// Get a record count
		$total = $obj->getCount( $filters );
		
		// Get records
		$rows = $obj->getRecords( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		$event = new EventsEvent( $database );
		$event->load( $ids[0] );

		// Output HTML
		EventsHtml::pages( $rows, $pageNav, $this->_option, $filters, $event );
	}
	
	//-----------

	protected function addpage()
	{
		$ids = JRequest::getVar( 'id', array() );
		if (is_array($ids)) {
			$id = (!empty($ids)) ? $ids[0] : 0;
		} else {
			$id = 0;
		}
		JRequest::setVar( 'id', array() );
		$this->editpage($id);
	}

	//-----------

	protected function editpage($eid=null) 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$eid = ($eid) ? $eid : JRequest::getInt( 'event', 0 );
		$ids = JRequest::getVar( 'id', array() );
		
		// Get the single ID we're working with
		if (is_array($ids)) {
			$id = (!empty($ids)) ? $ids[0] : 0;
		} else {
			$id = 0;
		}
		
		// Initiate database class and load info
		$page = new EventsPage( $database );
		$page->load( $id );
		
		$event = new EventsEvent( $database );
		$event->load( $eid );
		
		// Ouput HTML
		EventsHtml::editpage( $page, $event, $this->_option );
	}

	//-----------
	
	protected function savepage() 
	{
		$database =& JFactory::getDBO();
		
		// Bind incoming data to object
		$row = new EventsPage( $database );
		if (!$row->bind( $_POST )) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}
		
		if (!$row->alias) {
			$row->alias = $row->title;
		}
		
		$row->event_id = JRequest::getInt( 'event', 0 );
		$row->alias = preg_replace("/[^a-zA-Z0-9]/", "", $row->alias);
		$row->alias = strtolower($row->alias);
		
		// Get parameters
		$params = JRequest::getVar( 'params', '', 'post' );
		if (is_array( $params )) {
			$txt = array();
			foreach ( $params as $k=>$v) 
			{
				$txt[] = "$k=$v";
			}
			$row->params = implode( "\n", $txt );
		}
		
		// Check content for missing required data
		if (!$row->check()) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo EventsHtml::alert( $row->getError() );
			exit();
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=pages&id[]='.$row->event_id;
		$this->_message = JText::_('EVENTS_PAGE_SAVED');
	}
	
	//-----------

	protected function removepage() 
	{
		// Incoming
		$event = JRequest::getInt( 'event', 0 );
		$ids = JRequest::getVar( 'ids', array() );

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}
		
		// Do we have any IDs?
		if (!empty($ids)) {
			$database =& JFactory::getDBO();
		
			$page = new EventsPage( $database );
			
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id) 
			{
				// Remove the profile
				$page->delete( $id );
			}
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=pages&id[]='.$event;
		$this->_message = JText::_('EVENTS_PAGES_REMOVED');
	}
	
	//-----------

	protected function orderuppage() 
	{
		$this->reorderpage();
	}
	
	//-----------
	
	protected function orderdownpage() 
	{
		$this->reorderpage();
	}
	
	//-----------
	
	protected function reorderpage() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getVar( 'id', array() );
		$id = $id[0];
		$pid = JRequest::getInt( 'event', 0 );

		// Ensure we have an ID to work with
		if (!$id) {
			echo EventsHtml::alert( JText::_('No page ID found.') );
			exit;
		}
		
		// Ensure we have a parent ID to work with
		if (!$pid) {
			echo EventsHtml::alert( JText::_('No event ID found.') );
			exit;
		}

		// Get the element moving down - item 1
		$page1 = new EventsPage( $database );
		$page1->load( $id );

		// Get the element directly after it in ordering - item 2
		$page2 = clone( $page1 );
		$page2->getNeighbor( $this->_task );

		switch ($this->_task) 
		{
			case 'orderuppage':				
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $page2->ordering;
				$orderdn = $page1->ordering;
				
				$page1->ordering = $orderup;
				$page2->ordering = $orderdn;
				break;
			
			case 'orderdownpage':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $page1->ordering;
				$orderdn = $page2->ordering;
				
				$page1->ordering = $orderdn;
				$page2->ordering = $orderup;
				break;
		}
		
		// Save changes
		$page1->store();
		$page2->store();
		
		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option .'&task=pages&id[]='. $pid;
	}
	
	//-----------

	protected function cancelpage()
	{
		$workshop = JRequest::getInt( 'workshop', 0 );
		
		$this->_redirect = 'index.php?option='.$this->_option.'&task=pages&id[]='.$workshop;
	}
}
?>