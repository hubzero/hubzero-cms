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
?>

<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&filter='.$this->filter); ?>" method="post">
	<!-- <h3><a name="members"></a><?php echo JText::_('GROUPS_MEMBERS'); ?></h3> -->
	
	<div class="aside">
		<fieldset>
			<legend><?php echo JText::_('PLG_GROUPS_MEMBERS_SEARCH_LEGEND'); ?></legend>
			<label>
				<?php echo JText::_('PLG_GROUPS_MEMBERS_SEARCH_LABEL'); ?>
				<input type="text" name="q" value="<?php echo htmlentities($this->q,ENT_COMPAT,'UTF-8'); ?>" />
			</label>
			<input type="submit" value="<?php echo JText::_('PLG_GROUPS_MEMBERS_SEARCH'); ?>" />
		</fieldset>
	</div><!-- / .aside -->
	
	<div class="subject">
		<table id="members-list">
			<thead>
				<tr>
					<td colspan="4">
						<div id="sub-sub-menu">
							<ul>
								<li<?php if ($this->filter == '' || $this->filter == 'members') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members'); ?>"><span><?php echo JText::_('PLG_GROUPS_MEMBERS_MEMBERS'); ?></span></a></li>
								<li<?php if ($this->filter == 'managers') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&filter=managers'); ?>"><span><?php echo JText::_('PLG_GROUPS_MEMBERS_MANAGERS'); ?></span></a></li>
<?php if ($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
								<li<?php if ($this->filter == 'pending') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&filter=pending'); ?>"><span><?php echo JText::_('PLG_GROUPS_MEMBERS_PENDING'); ?></span></a></li>
								<li<?php if ($this->filter == 'invitees') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&filter=invitees'); ?>"><span><?php echo JText::_('PLG_GROUPS_MEMBERS_INVITEES'); ?></span></a></li>
<?php } ?>
							</ul>
						</div><!-- / #sub-sub-menu -->
					</td>
<?php
switch ($this->filter)
{
	case 'invitees':
?>
					<td class="message-member"><a class="message tooltips" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&task=message&users[]=invitees'); ?>" title="<?php echo JText::_('PLG_GROUPS_MEMBERS_SEND_ALL_INVITEES'); ?>"><?php echo JText::_('PLG_GROUPS_MEMBERS_MESSAGE'); ?></a></td>
<?php
	break;
	case 'pending':
?>
					<td class="message-member"><a class="message tooltips" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&task=message&users[]=pending'); ?>" title="<?php echo JText::_('PLG_GROUPS_MEMBERS_SEND_ALL_PENDING'); ?>"><?php echo JText::_('PLG_GROUPS_MEMBERS_MESSAGE'); ?></a></td>
<?php
	break;
	case 'managers':
?>
					<td class="message-member"><a class="message tooltips" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&task=message&users[]=managers'); ?>" title="<?php echo JText::_('PLG_GROUPS_MEMBERS_SEND_ALL_MANAGERS'); ?>"><?php echo JText::_('PLG_GROUPS_MEMBERS_MESSAGE'); ?></a></td>
<?php
	break;
	case 'members':
	default:
?>
					<td class="message-member"><a class="message tooltips" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=messages&task=new&users[]=all'); ?>" title="<?php echo JText::_('PLG_GROUPS_MEMBERS_SEND_ALL_MEMBERS'); ?>"><?php echo JText::_('PLG_GROUPS_MEMBERS_MESSAGE'); ?></a></td>
<?php
	break;
}
?>
				</tr>
			</thead>
			<tbody>
<?php
$cls = 'even';
if ($this->groupusers) {
	// Path to users' thumbnails
	$config =& JComponentHelper::getParams( 'com_members' );
	$thumb = $config->get('webpath');
	if (substr($thumb, 0, 1) != DS) {
		$thumb = DS.$thumb;
	}
	if (substr($thumb, -1, 1) == DS) {
		$thumb = substr($thumb, 0, (strlen($thumb) - 1));
	}
	
	// Default thumbnail
	$dfthumb = $config->get('defaultpic');
	if (substr($dfthumb, 0, 1) != DS) {
		$dfthumb = DS.$dfthumb;
	}
	$dfthumb = plgGroupsMembers::thumbit($dfthumb);
	
	// Some needed libraries
	ximport('xprofile');
	$juser =& JFactory::getUser();
	
	// Loop through the results
	$html = '';
	for ($i=$this->start, $n=$this->limit; $i < $n; $i++) 
	{
		if ($i >= count($this->groupusers)) {
			break;
		}
		$guser = $this->groupusers[$i];

		$u = new XProfile();
		$u->load( $guser );
		if (!is_object($u)) {
			continue;
		}
			
		$cls = (($cls == 'even') ? 'odd' : 'even');
		
		// User photo
		$uthumb = '';
		if ($u->get('picture')) {
			$uthumb = $thumb.DS.plgGroupsMembers::niceidformat($u->get('uidNumber')).DS.$u->get('picture');
			$uthumb = plgGroupsMembers::thumbit($uthumb);
		}

		if ($uthumb && is_file(JPATH_ROOT.$uthumb)) {
			$p = $uthumb;
		} else {
			$p = $dfthumb;
		}
		
		switch ($this->filter)
		{
			case 'invitees':
				$status = JText::_('PLG_GROUPS_MEMBERS_STATUS_INVITEE');
			break;
			case 'pending':
				$status = JText::_('PLG_GROUPS_MEMBERS_STATUS_PENDING');
			break;
			case 'managers':
				$status = JText::_('PLG_GROUPS_MEMBERS_STATUS_MANAGER');
				$cls .= ' manager';
			break;
			case 'members':
			default:
				$status = 'Member';
				if (in_array($guser,$this->managers)) {
					$status = JText::_('PLG_GROUPS_MEMBERS_STATUS_MANAGER');
					$cls .= ' manager';
				}
			break;
		}
		if ($juser->get('id') == $u->get('uidNumber')) {
			$cls .= ' me';
		}
		
		$html .= t.t.t.'<tr class="'.$cls.'">'.n;
		$html .= t.t.t.t.'<td class="photo"><img width="50" height="50" src="'.$p.'" alt="Photo for '.htmlentities($u->get('name'),ENT_COMPAT,'UTF-8').'" /></td>'.n;
		$html .= t.t.t.t.'<td>';
		$html .= '<span class="name"><a href="'.JRoute::_('index.php?option=com_members&id='.$u->get('uidNumber')).'">'.$u->get('name').'</a></span> <span class="status">'.$status.'</span><br />';
		if ($u->get('organization')) {
			$html .= '<span class="organization">'.$u->get('organization').'</span><br />';
		}
		if ($this->filter == 'pending') {
			$database =& JFactory::getDBO();
			$row = new GroupsReason( $database );
			$row->loadReason($u->get('username'), $this->group->cn);

			if ($row) {
				$html .= '<span class="reason">'.stripslashes($row->reason).'</span>';
			}
		} else {
			//$html .= '<span class="activity">Activity: </span>';
		}
		$html .= '</td>'.n;
		if ($this->authorized == 'manager' || $this->authorized == 'admin') {
			switch ($this->filter)
			{
				case 'invitees':
					$html .= t.t.t.t.'<td class="remove-member"><a class="cancel tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&task=cancel&users[]='.$guser).'" title="'.JText::sprintf('PLG_GROUPS_MEMBERS_CANCEL_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_GROUPS_MEMBERS_CANCEL').'</a></td>'.n;
					$html .= t.t.t.t.'<td class="approve-member"> </td>'.n;
				break;
				case 'pending':
					$html .= t.t.t.t.'<td class="decline-member"><a class="decline tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&task=deny&users[]='.$guser).'" title="'.JText::sprintf('PLG_GROUPS_MEMBERS_DECLINE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_GROUPS_MEMBERS_DENY').'</a></td>'.n;
					$html .= t.t.t.t.'<td class="approve-member"><a class="approve tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&task=approve&users[]='.$guser).'" title="'.JText::sprintf('PLG_GROUPS_MEMBERS_APPROVE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_GROUPS_MEMBERS_APPROVE').'</a></td>'.n;
				break;
				case 'managers':
				case 'members':
				default:
					if (!in_array($guser,$this->managers) || (in_array($guser,$this->managers) && count($this->managers) > 1)) {
						$html .= t.t.t.t.'<td class="remove-member"><a class="remove tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&task=remove&users[]='.$guser).'" title="'.JText::sprintf('PLG_GROUPS_MEMBERS_REMOVE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_GROUPS_MEMBERS_REMOVE').'</a></td>'.n;
					} else {
						$html .= t.t.t.t.'<td class="remove-member"> </td>'.n;
					}
					if (in_array($guser,$this->managers)) {
						if ($this->authorized == 'admin' || count($this->managers) > 1) {
							$html .= t.t.t.t.'<td class="demote-member"><a class="demote tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&task=demote&users[]='.$guser).'" title="'.JText::sprintf('PLG_GROUPS_MEMBERS_DEMOTE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_GROUPS_MEMBERS_DEMOTE').'</a></td>'.n;
						} else {
							$html .= t.t.t.t.'<td class="demote-member"> </td>'.n;
						}
					} else {
						$html .= t.t.t.t.'<td class="promote-member"><a class="promote tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=members&task=promote&users[]='.$guser).'" title="'.JText::sprintf('PLG_GROUPS_MEMBERS_PROMOTE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_GROUPS_MEMBERS_PROMOTE').'</a></td>'.n;
					}
				break;
			}
		} else {
			$html .= t.t.t.t.'<td class="remove-member"> </td>'.n;
			$html .= t.t.t.t.'<td class="demote-member"> </td>'.n;
		}
		if ($juser->get('id') == $u->get('uidNumber')) {
			$html .= t.t.t.t.'<td class="message-member"> </td>'.n;
		} else {
			$html .= t.t.t.t.'<td class="message-member"><a class="message tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->group->cn.'&active=messages&task=new&users[]='.$guser).'" title="Message :: Send a message to '.htmlentities($u->get('name'),ENT_COMPAT,'UTF-8').'">'.JText::_('PLG_GROUPS_MEMBERS_MESSAGE').'</a></td>'.n;
		}
		$html .= t.t.t.'</tr>'.n;
	}
	echo $html;
} else { ?>
				<tr class="odd">
					<td><?php echo JText::_('PLG_GROUPS_MEMBERS_NO_RESULTS'); ?></td>
				</tr>
<?php } ?>
			</tbody>
		</table>
		
		<?php echo $this->pageNav->getListFooter(); ?>
		
		<input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>" />
		<input type="hidden" name="active" value="members" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="filter" value="<?php echo $this->filter; ?>" />
	</div><!-- / .subject -->
</form>