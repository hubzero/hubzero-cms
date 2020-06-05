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

// Setup default values
if (empty($this->meta->eulaRequired))
{
	$this->meta->eulaRequired = 0;
}

if (empty($this->meta->eula))
{
	$this->meta->eula = '';
}

if (empty($this->meta->globalDownloadLimit))
{
	$this->meta->globalDownloadLimit = '';
}

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
	<div class="col span7">
		<fieldset class="adminform">
			<legend><span><?php echo 'Software download options'; ?></span></legend>

			<div class="input-wrap">
				<label for="eulaRequired"><?php echo 'Is EULA Required?'; ?>:</label>
				<select name="fields[eulaRequired]" id="eulaRequired">
					<option value="0"<?php if ($this->meta->eulaRequired == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
					<option value="1"<?php if ($this->meta->eulaRequired == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="eula"><?php echo 'EULA (if required, can be overridden on a SKU level)' ?>: </label><br />
				<?php echo $this->editor('fields[eula]', $this->escape(stripslashes($this->meta->eula)), 50, 10, 'eula', array('buttons' => false)); ?>
			</div>

			<div class="input-wrap">
				<label for="field-globalDownloadLimit"><?php echo 'Total Downloads Limit'; ?>:</label><br />
				<input type="text" name="fields[globalDownloadLimit]" id="field-globalDownloadLimit" size="30" maxlength="100" value="<?php echo $this->meta->globalDownloadLimit; ?>" />
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
