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

$this->css()
     ->js();
?>
<?php if (!$this->components) { ?>
	<p class="error"><?php echo JText::_('PLG_MEMBERS_MESSAGES_NO_COMPONENTS_FOUND'); ?></p>
<?php } else { ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=messages'); ?>" method="post" id="hubForm" class="full">
		<input type="hidden" name="action" value="savesettings" />
		<table class="settings">
			<caption>
				<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
			</caption>
			<thead>
				<tr>
					<th scope="col"><?php echo JText::_('PLG_MEMBERS_MESSAGES_SENT_WHEN'); ?></th>
				<?php foreach ($this->notimethods as $notimethod) { ?>
					<th scope="col"><input type="checkbox" name="override[<?php echo $notimethod; ?>]" value="all" onclick="HUB.MembersMsg.checkAll(this, 'opt-<?php echo $notimethod; ?>');" /> <?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_'.strtoupper($notimethod)); ?></th>
				<?php } ?>
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
			$lang    = JFactory::getLanguage();
			foreach ($this->components as $component)
			{
				if ($component->name != $sheader) {
					$sheader = $component->name;
					$lang->load($component->name);

					$display_header = $lang->hasKey($component->name) ? JText::_($component->name) : ucfirst(str_replace('com_', '', $component->name));
				?>
				<tr class="section-header">
					<th scope="col"><?php echo $this->escape($display_header); ?></th>
					<?php foreach ($this->notimethods as $notimethod) { ?>
						<th scope="col"><span class="<?php echo $notimethod; ?> iconed"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_'.strtoupper($notimethod)); ?></span></th>
					<?php } ?>
				</tr>
				<?php
				}
				$cls = (($cls == 'even') ? 'odd' : 'even');
				?>
				<tr class="<?php echo $cls; ?>">
					<th scope="col"><?php echo $this->escape($component->title); ?></th>
					<?php echo plgMembersMessages::selectMethod($this->notimethods, $component->action, $this->settings[$component->action]['methods'], $this->settings[$component->action]['ids']); ?>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>
	</form>
<?php } ?>