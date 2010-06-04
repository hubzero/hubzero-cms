<?php
/**
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * @license	GNU General Public License, version 2 (GPLv2) 
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

ximport('Hubzero_Controller');

class MeetingsController extends Hubzero_Controller
{
	public function execute()
	{
		// Get the component parameters
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		$default = 'browse';
		
		$task = strtolower(JRequest::getVar('task', $default, 'default'));
		
		$thisMethods = get_class_methods( get_class( $this ) );
		if (!in_array($task, $thisMethods)) {
			$task = $default;
			if (!in_array($task, $thisMethods)) {
				return JError::raiseError( 404, JText::_('Task ['.$task.'] not found') );
			}
		}

		$this->_task = $task;
		$this->$task();
	}

	//----------------------------------------------------------
	//  Views
	//----------------------------------------------------------
	
	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'meetings') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get filters
		$view->filters = array();
		$view->filters['search'] = urldecode(JRequest::getString('search'));
		$view->filters['sortby'] = JRequest::getVar( 'sortby', 'date_begin' );
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = JRequest::getInt('limitstart', 0);

		$obj = new MeetingsMeeting( $this->database );
		
		// Get a record count
		$view->total = $obj->getCount( $view->filters );
		
		// Get records
		$view->rows = $obj->getRecords( $view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function add()
	{
		$this->edit();
	}

	//-----------

	protected function edit() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'meeting') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$view->timezones = array(
			'EST' => 'EST',
			'CST' => 'CST',
			'MST' => 'MST',
			'PST' => 'PST');

		$id = JRequest::getInt('id', 0);
		
		// initiate database class and load info
		$view->row = new MeetingsMeeting( $this->database );
		$view->row->load( $id );

		if (!$id) {
			$view->row->date_begin = date( "Y-m-d H:i:s" );
			$view->row->date_end = date( "Y-m-d H:i:s" );
		}

		if ($view->row->time_zone_A) {
			$view->timezone = $view->row->time_zone_A;
		} else {
			$view->timezone = 'EST';
		}

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------
	
	protected function save() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$meeting = JRequest::getVar('meeting', array(), 'post');
		
		// Trim all posted items
		$meeting = array_map('trim',$meeting);

		// Initiate class and bind posted items to database fields
		$row = new MeetingsMeeting( $this->database );
		if (!$row->bind( $meeting )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		if ($row->id < 1) {
			// New entry
			$row->date_created = $row->date_created ? $row->date_created : date( "Y-m-d H:i:s" );
			$row->time_zone = '-05:00';
		} else {
			// Updating entry
			$row->date_modified = date( "Y-m-d H:i:s" );
			$row->date_created = $row->date_created ? $row->date_created : date( "Y-m-d H:i:s" );
		}

		$row->duration = $this->_timeDifference($row->date_begin, $row->date_end);

		// Code cleaner for xhtml transitional compliance
		$row->description = str_replace( '<br>', '<br />', $row->description );
		
		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('MEETING_SAVED');
	}
	
	//-----------

	protected function remove() 
	{
		// Incoming
		$ids = JRequest::getVar( 'ids', array() );

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}
		
		// Do we have any IDs?
		if (!empty($ids)) {
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id) 
			{
				$row = new MeetingsMeeting( $this->database );
				$row->load( $id );

				$root_cookie = MeetingsHelper::loginroot($this->config);

				// Deleting the meeting with the sco-id supplied
				$xml_src = $this->config->get('breezeServer').'/api/xml?action=sco-delete&sco-id='.$row->room_id.'&session='.$root_cookie;
				$result = MeetingsParser::parse($xml_src);

				foreach ($result as $arr) 
				{
					if ($arr['code'] == 'ok') {
						$error = 'success';
						break;
					} else {
						$error = 'failure';
						break;
					}
				}

				if ($error != 'success') {
					JError::raiseError( 500, JText::_('ERROR_DELETING_ROOM') );
					return;	
				} else {
					// Logging out the secret root
					MeetingsHelper::logoutroot($this->config, $root_cookie);

					// Delete contributor info - done last just in case something else goes wrong
					$row->delete();
				}
			}
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('MEETING_DELETED');
	}
	
	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}
	
	//----------------------------------------------------------
	//  Private functions
	//----------------------------------------------------------
	
	private function _timeDifference($start, $end)
	{
		if ($start && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $start, $regs )) {
			$start = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}

		if ($end && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $end, $regs )) {
			$end = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}

		$difference = $end - $start;
		if ($difference < 0) $difference = 0;

		$days_left = floor($difference/60/60/24);
		$hours_left = floor(($difference - $days_left*60*60*24)/60/60);
		$minutes_left = floor(($difference - $days_left*60*60*24 - $hours_left*60*60)/60);

		$total_hours = ($days_left*24) + $hours_left;
		$minutes_left = ($minutes_left < 10) ? '0'.$minutes_left : $minutes_left;
		$left = $total_hours.':'.$minutes_left.':00.000';

		return $left;
	}
}
