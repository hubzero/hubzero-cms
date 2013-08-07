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
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for events
 */
class EventsControllerEvents extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->dateFormat = '%d %b %Y';
		$this->dateFormatShort = '%d %b';
		$this->timeFormat = '%I:%M %p';
		$this->yearFormat  = "%Y";
		$this->monthFormat = "%m";
		$this->dayFormat   = "%d";
		$this->tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$this->dateFormat = 'd M Y';
			$this->dateFormatShort = 'd M';
			$this->timeFormat = 'h:i A';
			$this->yearFormat  = "Y";
			$this->monthFormat = "m";
			$this->dayFormat   = "d";
			$this->tz = true;
		}

		$this->_setup();

		$this->_getStyles();

		$this->_task = ($this->_task) ? $this->_task : JRequest::getString('task', $this->config->getCfg('startview'));

		switch ($this->_task)
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

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		switch ($this->_task)
		{
			case 'year':
				if ($this->year) {
					$pathway->addItem(
						$this->year,
						'index.php?option=' . $this->_option . '&year='.$this->year
					);
				}
			break;
			case 'month':
				if ($this->year) {
					$pathway->addItem(
						$this->year,
						'index.php?option=' . $this->_option . '&year='.$this->year
					);
				}
				if ($this->month) {
					$pathway->addItem(
						$this->month,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month='.$this->month
					);
				}
			break;
			case 'day':
				if ($this->year) {
					$pathway->addItem(
						$this->year,
						'index.php?option=' . $this->_option . '&year='.$this->year
					);
				}
				if ($this->month) {
					$pathway->addItem(
						$this->month,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month='.$this->month
					);
				}
				if ($this->day) {
					$pathway->addItem(
						$this->day,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month=' . $this->month . '&day=' . $this->day
					);
				}
			break;
			case 'week':
				if ($this->year) {
					$pathway->addItem(
						$this->year,
						'index.php?option=' . $this->_option . '&year='.$this->year
					);
				}
				if ($this->month) {
					$pathway->addItem(
						$this->month,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month='.$this->month
					);
				}
				if ($this->day) {
					$pathway->addItem(
						$this->day,
						'index.php?option=' . $this->_option . '&year=' . $this->year . '&month=' . $this->month . '&day=' . $this->day
					);
				}
				$pathway->addItem(
					JText::sprintf('EVENTS_WEEK_OF',$this->day),
					'index.php?option=' . $this->_option . '&year=' . $this->year . '&month=' . $this->month . '&day=' . $this->day . '&task=week'
				);
			break;
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_name));
		switch ($this->_task)
		{
			case 'year':
				if ($this->year) 
				{
					$this->_title .= ': ' . $this->year;
				}
			break;
			case 'month':
				if ($this->year) 
				{
					$this->_title .= ': ' . $this->year;
				}
				if ($this->month) 
				{
					$this->_title .= '/' . $this->month;
				}
			break;
			case 'day':
				if ($this->year) 
				{
					$this->_title .= ': ' . $this->year;
				}
				if ($this->month) 
				{
					$this->_title .= '/' . $this->month;
				}
				if ($this->day) {
					$this->_title .= '/' . $this->day;
				}
			break;
			case 'week':
				if ($this->year) 
				{
					$this->_title .= ': ' . $this->year;
				}
				if ($this->month) 
				{
					$this->_title .= '/' . $this->month;
				}
				if ($this->day) 
				{
					$this->_title .= '/' . $this->day;
				}
				if ($this->_task && $this->_task == 'week') 
				{
					$this->_title .= ': ' . JText::sprintf('EVENTS_WEEK_OF', $this->day);
				}
			break;
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Perform some initial setup and set some commonly used vars
	 * 
	 * @return     void
	 */
	private function _setup()
	{
		// Load the events configuration
		$config = new EventsConfigs($this->database);
		$config->load();

		$this->config = $config;

		// Set some defines

		/**
		 * Description for ''_CAL_CONF_STARDAY''
		 */
		define('_CAL_CONF_STARDAY', $config->getCfg('starday'));

		/**
		 * Description for ''_CAL_CONF_DATEFORMAT''
		 */
		define('_CAL_CONF_DATEFORMAT', $config->getCfg('dateformat'));

		$jconfig =& JFactory::getConfig();
		$this->offset = $jconfig->getValue('config.offset');

		// Incoming
		$year  = JRequest::getVar('year',  strftime("%Y", time()+($this->offset*60*60)));
		$month = JRequest::getVar('month', strftime("%m", time()+($this->offset*60*60)));
		$day   = JRequest::getVar('day',   strftime("%d", time()+($this->offset*60*60)));

		$category = JRequest::getInt('category', 0);

		if ($day<="9"&preg_match("/(^[1-9]{1})/", $day)) 
		{
			$day = "0$day";
		}
		if ($month<="9"&preg_match("/(^[1-9]{1})/", $month)) 
		{
			$month = "0$month";
		}

		$ee = new EventsEvent($this->database);

		// Find the date of the first event
		$row = $ee->getFirst();
		if ($row) 
		{
			$pyear = substr($row, 0, 4);
			$pmonth = substr($row, 4, 2);
			if ($year < $pyear) 
			{
				$year = $pyear;
			}
			if ($month < $pmonth) 
			{
				//$month = $pmonth;
			}
		}

		// Find the date of the last event
		$row = $ee->getLast();
		if ($row) 
		{
			$thisyear = strftime("%Y", time()+($this->offset*60*60));
			$fyear = substr($row,0,4);
			$fmonth = substr($row,4,2);
			if ($year > $fyear && $year > $thisyear) 
			{
				$year = ($fyear > $thisyear) ? $fyear : $thisyear;
			}
			if ($month > $fmonth) 
			{
				//$month = $fmonth;
			}
		}

		$this->year  = $year;
		$this->month = $month;
		$this->day   = $day;

		$this->category = $category;

		$this->gid = intval($this->juser->get('gid'));
	}

	/**
	 * List events for a given year
	 * 
	 * @return     void
	 */
	protected function year()
	{
		// Get some needed info
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$offset = $this->offset;
		$option = $this->_option;
		$gid    = $this->gid;

		// Set some filters
		$filters = array();
		$filters['gid'] = $gid;
		$filters['year'] = $year;
		$filters['category'] = $this->category;
		$filters['scope'] = 'event';
		
		// Retrieve records
		$ee = new EventsEvent($this->database);
		$rows = $ee->getEvents('year', $filters);

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if ($this->config->getCfg('adminlevel')) 
		{
			$authorized = $this->_authorize();
		}

		// Get a list of categories
		$categories = $this->_getCategories();

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Output HMTL
		$view = new JView(array(
			'name'   => 'browse',
			'layout' => 'year'
		));
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->task = $this->_task;
		$view->year = $year;
		$view->month = $month;
		$view->day = $day;
		$view->rows = $rows;
		$view->authorized = $authorized;
		$view->fields = $this->config->getCfg('fields');
		$view->category = $this->category;
		$view->categories = $categories;
		$view->offset = $offset;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * List events for a given year and month
	 * 
	 * @return     void
	 */
	protected function month()
	{
		// Get some needed info
		$offset = $this->offset;
		$option = $this->_option;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$gid    = $this->gid;

		// Set some dates
		$select_date = $year . '-' . $month . '-01 00:00:00';
		$select_date_fin = $year . '-' . $month . '-' . date("t",mktime(0, 0, 0, ($month+1), 0, $year)) . ' 23:59:59';

		// Set some filters
		$filters = array();
		$filters['gid'] = $gid;
		$filters['select_date'] = $select_date;
		$filters['select_date_fin'] = $select_date_fin;
		$filters['category'] = $this->category;
		$filters['scope'] = 'event';

		// Retrieve records
		$ee = new EventsEvent($this->database);
		$rows = $ee->getEvents('month', $filters);

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if ($this->config->getCfg('adminlevel')) 
		{
			$authorized = $this->_authorize();
		}

		// Get a list of categories
		$categories = $this->_getCategories();

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		$this->_getScripts('assets/js/' . $this->_name);

		// Output HTML
		$view = new JView(array(
			'name'   => 'browse',
			'layout' => 'month'
		));
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->task = $this->_task;
		$view->year = $year;
		$view->month = $month;
		$view->day = $day;
		$view->rows = $rows;
		$view->authorized = $authorized;
		$view->fields = $this->config->getCfg('fields');
		$view->category = $this->category;
		$view->categories = $categories;
		$view->offset = $offset;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * List events for a given year/month/week
	 * 
	 * @return     void
	 */
	protected function week()
	{
		// Get some needed info
		$offset = $this->offset;
		$option = $this->_option;
		$year   = intval($this->year);
		$month  = intval($this->month);
		$day    = intval($this->day);

		$startday = _CAL_CONF_STARDAY;
		$numday = ((date("w",mktime(0,0,0,$month,$day,$year))-$startday)%7);
		if ($numday == -1) 
		{
			$numday = 6;
		}
		$week_start = mktime(0, 0, 0, $month, ($day - $numday), $year);

		$this_date = new EventsDate();
		$this_date->setDate(strftime("%Y", $week_start), strftime("%m", $week_start), strftime("%d", $week_start));
		$this_enddate = clone($this_date);
		$this_enddate->addDays(+6);

		$sdt = JHTML::_('date', $this_date->year . '-' . $this_date->month . '-' . $this_date->day . ' 00:00:00', $this->dateFormatShort, $this->tz);
		$edt = JHTML::_('date', $this_enddate->year . '-' . $this_enddate->month . '-' . $this_enddate->day . ' 00:00:00', $this->dateFormatShort, $this->tz);

		$this_currentdate = $this_date;

		$categories = $this->_getCategories();

		$filters = array();
		$filters['gid'] = $this->gid;
		$filters['category'] = $this->category;
		$filters['scope'] = 'event';

		$ee = new EventsEvent($this->database);

		$rows = array();
		for ($d = 0; $d < 7; $d++)
		{
			if ($d > 0) 
			{
				$this_currentdate->addDays(+1);
			}
			$week = array();
			$week['day']   = sprintf("%02d", $this_currentdate->day);
			$week['month'] = sprintf("%02d", $this_currentdate->month);
			$week['year']  = sprintf("%4d",  $this_currentdate->year);

			$filters['select_date'] = sprintf("%4d-%02d-%02d", $week['year'], $week['month'], $week['day']);

			$rows[$d] = array();
			$rows[$d]['events'] = $ee->getEvents('day', $filters);
			$rows[$d]['week']   = $week;
		}

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if ($this->config->getCfg('adminlevel')) 
		{
			$authorized = $this->_authorize();
		}

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Output HTML;
		$view = new JView(array(
			'name'   => 'browse',
			'layout' => 'week'
		));
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->task = $this->_task;
		$view->year = $year;
		$view->month = $month;
		$view->day = $day;
		$view->rows = $rows;
		$view->authorized = $authorized;
		$view->fields = $this->config->getCfg('fields');
		$view->category = $this->category;
		$view->categories = $categories;
		$view->offset = $offset;
		$view->startdate = $sdt;
		$view->enddate = $edt;
		$view->week = $week;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * View events for a given day
	 * 
	 * @return     void
	 */
	protected function day()
	{
		// Get some needed info
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$offset = $this->offset;
		$option = $this->_option;

		// Get the events for this day
		$filters = array();
		$filters['gid'] = $this->gid;
		$filters['select_date'] = sprintf("%4d-%02d-%02d", $year, $month, $day);
		$filters['category'] = $this->category;
		$filters['scope'] = 'event';

		$ee = new EventsEvent($this->database);
		$rows = $ee->getEvents('day', $filters);

		// Go through each event and ensure it should be displayed
		$events = array();
		if (count($rows) > 0) 
		{
			foreach ($rows as $row)
			{
				$checkprint = new EventsRepeat($row, $year, $month, $day);
				if ($checkprint->viewable == true) 
				{
					$events[] = $row;
				}
			}
		}

		// Everyone has access unless restricted to admins in the configuration
		$authorized = true;
		if ($this->config->getCfg('adminlevel')) 
		{
			$authorized = $this->_authorize();
		}

		// Get a list of categories
		$categories = $this->_getCategories();

		// Build the page title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		// Output HTML
		$view = new JView(array(
			'name'   => 'browse',
			'layout' => 'day'
		));
		$view->option = $this->_option;
		$view->title = $this->_title;
		$view->task = $this->_task;
		$view->year = $year;
		$view->month = $month;
		$view->day = $day;
		$view->rows = $events;
		$view->authorized = $authorized;
		$view->fields = $this->config->getCfg('fields');
		$view->category = $this->category;
		$view->categories = $categories;
		$view->offset = $offset;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * View details of an event
	 * 
	 * @return     void
	 */
	protected function details()
	{
		// Get some needed info
		$offset = $this->offset;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$option = $this->_option;

		// Incoming
		$id = JRequest::getInt('id', 0, 'request');

		// Load event
		$row = new EventsEvent($this->database);
		$row->load($id);
		
		// Ensure we have an event
		if (!$row) 
		{
			JError::raiseError(404, JText::_('EVENTS_CAL_LANG_NO_EVENTFOR') . ' ' . JText::_('EVENTS_CAL_LANG_THIS_DAY'));
			return;
		}
		
		//is this a group rescricted event
		if ($row->scope == 'group')
		{
			ximport('Hubzero_Group');
			$group = Hubzero_Group::getInstance( $row->scope_id );
			
			//if we have a group and we are a member
			if (is_object($group))
			{
				//if we are a member of that group
				if (in_array($this->juser->get('id'), $group->get('members')))
				{
					//get the events publish up month and year so we can jump to that month/year
					$publishUpDate  = strtotime( $row->publish_up );
					$publishUpMonth = date("n", $publishUpDate);
					$publishUpYear  = date("Y", $publishUpDate);
					
					//redirect to group calendar
					$redirect = JRoute::_( 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&month=' . $publishUpMonth . '&year=' . $publishUpYear, false );
					$this->setRedirect( $redirect );
					return;
				}
				else
				{
					JError::raiseError(403, JText::_('Event is group restricted.'));
					return;
				}
			}
			else
			{
				JError::raiseError(404, JText::_('EVENTS_CAL_LANG_NO_EVENTFOR') . ' ' . JText::_('EVENTS_CAL_LANG_THIS_DAY'));
				return;
			}
		}

		$event_up = new EventsDate($row->publish_up);
		$row->start_date = EventsHtml::getDateFormat($event_up->year,$event_up->month,$event_up->day,0);
		$row->start_time = (defined('_CAL_USE_STD_TIME') && _CAL_USE_STD_TIME == 'YES')
						 ? $event_up->get12hrTime()
						 : $event_up->get24hrTime();

		$event_down = new EventsDate($row->publish_down);
		$row->stop_date = EventsHtml::getDateFormat($event_down->year,$event_down->month,$event_down->day,0);
		$row->stop_time = (defined('_CAL_USE_STD_TIME') && _CAL_USE_STD_TIME == 'YES')
						? $event_down->get12hrTime()
						: $event_down->get24hrTime();

		// Kludge for overnight events, advance the displayed stop_date by 1 day when an overnighter is detected
		if ($row->stop_time < $row->start_time) 
		{
			$event_down->addDays(1);
		}

		// Get time zone name (i.e. not just offset - ex: '-5')
		$row->time_zone = EventsHtml::getTimeZoneName($row->time_zone);

		// Parse http and mailto
		$alphadigit = "([a-z]|[A-Z]|[0-9])";

		// Adresse
		$row->adresse_info = preg_replace("/(mailto:\/\/)?((-|$alphadigit|\.)+)@((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"mailto:$2@$5$8\">$2@$5$8</a>", $row->adresse_info);
		$row->adresse_info = preg_replace("/(http:\/\/)((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"http://$2$5$8\">$1$2$5$8</a>", $row->adresse_info);

		// Contact
		$row->contact_info = stripslashes(strip_tags($row->contact_info));
		if (substr($row->contact_info, 0, strlen('mailto:')) == 'mailto:') 
		{
			$row->contact_info = '<a href="mailto:' . $this->obfuscate(substr($row->contact_info, strlen('mailto:'))) . '">' . $this->obfuscate(substr($row->contact_info, strlen('mailto:'))) . '</a>';
		}
		else if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $row->contact_info))
		{
			$row->contact_info = '<a href="mailto:' . $this->obfuscate($row->contact_info) . '">' . $this->obfuscate($row->contact_info) . '</a>';
		}
		//$row->contact_info = preg_replace("/(mailto:\/\/)?((-|$alphadigit|\.)+)@((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"mailto:$2@$5$8\">$2@$5$8</a>", $row->contact_info);
		$row->contact_info = preg_replace("/(http:\/\/)((-|$alphadigit|\.)+)(\.$alphadigit+)/i", "<a href=\"http://$2$5$8\">$1$2$5$8</a>", $row->contact_info);

		// Images - replace the {mosimage} plugins in both text areas
		if ($row->images) 
		{
			$row->images = explode("\n", $row->images);
			$images = array();

			foreach ($row->images as $img)
			{
				$temp = explode('|', trim($img));
				if (!isset($temp[1]))
				{
					$temp[1] = "left";
				}

				if (!isset($temp[2]))
				{
					$temp[2] = "Image";
				}

				if (!isset($temp[3]))
				{
					$temp[3] = "0";
				}

				$images[] = '<img src="./images/stories/' . $temp[0] . '" style="float:' . $temp[1] . ';" alt="' . $temp[2] . '" />';
			}

			$text = explode('{mosimage}', $row->content);

			$row->content = $text[0];

			for ($i=0, $n=count($text)-1; $i < $n; $i++)
			{
				if (isset($images[$i])) 
				{
					$row->content .= $images[$i];
				}
				if (isset($text[$i+1])) 
				{
					$row->content .= $text[$i+1];
				}
			}
			unset($text);
		}

		$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
		$row->content = preg_replace_callback("/$UrlPtrn/", array('EventsHtml', 'autolink'), trim(stripslashes($row->content)));
		$row->content = nl2br($row->content);
		$row->content = str_replace("[[BR]]", '<br />', $row->content);
		$row->content = str_replace(" * ", '<br />&nbsp;&bull;&nbsp;', $row->content);
		//$row->content = stripslashes($row->content);

		$fields = $this->config->getCfg('fields');
		if (!empty($fields)) 
		{
			for ($i=0, $n=count($fields); $i < $n; $i++)
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], $this->parseTag($row->content, $fields[$i][0]));

				// Clean the original text of any matches
				$row->content = str_replace('<ef:' . $fields[$i][0] . '>' . end($fields[$i]) . '</ef:' . $fields[$i][0] . '>', '', $row->content);
			}
			$row->content = trim($row->content);
		}

		$bits = explode('-', $row->publish_up);
		$eyear = $bits[0];
		$emonth = $bits[1];
		$edbits = explode(' ', $bits[2]);
		$eday = $edbits[0];

		// Everyone has access unless restricted to admins in the configuration
		$authorized = $this->_authorize($row->created_by_alias);

		$auth = true;
		if ($this->config->getCfg('adminlevel')) 
		{
			$auth = $this->_authorize();
		}

		// Get a list of categories
		$categories = $this->_getCategories();

		// Get tags on this event
		$rt = new EventsTags($this->database);
		$tags = $rt->get_tag_cloud(0, 0, $row->id);

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . JText::_(strtoupper($this->_task)) . ': ' . stripslashes($row->title));

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(JText::_(
				strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			$eyear, 
			'index.php?option=' . $this->_option . '&year=' . $eyear
		);
		$pathway->addItem(
			$emonth, 
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth
		);
		$pathway->addItem(
			$eday, 
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth . '&day=' . $eday
		);
		$pathway->addItem(
			stripslashes($row->title), 
			'index.php?option=' . $this->_option . '&task=details&id=' . $row->id
		);

		// Incoming
		$alias = JRequest::getVar('page', '');

		// Load the current page
		$page = new EventsPage($this->database);
		if ($alias) 
		{
			$page->loadFromAlias($alias, $row->id);
		}

		// Get the pages for this workshop
		$pages = $page->loadPages($row->id);

		if ($alias) 
		{
			$pathway->addItem(
				stripslashes($page->title),
				'index.php?option=' . $this->_option . '&task=details&id=' . $row->id . '&page=' . $page->alias
			);
		}

		// Build the HTML
		$view = new JView(array(
			'name' => 'details'
		));
		if (JRequest::getVar('no_html', 0))
		{
			$view->setLayout('modal');
		}
		$view->option = $this->_option;
		$view->title = JText::_(strtoupper($this->_name)) . ': ' . JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task));
		$view->task = $this->_task;
		$view->year = $eyear;
		$view->month = $emonth;
		$view->day = $eday;
		$view->row = $row;
		$view->authorized = $authorized;
		$view->fields = $fields;
		$view->config = $this->config;
		$view->categories = $categories;
		$view->offset = $offset;
		$view->tags = $tags;
		$view->auth = $auth;
		$view->page = $page;
		$view->pages = $pages;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * Obfuscate an email adress
	 * 
	 * @param      string $email Address to obfuscate
	 * @return     string
	 */
	public function obfuscate($email)
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++) 
		{
			$obfuscatedEmail .= '&#' . ord($email[$i]) . ';';
		}
		
		return $obfuscatedEmail;
	}

	/**
	 * Display a form for registering for an event
	 * 
	 * @return     void
	 */
	protected function register()
	{
		$document =& JFactory::getDocument();

		// Get some needed info
		$offset = $this->offset;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$option = $this->_option;

		// Incoming
		$id = JRequest::getInt('id', 0, 'request');

		// Ensure we have an ID
		if (!$id) 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		// Load event
		$event = new EventsEvent($this->database);
		$event->load($id);

		// Ensure we have an event
		if (!$event->title || $event->registerby == '0000-00-00 00:00:00') 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		$auth = true;
		if ($this->config->getCfg('adminlevel')) 
		{
			$auth = $this->_authorize();
		}

		$bits = explode('-', $event->publish_up);
		$eyear = $bits[0];
		$emonth = $bits[1];
		$edbits = explode(' ', $bits[2]);
		$eday = $edbits[0];

		// Set the page title
		$document->setTitle(JText::_(strtoupper($this->_name)).': '.JText::_('EVENTS_REGISTER').': '.stripslashes($event->title));

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(JText::_(
				strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			$eyear, 
			'index.php?option=' . $this->_option . '&year=' . $eyear
		);
		$pathway->addItem(
			$emonth, 
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth
		);
		$pathway->addItem(
			$eday, 
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth . '&day=' . $eday
		);
		$pathway->addItem(
			stripslashes($event->title), 
			'index.php?option=' . $this->_option . '&task=details&id=' . $event->id
		);
		$pathway->addItem(
			JText::_('EVENTS_REGISTER'),
			'index.php?option=' . $this->_option . '&task=details&id=' . $event->id . '&page=register'
		);

		$page = new EventsPage($this->database);
		$page->alias = $this->_task;

		// Get the pages for this workshop
		$pages = $page->loadPages($event->id);

		// Check if registration is still open
		$registerby = Hubzero_View_Helper_Html::mkt($event->registerby);
		$now = time();

		$register = array();
		if (!$this->juser->get('guest')) 
		{
			$profile = new Hubzero_User_Profile();
			$profile->load($this->juser->get('id'));

			$register['firstname']   = $profile->get('givenName');
			$register['lastname']    = $profile->get('surname');
			$register['affiliation'] = $profile->get('organization');
			$register['email']       = $profile->get('email');
			$register['telephone']   = $profile->get('phone');
			$register['website']     = $profile->get('url');
		}

		// Is the registration open?
		if ($registerby >= $now) 
		{
			// Is the registration restricted?
			if ($event->restricted) 
			{
				$passwrd = JRequest::getVar('passwrd', '', 'post');

				if ($event->restricted == $passwrd) 
				{
					// Instantiate a view
					$view = new JView(array('name' => 'register'));
					$view->state = 'open';
				} 
				else 
				{
					// Instantiate a view
					$view = new JView(array('name' => 'register', 'layout' => 'restricted'));
					$view->state = 'restricted';
				}
			} 
			else 
			{
				// Instantiate a view
				$view = new JView(array('name' => 'register'));
				$view->state = 'open';
			}
		} 
		else 
		{
			// Instantiate a view
			$view = new JView(array('name' => 'register', 'layout' => 'closed'));
			$view->state = 'closed';
		}

		// Output HTML
		$view->option = $this->_option;
		$view->title = JText::_(strtoupper($this->_name)) . ': ' . JText::_('EVENTS_REGISTER');
		$view->task = $this->_task;
		$view->year = $year;
		$view->month = $month;
		$view->day = $day;
		$view->offset = $offset;
		$view->event = $event;
		$view->authorized = $auth;
		$view->page = $page;
		$view->pages = $pages;
		$view->register = $register;
		$view->arrival = null;
		$view->departure = null;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}
		}
		$view->display();
	}

	/**
	 * Process event registration
	 * 
	 * @return     void
	 */
	protected function process()
	{
		$document =& JFactory::getDocument();

		// Get some needed info
		$offset = $this->offset;
		$year   = $this->year;
		$month  = $this->month;
		$day    = $this->day;
		$option = $this->_option;

		// Incoming
		$id = JRequest::getInt('id', 0, 'post');

		// Ensure we have an ID
		if (!$id) 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		// Load event
		$event = new EventsEvent($this->database);
		$event->load($id);
		$this->event = $event;

		// Ensure we have an event
		if (!$event->title) 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		$auth = true;
		if ($this->config->getCfg('adminlevel')) 
		{
			$auth = $this->_authorize();
		}

		$bits = explode('-', $event->publish_up);
		$eyear = $bits[0];
		$emonth = $bits[1];
		$edbits = explode(' ', $bits[2]);
		$eday = $edbits[0];

		$page = new EventsPage($this->database);
		$page->alias = $this->_task;

		// Get the pages for this workshop
		$pages = $page->loadPages($event->id);

		// Set the page title
		$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . JText::_('EVENTS_REGISTER') . ': ' . stripslashes($event->title));

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(JText::_(
				strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			$eyear, 
			'index.php?option=' . $this->_option . '&year=' . $eyear
		);
		$pathway->addItem(
			$emonth, 
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth
		);
		$pathway->addItem(
			$eday, 
			'index.php?option=' . $this->_option . '&year=' . $eyear . '&month=' . $emonth . '&day=' . $eday
		);
		$pathway->addItem(
			stripslashes($event->title), 
			'index.php?option=' . $this->_option . '&task=details&id=' . $event->id
		);
		$pathway->addItem(
			JText::_('EVENTS_REGISTER'),
			'index.php?option=' . $this->_option . '&task=details&id=' . $event->id . '&page=register'
		);

		// Incoming
		$register   = JRequest::getVar('register', NULL, 'post');
		$arrival    = JRequest::getVar('arrival', NULL, 'post');
		$departure  = JRequest::getVar('departure', NULL, 'post');
		$dietary    = JRequest::getVar('dietary', NULL, 'post');
		$bos        = JRequest::getVar('bos', NULL, 'post');
		$dinner     = JRequest::getVar('dinner', NULL, 'post');
		$disability = JRequest::getVar('disability', NULL, 'post');
		$race       = JRequest::getVar('race', NULL, 'post');

		if ($register) 
		{
			$register = array_map('trim', $register);
			$register = array_map(array('Hubzero_View_Helper_Html', 'purifyText'), $register);

			$validemail = $this->_validEmail($register['email']);
		}
		if ($arrival) 
		{
			$arrival = array_map('trim', $arrival);
			$arrival = array_map(array('Hubzero_View_Helper_Html', 'purifyText'), $arrival);
		}
		if ($departure) 
		{
			$departure = array_map('trim', $departure);
			$departure = array_map(array('Hubzero_View_Helper_Html', 'purifyText'), $departure);
		}
		if ($dietary) 
		{
			$dietary = array_map('trim', $dietary);
			$dietary = array_map(array('Hubzero_View_Helper_Html', 'purifyText'), $dietary);
		}

		if ($register['firstname'] && $register['lastname'] && $register['affiliation'] && ($validemail == 1)) 
		{
			$jconfig =& JFactory::getConfig();

			$email = $event->email;
			$subject = JText::_('EVENTS_EVENT_REGISTRATION') . ' (' . $event->id . ')';
			$hub = array(
				'email' => $register['email'],
				'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_('EVENTS_EVENT_REGISTRATION')
			);

			$eview = new JView(array('name'=>'register','layout'=>'email'));
			$eview->option = $this->_option;
			$eview->sitename= $jconfig->getValue('config.sitename');
			$eview->register = $register;
			$eview->race = $race;
			$eview->dietary = $dietary;
			$eview->disability = $disability;
			$eview->arrival = $arrival;
			$eview->departure = $departure;
			$eview->dinner = $dinner;
			$eview->bos = $bos;
			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			$this->_sendEmail($hub, $email, $subject, $message);

			$this->_log($register);

			$view = new JView(array('name' => 'register', 'layout' => 'thanks'));
		} 
		else 
		{
			$view = new JView(array('name' => 'register'));
		}
		$view->state = 'open';
		$view->option = $this->_option;
		$view->title = JText::_(strtoupper($this->_name)) . ': ' . JText::_('EVENTS_REGISTER');
		$view->task = $this->_task;
		$view->year = $year;
		$view->month = $month;
		$view->day = $day;
		$view->offset = $offset;
		$view->event = $event;
		$view->authorized = $auth;
		$view->page = $page;
		$view->pages = $pages;
		$view->register = $register;
		$view->arrival = $arrival;
		$view->departure = $departure;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Log someone registering for an event
	 * 
	 * @param      unknown $reg Parameter description (if any) ...
	 * @return     void
	 */
	private function _log($reg)
	{
		$this->database->setQuery(
			'INSERT INTO #__events_respondents(
				event_id,
				first_name, last_name, affiliation, title, city, state, zip, country, telephone, fax, email,
				website, position_description, highest_degree, gender, arrival, departure, disability_needs, 
				dietary_needs, attending_dinner, abstract, comment
			)
			VALUES (' .
				$this->event->id . ', ' .
				$this->_getValueString($this->database, $reg, array(
					'firstname', 'lastname', 'affiliation', 'title', 'city', 'state', 'postalcode', 'country', 'telephone', 'fax', 'email',
					'website', 'position', 'degree', 'gender', 'arrival', 'departure', 'disability',
					'dietary', 'dinner', 'additional', 'comments'
				)) .
			')'
		);
		$this->database->query();
		$races = JRequest::getVar('race', NULL, 'post');
		if (!is_null($races) && (!isset($races['refused']) || !$races['refused'])) 
		{
			$resp_id = $this->database->insertid();
			foreach (array('nativeamerican', 'asian', 'black', 'hawaiian', 'white', 'hispanic') as $race)
			{
				if (array_key_exists($race, $races) && $races[$race]) 
				{
					$this->database->execute(
						'INSERT INTO #__events_respondent_race_rel(respondent_id, race, tribal_affiliation) 
						VALUES (' . $resp_id . ', \'' . $race . '\', ' . ($race == 'nativeamerican' ? $this->database->quote($races['nativetribe']) : 'NULL') . ')'
					);
				}
			}
		}
	}

	/**
	 * Short description for '_getValueString'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      array $reg Parameter description (if any) ...
	 * @param      array $values Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _getValueString($database, $reg, $values)
	{
		$rv = array();
		foreach ($values as $val)
		{
			switch ($val)
			{
				case 'position':
					$rv[] = ((isset($reg['position']) || isset($reg['position_other'])) && ($reg['position'] || $reg['position_other']))
						? $this->database->quote(
							$reg['position'] ? $reg['position'] : $reg['position_other']
						)
						: 'NULL';
				break;
				case 'gender':
					$rv[] = (isset($reg['sex']) && ($reg['sex'] == 'male' || $reg['sex'] == 'female'))
						? '\'' . substr($reg['sex'], 0, 1) . '\''
						: 'NULL';
				break;
				case 'dinner':
					$dinner = JRequest::getVar('dinner', NULL, 'post');
					$rv[] = is_null($dinner) ? 'NULL' : $dinner ? '1' : '0';
				break;
				case 'dietary':
					$rv[] = (($dietary = JRequest::getVar('dietary', NULL, 'post')))
						? $this->database->quote($dietary['specific'])
						: 'NULL';
				break;
				case 'arrival': case 'departure':
					$rv[] = ($date = JRequest::getVar($val, NULL, 'post'))
						? $this->database->quote($date['day'] . ' ' . $date['time'])
						: 'NULL';
				break;
				case 'disability':
					$disability = JRequest::getVar('disability', NULL, 'post');
					$rv[] = ($disability) ? '1' : '0';
				break;
				default:
					$rv[] = array_key_exists($val, $reg) && isset($reg[$val]) ? $this->database->quote($reg[$val]) : 'NULL';
			}
		}
		return implode($rv, ',');
	}

	/**
	 * Redirect to login form
	 * 
	 * @return     void
	 */
	protected function login()
	{
		$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn)),
			JText::_('EVENTS_LOGIN_NOTICE'),
			'warning'
		);
	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $row Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function edit($row=NULL)
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			$pathway =& JFactory::getApplication()->getPathway();
			if (count($pathway->getPathWay()) <= 0) 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_name)), 
					'index.php?option=' . $this->_option
				);
			}
			$pathway->addItem(
				JText::_('EVENTS_CAL_LANG_ADD_TITLE'), 
				'index.php?option=' . $this->_option . '&task=add'
			);

			$this->login();
			return;
		}

		// Push some styles to the tmeplate
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'calendar.css');

		$this->_getScripts('assets/js/' . $this->_name);
		if (!JPluginHelper::isEnabled('system', 'jquery'))
		{
			$this->_getScripts('assets/js/calendar.rc4');
		}

		// We need at least one category before we can proceed
		$cat = new EventsCategory($this->database);
		if ($cat->getCategoryCount($this->_option) < 1) 
		{
			JError::raiseError(500, JText::_('EVENTS_LANG_NEED_CATEGORY'));
			return;
		}

		// Incoming
		$id = JRequest::getInt('id', 0, 'request');

		// Load event object
		if (!is_object($row)) 
		{
			$row = new EventsEvent($this->database);
			$row->load($id);
		}

		// Do we have an ID?
		if ($row->id) 
		{
			// Yes - edit mode

			// Are they authorized to make edits?
			if (!$this->_authorize($row->created_by)) 
			{
				// Not authorized - redirect
				$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
				return;
			}

			$event_up = new EventsDate($row->publish_up);
			$start_publish = sprintf("%4d-%02d-%02d",$event_up->year,$event_up->month,$event_up->day);
			$start_time = $event_up->hour . ':' . $event_up->minute;

			$event_down = new EventsDate($row->publish_down);
			$stop_publish = sprintf("%4d-%02d-%02d",$event_down->year,$event_down->month,$event_down->day);
			$end_time = $event_down->hour . ':' . $event_down->minute;

			$time_zone = $row->time_zone;

			$event_registerby = new EventsDate($row->registerby);
			$registerby_date = sprintf("%4d-%02d-%02d",$event_registerby->year,$event_registerby->month,$event_registerby->day);
			$registerby_time = $event_registerby->hour . ':' . $event_registerby->minute;

			$row->reccurday_month = 99;
			$row->reccurday_week = 99;
			$row->reccurday_year = 99;

			if ($row->reccurday <> '') 
			{
				if ($row->reccurtype == 1) 
				{
					$row->reccurday_week = $row->reccurday;
				} 
				elseif ($row->reccurtype == 3) 
				{
					$row->reccurday_month = $row->reccurday;
				} 
				elseif ($row->reccurtype == 5) 
				{
					$row->reccurday_year = $row->reccurday;
				}
			}
			$arr = array(
				JHTML::_('select.option', 0, strtolower(JText::_('EVENTS_NO')), 'value', 'text'),
				JHTML::_('select.option', 1, strtolower(JText::_('EVENTS_YES')), 'value', 'text'),
			);

			$lists['state'] = JHTML::_('select.genericlist', $arr, 'state', '', 'value', 'text', $row->state, false, false);
		} 
		else 
		{
			if ($row->publish_up && $row->publish_up != '0000-00-00 00:00:00') 
			{
				$event_up = new EventsDate($row->publish_up);
				$start_publish = sprintf("%4d-%02d-%02d", $event_up->year, $event_up->month, $event_up->day);
				$start_time = $event_up->hour . ':' . $event_up->minute;

				$event_down = new EventsDate($row->publish_down);
				$stop_publish = sprintf("%4d-%02d-%02d", $event_down->year, $event_down->month, $event_down->day);
				$end_time = $event_down->hour . ':' . $event_down->minute;

				$time_zone = $row->time_zone;

				$event_registerby = new EventsDate($row->registerby);
				$registerby_date = sprintf("%4d-%02d-%02d", $event_registerby->year, $event_registerby->month, $event_registerby->day);
				$registerby_time = $event_registerby->hour . ':' . $event_registerby->minute;
			} 
			else 
			{
				// No ID - we're creating a new event
				$year  = $this->year;
				$month = $this->month;
				$day   = $this->day;

				if ($year && $month && $day) 
				{
					$start_publish = $year . '-' . $month . '-' . $day;
					$stop_publish = $year . '-' . $month . '-' . $day;
					$registerby_date = $year . '-' . $month . '-' . $day;
				} 
				else 
				{
					$offset = $this->offset;

					$start_publish = strftime("%Y-%m-%d", time()+($offset*60*60)); //date("Y-m-d");
					$stop_publish = strftime("%Y-%m-%d", time()+($offset*60*60));  //date("Y-m-d");
					$registerby_date = strftime("%Y-%m-%d", time()+($offset*60*60));  //date("Y-m-d");
				}

				$start_time = "08:00";
				$end_time = "17:00";
				$registerby_time = "08:00";
				$time_zone = -5;
			}

			// If user hits refresh, try to maintain event form state
			$row->bind($_POST);
			$row->reccurday_month = -1;
			$row->reccurday_week = -1;
			$row->reccurday_year = -1;
			$row->created_by_alias = $this->juser->get('username');
			$row->created_by = $this->juser->get('id');
			$row->reccurtype = 0;

			$lists = '';
		}

		// Get custom fields
		$fields = $this->config->getCfg('fields');
		if (!empty($fields)) 
		{
			for ($i=0, $n=count($fields); $i < $n; $i++)
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], $this->parseTag($row->content, $fields[$i][0]));

				// Clean the original text of any matches
				$row->content = str_replace('<ef:' . $fields[$i][0] . '>' . end($fields[$i]) . '</ef:' . $fields[$i][0] . '>', '', $row->content);
			}
			$row->content = trim($row->content);
		}

		list($start_hrs, $start_mins) = explode(':', $start_time);
		list($end_hrs, $end_mins) = explode(':', $end_time);
		list($registerby_hrs, $registerby_mins) = explode(':', $registerby_time);
		$start_pm = false;
		$end_pm = false;
		$registerby_pm = false;
		if ($this->config->getCfg('calUseStdTime') == 'YES') 
		{
			$start_hrs = intval($start_hrs);
			if ($start_hrs >= 12) $start_pm = true;
			if ($start_hrs > 12) $start_hrs -= 12;
			else if ($start_hrs == 0) $start_hrs = 12;
			if (strlen($start_mins) == 1) $start_mins = '0'.$start_mins;
			$start_time = $start_hrs . ':' . $start_mins;

			$end_hrs = intval($end_hrs);
			if ($end_hrs >= 12) $end_pm = true;
			if ($end_hrs > 12) $end_hrs -= 12;
			else if ($end_hrs == 0) $end_hrs = 12;

			$registerby_hrs = intval($registerby_hrs);
			if ($registerby_hrs >= 12) $registerby_pm = true;
			if ($registerby_hrs > 12) $registerby_hrs -= 12;
			else if ($registerby_hrs == 0) $registerby_hrs = 12;
			if (strlen($registerby_mins) == 1) $registerby_mins = '0' . $registerby_mins;
			$registerby_time = $registerby_hrs . ':' . $registerby_mins;
		}
		if (strlen($start_mins) == 1) $start_mins = '0' . $start_mins;
		if (strlen($start_hrs) == 1) $start_hrs = '0' . $start_hrs;
		$start_time = $start_hrs . ':' . $start_mins;

		if (strlen($end_mins) == 1) $end_mins = '0' . $end_mins;
		if (strlen($end_hrs) == 1) $end_hrs = '0' . $end_hrs;
		$end_time = $end_hrs . ':' . $end_mins;

		if (strlen($registerby_mins) == 1) $registerby_mins = '0' . $registerby_mins;
		if (strlen($registerby_hrs) == 1) $registerby_hrs = '0' . $registerby_hrs;
		$registerby_time = $registerby_hrs . ':' . $registerby_mins;

		$times = array();
		$times['start_publish'] = $start_publish;
		$times['start_time'] = $start_time;
		$times['start_pm'] = $start_pm;

		$times['stop_publish'] = $stop_publish;
		$times['end_time'] = $end_time;
		$times['end_pm'] = $end_pm;

		$times['time_zone'] = $time_zone;

		$times['registerby_date'] = $registerby_date;
		$times['registerby_time'] = $registerby_time;
		$times['registerby_pm'] = $registerby_pm;

		// Get tags on this event
		$rt = new EventsTags($this->database);
		$lists['tags'] = $rt->get_tag_string($row->id, 0, 0, NULL, 0, 1);

		// Set the title
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task)));

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}
		$p = 'index.php?option=' . $this->_option . '&task=' . $this->_task;
		if ($row->id) 
		{
			$p .= '&id=' . $row->id;
		}
		$pathway->addItem(
			JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task)), 
			$p
		);
		if ($row->id) 
		{
			$pathway->addItem(
				stripslashes($row->title), 
				'index.php?option=' . $this->_option . '&task=details&id=' . $row->id
			);
		}

		// Output HTML
		$view = new JView(array(
			'name' => 'edit'
		));
		$view->option = $this->_option;
		$view->title = JText::_(strtoupper($this->_name)) . ': ' . JText::_(strtoupper($this->_name) . '_' . strtoupper($this->_task));
		$view->task = $this->_task;
		$view->config = $this->config;
		$view->row = $row;
		$view->fields = $fields;
		$view->times = $times;
		$view->lists = $lists;
		$view->gid = $this->gid;
		$view->admin = $this->_authorize();
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}
		$view->display();
	}

	/**
	 * Delete an event
	 * 
	 * @return     void
	 */
	protected function delete()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			$this->login();
			return;
		}

		// Incoming
		$id = JRequest::getInt('id', 0, 'request');

		// Ensure we have an ID to work with
		if (!$id) 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		// Load event object
		$event = new EventsEvent($this->database);
		$event->load($id);

		if (!$this->_authorize($event->created_by)) 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
			return;
		}

		// Delete the event
		$event->delete($id);

		// Delete any associated pages 
		$ep = new EventsPage($this->database);
		$ep->deletePages($id);

		// Delete any associated respondents
		$er = new EventsRespondent(array());
		$er->deleteRespondents($id);

		// Delete tags on this event
		$rt = new EventsTags($this->database);
		$rt->remove_all_tags($id);

		// Load the event's category and update the count
		$category = new EventsCategory($this->database);
		$category->updateCount($event->catid);

		$jconfig =& JFactory::getConfig();

		// E-mail subject line
		$subject  = '[' . $jconfig->getValue('config.sitename') . ' ' . JText::_('EVENTS') . '] - ' . JText::_('EVENTS_EVENT_DELETED');

		// Build the message to be e-mailed
		$eview = new JView(array(
			'name'   => 'emails',
			'layout' => 'deleted'
		));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->event = $event;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Send the e-mail
		$this->_sendMail($jconfig->getValue('config.sitename'), $jconfig->getValue('config.mailfrom'), $subject, $message);

		// Go back to the default front page
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option);
	}

	/**
	 * Save an event
	 * 
	 * @return     void
	 */
	protected function save()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			$this->login();
			return;
		}

		$offset = $this->offset;

		// Incoming
		$start_time = JRequest::getVar('start_time', '08:00', 'post');
		$state_time = ($start_time) ? $start_time : '08:00';
		$start_pm   = JRequest::getInt('start_pm', 0, 'post');
		$end_time   = JRequest::getVar('end_time', '17:00', 'post');
		$end_time   = ($end_time) ? $end_time : '17:00';
		$end_pm     = JRequest::getInt('end_pm', 0, 'post');
		$time_zone	= JRequest::getVar('time_zone', -5, 'post');

		$reccurweekdays = JRequest::getVar('reccurweekdays', array(), 'post');
		$reccurweeks    = JRequest::getVar('reccurweeks', array(), 'post');
		$reccurday_week = JRequest::getVar('reccurday_week', '', 'post');
		$reccurday_year = JRequest::getVar('reccurday_year', '', 'post');

		// Bind the posted data to an event object
		$row = new EventsEvent($this->database);
		if (!$row->bind($_POST)) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// New entry or existing?
		if ($row->id) 
		{
			// Existing - update modified info
			$row->modified = strftime("%Y-%m-%d %H:%M:%S", time()+($offset*60*60));
			if ($this->juser->get('id')) 
			{
				$row->modified_by = $this->juser->get('id');
			}
		} 
		else 
		{
			// New - set created info
			$row->created = strftime("%Y-%m-%d %H:%M:%S", time()+($offset*60*60));
			if ($this->juser->get('id')) 
			{
				$row->created_by = $this->juser->get('id');
			}
		}

		// Set some fields and do some cleanup work
		if (is_null($row->useCatColor)) 
		{
			$row->useCatColor = 0;
		}
		if ($row->catid) 
		{
			$row->catid = intval($row->catid);
		}

		$row->title = htmlentities($row->title);

		$row->content = $_POST['econtent'];
		$row->content = $this->_clean($row->content);

		// Get the custom fields defined in the events configuration
		if (isset($_POST['fields'])) 
		{
			$fields = $_POST['fields'];
			$fields = array_map('trim', $fields);

			// Wrap up the content of the field and attach it to the event content
			$fs = $this->config->fields;
			foreach ($fields as $param=>$value)
			{
				if (trim($value) != '') 
				{
					$row->content .= '<ef:' . $param . '>' . $this->_clean($value) . '</ef:' . $param . '>';
				} 
				else 
				{
					foreach ($fs as $f)
					{
						if ($f[0] == $param && end($f) == 1) 
						{
							JError::raiseError(500, JText::sprintf('EVENTS_REQUIRED_FIELD_CHECK', $f[1]));
							return;
						}
					}
				}
			}
		}

		// Clean adresse
		$row->adresse_info = $this->_clean($row->adresse_info);

		// Clean contact
		$row->contact_info = $this->_clean($row->contact_info);

		// Clean extra
		$row->extra_info = $this->_clean($row->extra_info);

		// Prepend http:// to URLs without it
		if ($row->extra_info != NULL) 
		{
			if ((substr($row->extra_info, 0, 7) != 'http://') && (substr($row->extra_info, 0, 8) != 'https://')) 
			{
				$row->extra_info = 'http://' . $row->extra_info;
			}
		}

		$row->created_by_alias = htmlentities($row->created_by_alias);

		// Reformat the time into 24hr format if necessary
		if ($this->config->getCfg('calUseStdTime') =='YES') 
		{
			list($hrs, $mins) = explode(':', $start_time);
			$hrs = intval($hrs);
			$mins = intval($mins);
			if ($hrs != 12 && $start_pm) $hrs += 12;
			else if ($hrs == 12 && !$start_pm) $hrs = 0;
			if ($hrs < 10) $hrs = '0' . $hrs;
			if ($mins < 10) $mins = '0' . $mins;
			$start_time = $hrs . ':' . $mins;

			list($hrs, $mins) = explode(':', $end_time);
			$hrs = intval($hrs);
			$mins = intval($mins);
			if ($hrs!= 12 && $end_pm) $hrs += 12;
			else if ($hrs == 12 && !$end_pm) $hrs = 0;
			if ($hrs < 10) $hrs = '0' . $hrs;
			if ($mins < 10) $mins = '0' . $mins;
			$end_time = $hrs . ':' . $mins;
		}

		$rpup = $row->publish_up;
		if ($row->publish_up) 
		{
			$publishtime = $row->publish_up . ' ' . $start_time . ':00';
			$row->publish_up = strftime("%Y-%m-%d %H:%M:%S", strtotime($publishtime));
		} 
		else 
		{
			$row->publish_up = strftime("%Y-%m-%d 00:00:00", time()+($offset*60*60));
		}

		if ($row->publish_down) 
		{
			$publishtime = $row->publish_down . ' ' . $end_time . ':00';
			$row->publish_down = strftime("%Y-%m-%d %H:%M:%S", strtotime($publishtime));
		} 
		else 
		{
			$publishtime = $rpup . ' ' . $end_time . ':00';
			//$row->publish_down = strftime("%Y-%m-%d 23:59:59", time()+($offset*60*60));
			$row->publish_down = strftime("%Y-%m-%d %H:%M:%S", strtotime($publishtime));
		}

		if ($row->publish_up <> $row->publish_down) 
		{
			$row->reccurtype = intval($row->reccurtype);
		} 
		else 
		{
			$row->reccurtype = 0;
		}

		switch ($row->reccurtype)
		{
			case 0:
				$row->reccurday = '';
			break;
			case 1:
				$row->reccurday =  $reccurday_week;
			break;
			case 2:
				$row->reccurday = '';
			break;
			case 3:
				$row->reccurday = $reccurday_month;
			break;
			case 4:
				$row->reccurday = '';
			break;
			case 5:
				$row->reccurday = $reccurday_year;
			break;
		}

		// Reccur week days
		if (empty($reccurweekdays)) 
		{
			$weekdays = '';
		} 
		else 
		{
			$weekdays = implode('|', $reccurweekdays);
		}
		$row->reccurweekdays = $weekdays;

		// Reccur viewable weeks
		$reccurweekss = JRequest::getVar('reccurweekss', '', 'post');
		$reccurweeks = array();
		if ($reccurweekss) 
		{
			$reccurweeks[] = $reccurweekss;
		}
		if (empty($reccurweeks)) 
		{
			$weekweeks = '';
		} 
		else 
		{
			$weekweeks = implode('|', $reccurweeks);
		}
		$row->reccurweeks = $weekweeks;

		// Always unpublish if no Publisher otherwise publish automatically
		if ($this->config->getCfg('adminlevel')) 
		{
			$row->state = 0;
		} 
		else 
		{
			$row->state = 1;
		}

		$row->state = 1;
		$row->mask = 0;

		$pubdow = strtotime($row->publish_down);
		$pubup = strtotime($row->publish_up);
		if ($pubdow <= $pubup) 
		{
			// Set the error message
			$this->setError(JText::_('Event end time cannot be before event start time.'));
			// Fall through to the edit view
			$this->edit($row);
			return;
		}
		
		//set the scope to be regular events
		$row->scope = 'event';

		if (!$row->check()) 
		{
			// Set the error message
			$this->setError($row->getError());
			// Fall through to the edit view
			$this->edit($row);
			return;
		}
		if (!$row->store()) 
		{
			// Set the error message
			$this->setError($row->getError());
			// Fall through to the edit view
			$this->edit($row);
			return;
		}
		$row->checkin();

		// Incoming tags
		$tags = JRequest::getVar('tags', '', 'post');

		// Save the tags
		$rt = new EventsTags($this->database);
		$rt->tag_object($this->juser->get('id'), $row->id, $tags, 1, 0);

		$jconfig =& JFactory::getConfig();

		// Build the message to be e-mailed
		if ($state == 'add') 
		{
			$subject  = '[' . $jconfig->getValue('config.sitename') . ' ' . JText::_('EVENTS_CAL_LANG_CAL_TITLE') . '] - ' . JText::_('EVENTS_CAL_LANG_MAIL_ADDED');

			$eview = new JView(array('name'=>'emails','layout'=>'created'));
		} 
		else 
		{
			$subject  = '[' . $jconfig->getValue('config.sitename') . ' ' . JText::_('EVENTS_CAL_LANG_CAL_TITLE') . '] - ' . JText::_('EVENTS_CAL_LANG_MAIL_ADDED');

			$eview = new JView(array('name'=>'emails','layout'=>'edited'));
		}
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->row = $row;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Send the e-mail
		$this->_sendMail($jconfig->getValue('config.sitename'), $jconfig->getValue('config.mailfrom'), $subject, $message);

		// Redirect to the details page for the event we just created
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&task=details&id=' . $row->id);
	}

	/**
	 * Send an email
	 * 
	 * @param      array &$hub Parameter description (if any) ...
	 * @param      unknown $email Parameter description (if any) ...
	 * @param      unknown $subject Parameter description (if any) ...
	 * @param      unknown $message Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	private function _sendEmail(&$hub, $email, $subject, $message)
	{
		if ($hub) 
		{
			$jconfig =& JFactory::getConfig();
			$contact_email = $hub['email'];
			$contact_name  = $hub['name'];

			$args = "-f '" . $contact_email . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
			$headers .= 'From: ' . $contact_name .' <' . $contact_email . ">\n";
			$headers .= 'Reply-To: ' . $contact_name .' <' . $contact_email . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '.  $jconfig->getValue('config.sitename') ."\n";
			if (mail($email, $subject, $message, $headers, $args)) 
			{
				return(1);
			}
		}
		return(0);
	}

	/**
	 * Check if an email address is valid
	 * 
	 * @param      string $email Email address to check
	 * @return     integer 1 = valid, 0 = invalid
	 */
	private function _validEmail($email)
	{
		if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
			return(1);
		} 
		else 
		{
			return(0);
		}
	}

	/**
	 * Get all the events categories
	 * 
	 * @return     array
	 */
	private function _getCategories()
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$sql = "SELECT * FROM #__categories WHERE section='" . $this->_option . "' AND published = '1' ORDER BY ordering ASC";
		}
		else
		{
			$sql = "SELECT * FROM #__categories WHERE extension='" . $this->_option . "' AND published = '1' ORDER BY lft ASC";
		}

		$this->database->setQuery($sql);
		$cats = $this->database->loadObjectList();

		$c = array();
		foreach ($cats as $cat)
		{
			$c[$cat->id] = $cat->title;
		}

		return $c;
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
	public function parseTag($text, $tag)
	{
		preg_match("#<ef:" . $tag . ">(.*?)</ef:" . $tag . ">#s", $text, $matches);
		if (count($matches) > 0) 
		{
			$match = $matches[0];
			$match = str_replace('<ef:' . $tag . '>', '', $match);
			$match = str_replace('</ef:' . $tag . '>', '', $match);
		} 
		else 
		{
			$match = '';
		}
		return $match;
	}

	/**
	 * Short description for '_clean'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _clean($string)
	{
		if (get_magic_quotes_gpc()) 
		{
			$string = stripslashes($string);
		}

		// strip out any KL_PHP, script, style, HTML comments
		$string = preg_replace('/{kl_php}(.*?){\/kl_php}/s', '', $string);
		$string = preg_replace("'<head[^>]*?>.*?</head>'si", '', $string);
		$string = preg_replace("'<body[^>]*?>.*?</body>'si", '', $string);
		$string = preg_replace("'<style[^>]*>.*?</style>'si", '', $string);
		$string = preg_replace("'<script[^>]*>.*?</script>'si", '', $string);
		$string = preg_replace('/<!--.+?-->/', '', $string);

		$string = str_replace(array("&amp;","&lt;","&gt;"),array("&amp;amp;","&amp;lt;","&amp;gt;",),$string);
		// fix &entitiy\n;

		$string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "$1;", $string);
		$string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', "$1$2;", $string);
		$string = html_entity_decode($string, ENT_COMPAT, "UTF-8");

		// remove any attribute starting with "on" or xmlns
		$string = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu', "$1>", $string);
		// remove javascript: and vbscript: protocol
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string);
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string);
		//<span style="width: expression(alert('Ping!'));"></span> 
		// only works in ie...
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU', "$1>", $string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU', "$1>", $string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu', "$1>", $string);
		//remove namespaced elements (we do not need them...)
		$string = preg_replace('#</*\w+:\w[^>]*>#i',"",$string);
		//remove really unwanted tags
		do {
			$oldstring = $string;
			$string = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|input|select|textarea|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', '', $string);
		} while ($oldstring != $string);

		return $string;
	}

	/**
	 * Short description for '_sendMail'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      string $email Parameter description (if any) ...
	 * @param      unknown $subject Parameter description (if any) ...
	 * @param      unknown $message Parameter description (if any) ...
	 * @return     void
	 */
	private function _sendMail($name, $email, $subject, $message)
	{
		$name .= ' ' . JText::_('EVENTS_ADMINISTRATOR');

		$headers  = "";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "From: " . $name . " <" . $email . ">\r\n";
		$headers .= "Reply-To: <" . $email . ">\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-MSMail-Priority: Low\r\n";
		$headers .= "X-Mailer: Joomla 1.5\r\n";

		@mail($email, $subject, $message, $headers);
	}

	/**
	 * Short description for '_authorize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	protected function _authorize($id='')
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return false;
		}

		// Check if they're a site admin from Joomla
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			if ($this->juser->authorise('core.admin', $this->_option . '.component'))
			{
				return true;
			}
		}
		else 
		{
			if ($this->juser->authorize($this->_option, 'manage')) 
			{
				return true;
			}
		}

		// Check against events configuration
		if (!$this->config->getCfg('adminlevel')) 
		{
			if ($id && $id == $this->juser->get('id')) 
			{
				return true;
			}
		}

		return false;
	}
}

