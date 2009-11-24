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
<h3><a name="messages"></a><?php echo JText::_('MESSAGES'); ?></h3>
<div class="withleft">
	<div class="aside">
		<ul>
			<li<?php if ($this->xmr->state == 0) { echo ' class="active"'; } ?>><a class="box" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=inbox'); ?>"><span><?php echo JText::_('MESSAGES_INBOX'); ?></span></a></li>
			<li><a class="sent" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=sent'); ?>"><span><?php echo JText::_('MESSAGES_SENT'); ?></span></a></li>
			<li<?php if ($this->xmr->state == 1) { echo ' class="active"'; } ?>><a class="archive" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=archive'); ?>"><span><?php echo JText::_('MESSAGES_ARCHIVE'); ?></span></a></li>
			<li<?php if ($this->xmr->state == 2) { echo ' class="active"'; } ?>><a class="trash" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=trash'); ?>"><span><?php echo JText::_('MESSAGES_TRASH'); ?></span></a></li>
			<li><a class="config" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=settings'); ?>"><span><?php echo JText::_('MESSAGES_SETTINGS'); ?></span></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages'); ?>" method="post" id="hubForm" class="full">
<?php if ($this->xmr->state != 2) { ?>
			<fieldset id="filters">
				<a class="trash" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&mid[]='.$this->xmessage->id.'&action=sendtotrash'); ?>" title="<?php echo JText::_('MESSAGES_TRASH'); ?>"><?php echo JText::_('MESSAGES_TRASH'); ?></a>
			</fieldset>
<?php } ?>
			<fieldset id="actions">
				<select class="option" name="action">
					<option value=""><?php echo JText::_('MSG_WITH_SELECTED'); ?></option>
<?php 
			switch ($this->xmr->state) 
			{
				case 2:
?>
					<option value="sendtoinbox"><?php echo JText::_('MSG_SEND_TO_INBOX'); ?></option>
					<option value="sendtoarchive"><?php echo JText::_('MSG_SEND_TO_ARCHIVE'); ?></option>
<?php
				break;
				case 1:
?>
					<option value="sendtoinbox"><?php echo JText::_('MSG_SEND_TO_INBOX'); ?></option>
					<option value="sendtotrash"><?php echo JText::_('MSG_SEND_TO_TRASH'); ?></option>
<?php
				break;
				case 0:
				default:
?>
					<option value="sendtoarchive"><?php echo JText::_('MSG_SEND_TO_ARCHIVE'); ?></option>
					<option value="sendtotrash"><?php echo JText::_('MSG_SEND_TO_TRASH'); ?></option>
<?php
				break;
			}
?>
				</select> 
				<input class="option" type="submit" value="<?php echo JText::_('MSG_APPLY'); ?>" />
				<input type="hidden" name="mid[]" id="msg<?php echo $this->xmessage->id; ?>" value="<?php echo $this->xmessage->id; ?>" />
			</fieldset>
			<table class="profile" summary="<?php echo JText::_('TBL_SUMMARY_OVERVIEW'); ?>">
				<tbody>
					<tr>
						<th><?php echo JText::_('Received'); ?></th>
						<td><?php echo JHTML::_('date', $this->xmessage->created, '%d %b, %Y'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('From'); ?></th>
						<td><?php echo $this->from; ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Subject'); ?></th>
						<td><?php echo stripslashes($this->xmessage->subject); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Message'); ?></th>
						<td><?php echo $this->xmessage->message; ?></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div><!-- / .subject -->
</div><!-- / .withleft -->