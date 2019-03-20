<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/* @var $this UsersViewNote */

Html::behavior('tooltip');
Html::behavior('formvalidation');
?>
<script language="javascript" type="text/javascript">
Hubzero.submitbutton = function(task)
{
	if (task == 'note.cancel' || document.formvalidator.isValid($('#item-form'))) {
		Hubzero.submitform(task, document.getElementById('item-form'));
	}
}
</script>
<form action="<?php echo Route::url('index.php?option=com_users&view=note&id='.(int) $this->item->id);?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<fieldset class="adminform">
		<legend>
			<span><?php echo $this->item->id ? Lang::txt('COM_USERS_EDIT_NOTE_N', $this->item->id) : Lang::txt('COM_USERS_NEW_NOTE');?></span>
		</legend>

		<div class="input-wrap">
			<?php echo $this->form->getLabel('subject'); ?>
			<?php echo $this->form->getInput('subject'); ?>
		</div>
		<div class="input-wrap">
			<?php echo $this->form->getLabel('user_id'); ?>
			<?php echo $this->form->getInput('user_id'); ?>
		</div>
		<div class="input-wrap">
			<?php echo $this->form->getLabel('catid'); ?>
			<?php echo $this->form->getInput('catid'); ?>
		</div>
		<div class="input-wrap">
			<?php echo $this->form->getLabel('state'); ?>
			<?php echo $this->form->getInput('state'); ?>
		</div>
		<div class="input-wrap">
			<?php echo $this->form->getLabel('review_time'); ?>
			<?php echo $this->form->getInput('review_time'); ?>
		</div>

		<div class="input-wrap">
			<?php echo $this->form->getLabel('body'); ?>
			<?php echo $this->form->getInput('body'); ?>
		</div>

		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>
	</fieldset>
</form>
