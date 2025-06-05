<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<div class="<?php echo $this->module->module; ?>">
<?php if ($this->recipient === ''): ?>
	<p class="error"><?php echo Lang::txt('MOD_RAPID_CONTACT_ERROR_NO_RECIPIENT'); ?></p>
<?php else: ?>
	<form method="post" action="<?php echo $this->url; ?>" id="<?php echo $this->module->module; ?>-form-<?php echo $this->module->id; ?>" class="<?php echo $this->mod_class_suffix; ?>">
		<fieldset>
			<legend><?php echo Lang::txt('MOD_RAPID_CONTACT_FORM'); ?></legend>

			<?php if ($this->replacement): ?>
				<p class="passed"><?php echo $this->replacement; ?></p>
			<?php endif; ?>
			<?php if ($this->pre_text): ?>
				<p><?php echo $this->pre_text; ?></p>
			<?php endif; ?>
			<?php if ($this->error): ?>
				<p class="error"><?php echo $this->error; ?></p>
			<?php endif; ?>

			<div class="form-group input-wrap">
				<label for="contact-name<?php echo $this->module->id; ?>">
					<?php echo $this->name_label; ?>
				</label>
				<span class="input">
					<input type="text" class="form-control" id="contact-name<?php echo $this->module->id; ?>" name="rp[name]" value="<?php echo $this->escape($this->posted['name']); ?>" />
				</span>
			</div>

			<div class="form-group input-wrap">
				<label for="contact-email<?php echo $this->module->id; ?>">
					<?php echo $this->email_label; ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				</label>
				<span class="input">
					<input type="email" class="form-control" id="contact-email<?php echo $this->module->id; ?>" name="rp[email]" value="<?php echo $this->escape($this->posted['email']); ?>" />
				</span>
			</div>

			<div class="form-group input-wrap">
				<label for="contact-subject<?php echo $this->module->id; ?>">
					<?php echo $this->subject_label; ?>
				</label>
				<span class="input">
					<input type="text" class="form-control" id="contact-subject<?php echo $this->module->id; ?>" name="rp[subject]" value="<?php echo $this->escape($this->posted['subject']); ?>" />
				</span>
			</div>

			<div class="form-group input-wrap">
				<label for="contact-comments<?php echo $this->module->id; ?>">
					<?php echo $this->message_label; ?>
				</label>
				<span class="input">
					<textarea class="form-control" name="rp[message]" id="contact-comments<?php echo $this->module->id; ?>" cols="35" rows="10"><?php echo $this->escape(!isset($this->posted['message']) ? '' : $this->posted['message']); ?></textarea>
				</span>
			</div>

			<?php if ($this->enable_anti_spam): ?>
				<div class="form-group input-wrap">
					<label for="contact-antispam<?php echo $this->module->id; ?>">
						<?php echo $this->anti_spam_q; ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
					</label>
					<span class="input">
						<input type="text" class="form-control" id="contact-antispam<?php echo $this->module->id; ?>" name="rp[anti_spam_answer]" />
					</span>
				</div>
			<?php endif; ?>

			<span class="submit">
				<?php echo Html::input('token'); ?>
				<input type="submit" class="btn" value="<?php echo $this->escape($this->button_text); ?>" />
			</span>
		</fieldset>
	</form>
<?php endif; ?>
</div>
