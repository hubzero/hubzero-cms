<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

$text = ($this->task == 'edit' ? Lang::txt('COM_STOREFRONT_EDIT') : Lang::txt('COM_STOREFRONT_NEW'));

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': ' . Lang::txt('COM_STOREFRONT_OPTION_GROUP') . ': ' . $text, 'storefront');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();
//Toolbar::spacer();
//Toolbar::help('category');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
	<div class="col span7">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_STOREFRONT_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_STOREFRONT_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[ogName]" id="field-title" class="required" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->getName())); ?>" />
			</div>

		</fieldset>
	</div>
	<div class="col span5">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_STOREFRONT_ID'); ?>:</th>
					<td>
						<?php echo $this->row->getId(); ?>
						<input type="hidden" name="fields[ogId]" id="field-id" value="<?php echo $this->escape($this->row->getId()); ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_STOREFRONT_PARAMETERS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_STOREFRONT_PUBLISH'); ?>:</label>
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->getActiveStatus() == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->getActiveStatus() == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
