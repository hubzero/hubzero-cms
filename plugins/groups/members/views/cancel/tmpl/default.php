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
<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=members'); ?>" method="post" id="hubForm">
	<div class="explaination">
		<p class="info"><?php echo JText::_('PLG_GROUPS_MEMBERS_CANCEL_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<h3><?php echo JText::_('PLG_GROUPS_MEMBERS_CANCEL_INVITATION'); ?></h3>

		<label>
			<?php echo JText::_('PLG_GROUPS_MEMBERS_CANCEL_INVITATIONS'); ?><br />
<?php 
$names = array();
foreach ($this->users as $user) 
{
	$u =& JUser::getInstance($user);
	$names[] = $u->get('name');
?>
			<input type="hidden" name="users[]" value="<?php echo $user; ?>" />
<?php
}
?>
			<strong><?php echo implode(', ',$names); ?></strong>
		</label>
		<label>
			<?php echo JText::_('PLG_GROUPS_MEMBERS_CANCEL_REASON'); ?>
			<textarea name="reason" id="reason" rows="12" cols="50"></textarea>
		</label>
	</fieldset><div class="clear"></div>
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="members" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="confirmcancel" />
	<p class="submit">
		<input type="submit" value="<?php echo JText::_('PLG_GROUPS_MEMBERS_SUBMIT'); ?>" />
	</p>
</form>