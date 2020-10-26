<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

$text = ($this->task == 'edit' ? Lang::txt('COM_STOREFRONT_EDIT') : Lang::txt('COM_STOREFRONT_NEW'));

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': ' . Lang::txt('COM_STOREFRONT_PRODUCT_META') . ': ' . $text, 'storefront');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
	<div class="col span7">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_STOREFRONT_GROUP_MEMBERSHIP_OPTIONS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-groupId"><?php echo Lang::txt('COM_STOREFRONT_GROUP_IDS'); ?>:</label><br />
				<input type="text" name="fields[groupId]" id="field-groupId" size="30" maxlength="100" value="<?php echo $this->meta->groupId; ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col span5">

		<table class="meta">
			<tbody>
			<tr>
				<th class="key"><?php echo Lang::txt('COM_STOREFRONT_PRODUCT') . ' ' . Lang::txt('COM_STOREFRONT_ID'); ?>:</th>
				<td>
					<?php echo $this->row->getId(); ?>
					<input type="hidden" name="fields[pId]" id="field-id" value="<?php echo $this->escape($this->row->getId()); ?>" />
					<input type="hidden" name="id" id="id" value="<?php echo $this->escape($this->row->getId()); ?>" />
				</td>
			</tr>
			<tr>
				<th class="key"><?php echo Lang::txt('COM_STOREFRONT_PRODUCT'); ?>:</th>
				<td>
					<?php echo $this->row->getName(); ?>
				</td>
			</tr>
			</tbody>
		</table>

	</div>
	</div>

	<?php /*
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col span12">
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
		<?php endif; ?>
	*/ ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
