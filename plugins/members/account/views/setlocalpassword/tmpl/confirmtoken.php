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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css()
     ->css('providers.css', 'com_users')
     ->js()
     ->js('jquery.hoverIntent', 'system');
?>

<h3 class="section-header"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_ENTER_CONFIRMATION_TOKEN'); ?></h3>

<?php if (isset($this->notifications) && count($this->notifications) > 0) {
	foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } // close foreach
} // close if count ?>

<div id="members-account-section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option .
										'&id=' . $this->id .
										'&active=account' .
										'&task=confirmtoken'); ?>" method="post">
		<fieldset>
			<legend><?php echo JText::_('PLG_MEMBERS_ACCOUNT_ENTER_CONFIRMATION_TOKEN'); ?></legend>
			<div class="fieldset-grouping">
				<label for="token"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_TOKEN'); ?>:</label>
				<input id="token" name="token" type="text" class="required" size="36" />
			</div>
		</fieldset>

		<div class="clear"></div>

		<p class="submit">
			<input name="change" type="submit" value="<?php echo JText::_('PLG_MEMBERS_ACCOUNT_SUBMIT'); ?>" />
			<input type="reset" class="cancel" value="<?php echo JText::_('PLG_MEMBERS_ACCOUNT_CANCEL'); ?>" />
		</p>

		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
<div class="clear"></div>