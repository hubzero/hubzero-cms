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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'EventsController'
 * 
 * Long description (if any) ...
 */
class EventsController extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function execute()
	{
		$config = new EventsConfigs( $this->database );
		$config->load();
		$this->config = $config;

		$tables = $this->database->getTableList();
		$table = $this->database->_table_prefix.'events_respondent_race_rel';
		if (!in_array($table,$tables)) {
			$this->database->setQuery( "CREATE TABLE `#__events_respondent_race_rel` (
			  `respondent_id` int(11) default NULL,
			  `race` varchar(255) default NULL,
			  `tribal_affiliation` varchar(255) default NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;" );
			if (!$this->database->query()) {
				echo $this->database->getErrorMsg();
				return false;
			}
		}

	/**
	 * Description for ''_CAL_CONF_STARDAY''
	 */
		define( '_CAL_CONF_STARDAY', $config->getCfg('starday'));

	/**
	 * Description for ''_CAL_CONF_DEFCOLOR''
	 */
		define( '_CAL_CONF_DEFCOLOR', $config->getCfg('navbarcolor'));

		$this->_task = strtolower(JRequest::getString('task', ''));

		switch ($this->_task)
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

	/**
	 * Short description for 'getScripts'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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


	/**
	 * Short description for 'events'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function events()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'events') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit']  = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']  = JRequest::getVar('limitstart', 0, '', 'int');
		$view->filters['search'] = urldecode(JRequest::getString('search'));
		$view->filters['catid']  = JRequest::getVar('catid', 0, '', 'int');

		$ee = new EventsEvent( $this->database );

		// Get a record count
		$view->total = $ee->getCount( $view->filters );

		// Get records
		$view->rows = $ee->getRecords( $view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Get list of categories
		$categories[] = JHTML::_('select.option', '0', '- '.JText::_('EVENTS_CAL_LANG_EVENT_ALLCAT'), 'value', 'text' );
		$this->database->setQuery( "SELECT id AS value, title AS text FROM #__categories WHERE section='$this->_option' ORDER BY ordering" );
		$categories = array_merge( $categories, $this->database->loadObjectList() );

		$view->clist = JHTML::_('select.genericlist', $categories, 'catid', 'class="inputbox"','value', 'text', $view->filters['catid'], false, false );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function edit()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'event') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->config = $this->config;

		$config = JFactory::getConfig();
		$offset = $config->getValue('config.offset');

		// We need at least one category before we can proceed
		$cat = new EventsCategory( $this->database );
		if ($cat->getCategoryCount( $this->_option ) < 1) {
			JError::raiseError( 500, JText::_('EVENTS_LANG_NEED_CATEGORY') );
			return;
		}

		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );

		// Load the event object
		$view->row = new EventsEvent( $this->database );
		$view->row->load( $id );

		// Fail if checked out not by 'me'
		if ($view->row->checked_out && $view->row->checked_out <> $this->juser->get('id')) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_('EVENTS_CAL_LANG_WARN_CHECKEDOUT');
		}

		$document =& JFactory::getDocument();
		$document->addStyleSheet('..'.DS.'components'.DS.$this->_option.DS.'calendar.css');

		if ($view->row->id) {
			$view->row->checkout( $this->juser->get('id') );

			if (trim( $view->row->images )) {
				$view->row->images = explode( "\n", $view->row->images );
			} else {
				$view->row->images = array();
			}

			if (trim( $view->row->publish_down ) == '0000-00-00 00:00:00') {
				$view->row->publish_down = JText::_('EVENTS_CAL_LANG_NEVER');
			}

			$event_up = new EventsDate( $view->row->publish_up );
			$start_publish = sprintf( "%4d-%02d-%02d",$event_up->year,$event_up->month,$event_up->day);
			$start_time = $event_up->hour .':'. $event_up->minute;

			$event_down = new EventsDate( $view->row->publish_down );
			$stop_publish = sprintf( "%4d-%02d-%02d",$event_down->year,$event_down->month,$event_down->day);
			$end_time = $event_down->hour .':'. $event_down->minute;

			$view->row->reccurday_month = 99;
			$view->row->reccurday_week = 99;
			$view->row->reccurday_year = 99;

			if ($view->row->reccurday <> '') {
				if ($view->row->reccurtype == 1) {
					$view->row->reccurday_week = $view->row->reccurday;
				} elseif ($view->row->reccurtype == 3) {
					$view->row->reccurday_month = $view->row->reccurday;
				} elseif ($view->row->reccurtype == 5) {
					$view->row->reccurday_year = $view->row->reccurday;
				}
			}
		} else {
			$view->row->state = 0;
			$view->row->images = array();
			$start_publish = strftime( "%Y-%m-%d", time()+($offset*60*60) );
			$stop_publish = strftime( "%Y-%m-%d", time()+($offset*60*60) );
			$start_time = "08:00";
	        $end_time = "17:00";
			$view->row->color_bar = EventsHtml::getColorBar(null,'');

			$view->row->reccurday_month = -1;
			$view->row->reccurday_week = -1;
			$view->row->reccurday_year = -1;
		}

		// Get list of groups
		$this->database->setQuery( "SELECT id AS value, name AS text FROM #__groups ORDER BY id" );
		$groups = $this->database->loadObjectList();

		// Build the html select list
		$view->glist = JHTML::_('select.genericlist', $groups, 'access', 'class="inputbox"','value', 'text', intval( $view->row->access ), false, false );

		$view->fields = $this->config->getCfg('fields');
		if (!empty($view->fields)) {
			for ($i=0, $n=count( $view->fields ); $i < $n; $i++)
			{
				// explore the text and pull out all matches
				array_push($view->fields[$i], $this->parseTag($view->row->content, $view->fields[$i][0]));
				// clean the original text of any matches
				$view->row->content = str_replace('<ef:'.$view->fields[$i][0].'>'.end($view->fields[$i]).'</ef:'.$view->fields[$i][0].'>','',$view->row->content);
			}
			$view->row->content = trim($view->row->content);
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

		$view->times = array();
		$view->times['start_publish'] = $start_publish;
		$view->times['start_time'] = $start_time;
		$view->times['start_pm'] = $start_pm;
		$view->times['stop_publish'] = $stop_publish;
		$view->times['end_time'] = $end_time;
		$view->times['end_pm'] = $end_pm;

		// Get tags on this event
		$rt = new EventsTags( $this->database );
		$view->tags = $rt->get_tag_string($view->row->id, 0, 0, NULL, 0, 1);

		// Output HTML
		//EventsHtml::edit( $row, $this->config, $fields, $glist, $times, $juser->get('id'), $this->_option, $tags );
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'parseTag'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'cancel'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function cancel()
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Check in the event
		$event = new EventsEvent( $this->database );
		$event->load( $id );
		$event->checkin();

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
		$row = new EventsEvent( $this->database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
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
			JError::raiseError( 500, $row->getError() );
			return;
		}
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		$row->checkin();

		// Incoming tags
		$tags = JRequest::getVar( 'tags', '', 'post' );

		// Save the tags
		$rt = new EventsTags( $this->database );
		$rt->tag_object($juser->get('id'), $row->id, $tags, 1, 0);

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('EVENTS_CAL_LANG_SAVED');
	}

	/**
	 * Short description for 'clean'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
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

	/**
	 * Short description for 'publish'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function publish()
	{
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
		$event = new EventsEvent( $this->database );

		// Loop through the IDs and publish the event
		foreach ($ids as $id)
		{
			$event->publish( $id );
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('EVENTS_CAL_LANG_PUBLISHED');
	}

	/**
	 * Short description for 'unpublish'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function unpublish()
	{
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
		$event = new EventsEvent( $this->database );

		// Loop through the IDs and unpublish the event
		foreach ($ids as $id)
		{
			$event->unpublish( $id );
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('EVENTS_CAL_LANG_UNPUBLISHED');
	}

	/**
	 * Short description for 'setType'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function setType()
	{
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
			$event = new EventsEvent( $this->database );
			$event->load( $id );
			$event->announcement = $v;
			if (!$event->store()) {
				JError::raiseError( 500, $event->getError() );
				return;
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	/**
	 * Short description for 'remove'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function remove()
	{
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
		$event = new EventsEvent( $this->database );

		// Instantiate an event tags object
		$rt = new EventsTags( $this->database );

		// Instantiate a page object
		$ep = new EventsPage( $this->database );

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


	/**
	 * Short description for 'configure'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function configure()
	{
		$view = new JView( array('name'=>'configure') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->config = $this->config;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'saveconfig'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function saveconfig()
	{
		// Get the configuration
		$config = JRequest::getVar('config', array(), 'post');
		foreach ($config as $n=>$v)
		{
			$box = array();
			$box['param'] = $n;
			$box['value'] = $v;

			$row = new EventsConfig( $this->database );
			if (!$row->bind( $box )) {
				JError::raiseError( 500, $row->getError() );
				return;
			}
			// Check content
			if (!$row->check()) {
				JError::raiseError( 500, $row->getError() );
				return;
			}
			// Store content
			if (!$row->store()) {
				JError::raiseError( 500, $row->getError().' '.$row->param.' '.$row->value );
				return;
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

		$row = new EventsConfig( $this->database );
		if (!$row->bind( $box )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		// Store content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('EVENTS_CAL_LANG_CONFIG_SAVED');
	}

	/**
	 * Short description for 'normalize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $txt Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
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


	/**
	 * Short description for 'cats'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function cats()
	{
		$view = new JView( array('name'=>'categories') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$view->section = $this->_option;

		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$limit = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		$view->section_name = '';
		if (intval( $view->section ) > 0) {
			$table = 'content';

			$this->database->setQuery( "SELECT name FROM #__sections WHERE id='$view->section'" );
			$view->section_name = $this->database->loadResult();
			if ($this->database->getErrorNum()) {
				JError::raiseError( 500, $this->database->getErrorMsg() );
				return;
			}
			$view->section_name .= ' Section';
		} else if (strpos( $view->section, 'com_' ) === 0) {
			$table = substr( $view->section, 4 );

			$this->database->setQuery( "SELECT name FROM #__components WHERE link='option=$view->section'" );
			$view->section_name = $this->database->loadResult();
			if ($this->database->getErrorNum()) {
				JError::raiseError( 500, $this->database->getErrorMsg() );
				return;
			}
		} else {
			$table = $view->section;
		}

		// Get the total number of records
		$this->database->setQuery( "SELECT count(*) FROM #__categories WHERE section='$view->section'" );
		$total = $this->database->loadResult();
		if ($this->database->getErrorNum()) {
			JError::raiseError( 500, $this->database->stderr() );
			return;
		}

		// dmcd may 22/04  added #__events_categories table to fetch category color property
		$this->database->setQuery( "SELECT  c.*, g.name AS groupname, u.name AS editor, cc.color AS color, "
		. "COUNT(DISTINCT s2.checked_out) AS checked_out, COUNT(DISTINCT s1.id) AS num"
		. "\nFROM #__categories AS c"
		. "\nLEFT JOIN #__users AS u ON u.id = c.checked_out"
		. "\nLEFT JOIN #__groups AS g ON g.id = c.access"
		. "\nLEFT JOIN #__$table AS s1 ON s1.catid = c.id"
		. "\nLEFT JOIN #__$table AS s2 ON s2.catid = c.id AND s2.checked_out > 0"
		. "\nLEFT JOIN #__${table}_categories AS cc ON cc.id = c.id"
		. "\nWHERE section='$view->section'"
		. "\nGROUP BY c.id"
		. "\nORDER BY c.ordering, c.name"
		. "\nLIMIT $limitstart,$limit"
		);

		// Execute query
		$view->rows = $this->database->loadObjectList();
		if ($this->database->getErrorNum()) {
			JError::raiseError( 500, $this->database->stderr() );
			return;
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $this->total, $limitstart, $limit );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'editcat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function editcat()
	{
		$view = new JView( array('name'=>'category') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Load the category
		$view->row = new EventsCategory( $this->database );
		$view->row->load( $id );

		// Fail if checked out not by 'me'
		if ($view->row->checked_out && $view->row->checked_out <> $this->juser->get('id')) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
			$this->_message = JText::_('EVENTS_CAL_LANG_CATEGORY_CHECKEDOUT');
			return;
		}

		if ($view->row->id) {
			// Existing record
			$view->row->checkout( $this->juser->get('id') );
		} else {
			// New record
			$view->row->section = $this->_option;
			$view->row->color = '';
		}

		// Make order list
		$order = array();

		$max = intval( $view->row->getCategoryCount() ) + 1;
		for ($i=1; $i < $max; $i++)
		{
			$order[] = JHTML::_('select.option', $i, $i, 'value', 'text' );
		}

		$ipos[] = JHTML::_('select.option', 'left', JText::_('left'), 'value', 'text' );
		$ipos[] = JHTML::_('select.option', 'right', JText::_('right'), 'value', 'text' );

		$view->iposlist = JHTML::_('select.genericlist', $ipos, 'image_position', 'class="inputbox" size="1"','value', 'text', $view->row->image_position ? $view->row->image_position : 'left', false, false );

		$imgFiles = $this->readDirectory( JPATH_ROOT.DS.'images'.DS.'stories' );
		$images = array( JHTML::_('select.option', '', JText::_('Select Image'), 'value', 'text') );
		foreach ($imgFiles as $file)
		{
			if (eregi( "bmp|gif|jpg|png", $file )) {
				$images[] = JHTML::_('select.option', $file, $file, 'value', 'text' );
			}
		}

		$view->imagelist = JHTML::_('select.genericlist', $images, 'image', 'class="inputbox" size="1"'
		. " onchange=\"javascript:if (document.forms[0].image.options[selectedIndex].value!='') {document.imagelib.src='../images/stories/' + document.forms[0].image.options[selectedIndex].value} else {document.imagelib.src='../images/M_images/blank.png'}\"",
		'value', 'text', $view->row->image, false, false );

		$view->orderlist = JHTML::_('select.genericlist', $order, 'ordering', 'class="inputbox" size="1"','value', 'text', $view->row->ordering, false, false );

		// Get list of groups
		$this->database->setQuery( "SELECT id AS value, name AS text FROM #__groups ORDER BY id" );
		$view->groups = $this->database->loadObjectList();

		$view->glist = JHTML::_('select.genericlist', $view->groups, 'access', 'class="inputbox" size="1"','value', 'text', intval( $view->row->access ), false, false );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'readDirectory'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $path Parameter description (if any) ...
	 * @param      string $filter Parameter description (if any) ...
	 * @param      boolean $recurse Parameter description (if any) ...
	 * @param      boolean $fullpath Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function readDirectory( $path, $filter='.', $recurse=false, $fullpath=false  )
	{
		$arr = array(null);

		// Get the files and folders
		jimport('joomla.filesystem.folder');
		$files   = JFolder::files($path, $filter, $recurse, $fullpath);
		$folders = JFolder::folders($path, $filter, $recurse, $fullpath);
		// Merge files and folders into one array
		$arr = array_merge($files, $folders);
		// Sort them all
		asort($arr);
		return $arr;
	}

	/**
	 * Short description for 'savecat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function savecat()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$row = new EventsCategory( $this->database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		$row->checkin();
		//$row->updateOrder( "section='$row->section'" );

		if ($oldtitle = JRequest::getVar('oldtitle', null, 'post')) {
			if ($oldtitle != $row->title) {
				$this->database->setQuery( "UPDATE #__menu SET name='$row->title' WHERE name='$oldtitle' AND type='content_category'" );
				$this->database->query();
			}
		}

		// Update Section Count
		if ($row->section != 'com_weblinks') {
			$this->database->setQuery( "UPDATE #__sections SET count=count+1 WHERE id = '$row->section'");
		}

		if (!$this->database->query()) {
			JError::raiseError( 500, $this->database->getErrorMsg() );
			return;
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
	}

	/**
	 * Short description for 'cancelcat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function cancelcat()
	{
		// Checkin the category
		$row = new EventsCategory( $this->database );
		$row->bind( $_POST );
		$row->checkin();

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
	}

	/**
	 * Short description for 'publishcat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function publishcat()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
		$event = new EventsCategory( $this->database );

		// Loop through the IDs and publish the category
		foreach ($ids as $id)
		{
			$event->publish( $id );
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
		$this->_message = JText::_('EVENTS_CAL_LANG_CATEGORY_PUBLISHED');
	}

	/**
	 * Short description for 'unpublishcat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function unpublishcat()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
		$event = new EventsCategory( $this->database );

		// Loop through the IDs and unpublish the category
		foreach ($ids as $id)
		{
			$event->unpublish( $id );
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
		$this->_message = JText::_('EVENTS_CAL_LANG_CATEGORY_UNPUBLISHED');
	}

	/**
	 * Short description for 'orderup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function orderup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Load the category, reorder, save
		$row = new EventsCategory( $this->database );
		$row->load( $id );
		$row->move( -1, "section='$row->section'" );

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
	}

	/**
	 * Short description for 'orderdown'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function orderdown()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Load the category, reorder, save
		$row = new EventsCategory( $this->database );
		$row->load( $id );
		$row->move( 1, "section='$row->section'" );

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=cats';
	}

	/**
	 * Short description for 'removecat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function removecat()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
				$cat = new EventsCategory( $this->database );
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


	/**
	 * Short description for 'viewrespondent'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function viewrespondent()
	{
		$view = new JView( array('name'=>'respondent') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$view->resp = new EventsRespondent(array('respondent_id' => JRequest::getInt('id', 0)));

		// Incoming
		$ids = JRequest::getInt('event_id', 0);
		$id = $ids[0];

		$view->event = new EventsEvent( $this->database );
		$view->event->load( $id );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'viewlist'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function viewlist()
	{
		$view = new JView( array('name'=>'respondents') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$view->resp = $this->getRespondents();

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		$id = $ids[0];

		if (!$id) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}

		$view->event = new EventsEvent( $this->database );
		$view->event->load( $id );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'getRespondents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for 'downloadlist'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function downloadlist()
	{
		EventsHtml::downloadlist($this->getRespondents(), $this->_option);
	}

	/**
	 * Short description for 'removerespondent'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function removerespondent()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$workshop = JRequest::getInt( 'workshop', 0 );
		$ids = JRequest::getVar( 'rid', array() );

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids)) {
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


	/**
	 * Short description for 'pages'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function pages()
	{
		$ids = JRequest::getVar( 'id', array(0) );
		if (count($ids) < 1) {
			$this->cancel();
			return;
		}

		$view = new JView( array('name'=>'pages') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Get filters
		$view->filters = array();
		$view->filters['event_id'] = $ids[0];
		$view->filters['search'] = urldecode(JRequest::getString('search'));
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.pages.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['limit'] = ($view->filters['limit']) ? $view->filters['limit'] : 25;
		$view->filters['start'] = JRequest::getInt('limitstart', 0);

		$obj = new EventsPage( $this->database );

		// Get a record count
		$view->total = $obj->getCount( $view->filters );

		// Get records
		$view->rows = $obj->getRecords( $view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		$view->event = new EventsEvent( $this->database );
		$view->event->load( $ids[0] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'addpage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'editpage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $eid Parameter description (if any) ...
	 * @return     void
	 */
	protected function editpage($eid=null)
	{
		$view = new JView( array('name'=>'page') );
		$view->option = $this->_option;
		$view->task = $this->_task;

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
		$view->page = new EventsPage( $this->database );
		$view->page->load( $id );

		$view->event = new EventsEvent( $this->database );
		$view->event->load( $eid );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'savepage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function savepage()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Bind incoming data to object
		$row = new EventsPage( $this->database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
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
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=pages&id[]='.$row->event_id;
		$this->_message = JText::_('EVENTS_PAGE_SAVED');
	}

	/**
	 * Short description for 'removepage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function removepage()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$event = JRequest::getInt( 'event', 0 );
		$ids = JRequest::getVar( 'id', array(0) );

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids)) {
			$page = new EventsPage( $this->database );

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

	/**
	 * Short description for 'orderuppage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function orderuppage()
	{
		$this->reorderpage();
	}

	/**
	 * Short description for 'orderdownpage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function orderdownpage()
	{
		$this->reorderpage();
	}

	/**
	 * Short description for 'reorderpage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function reorderpage()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit( 'Invalid Token' );

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
		$page1 = new EventsPage( $this->database );
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

	/**
	 * Short description for 'cancelpage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function cancelpage()
	{
		$workshop = JRequest::getInt( 'event', 0 );

		$this->_redirect = 'index.php?option='.$this->_option.'&task=pages&id[]='.$workshop;
	}
}

