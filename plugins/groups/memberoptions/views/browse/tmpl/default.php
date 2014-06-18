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
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser = JFactory::getUser();
$params = $params =  JComponentHelper::getParams('com_groups');

$allowEmailResponses = $params->get('email_comment_processing');

// Be sure to update this if you add more options
$atLeastOneOption = false;
if ($allowEmailResponses)
{
	$atLeastOneOption = true;
}
?>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=memberoptions'); ?>" method="post" id="memberoptionform">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="action" value="savememberoptions" />
	<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID;?>" />

	<div class="group-content-header">
		<h3><?php echo JText::_('GROUP_MEMBEROPTIONS'); ?></h3>
	</div>

	<p><?php echo JText::_('GROUP_MEMBEROPTIONS_DESC'); ?></p>

	<?php if ($allowEmailResponses) { ?>
		<div style="padding-top:25px;">
			<input type="checkbox" id="recvpostemail" value="1" name="recvpostemail" <?php if ($this->recvEmailOptionValue == 1) { echo 'checked="checked"'; } ?> />
			<label for="recvpostemail"><?php echo JText::_('GROUP_RECEIVE_EMAILS_DISCUSSION_POSTS'); ?></label>
		</div>
	<?php
	}
	?>

	<?php if ($atLeastOneOption) { ?>
		<div style="padding-top:25px;">
			<input type="submit" value="Save" />
		</div>
	<?php
	}
	else{
		echo JText::_('GROUP_MEMBEROPTIONS_NONE');
	}
	?>
</form>
