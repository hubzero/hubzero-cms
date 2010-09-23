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

if ($this->authorized != 'admin' 
 && $this->authorized != 'manager' 
 && $this->authorized != 'member') {
	$registered = false;
} else {
	$registered = true;
}

$isApplicant = $this->group->isApplicant($this->juser->get('id'));

// Get the group tags
$database =& JFactory::getDBO();
$gt = new GroupsTags( $database );
$tags = $gt->get_tag_cloud(0,0,$this->group->get('gidNumber'));
if (!$tags) {
	$tags = JText::_('None');
}

// Get the managers
$managers = $this->group->get('managers');
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
switch ($this->group->get('join_policy')) 
{
	case 3: $policy = JText::_('Closed');      break;
	case 2: $policy = JText::_('Invite Only'); break;
	case 1: $policy = JText::_('Restricted');  break;
	case 0:
	default: $policy = JText::_('Open'); break;
}

// Determine the privacy
switch ($this->group->get('privacy')) 
{
	case 1: $privacy = JText::_('Protected'); break;
	case 4: $privacy = JText::_('Private');   break;
	case 0:
	default: $privacy = JText::_('Public'); break;
}

// Get the group creation date
$gl = new XGroupLog( $database );
$gl->getLog( $this->group->get('gidNumber'), 'first' );

if ($isApplicant) {
	$cls = 'pending';
} else {
	$cls = $this->ismember;
}
?>
	<h3><a name="overview"></a><?php echo JText::_('GROUPS_OVERVIEW'); ?></h3>
	<div class="aside">
<?php
$aside = '';
switch ($this->group->get('join_policy')) 
{
	case 3:
		if ($isApplicant || $this->ismember) {
			if ($this->ismember == 'invitee') {
				$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT_INVITE').'</a></p>'."\n";
			} else {
				$aside .= '<p id="group-status" class="'.$cls.'"><span>You are a </span>'.$cls.'</p>'."\n";
				if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
					$aside .= '';
				} else {
					$aside .= '<p id="group-cancel" class="'.$cls.'"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'."\n";
				}
			}
		}
	break;
	
	case 2:
		if ($this->ismember == 'invitee') {
			$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT_INVITE').'</a></p>'."\n";
		} else {
			if ($isApplicant || $this->ismember == 'manager' || $this->ismember == 'member') {
				$aside .= '<p id="group-status" class="'.$cls.'"><span>You are a </span>'.$cls.'</p>'."\n";
				if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
					$aside .= '';
				} else {
					$aside .= '<p id="group-cancel" class="'.$cls.'"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'."\n";
				}
			}
		}
	break;
	
	case 1:
		if ($isApplicant || $this->ismember) {
			if ($this->ismember == 'invitee') {
				$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT_INVITE').'</a></p>'."\n";
			} else {
				$aside .= '<p id="group-status" class="'.$cls.'"><span>You are a </span>'.$cls.'</p>'."\n";
				if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
					$aside .= ''."\n";
				} else {
					$aside .= '<p id="group-cancel" class="'.$cls.'"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'."\n";
				}
			}
		} else {
			$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_REQUEST_MEMBERSHIP_TO_GROUP').'</a></p>'."\n";
		}
	break;
	
	case 0:
	default:
		if ($isApplicant || $this->ismember) {
			if ($this->ismember == 'invitee') {
				$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=accept').'">'.JText::_('GROUPS_ACCEPT_INVITE').'</a></p>'."\n";
			} else {
				$aside .= '<p id="group-status" class="'.$cls.'"><span>You are a </span>'.$cls.'</p>'."\n";
				if ($this->ismember == 'manager' && count($this->group->get('managers')) == 1) {
					$aside .= ''."\n";
				} else {
					$aside .= '<p id="group-cancel" class="'.$cls.'"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'."\n";
				}
			}
		} else {
			$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=join').'">'.JText::_('GROUPS_JOIN_GROUP').'</a></p>'."\n";
		}
	break;
}
echo $aside;
?>
		<div class="metadata">
			<table summary="Meatadata about this group">
				<tbody>
					<tr>
						<th>Managers:</th>
						<td><?php echo $m; ?></td>
					</tr>
					<tr>
						<th>Members:</th>
						<td><?php echo count($this->group->get('members')); ?></td>
					</tr>
					<tr>
						<th>Access:</th>
						<td><?php echo $privacy; ?></td>
					</tr>
					<tr>
						<th>Join Policy:</th>
						<td><?php echo $policy; ?></td>
					</tr>
					<tr>
						<th>Created:</th>
						<td><?php echo JHTML::_('date', $gl->timestamp, '%d %b. %Y'); ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong>Tags:</strong><br />
						<?php echo $tags; ?></td>
					</tr>
				</tbody>
			</table>
		</div><!-- / .meatdata -->
<?php if ($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
		<div class="admin-options">
			<p class="edit"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit'); ?>"><?php echo JText::_('GROUPS_EDIT_GROUP'); ?></a></p>
			<p class="delete"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete'); ?>"><?php echo JText::_('GROUPS_DELETE_GROUP'); ?></a></p>
			<p class="invite"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite'); ?>"><?php echo JText::_('GROUPS_INVITE_USERS'); ?></a></p>
		</div>
<?php } ?>
	</div><!-- / .aside -->
	<div class="subject">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php
if ($this->authorized == 'admin' 
 || $this->authorized == 'manager' 
 || $this->authorized == 'member') {
	if ($this->private_desc) {
?>
		<div class="dashboard" id="private-text">
			<h4 class="dash-header"><?php echo JText::_('GROUPS_PRIVATE_TEXT'); ?></h4>
			<?php echo $this->private_desc; ?>
		</div><!-- / #private-text.dashboard -->
<?php } else if ($this->public_desc) { ?>
		<div class="dashboard">
			<h4 class="dash-header"><?php echo JText::_('GROUPS_ABOUT'); ?></h4>
			<?php echo $this->public_desc; ?>
		</div><!-- / .dashboard -->
<?php }
} else {
	if ($this->public_desc) { 
?>
		<div class="dashboard">
			<h4 class="dash-header"><?php echo JText::_('GROUPS_ABOUT'); ?></h4>
			<?php echo $this->public_desc; ?>
		</div><!-- / .dashboard -->
<?php
	}
}

$k = 0;

$html  = '';
foreach ($this->sections as $section)
{
	if (isset($section['metadata']) && $section['metadata'] != '') {
		$name = key($this->cats[$k]);
		
		$html .= '<div class="dashboard">'."\n";
		$html .= '<h4 class="dash-header">'.JText::_(strtoupper($this->cats[$k][$name]).'_DASHBOARD').' <small>'.$section['metadata'].'</small></h4>'."\n";
		$html .= $section['dashboard'];
		$html .= '</div>'."\n";
	}
	$k++;
}
$html .= '<div class="clear"></div>';
echo $html;
?>
	</div><!-- / .subject -->