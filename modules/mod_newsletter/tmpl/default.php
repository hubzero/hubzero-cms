<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
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
defined('_JEXEC') or die('Restricted access');
?>

<?php if (is_object($this->mailinglist)) : ?>
	<div class="mailinglist-details">
		<span class="name">
			<?php echo $this->mailinglist->name; ?>
		</span>
		<span class="description">
			<?php echo nl2br($this->mailinglist->description); ?>
		</span>
	</div>

	<form class="mailinglist-signup" action="index.php" method="post">
		<?php if (is_object($this->subscription)) : ?>
			<span>It seems you are already subscribed to this mailing list. <a href="<?php echo JRoute::_('index.php?option=com_newsletter&task=subscribe'); ?>">Click here</a> to manage your newsletter mailing list subscriptions.</span>
		<?php else : ?>
			<label>Email Address: <span class="required">Required</span>
				<input type="text" name="email_<?php echo JUtility::getToken(); ?>" id="email" value="<?php echo $this->juser->get('email'); ?>" />
				<input type="hidden" name="list_<?php echo JUtility::getToken(); ?>" value="<?php echo $this->mailinglist->id; ?>" />
			</label>
			<label id="hp1">Honey Pot: <span class="optional">Please leave blank.</span>
				<input type="text" name="hp1" value="" />
			</label>
			<input type="submit" value="Sign Up!" id="sign-up-submit" />
			<input type="hidden" name="option" value="com_newsletter" />
			<input type="hidden" name="controller" value="mailinglist" />
			<input type="hidden" name="subscriptionid" value="<?php echo $this->subscriptionId; ?>" />
			<input type="hidden" name="task" value="dosinglesubscribe" />
			<input type="hidden" name="return" value="<?php echo base64_encode($_SERVER['REQUEST_URI']); ?>">
			<?php echo JHTML::_( 'form.token' ); ?>
		<?php endif; ?>
	</form>
<?php else : ?>
	<p class="warning">
		<?php echo JText::_('The newsletter mailing list module setup is not complete.'); ?>
	</p>
<?php endif; ?>