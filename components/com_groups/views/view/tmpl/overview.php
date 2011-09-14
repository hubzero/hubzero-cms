<?php
/**
 * @package     hubzero-cms
 * @author      Christopher Smoak <csmoak@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

$access = $this->group->getPluginAccess('overview');

//is membership control managed on group?
$membership_control = $this->gparams->get('membership_control', 1);
?>

<div id="content_aside">
	<?php if(($access == 'anyone') || ($access == 'registered' && $registered) || ($access == 'members' && in_array($this->user->get('id'), $this->group->get('members')))) : ?>
		<?php
			$controller = '';
			switch ($this->group->get('join_policy')) {
				case 3:
					if ($isApplicant || $this->ismember) {
						if ($this->ismember == 'invitee') {
							if($membership_control == 1) {
								$controller .= '<a id="accept" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT').'</a>'."\n";
							}
						} else {
							$controller .= '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Group </span>'.$cls.'</div>'."\n";
							$controller .= '<div id="controls">'."\n";
							$controller .= '<ul id="control_items">'."\n";
							if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
								$controller .= '';
							} else {
								if($membership_control == 1) {
									$controller .= '<li><a class="cancel_group_membership" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel').'">'.JText::_('GROUPS_CANCEL_LINK').'</a></li>'."\n";
								}
							}
							if ($this->authorized == 'admin' || $this->authorized == 'manager') {
								if($membership_control == 1) {
									$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
								}
								$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
								$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
								$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';

								if($membership_control == 1) {
									$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
								}
							}
							$controller .= '</ul>'."\n";
							$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', ucfirst($cls)).'</a>';
							$controller .= '</div>'."\n";
						}
					} else {
						if($membership_control == 1) {
							$controller .= '<p id="closed">'.JText::_('GROUPS_CLOSED').'</p>'."\n";
						}
					}
					break;

				case 2:
					if ($isApplicant || $this->ismember) {
						if ($this->ismember == 'invitee') {
							if($membership_control == 1) {
								$controller .= '<a id="accept" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT').'</a>'."\n";
							}
						} else {
							if ($isApplicant || $this->ismember == 'manager' || $this->ismember == 'member') {
								$controller .= '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Group </span>'.$cls.'</div>'."\n";
								$controller .= '<div id="controls">'."\n";
								$controller .= '<ul id="control_items">'."\n";
								if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
									$controller .= '';
								} else {
									if($membership_control == 1) {
										$controller .= '<li><a class="cancel_group_membership" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel').'">'.JText::_('GROUPS_CANCEL_LINK').'</a></li>'."\n";
									}
								}
								if ($this->authorized == 'admin' || $this->authorized == 'manager') {
									if($membership_control == 1) {
										$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
									}
									$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
									$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
									$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';

									if($membership_control == 1) {
										$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
									}
								}
								$controller .= '</ul>'."\n";
								$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', ucfirst($cls)).'</a>';
								$controller .= '</div>'."\n";
							}
						}
					} else {
						if($membership_control == 1) {
							$controller .= '<p id="invite">'.JText::_('GROUPS_INVITE_ONLY').'</p>'."\n";
						}
					}
					break;

				case 1:
					if ($isApplicant || $this->ismember) {
						if ($this->ismember == 'invitee') {
							if($membership_control == 1) {
								$controller .= '<a id="accept" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT').'</a>'."\n";
							}
						} else {
							$controller .= '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Group </span>'.$cls.'</div>'."\n";
							$controller .= '<div id="controls">'."\n";
							$controller .= '<ul id="control_items">'."\n";
							if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
								$controller .= '';
							} else {
								if($membership_control == 1) {
									$controller .= '<li><a class="cancel_group_membership" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel').'">'.JText::_('GROUPS_CANCEL_LINK').'</a></li>'."\n";
								}
							}
							if ($this->authorized == 'admin' || $this->authorized == 'manager') {
								if($membership_control == 1) {
									$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
								}
								$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
								$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
								$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';

								if($membership_control == 1) {
									$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
								}
							}
							$controller .= '</ul>'."\n";
							$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', ucfirst($cls)).'</a>';
							$controller .= '</div>'."\n";
						}
					} else {
						if($membership_control == 1) {
							$controller .= '<a id="join" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_REQUEST_LINK').'</a>'."\n";
						}
					}
					break;

				case 0:
				//default:
					if ($isApplicant || $this->ismember) {
						if ($this->ismember == 'invitee') {
							if($membership_control == 1) {
								$controller .= '<a id="accept" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT').'</a>'."\n";
							}
						} else {
							$controller .= '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Group </span>'.$cls.'</div>'."\n";
							$controller .= '<div id="controls">'."\n";
							$controller .= '<ul id="control_items">'."\n";
							if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
								$controller .= '';
							} else {
								if($membership_control == 1) {
									$controller .= '<li><a class="cancel_group_membership" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel').'">'.JText::_('GROUPS_CANCEL_LINK').'</a></li>'."\n";
								}
							}
							if ($this->authorized == 'admin' || $this->authorized == 'manager') {
								if($membership_control == 1) {
									$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
								}
								$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
								$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
								$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';

								if($membership_control == 1) {
									$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
								}
							}

							$controller .= '</ul>'."\n";
							$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', ucfirst($cls)).'</a>';
							$controller .= '</div>'."\n";
						}
					} else {
						if($membership_control == 1) {
							$controller .= '<a id="join" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_JOIN_LINK').'</a>'."\n";
						}
					}
					break;
			}

			if($this->authorized == 'admin' && strpos($controller,"status") === false) {
				$controller  = '<div id="status" class="'.$cls.'"><span id="group_status_unnecessary">Joomla </span>Admin</div>'."\n";
				$controller .= '<div id="controls">'."\n";
				$controller .= '<ul id="control_items">'."\n";

				if($membership_control == 1) {
					if($this->group->get('join_policy') == 0) {
						$controller .= '<li><a class="join" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_JOIN_CONTROLLER_LINK').'</a></li>'."\n";
					} elseif($this->group->get('join_policy') == 1) {
						$controller .= '<li><a class="join" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_REQUEST_CONTROLLER_LINK').'</a></li>'."\n";
					}

					$controller .= '<li><a class="invite" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite').'">'.JText::_('GROUPS_INVITE_LINK').'</a></li>';
				}

				$controller .= '<li><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit').'">'.JText::_('GROUPS_EDIT_LINK').'</a></li>';
				$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize').'">'.JText::_('GROUPS_CUSTOMIZE_LINK').'</a></li>';
				$controller .= '<li><a class="customize" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=managepages').'">'.JText::_('GROUPS_PAGES_LINK').'</a></li>';

				if($membership_control == 1) {
					$controller .= '<li><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete').'">'.JText::_('GROUPS_DELETE_LINK').'</a></li>';
				}

				$controller .= '</ul>'."\n";
				$controller .= '<a href="#" id="toggle-controls">'.JText::sprintf('GROUPS_TOGGLE_CONTROLS', 'Admin').'</a>';
				$controller .= '</div>'."\n";
			}

			if($controller != "") {
				echo "<div id=\"controller\">{$controller}</div>";
			}
		?>

		<div id="modules">
			<?php echo $this->group_modules; ?>
		</div><!-- // end modules -->
	<?php endif; ?>
</div><!-- //end content aside -->


<div id="content_main">
	<?php echo $this->group_overview; ?>
</div><!-- // end content main -->

