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
?>

<script type="text/javascript">
	Hubzero.submitbutton = function(task)
	{
		if (task == 'group.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Hubzero.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=com_users&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-100">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_USERS_USERGROUP_DETAILS');?></span></legend>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?>
			</div>

			<?php $parent_id = $this->form->getField('parent_id');?>

			<?php if (!$parent_id->hidden) { ?>
				<div class="input-wrap">
					<?php echo $parent_id->label; ?>
					<?php echo $parent_id->input; ?>
				</div>
			<?php } else { ?>
				<?php echo $parent_id->input; ?>
			<?php } ?>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>
	</div>
</form>
<div class="clr"></div>
