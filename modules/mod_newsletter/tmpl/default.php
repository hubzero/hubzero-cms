<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

	<form class="mailinglist-signup" action="<?php echo Route::url('index.php?option=com_newsletter'); ?>" method="post">
		<fieldset>
		<?php if (is_object($this->subscription)) : ?>
			<span><?php echo Lang::txt('MOD_NEWSLETTER_ALREADY_SUBSCRIBED', Route::url('index.php?option=com_newsletter&task=subscribe')); ?></span>
		<?php else : ?>
			<label for="email">
				<?php echo Lang::txt('MOD_NEWSLETTER_EMAIL'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
				<input type="text" name="email_<?php echo JUtility::getToken(); ?>" id="email" value="<?php echo User::get('email'); ?>" data-invalid="<?php echo Lang::txt('MOD_NEWSLETTER_EMAIL_INVALID'); ?>" />
			</label>

			<label for="hp1_<?php echo JUtility::getToken(); ?>" id="hp1">
				<?php echo Lang::txt('MOD_NEWSLETTER_HONEYPOT'); ?> <span class="optional"><?php echo Lang::txt('MOD_NEWSLETTER_HONEYPOT_LEAVE_BLANK'); ?></span>
				<input type="text" name="hp1" id="hp1_<?php echo JUtility::getToken(); ?>" value="" />
			</label>

			<input type="submit" value="<?php echo Lang::txt('MOD_NEWSLETTER_SIGN_UP'); ?>" id="sign-up-submit" />

			<input type="hidden" name="list_<?php echo JUtility::getToken(); ?>" value="<?php echo $this->mailinglist->id; ?>" />
			<input type="hidden" name="option" value="com_newsletter" />
			<input type="hidden" name="controller" value="mailinglist" />
			<input type="hidden" name="subscriptionid" value="<?php echo $this->subscriptionId; ?>" />
			<input type="hidden" name="task" value="dosinglesubscribe" />
			<input type="hidden" name="return" value="<?php echo base64_encode($_SERVER['REQUEST_URI']); ?>">

			<?php echo JHTML::_('form.token'); ?>
		<?php endif; ?>
		</fieldset>
	</form>
<?php else : ?>
	<p class="warning">
		<?php echo Lang::txt('MOD_NEWSLETTER_SETUP_INCOMPLETE'); ?>
	</p>
<?php endif; ?>