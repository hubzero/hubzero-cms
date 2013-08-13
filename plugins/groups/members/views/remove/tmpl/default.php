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
?>
<form action="<?php echo JRoute::_('index.php?option='.$option.'&cn='.$this->group->get('cn').'&active=members'); ?>" method="post" id="hubForm">
	<div class="explaination">
		<p class="info"><?php echo JText::_('PLG_GROUPS_MEMBERS_REMOVE_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo JText::_('PLG_GROUPS_MEMBERS_REMOVE_MEMBERSHIP'); ?></legend>

		<label>
			<?php echo JText::_('PLG_GROUPS_MEMBERS_REMOVE_USERS'); ?><br />
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
			<?php echo JText::_('PLG_GROUPS_MEMBERS_REMOVE_REASON'); ?>
			<textarea name="reason" id="reason" rows="12" cols="50"></textarea>
		</label>
	</fieldset><div class="clear"></div>
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="members" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="action" value="confirmremove" />
	<p class="submit">
		<input type="submit" value="<?php echo JText::_('PLG_GROUPS_MEMBERS_SUBMIT'); ?>" />
	</p>
</form>
