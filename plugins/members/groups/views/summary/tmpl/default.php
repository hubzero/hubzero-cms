<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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
?>
<h3 class="section-header"><a name="groups"></a><?php echo JText::_('PLG_MEMBERS_GROUPS'); ?></h3>
<div class="aside">
	<div class="container">
		<h4><?php echo JText::_('Your Groups'); ?></h4>
		<p class="starter"><span class="starter-point"></span>These are the groups you have membership in or have been invited to.</p>
	</div><!-- / .container -->
	<div class="container">
		<h4><?php echo JText::_('PLG_MEMBERS_GROUPS_WHAT_ARE_GROUPS'); ?></h4>
		<p class="starter"><span class="starter-point"></span><?php echo JText::_('PLG_MEMBERS_GROUPS_EXPLANATION'); ?></p>
		<p>Go to the <a href="<?php echo JRoute::_('index.php?option=com_groups'); ?>"><?php echo JText::_('Groups page'); ?></a>.</p>
	</div><!-- / .container -->
	<p class="add"><a href="<?php echo JRoute::_('index.php?option=com_groups&task=new'); ?>"><?php echo JText::_('PLG_MEMBERS_GROUPS_CREATE'); ?></a></p>
</div><!-- / .aside -->
<div class="subject">
	<div class="container">
		<table class="groups entries" summary="<?php echo JText::_('PLG_MEMBERS_GROUPS_TBL_SUMMARY'); ?>">
			<caption>
				<?php echo JText::_('Your Groups'); ?>
				<span>(<?php echo count($this->groups); ?>)</span>
			</caption>
			<tbody>
<?php
if ($this->groups) {
	foreach ($this->groups as $group) 
	{
		$status = '';
		if ($group->manager && $group->published) {
			$status = 'manager';
			
			$opt  = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&active=members') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_MANAGE').'</a>';
			$opt .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=edit') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_EDIT').'</a>';
			$opt .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=delete') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_DELETE').'</a>';
		} else {
			if (!$group->published) {
				$status = 'new';
			} else {
				if ($group->registered) {
					if ($group->regconfirmed) {
						$status = 'member';
						$opt = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=cancel') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
					} else {
						$status = 'pending';
						$opt = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=cancel') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
					}
				} else {
					if ($group->regconfirmed) {
						$status = 'invitee';
						$opt  = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=accept') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_ACCEPT').'</a>';
						$opt .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=cancel') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
					} else {
						$opt = '';
					}
				}
			}
		}
?>
				<tr>
					<th>
						<span class="entry-id"><?php echo $group->gidNumber; ?></span>
					</th>
					<td>
						<a class="entry-title" rel="<?php echo $group->gidNumber; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='. $group->cn); ?>"><?php echo stripslashes($group->description); ?></a><br />
						<span class="entry-details">
							<span class="entry-alias"><?php echo $group->cn; ?></span>
						</span>
					</td>
					<td>
						<?php
						switch ($group->join_policy) 
						{
							case 3: echo '<span class="closed join-policy">'.JText::_('Closed').'</span>'."\n"; break;
							case 2: echo '<span class="inviteonly join-policy">'.JText::_('Invite Only').'</span>'."\n"; break;
							case 1: echo '<span class="restricted join-policy">'.JText::_('Restricted').'</span>'."\n";  break;
							case 0:
							default: echo '<span class="open join-policy">'.JText::_('Open').'</span>'."\n"; break;
						}
?>
					</td>
					<td>
						<span class="<?php echo $status; ?> status"><?php
							switch ($status) 
							{
								case 'manager': echo JText::_('PLG_MEMBERS_GROUPS_STATUS_MANAGER'); break;
								case 'new': echo JText::_('PLG_MEMBERS_GROUPS_STATUS_NEW_GROUP'); break;
								case 'member': echo JText::_('PLG_MEMBERS_GROUPS_STATUS_APPROVED'); break;
								case 'pending': echo JText::_('PLG_MEMBERS_GROUPS_STATUS_PENDING'); break;
								case 'invitee': echo JText::_('PLG_MEMBERS_GROUPS_STATUS_INVITED'); break;
								default: break;
							}
						?></span>
					</td>
					<td>
						<?php echo $opt; ?>
					</td>
				</tr>
<?php
	}
} else {
?>
				<tr>
					<td colspan="5"><?php echo JText::_('PLG_MEMBERS_GROUPS_NO_MEMBERSHIPS'); ?></td>
				</tr>
<?php
}
?>
			</tbody>
		</table>
	</div><!-- / .container -->
</div><!-- / .subject -->
<div class="clear"></div>
