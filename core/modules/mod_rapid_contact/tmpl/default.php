<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<div class="<?php echo $this->module->module; ?>">
<?php if ($this->recipient === '') { ?>
	<p class="error"><?php echo Lang::txt('MOD_RAPID_CONTACT_ERROR_NO_RECIPIENT'); ?></p>
<?php } else { ?>
	<form method="post" action="<?php echo $this->url; ?>" id="<?php echo $this->module->module; ?>-form-<?php echo $this->module->id; ?>" class="<?php echo $this->mod_class_suffix; ?>">
		<fieldset>
			<legend><?php echo Lang::txt('MOD_RAPID_CONTACT_FORM'); ?></legend>

			<?php if ($this->replacement) { ?>
				<p class="passed"><?php echo $this->replacement; ?></p>
			<?php } ?>
			<?php if ($this->pre_text) { ?>
				<p><?php echo $this->pre_text; ?></p>
			<?php } ?>
			<?php if ($this->error) { ?>
				<p class="error"><?php echo $this->error; ?></p>
			<?php } ?>

			<div class="input-wrap">
				<label for="contact-name<?php echo $this->module->id; ?>">
					<?php echo $this->name_label; ?>
				</label>
				<span class="input">
					<input type="text" id="contact-name<?php echo $this->module->id; ?>" name="rp[name]" value="<?php echo $this->escape($this->posted['name']); ?>" />
				</span>
			</div>

			<div class="input-wrap">
				<label for="contact-email<?php echo $this->module->id; ?>">
					<?php echo $this->email_label; ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				</label>
				<span class="input">
					<input type="email" id="contact-email<?php echo $this->module->id; ?>" name="rp[email]" value="<?php echo $this->escape($this->posted['email']); ?>" />
				</span>
			</div>

			<div class="input-wrap">
				<label for="contact-subject<?php echo $this->module->id; ?>">
					<?php echo $this->subject_label; ?>
				</label>
				<span class="input">
					<input type="text" id="contact-subject<?php echo $this->module->id; ?>" name="rp[subject]" value="<?php echo $this->escape($this->posted['subject']); ?>" />
				</span>
			</div>

			<div class="input-wrap">
				<label for="contact-comments<?php echo $this->module->id; ?>">
					<?php echo $this->message_label; ?>
				</label>
				<span class="input">
					<textarea name="rp[message]" id="contact-comments<?php echo $this->module->id; ?>" cols="35" rows="10"><?php echo $this->escape($this->posted['message']); ?></textarea>
				</span>
			</div>

			<?php if ($this->enable_anti_spam) { ?>
				<div class="input-wrap">
					<label for="contact-antispam<?php echo $this->module->id; ?>">
						<?php echo $this->anti_spam_q; ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
					</label>
					<span class="input">
						<input type="text" id="contact-antispam<?php echo $this->module->id; ?>" name="rp[anti_spam_answer]" />
					</span>
				</div>
			<?php } ?>

			<span class="submit">
				<?php echo Html::input('token'); ?>
				<input type="submit" class="btn" value="<?php echo $this->escape($this->button_text); ?>" />
			</span>
		</fieldset>
	</form>
<?php } ?>
</div>