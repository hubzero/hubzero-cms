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
Html::addIncludePath(Component::path($this->option) . '/helpers/html');
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

Toolbar::title(Lang::txt('COM_MESSAGES_WRITE_PRIVATE_MESSAGE'), 'new-privatemessage.png');
Toolbar::save('message.save', 'COM_MESSAGES_TOOLBAR_SEND');
Toolbar::cancel('message.cancel');
Toolbar::help('JHELP_COMPONENTS_MESSAGING_WRITE');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'message.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>
<form action="<?php echo Route::url('index.php?option=com_messages'); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

		<fieldset class="adminform">
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MESSAGES_FIELD_USER_ID_TO_DESC'); ?>">
				<label for="field-title"><?php echo Lang::txt('COM_MESSAGES_FIELD_USER_ID_TO_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<?php
				$mc = Event::trigger('hubzero.onGetSingleEntry', array(
					array(
						'members',   // The component to call
						'fields[user_id_to]',        // Name of the input field
						'field-user_id_to', // ID of the input field
						'',          // CSS class(es) for the input field
						'' // The value of the input field
					)
				));
				if (count($mc) > 0) {
					echo $mc[0];
				} else { ?>
					<input type="text" name="fields[user_id_to]" id="field-user_id_to" value="<?php echo $this->item->get('user_id_to'); ?>" />
				<?php } ?>
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MESSAGES_FIELD_SUBJECT_DESC'); ?>">
				<label for="field-subject"><?php echo Lang::txt('COM_MESSAGES_FIELD_SUBJECT_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[subject]" id="field-subject" maxlength="250" value="<?php echo $this->escape($this->item->get('subject')); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MESSAGES_FIELD_MESSAGE_DESC'); ?>">
				<label for="field-message"><?php echo Lang::txt('COM_MESSAGES_FIELD_MESSAGE_LABEL'); ?>:</label>
				<textarea name="message" id="field-message" cols="80" rows="10"><?php echo $this->escape($this->item->get('message')); ?></textarea>
			</div>
		</fieldset>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>

</form>
