<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="group btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn')); ?>"><?php echo JText::_('Back to Group'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=invite'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><strong>Invite Members to Your Group</strong></p>
			<p>Start typing the names of registered members on the hub and suggestions matching the text entered with be displayed.</p>
			<p><img src="/components/com_groups/assets/img/invite_example.jpg" alt="Example Auto-Completer" width="100%" style="border:3px solid #aaa;" />
			<!--
			<div class="admin-options">
				<p><a href="<?php //echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=view'); ?>"><?php //echo JText::_('GROUPS_VIEW_GROUP'); ?></a></p>
				<p><a href="<?php //echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit'); ?>"><?php //echo JText::_('GROUPS_EDIT_GROUP'); ?></a></p>
				<p><a href="<?php //echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize'); ?>"><?php //echo JText::_('GROUPS_CUSTOMIZE_GROUP'); ?></a></p>
				<p><a href="<?php //echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete'); ?>"><?php //echo JText::_('GROUPS_DELETE_GROUP'); ?></a></p>
			</div>
			-->
		</div>
		<fieldset>
			<legend><?php echo JText::_('GROUPS_INVITE_HEADER'); ?></legend>

	 		<p><?php echo JText::sprintf('GROUPS_INVITE_EXPLANATION',$this->group->get('description')); ?></p>

			<label>
				<?php echo JText::_('GROUPS_INVITE_LOGINS'); ?>
				<?php 
					$mc = $dispatcher->trigger( 'onGetMultiEntry', array(array('members', 'logins', 'acmembers')) );
					if (count($mc) > 0) {
						echo $mc[0];
					} else { ?>
						<input type="text" name="logins" id="acmembers" value="" size="35" />
					<?php } ?>
				<span class="hint"><?php echo JText::_('Enter names or e-mails separated by commas'); ?></span>
			</label>
			<label for="msg">
				<?php echo JText::_('GROUPS_INVITE_MESSAGE'); ?>
				<textarea name="msg" id="msg" rows="12" cols="50"><?php echo $this->escape(stripslashes($this->msg)); ?></textarea>
			</label>
		</fieldset>
		<div class="clear"></div>
		<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="task" value="invite" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		<p class="submit"><input type="submit" value="<?php echo JText::_('INVITE'); ?>" /></p>
	</form>
</div><!-- / .main section -->
