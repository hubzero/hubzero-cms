<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" name="adminForm" method="post" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
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
