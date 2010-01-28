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
<h3><a name="groups"></a><?php echo JText::_('PLG_MEMBERS_GROUPS'); ?></h3>
<div class="aside">
	<ul class="sub-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_groups'); ?>"><?php echo JText::_('PLG_MEMBERS_GROUPS_ALL'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_groups&task=new'); ?>"><?php echo JText::_('PLG_MEMBERS_GROUPS_CREATE'); ?></a></li>
	</ul>
	<p class="help">
		<strong><?php echo JText::_('PLG_MEMBERS_GROUPS_WHAT_ARE_GROUPS'); ?></strong><br />
		<?php echo JText::_('PLG_MEMBERS_GROUPS_EXPLANATION'); ?>
	</p>
</div><!-- / .aside -->
<div class="subject">
	<table id="grouplist" summary="<?php echo JText::_('PLG_MEMBERS_GROUPS_TBL_SUMMARY'); ?>">
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_GROUPS_TBL_TH_NAME'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_GROUPS_TBL_TH_STATUS'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_GROUPS_TBL_TH_OPTION'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
if ($this->groups) {
	$cls = 'even';
	foreach ($this->groups as $group) 
	{
		$cls = (($cls == 'even') ? 'odd' : 'even');
?>
			<tr class="<?php echo $cls; ?>">
				<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='. $group->cn); ?>"><?php echo $group->description; ?></a></td>
				<td>
<?php
			if ($group->manager && $group->published) {
				echo '<span class="manager status">'.JText::_('PLG_MEMBERS_GROUPS_STATUS_MANAGER').'</span>';
				$opt  = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&active=members') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_MANAGE').'</a>';
				$opt .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=edit') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_EDIT').'</a>';
				$opt .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=delete') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_DELETE').'</a>';
			} else {
				if (!$group->published) {
					echo JText::_('PLG_MEMBERS_GROUPS_STATUS_NEW_GROUP');
				} else {
					if ($group->registered) {
						if ($group->regconfirmed) {
							echo '<span class="member status">'.JText::_('PLG_MEMBERS_GROUPS_STATUS_APPROVED').'</span>';
							$opt = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=cancel') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
						} else {
							echo '<span class="pending status">'.JText::_('PLG_MEMBERS_GROUPS_STATUS_PENDING').'</span>';
							$opt = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=cancel') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
						}
					} else {
						if ($group->regconfirmed) {
							echo '<span class="invitee status">'.JText::_('PLG_MEMBERS_GROUPS_STATUS_INVITED').'</span>';
							$opt  = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=accept') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_ACCEPT').'</a>';
							$opt .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=cancel') .'">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
						} else {
							echo '<span class="status"> </span>';
							$opt = '';
						}
					}
				}
			}
?>
				</td>
				<td><?php echo $opt; ?></td>
			</tr>
<?php
	}
} else {
?>
			<tr class="odd">
				<td colspan="3"><?php echo JText::_('PLG_MEMBERS_GROUPS_NO_MEMBERSHIPS'); ?></td>
			</tr>
<?php
}
?>
		</tbody>
	</table>
</div><!-- / .subject -->