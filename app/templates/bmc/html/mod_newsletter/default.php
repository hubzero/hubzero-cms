<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$token = Session::getFormToken();

if (is_object($this->mailinglist)) : ?>
	<form class="mailinglist-signup" action="<?php echo Route::url('index.php?option=com_newsletter'); ?>" method="post">
		<h4>Subscribe to our newsletter</h4>

		<?php if (is_object($this->subscription)) : ?>
			<span><?php echo Lang::txt('You are already subscribed to our newsletter. <br><a href="%s">Manage your newsletter subscriptions.</a>', Route::url('index.php?option=com_newsletter&task=subscribe')); ?></span>
		<?php else : ?>
			<div>
				<label for="email">
					<input type="text" name="email_<?php echo $token; ?>" id="email" value="<?php echo User::get('email'); ?>" placeholder="email address" data-invalid="<?php echo Lang::txt('MOD_NEWSLETTER_EMAIL_INVALID'); ?>" />
				</label>

				<label for="hp1_<?php echo $token; ?>" id="hp1">
					<?php echo Lang::txt('MOD_NEWSLETTER_HONEYPOT'); ?> <span class="optional"><?php echo Lang::txt('MOD_NEWSLETTER_HONEYPOT_LEAVE_BLANK'); ?></span>
					<input type="text" name="hp1" id="hp1_<?php echo $token; ?>" value="" />
				</label>

				<input type="submit" value="<?php echo Lang::txt('MOD_NEWSLETTER_SIGN_UP'); ?>" id="sign-up-submit" />

				<input type="hidden" name="list_<?php echo $token; ?>" value="<?php echo $this->mailinglist->id; ?>" />
				<input type="hidden" name="option" value="com_newsletter" />
				<input type="hidden" name="controller" value="mailinglists" />
				<input type="hidden" name="subscriptionid" value="<?php echo $this->subscriptionId; ?>" />
				<input type="hidden" name="task" value="dosinglesubscribe" />
				<input type="hidden" name="return" value="<?php echo base64_encode($_SERVER['REQUEST_URI']); ?>">
			</div>

			<?php echo Html::input('token'); ?>
		<?php endif; ?>
	</form>
<?php else : ?>
	<p class="warning">
		<?php echo Lang::txt('MOD_NEWSLETTER_SETUP_INCOMPLETE'); ?>
	</p>
<?php endif;
