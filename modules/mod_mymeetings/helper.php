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

class modMyMeetings
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
	
	private function _checkmeetings($rows)
	{
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			$currentuser = $juser->get('username');
		} else {
			$currentuser = '';
		}

		foreach ($rows as $row)
		{
			// Check if user is PARTCIPANT
			if ($row->participants != '') {
				$participants_temp = explode(',',$row->participants);
				foreach ($participants_temp as $temp)
				{
					$temp = trim($temp);
					if ($currentuser == $temp) {
						return('hasmeetings');
					}
				}
			}

			// Check if user is PRESENTER
			if ($row->presenters) {
				$presenters_temp = explode(',',$row->presenters);
				foreach ($presenters_temp as $temp) 
				{
					$temp = trim($temp);
					if ($currentuser == $temp) {
						return('hasmeetings');
					}
				}
			}

			// Check if user is CO-HOST
			if ($row->hosts) {
				$hosts_temp = explode(',',$row->hosts);
				foreach ($hosts_temp as $temp) 
				{
					$temp = trim($temp);
					if ($currentuser == $temp) {
						return('hasmeetings');
					}
				}
			}

			// Check if user is OWNER
			if ($currentuser == $row->owner) {
				return('hasmeetings');
			}
		}
		return(null);
	}

	//-----------

	public function display()
	{
		include_once( JPATH_ROOT.DS.'components'.DS.'com_meetings'.DS.'meetings.class.php' );

		$this->option = 'com_meetings';

		// Get the component parameters
		$mconfig =& JComponentHelper::getParams( $this->option );
		$this->mconfig = $mconfig;
		
		$this->error = false;
		if (!$mconfig->get('breezeServer')) {
			$this->error = true;
			return false;
		}

		$database =& JFactory::getDBO();

		$mm = new MeetingsMeeting( $database );
		$this->rows = $mm->getModRecords( date('Y-m-d') );
		
		$this->has_meetings = false;
		if ($this->rows) {
			$this->has_meetings = $this->_checkmeetings($this->rows);
		}
		
		if ($this->has_meetings) {
			// Push the module CSS to the template
			ximport('xdocument');
			XDocument::addModuleStyleSheet('mod_mymeetings');
		}
	}
}
