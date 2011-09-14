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
<h3 class="section-header"><a name="messages"></a><?php echo JText::_('PLG_MEMBERS_MESSAGES'); ?></h3>
<div class="withleft">
	<div class="aside">
		<ul>
			<li<?php if ($this->xmr->state == 0) { echo ' class="active"'; } ?>><a class="box" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=inbox'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_INBOX'); ?></span></a></li>
			<li><a class="sent" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=sent'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_SENT'); ?></span></a></li>
			<li<?php if ($this->xmr->state == 1) { echo ' class="active"'; } ?>><a class="archive" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=archive'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_ARCHIVE'); ?></span></a></li>
			<li<?php if ($this->xmr->state == 2) { echo ' class="active"'; } ?>><a class="trash" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=trash'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_TRASH'); ?></span></a></li>
			<li><a class="config" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=settings'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_SETTINGS'); ?></span></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages'); ?>" method="post" id="hubForm" class="full">
<?php if ($this->xmr->state != 2) { ?>
			<fieldset id="filters">
				<a class="trash" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&mid[]='.$this->xmessage->id.'&action=sendtotrash'); ?>" title="<?php echo JText::_('PLG_MEMBERS_MESSAGES_TRASH'); ?>"><?php echo JText::_('PLG_MEMBERS_MESSAGES_TRASH'); ?></a>
			</fieldset>
<?php } ?>
			<fieldset id="actions">
				<select class="option" name="action">
					<option value=""><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_WITH_SELECTED'); ?></option>
<?php 
			switch ($this->xmr->state)
			{
				case 2:
?>
					<option value="sendtoinbox"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_INBOX'); ?></option>
					<option value="sendtoarchive"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_ARCHIVE'); ?></option>
<?php
				break;
				case 1:
?>
					<option value="sendtoinbox"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_INBOX'); ?></option>
					<option value="sendtotrash"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_TRASH'); ?></option>
<?php
				break;
				case 0:
				default:
?>
					<option value="sendtoarchive"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_ARCHIVE'); ?></option>
					<option value="sendtotrash"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_TRASH'); ?></option>
<?php
				break;
			}
?>
				</select> 
				<input class="option" type="submit" value="<?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_APPLY'); ?>" />
				<input type="hidden" name="mid[]" id="msg<?php echo $this->xmessage->id; ?>" value="<?php echo $this->xmessage->id; ?>" />
			</fieldset>
			<table class="profile" summary="<?php echo JText::_('PLG_MEMBERS_MESSAGES_TBL_SUMMARY_OVERVIEW'); ?>">
				<tbody>
					<tr>
						<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_DATE_RECEIVED'); ?></th>
						<td><?php echo JHTML::_('date', $this->xmessage->created, '%d %b, %Y'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_FROM'); ?></th>
						<td><?php echo $this->from; ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
						<td><?php echo stripslashes($this->xmessage->subject); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_MESSAGE'); ?></th>
						<td><?php echo $this->xmessage->message; ?></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div><!-- / .subject -->
</div><!-- / .withleft -->
