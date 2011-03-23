<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
 * @copyright	Copyright 2005-2010 by Purdue Research Foundation, West Lafayette, IN 47906
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

$registered = false;
if ($this->authorized == 'admin' || $this->authorized == 'manager' || $this->authorized == 'member') {
	$registered = true;
}

$cls = $this->ismember;
$isApplicant = $this->group->isApplicant($this->user->get('id'));
if ($isApplicant) {
	$cls = 'pending';
}
?>

<div id="content_aside">
	<div id="controller">
	<?php
		$controller = '';
		switch ($this->group->get('join_policy')) {
			case 3:
				if ($isApplicant || $this->ismember) {
					if ($this->ismember == 'invitee') {
						$controller .= '<a id="accept" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT').'</a>'."\n";
					} else {
						$controller .= '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Group </span>'.$cls.'</div>'."\n";
						$controller .= '<div id="controls">'."\n";
						$controller .= '<ul id="control_items">'."\n";
						if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
							$controller .= '';
						} else {
							$controller .= '<li><a class="cancel_group_membership" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel').'">'.JText::_('GROUPS_CANCEL_LINK').'</a></li>'."\n";
						}
						if ($this->authorized == 'admin' || $this->authorized == 'manager') {
							$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
							$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
							$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
							$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';
							$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
						}	
						$controller .= '</ul>'."\n";
						$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', ucfirst($cls)).'</a>';
						$controller .= '</div>'."\n";
					}
				} else {
					$controller .= '<p id="closed">'.JText::_('GROUPS_CLOSED').'</p>'."\n";
				}
				break;
				
			case 2:
				if ($isApplicant || $this->ismember) {
					if ($this->ismember == 'invitee') {
						$controller .= '<a id="accept" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT').'</a>'."\n";
					} else {
						if ($isApplicant || $this->ismember == 'manager' || $this->ismember == 'member') {
							$controller .= '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Group </span>'.$cls.'</div>'."\n";
							$controller .= '<div id="controls">'."\n";
							$controller .= '<ul id="control_items">'."\n";
							if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
								$controller .= '';
							} else {
								$controller .= '<li><a class="cancel_group_membership" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel').'">'.JText::_('GROUPS_CANCEL_LINK').'</a></li>'."\n";
							}
							if ($this->authorized == 'admin' || $this->authorized == 'manager') {
								$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
								$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
								$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
								$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';
								$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
							}
							$controller .= '</ul>'."\n";
							$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', ucfirst($cls)).'</a>';	
							$controller .= '</div>'."\n";
						}
					}
				} else {
					$controller .= '<p id="invite">'.JText::_('GROUPS_INVITE_ONLY').'</p>'."\n";
				}
				break;
			
			case 1:
				if ($isApplicant || $this->ismember) {
					if ($this->ismember == 'invitee') {
						$controller .= '<a id="accept" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT').'</a>'."\n";
					} else {
						$controller .= '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Group </span>'.$cls.'</div>'."\n";
						$controller .= '<div id="controls">'."\n";
						$controller .= '<ul id="control_items">'."\n";
						if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
							$controller .= '';
						} else {
							$controller .= '<li><a class="cancel_group_membership" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel').'">'.JText::_('GROUPS_CANCEL_LINK').'</a></li>'."\n";
						}
						if ($this->authorized == 'admin' || $this->authorized == 'manager') {
							$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
							$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
							$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
							$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';
							$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
						}
						$controller .= '</ul>'."\n";
						$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', ucfirst($cls)).'</a>';	
						$controller .= '</div>'."\n";
					}
				} else {
					$controller .= '<a id="join" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_REQUEST_LINK').'</a>'."\n";
				}
				break;
				
			case 0:
			//default:
				if ($isApplicant || $this->ismember) {
					if ($this->ismember == 'invitee') {
						$controller .= '<a id="accept" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT').'</a>'."\n";
					} else {
						$controller .= '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Group </span>'.$cls.'</div>'."\n";
						$controller .= '<div id="controls">'."\n";
						$controller .= '<ul id="control_items">'."\n";
						if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
							$controller .= '';
						} else {
							$controller .= '<li><a class="cancel_group_membership" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel').'">'.JText::_('GROUPS_CANCEL_LINK').'</a></li>'."\n";
						}
						if ($this->authorized == 'admin' || $this->authorized == 'manager') {
							$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
							$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
							$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
							$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';
							$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
						}
						
						$controller .= '</ul>'."\n";
						$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', ucfirst($cls)).'</a>';
						$controller .= '</div>'."\n";
					}
				} else {
					$controller .= '<a id="join" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_JOIN_LINK').'</a>'."\n";
				}
				break;
		}
		
		
		
		if($this->authorized == 'admin' && strpos($controller,"status") === false) {
			$controller  = '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Joomla </span>Admin</div>'."\n";
			$controller .= '<div id="controls">'."\n";
			$controller .= '<ul id="control_items">'."\n";
			
			if($this->group->get('join_policy') == 0) {
				$controller .= '<li><a class="join" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_JOIN_CONTROLLER_LINK').'</a></li>'."\n";
			} elseif($this->group->get('join_policy') == 1) {
				$controller .= '<li><a class="join" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_REQUEST_CONTROLLER_LINK').'</a></li>'."\n";
			}
			
			$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
			$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
			$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
			$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';
			$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
			
			
			$controller .= '</ul>'."\n";
			$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', 'Admin').'</a>';
			$controller .= '</div>'."\n";
		}
		
		echo $controller;
	
	?>
	</div><!-- // end controller -->

	<div id="modules">
		<?php echo $this->group_modules; ?>
	</div><!-- // end modules -->
	
</div><!-- //end content aside -->


<div id="content_main">
	<?php echo $this->group_overview; ?>
</div><!-- // end content main -->
