<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$token = Session::getFormToken();

if (is_object($this->mailinglist)) : ?>
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
				<input type="text" name="email_<?php echo $token; ?>" id="email" value="<?php echo User::get('email'); ?>" data-invalid="<?php echo Lang::txt('MOD_NEWSLETTER_EMAIL_INVALID'); ?>" />
			</label>

			<label for="hp1_<?php echo $token; ?>" id="hp1">
				<?php echo Lang::txt('MOD_NEWSLETTER_HONEYPOT'); ?> <span class="optional"><?php echo Lang::txt('MOD_NEWSLETTER_HONEYPOT_LEAVE_BLANK'); ?></span>
				<input type="text" name="hp1" id="hp1_<?php echo $token; ?>" value="" />
			</label>

			<input type="submit" value="<?php echo Lang::txt('MOD_NEWSLETTER_SIGN_UP'); ?>" id="sign-up-submit" />

			<input type="hidden" name="list_<?php echo $token; ?>" value="<?php echo $this->mailinglist->id; ?>" />
			<input type="hidden" name="option" value="com_newsletter" />
			<input type="hidden" name="controller" value="mailinglist" />
			<input type="hidden" name="subscriptionid" value="<?php echo $this->subscriptionId; ?>" />
			<input type="hidden" name="task" value="dosinglesubscribe" />
			<input type="hidden" name="return" value="<?php echo base64_encode($_SERVER['REQUEST_URI']); ?>">

			<?php echo Html::input('token'); ?>
		<?php endif; ?>
		</fieldset>
	</form>
<?php else : ?>
	<p class="warning">
		<?php echo Lang::txt('MOD_NEWSLETTER_SETUP_INCOMPLETE'); ?>
	</p>
<?php endif;