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
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="group" href="<?php echo JRoute::_('index.php?option='.$this->option); ?>"><?php echo JText::_('GROUPS_ALL_GROUPS'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<div class="admin-options">
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=view'); ?>"><?php echo JText::_('GROUPS_VIEW_GROUP'); ?></a></p>
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=edit'); ?>"><?php echo JText::_('GROUPS_EDIT_GROUP'); ?></a></p>
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=customize'); ?>"><?php echo JText::_('GROUPS_CUSTOMIZE_GROUP'); ?></a></p>
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&task=invite'); ?>"><?php echo JText::_('GROUPS_INVITE_USERS'); ?></a></p>
			</div>
		</div>
		<fieldset>
			<h3><?php echo JText::_('GROUPS_DELETE_HEADER'); ?></h3>

	 		<p class="warning"><?php echo JText::sprintf('GROUPS_DELETE_WARNING',$this->group->get('description')).'<br /><br />'.$this->log; ?></p>

			<label>
				<?php echo JText::_('GROUPS_DELETE_MESSAGE'); ?>
				<textarea name="msg" id="msg" rows="12" cols="50"><?php echo htmlentities($this->msg); ?></textarea>
			</label>
			<label>
				<input type="checkbox" class="option" name="confirmdel" value="1" /> 
				<?php echo JText::_('GROUPS_DELETE_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>
		<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="task" value="delete" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<p class="submit"><input type="submit" value="<?php echo JText::_('DELETE'); ?>" /></p>
	</form>
</div><!-- / .main section -->
