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
<div class="withleft">
	<div class="aside">
		<ul>
			<li><a class="sent" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>"><span><?php echo JText::_('PLG_GROUPS_MESSAGES_SENT'); ?></span></a></li>
			<li class="active"><a class="new-message" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages&task=new'); ?>"><span><?php echo JText::_('PLG_GROUPS_MESSAGES_SEND'); ?></span></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>" method="post" class="full" id="hubForm<?php if ($this->no_html) { echo '-ajax'; }; ?>">
			<fieldset>
				<h3><?php echo JText::_('GROUP_MESSAGE_MEMBERS'); ?></h3>

				<label>
					<?php echo JText::_('GROUP_MESSAGE_USERS'); ?> 
<?php 
		$names = array();
		switch ($this->users[0]) 
		{
			case 'invitees':
			$names[] = JText::_('GROUP_MESSAGE_ALL_INVITEES');
?>
						<input type="hidden" name="users[]" value="<?php echo $this->users[0]; ?>" />
<?php
			break;
			case 'pending':
			$names[] = JText::_('GROUP_MESSAGE_ALL_PENDING_MEMBERS');
?>
				<input type="hidden" name="users[]" value="<?php echo $this->users[0]; ?>" />
<?php
			break;
			case 'managers':
			$names[] = JText::_('GROUP_MESSAGE_ALL_MANAGERS');
?>
				<input type="hidden" name="users[]" value="<?php echo $this->users[0]; ?>" />
<?php
			break;
			case 'all':
			$names[] = JText::_('GROUP_MESSAGE_ALL_MEMBERS');
?>
				<input type="hidden" name="users[]" value="<?php echo $this->users[0]; ?>" />
<?php
			break;
			default:
			foreach ($this->users as $user) 
			{
				$u =& JUser::getInstance($user);
				$names[] = $u->get('name');
?>
						<input type="hidden" name="users[]" value="<?php echo $user; ?>" />
<?php
			}
			break;
		}
?>
					<strong><?php echo implode(', ',$names); ?></strong>
				</label>
				<label>
					<?php echo JText::_('GROUP_MESSAGE'); ?>
					<textarea name="message" id="msg-message" rows="12" cols="50"></textarea>
				</label>
			</fieldset><div class="clear"></div>
			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="active" value="messages" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="send" />
			<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('GROUP_MESSAGE_SEND'); ?>" />
			</p>
		</form>
	</div><!-- / .subject -->
</div><!-- / .withleft-->