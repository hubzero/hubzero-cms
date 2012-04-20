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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'InformationModule'
 * 
 * Long description (if any) ...
 */
Class InformationModule
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
			'name' => 'information',
			'title' => 'General Group Informaiton',
			'description' => 'Information block shows basic details about the group, including the managers, number of members, etc',
			'input_title' => '',
			'input' => 'There is nothing you can edit for this group module. Clicking the update button below or the back above will take you back to the manage pages dashboard.'
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

		//get the group object
		$group = Hubzero_Group::getInstance( $this->group->get('gidNumber') );

		// Get the group tags
		$database =& JFactory::getDBO();
		$gt = new GroupsTags( $database );
		$tags = $gt->get_tag_cloud(0,0,$group->get('gidNumber'));
		if (!$tags) {
			$tags = JText::_('None');
		}

		// Get the managers
		$managers = $group->get('managers');
		$m = array();
		if ($managers) {
			foreach ($managers as $manager)
			{
				$person =& JUser::getInstance($manager);
				$m[] = '<a href="'.JRoute::_('index.php?option=com_members&id='.$manager).'" rel="member">'.stripslashes($person->get('name')).'</a>';
			}
		}
		$m = implode(', ', $m);

		// Determine the join policy
		switch ($group->get('join_policy'))
		{
			case 3: $policy = JText::_('Closed');      break;
			case 2: $policy = JText::_('Invite Only'); break;
			case 1: $policy = JText::_('Restricted');  break;
			case 0:
			default: $policy = JText::_('Open'); break;
		}

		// Determine the privacy
		switch ($group->get('privacy'))
		{
			case 1: $privacy = JText::_('Hidden'); break;
			case 0:
			default: $privacy = JText::_('Visible'); break;
		}

		// Get the group creation date
		$gl = new XGroupLog( $database );
		$gl->getLog( $group->get('gidNumber'), 'first' );
        
		$dateFormat = '%d %b, %Y';
		$timeFormat = '%I:%M %p';
		$tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'd M, Y';
			$timeFormat = 'h:m a';
			$tz = true;
		}

		$items = array(
			"Managers" => $m,
			"Members" => count($group->get('members')),
			"Discoverability" => $privacy,
			"Policy" => $policy,
			"Created" => JHTML::_('date', $gl->timestamp, $dateFormat, $tz),
			"Tags" => $tags
		);
		
		$information  = '<div class="group-info-mod">';
			$information .= '<ul>'; 
			foreach($items as $k => $v)
			{
				$information .= '<li>';
					$information .= '<span class="label">'.$k.'</span>';
					$information .= '<span class="value">'.$v.'</span>';
				$information .= '</li>';
			}
			$information .= '</ul>';
		$information .= '</div>';

		$content = $information;

		//return content
		return $content;
	}

}
?>