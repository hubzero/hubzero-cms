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
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="group" href="<?php echo JRoute::_('index.php?option='.$this->option); ?>"><?php echo JText::_('GROUPS_ALL_GROUPS'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<div class="admin-options">
				<p class="group"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=view'); ?>"><?php echo JText::_('GROUPS_VIEW_GROUP'); ?></a></p>
				<p class="edit"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit'); ?>"><?php echo JText::_('GROUPS_EDIT_GROUP'); ?></a></p>
				<p class="delete"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=delete'); ?>"><?php echo JText::_('GROUPS_DELETE_GROUP'); ?></a></p>
			</div>
		</div>
		<fieldset>
			<legend><?php echo JText::_('GROUPS_INVITE_HEADER'); ?></legend>

	 		<p><?php echo JText::sprintf('GROUPS_INVITE_EXPLANATION',$this->group->get('description')); ?></p>

			<label>
				<?php echo JText::_('GROUPS_INVITE_LOGINS'); ?>
				<textarea name="logins" id="logins" rows="8" cols="50"></textarea>
				<span class="hint"><?php echo JText::_('Enter logins or e-mails separated by commas'); ?></span>
			</label>
			<label>
				<?php echo JText::_('GROUPS_INVITE_MESSAGE'); ?>
				<textarea name="msg" id="msg" rows="12" cols="50"><?php echo htmlentities(stripslashes($this->msg)); ?></textarea>
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