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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'EventsModule'
 * 
 * Long description (if any) ...
 */
Class EventsModule
{

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $group Parameter description (if any) ...
	 * @return     void
	 */
	function __construct( $group )
	{
		//group object
		$this->group = $group;

	}

	/**
	 * Short description for 'onManageModules'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	function onManageModules()
	{
		$mod = array(
			'name' => 'events',
			'title' => 'Upcoming Events List',
			'description' => 'Displays a list of upcoming events taken from group calendar.',
			'input_title' => 'Number of Events to Display',
			'input' => "<input type=\"text\" name=\"module[content]\" value=\"{{VALUE}}\" />"
		);

		return $mod;
	}

	/**
	 * Short description for 'render'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	function render()
	{
		//var to hold content being returned
		$content  = '';

		//get the user
		$juser =& JFactory::getUser();

		//get the group
		$group = Hubzero_Group::getInstance($this->group->get('gidNumber'));

		//get the group members
		$members = $group->get('members');

		//get the calendar plugins access level
		$calendar_plugin_preference = $group->getPluginAccess('calendar');

		//if there isnt a preference set or there calendar plugin is set to hidden return nothing
		if($calendar_plugin_preference == 'nobody') {
			return;
		}

		//if calendar plugin access is limited to registered users and user is not logged in, show nothing
		if($calendar_plugin_preference == 'registered' && $juser->get('guest') == true) {
			return;
		}

		//if calendar access level is members and user is not a group member show nothing
		if($calendar_plugin_preference == 'members' && !in_array($juser->get('id'),$members)) {
			return;
		}

		//defautl # of events
		$default_num = 3;

		//get the number of events to show
		$num_events = (is_numeric($this->content)) ? $this->content : $default_num;

		//otherwise build the calendar
		$content .= $this->displayGroupEvents( $group, $num_events );

		//return the content
		return $content;
	}

	/**
	 * Short description for 'displayGroupEvents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $group Parameter description (if any) ...
	 * @param      string $num_events Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function displayGroupEvents( $group, $num_events )
	{
		//instantiate database
		$db =& JFactory::getDBO();

		//build query
		$sql = "SELECT * FROM #__xgroups_events 
				WHERE end >= NOW()
				AND gidNumber=".$db->Quote($this->group->get('gidNumber'))." 
				AND active=1 
				ORDER BY start ASC
				LIMIT ".$num_events;
		$db->setQuery($sql);
		$events = $db->loadAssocList();

		$content = "<div class=\"group_module_custom upcoming_events\">";
		$content .= "<h3>Upcoming Group Events</h3>";

		foreach($events as $event) {
			$content .= "<div class=\"event\">";

			$link = JRoute::_('index.php?option=com_groups&gid='.$group->get('cn').'&active=calendar&month='.date("m",strtotime($event['start'])).'&year='.date("Y",strtotime($event['start'])));
			$content .= "<a class=\"title\" href=\"{$link}\">{$event['title']}</a>";

			if(date("d",strtotime($event['start'])) == date("d",strtotime($event['end']))) {
				$date = date("M d, Y",strtotime($event['start'])) . ' @ ' . date("g:ia",strtotime($event['start'])) .' to '. date("g:ia",strtotime($event['end']));
			} else {
				$date = date("M d, Y g:ia",strtotime($event['start'])) . ' to <br>' . date("M d, Y g:ia",strtotime($event['end']));
			}

			$content .= "<span class=\"date\">{$date}</span>";

			$details = nl2br($event['details']);
			if(strlen($details) > 150) {
				$details = substr($details,0,150) . "...";
				//$details = $details . "...";
			}

			$content .= "<span class=\"details\">{$details}</span>";
			$content .= "</div>";
		}

		$content .= "</div>";

		return $content;
	}

}

?>