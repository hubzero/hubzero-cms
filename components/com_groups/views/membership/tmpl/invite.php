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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// push css
$this->css();

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher = JDispatcher::getInstance();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-group btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn')); ?>">
					<?php echo JText::_('COM_GROUPS_ACTION_BACK_TO_GROUP'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php
		foreach ($this->notifications as $notification)
		{
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<h3><?php echo JText::_('COM_GROUPS_INVITE_SIDEBAR_HELP_TITLE'); ?></h3>
			<p><?php echo JText::_('COM_GROUPS_INVITE_SIDEBAR_HELP_DESC'); ?></p>
			<p><img src="<?php echo JURI::base(true); ?>/components/com_groups/assets/img/invite_example.jpg" alt="Example Auto-Completer" width="100%" style="border:3px solid #aaa;" />
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_GROUPS_INVITE_SECTION_TITLE'); ?></legend>

	 		<p><?php echo JText::sprintf('COM_GROUPS_INVITE_SECTION_DESC',$this->group->get('description')); ?></p>

			<label>
				<?php echo JText::_('COM_GROUPS_INVITE_LOGINS'); ?> <span class="required"><?php echo JText::_('COM_GROUPS_REQUIRED'); ?></span>
				<?php
					$mc = $dispatcher->trigger( 'onGetMultiEntry', array(array('members', 'logins', 'acmembers')) );
					if (count($mc) > 0) {
						echo $mc[0];
					} else { ?>
						<input type="text" name="logins" id="acmembers" value="" size="35" />
					<?php } ?>
				<span class="hint"><?php echo JText::_('COM_GROUPS_INVITE_LOGINS_HINT'); ?></span>
			</label>
			<label for="msg">
				<?php echo JText::_('COM_GROUPS_INVITE_MESSAGE'); ?>
				<textarea name="msg" id="msg" rows="12" cols="50"><?php echo $this->escape(stripslashes($this->msg)); ?></textarea>
			</label>
		</fieldset>
		<div class="clear"></div>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="membership" />
		<input type="hidden" name="task" value="doinvite" />
		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_GROUPS_INVITE_BTN_TEXT'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
