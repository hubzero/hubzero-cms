<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$isNew = $this->item->isNew();
$canDo = Components\Menus\Helpers\Menus::getActions();

Toolbar::title(Lang::txt($isNew ? 'COM_MENUS_VIEW_NEW_MENU_TITLE' : 'COM_MENUS_VIEW_EDIT_MENU_TITLE'), 'menu');

// If a new item, can save the item.  Allow users with edit permissions to apply changes to prevent returning to grid.
if ($isNew && $canDo->get('core.create'))
{
	if ($canDo->get('core.edit'))
	{
		Toolbar::apply();
	}
	Toolbar::save();
}

// If user can edit, can save the item.
if (!$isNew && $canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}

// If the user can create new items, allow them to see Save & New
if ($canDo->get('core.create'))
{
	Toolbar::save2new();
}
if ($isNew)
{
	Toolbar::cancel();
}
else
{
	Toolbar::cancel('cancel', 'JTOOLBAR_CLOSE');
}
Toolbar::divider();
Toolbar::help('menu');

// Include the component HTML helpers.
Html::addIncludePath(Component::path($this->option) . '/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('formvalidation');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&cid=' . (int) $this->item->get('id')); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
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

	<input type="hidden" name="cid" value="<?php echo (int) $this->item->get('id'); ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
</form>
