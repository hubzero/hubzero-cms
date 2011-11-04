<?php
/**
 * @package		HUBzero                                  CMS
 * @author		Christopher                               Smoak <csmoak@purdue.edu>
 * @copyright	Copyright                               2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *                                                    
 *                                                    Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 *                                                    All rights reserved.
 *                                                    
 *                                                    This program is free software; you can redistribute it and/or
 *                                                    modify it under the terms of the GNU General Public License,
 *                                                    version 2 as published by the Free Software Foundation.
 *                                                    
 *                                                    This program is distributed in the hope that it will be useful,
 *                                                    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                                                    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                                                    GNU General Public License for more details.
 *                                                    
 *                                                    You should have received a copy of the GNU General Public License
 *                                                    along with this program; if not, write to the Free Software
 *                                                    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'GroupEventMacro'
 * 
 * Long description (if any) ...
 */
class GroupEventMacro extends WikiMacro
{

	/**
	 * Short description for 'description'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Displays group events";
		$txt['html'] = '<p>Displays group events.</p>';

		return $txt['html'];
	}

	/**
	 * Short description for 'render'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function render()
	{
		//get the args passed in
		$args = explode(",",$this->args);

		//parse each arg into key value pair
		foreach($args as $a) {
			$kv[] = explode("=",trim($a));

		}

		//set final args
		foreach ($kv as $k) 
		{
			$arg[$k[0]] = (isset($k[1])) ? $k[1] : $k[0];
		}

		//set a default
		$default_events = 3;

		//get the user defined # of events
		$user_events = (isset($arg['number'])) ? $arg['number'] : $default_events;

		//decide whether or not to use default number of events
		$num_events = (is_int($user_events) && $user_events != 0) ? $user_events : $default_events;

		//get the group
		$gid = JRequest::getVar('gid');

		//import the Hubzero Group Library
		ximport('Hubzero_Group');

		//get the group object based on gid
		$group = Hubzero_Group::getInstance($gid);

		//check to make sure we have a valid group
		if(!is_object($group)) {
			return "[This macro is designed for Groups only]";
		}

		//create the html container
		$html  = "<div class=\"upcoming_events\">";

		//display the title
		$html .= "<h3>{$arg['title']}</h3>";

		//render the events
		$html .= $this->renderGroupEvents( $group, $num_events );

		//close the container
		$html .= "</div>";

		//return rendered events
		return $html;
	}

	/**
	 * Short description for 'renderGroupEvents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $group Parameter description (if any) ...
	 * @param      string $num_events Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function renderGroupEvents( $group, $num_events )
	{
		//instantiate database
		$db =& JFactory::getDBO();

		//build query
		$sql = "SELECT * FROM #__xgroups_events 
				WHERE end >= NOW()
				AND gidNumber=".$db->Quote($group->get('gidNumber'))." 
				AND active=1 
				ORDER BY start ASC
				LIMIT ".$num_events;
		$db->setQuery($sql);
		$events = $db->loadAssocList();

		$content = '';

		if(count($events) > 0) {
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
		} else {
			$content .= "<p>Currently there are no upcoming group events. Add an event by  <a href=\"groups/{$group->get('cn')}/calendar?task=add\">clicking here.</a></p>";
		}

		return $content;
	}
}
?>