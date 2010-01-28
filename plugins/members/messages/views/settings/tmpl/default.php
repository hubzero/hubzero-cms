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
<h3><a name="messages"></a><?php echo JText::_('PLG_MEMBERS_MESSAGES'); ?></h3>
<div class="withleft">
	<div class="aside">
		<ul>
			<li><a class="box" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=inbox'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_INBOX'); ?></span></a></li>
			<li><a class="sent" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=sent'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_SENT'); ?></span></a></li>
			<li><a class="archive" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=archive'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_ARCHIVE'); ?></span></a></li>
			<li><a class="trash" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=trash'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_TRASH'); ?></span></a></li>
			<li class="active"><a class="config" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=settings'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_SETTINGS'); ?></span></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if (!$this->components) { ?>
		<p class="error"><?php echo JText::_('No components configured for XMessages.'); ?></p>
<?php } else { ?>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages'); ?>" method="post" id="hubForm" class="full">
			<input type="hidden" name="action" value="savesettings" />
			<table class="settings" summary="<?php echo JText::_('PLG_MEMBERS_MESSAGES_TBL_SUMMARY_METHODS'); ?>">
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('PLG_MEMBERS_MESSAGES_SENT_WHEN'); ?></th>
<?php
	foreach ($this->notimethods as $notimethod) 
	{
?>
						<th scope="col"><input type="checkbox" name="override[<?php echo $notimethod; ?>]" value="all" onclick="HUB.MembersMsg.checkAll(this, 'opt-<?php echo $notimethod; ?>');" /> <?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_'.strtoupper($notimethod)); ?></th>
<?php
	}
?>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo (count($this->notimethods) + 1); ?>">
							<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
						</td>
					</tr>
				</tfoot>
				<tbody>
<?php
				$cls = 'even';

				$sheader = '';
				foreach ($this->components as $component) 
				{
					if ($component->name != $sheader) {
						$sheader = $component->name;
?>
					<tr class="section-header">
						<th scope="col"><?php echo $component->name; ?></th>
<?php
						foreach ($this->notimethods as $notimethod) 
						{
?>
						<th scope="col"><span class="<?php echo $notimethod; ?> iconed"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_'.strtoupper($notimethod)); ?></span></th>
<?php
						}
?>
					</tr>
<?php
					}
					$cls = (($cls == 'even') ? 'odd' : 'even');
?>
					<tr class="<?php echo $cls; ?>">
						<th scope="col"><?php echo $component->title; ?></th>
						<?php echo plgMembersMessages::selectMethod($this->notimethods, $component->action, $this->settings[$component->action]['methods'], $this->settings[$component->action]['ids']); ?>
					</tr>
<?php
				}
?>
				</tbody>
			</table>
		</form>
<?php } ?>
	</div><!-- / .subject -->
</div><!-- / .withleft -->