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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Include the HTML helpers.
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'config.cancel' || document.formvalidator.isValid($('#config-form'))) {
			Joomla.submitform(task, document.getElementById('config-form'));
		}
	}
</script>
<form action="<?php echo Route::url('index.php?option=com_messages&controller=configs'); ?>" method="post" name="adminForm" id="message-form" class="form-validate">
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" onclick="Joomla.submitform('save', this.form);window.top.setTimeout('window.parent.$.fancybox.close()', 1400);"><?php echo Lang::txt('JSAVE');?></button>
				<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo Lang::txt('JCANCEL');?></button>
			</div>
			<?php echo Lang::txt('COM_MESSAGES_MY_SETTINGS') ?>
		</div>
	</fieldset>

	<fieldset class="adminform" style="padding-top: 3em;">
		<fieldset>
			<legend><?php echo Lang::txt('COM_MESSAGES_FIELD_LOCK_LABEL'); ?></legend>

			<div class="input-wrap">
				<input type="radio" name="lock" id="cfg-lock-yes" value="1"<?php if ($this->item->get('lock', 0)) { echo ' checked="checked"'; } ?> />
				<label for="cfg-lock-yes"><?php echo Lang::txt('JYes'); ?></label>
			</div>

			<div class="input-wrap">
				<input type="radio" name="lock" id="cfg-lock-no" value="0"<?php if ($this->item->get('lock', 0)) { echo ' checked="checked"'; } ?> />
				<label for="cfg-lock-no"><?php echo Lang::txt('JNo'); ?></label>
			</div>
		</fieldset>

		<fieldset>
			<legend><?php echo Lang::txt('COM_MESSAGES_FIELD_MAIL_ON_NEW_LABEL'); ?></legend>

			<div class="input-wrap">
				<input type="radio" name="mail_on_new" id="cfg-mail_on_new-yes" value="1"<?php if ($this->item->get('mail_on_new', 1)) { echo ' checked="checked"'; } ?> />
				<label for="cfg-mail_on_new-yes"><?php echo Lang::txt('JYes'); ?></label>
			</div>

			<div class="input-wrap">
				<input type="radio" name="mail_on_new" id="cfg-mail_on_new-no" value="0"<?php if ($this->item->get('mail_on_new', 1)) { echo ' checked="checked"'; } ?> />
				<label for="cfg-mail_on_new-no"><?php echo Lang::txt('JNo'); ?></label>
			</div>
		</fieldset>

		<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MESSAGES_FIELD_AUTO_PURGE_DESC'); ?>">
			<label for="cfg-auto_purge"><?php echo Lang::txt('COM_MESSAGES_FIELD_AUTO_PURGE_LABEL'); ?></label>
			<input type="text" name="auto_purge" id="cfg-auto_purge" value="<?php echo $this->item->get('auto_purge', 7); ?>" />
			<span class="hint"><?php echo Lang::txt('COM_MESSAGES_FIELD_AUTO_PURGE_DESC'); ?></span>
		</div>

		<input type="hidden" name="option" value="com_messages" />
		<input type="hidden" name="controller" value="configs" />
		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>
	</fieldset>
</form>
