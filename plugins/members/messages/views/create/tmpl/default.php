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

$u =& JUser::getInstance($this->user);
?>
<?php if ($this->getError()) { ?>
<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo JRoute::_('index.php?option='.$option.'&id='.$this->member->get('uidNumber').'&active=messages'); ?>" method="post" id="hubForm<?php if ($this->no_html) { echo '-ajax'; }; ?>">
	<div class="explaination">
		<p class="info"><?php echo JText::_('MEMBER_MESSAGE_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<h3><?php echo JText::_('MEMBER_MESSAGE_MEMBERS'); ?></h3>

		<label>
			<?php echo JText::_('MEMBER_MESSAGE_USERS'); ?> 
			<input type="hidden" name="users[]" value="<?php echo $u->get('id'); ?>" />
			<strong><?php echo $u->get('name'); ?></strong>
		</label>
		<label>
			<?php echo JText::_('MEMBER_MESSAGE'); ?>
			<textarea name="message" id="msg-message" rows="12" cols="50"></textarea>
		</label>
	</fieldset><div class="clear"></div>
	<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="active" value="messages" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="action" value="send" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
	<p class="submit">
		<input type="submit" value="<?php echo JText::_('MEMBER_MESSAGE_SEND'); ?>" />
	</p>
</form>