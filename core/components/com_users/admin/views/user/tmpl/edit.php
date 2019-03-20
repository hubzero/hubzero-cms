<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('formvalidation');
$canDo = UsersHelper::getActions();

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();
?>

<script type="text/javascript">
	Hubzero.submitbutton = function(task)
	{
		if (task == 'user.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Hubzero.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=com_users&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_USERS_USER_ACCOUNT_DETAILS'); ?></span></legend>

				<?php foreach ($this->form->getFieldset('user_details') as $field) :?>
					<div class="input-wrap">
						<?php echo $field->label; ?>
						<?php echo $field->input; ?>
					</div>
				<?php endforeach; ?>
			</fieldset>

			<?php if ($this->grouplist) :?>
			<fieldset id="user-groups" class="adminform">
				<legend><?php echo Lang::txt('COM_USERS_ASSIGNED_GROUPS'); ?></legend>

				<?php echo $this->loadTemplate('groups');?>
			</fieldset>
			<?php endif; ?>
		</div>

		<div class="col span5">
			<?php
			echo Html::sliders('start');

			foreach ($fieldsets as $fieldset) :
				if ($fieldset->name == 'user_details') :
					continue;
				endif;
				echo Html::sliders('panel', Lang::txt($fieldset->label), $fieldset->name);
			?>
			<fieldset class="panelform">
				<?php foreach ($this->form->getFieldset($fieldset->name) as $field): ?>
					<?php if ($field->hidden): ?>
						<?php echo $field->input; ?>
					<?php else: ?>
						<div class="input-wrap">
							<?php echo $field->label; ?>
							<?php echo $field->input; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</fieldset>
			<?php endforeach; ?>
			<?php echo Html::sliders('end'); ?>

			<input type="hidden" name="task" value="" />
			<?php echo Html::input('token'); ?>
		</div>
	</div>
</form>
