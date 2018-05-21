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

// no direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEMBERS_MASS_MAIL'), 'massmail');
Toolbar::custom('send', 'send.png', 'send_f2.png', 'COM_MEMBERS_TOOLBAR_MAIL_SEND_MAIL', false);
Toolbar::cancel('cancelmail');
Toolbar::divider();
Toolbar::preferences('com_members');
Toolbar::divider();
Toolbar::help('mail');

// Load the tooltip behavior.
Html::behavior('tooltip');
?>
<script>
Joomla.submitbutton = function(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancelmail') {
		Joomla.submitform(pressbutton);
		return;
	}
	// do field validation
	if (form.fields_subject.value == ""){
		alert("<?php echo Lang::txt('COM_MEMBERS_MAIL_PLEASE_FILL_IN_THE_SUBJECT'); ?>");
	} else if (getSelectedValue('adminForm','fields[group]') < 0){
		alert("<?php echo Lang::txt('COM_MEMBERS_MAIL_PLEASE_SELECT_A_GROUP'); ?>");
	} else if (form.fields_message.value == ""){
		alert("<?php echo Lang::txt('COM_MEMBERS_MAIL_PLEASE_FILL_IN_THE_MESSAGE'); ?>");
	} else {
		Joomla.submitform(pressbutton);
	}
}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" name="adminForm" method="post" id="item-form">
	<p class="warning"><?php echo Lang::txt('COM_MEMBERS_MAIL_DO_NOT_USE_FOR_COMMERCIAL_USE'); ?></p>
	<div class="grid">
		<div class="col span5">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MEMBERS_MAIL_DETAILS'); ?></legend>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('recurse'); ?>
					<?php echo $this->form->getInput('recurse'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('mode'); ?>
					<?php echo $this->form->getInput('mode'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('disabled'); ?>
					<?php echo $this->form->getInput('disabled'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('group'); ?>
					<?php echo $this->form->getInput('group'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('bcc'); ?>
					<?php echo $this->form->getInput('bcc'); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span7">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MEMBERS_MAIL_MESSAGE'); ?></legend>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('subject'); ?>
					<?php echo $this->form->getInput('subject'); ?>
				</div>

				<div class="input-wrap">
					<?php echo $this->form->getLabel('message'); ?>
					<?php echo $this->form->getInput('message'); ?>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="send" />

	<?php echo Html::input('token'); ?>
</form>
