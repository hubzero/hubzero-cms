<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('formvalidation');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'menu.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=com_menus&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form">
	<div class="width-40">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MENUS_MENU_DETAILS');?></span></legend>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('menutype'); ?>
				<?php echo $this->form->getInput('menutype'); ?>
			</div>

			<div class="input-wrap">
				<?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
</form>
