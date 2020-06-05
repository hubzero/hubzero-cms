<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;

Toolbar::title(Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY') . ': ' . Arr::getValue($this->config, 'name', ''), 'packages');

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();
if ($canDo->get('core.edit'))
{
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=save'); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<?php if ($this->getError()): ?>
			<div class="col span12">
				<p class="error"><?php echo $this->getError(); ?></p>
			</div>
		<?php else: ?>
			<div class="col span7">
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_INSTALLER_PACKAGES_BASIC_INFO'); ?></span></legend>

					<div class="input-wrap">
						<label for="field-name"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_NAME'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
						<input name="name" id="field-name" type="text" class="required" value="<?php echo $this->escape(Arr::getValue($this->config, 'name', '')); ?>"></input>
					</div>

					<div class="input-wrap">
						<label for="field-alias"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_ALIAS'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
						<input name="alias" id="field-alias" type="text" class="required" value="<?php echo isset($this->alias) ? $this->escape($this->alias) : ''; ?>"></input>
					</div>

					<div class="input-wrap">
						<label for="field-description"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_DESCRIPTION'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
						<input name="description" id="field-description" type="text" class="required" value="<?php echo $this->escape(Arr::getValue($this->config, 'description', '')); ?>"></input>
					</div>

					<div class="input-wrap">
						<label for="field-url"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_URL'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
						<input name="url" id="field-url" type="text" class="required" value="<?php echo $this->escape(Arr::getValue($this->config, 'url', '')); ?>"></input>
					</div>

					<div class="input-wrap">
						<label for="field-type"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_TYPE'); ?></label>
						<select name="type" id="field-type">
							<option value="github" selected="<?php echo Arr::getValue($this->config, 'type', '') == 'github' ? 'true' : ''; ?>">Github</option>
							<option value="gitlab" selected="<?php echo Arr::getValue($this->config, 'type', '') == 'gitlab' ? 'true' : ''; ?>">Gitlab</option>
						</select>
					</div>
				</fieldset>
			</div>
			<div class="col span5">
				<?php if (!$this->isNew): ?>
					<p class="warning">
						<a class="button" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&alias=' . $this->alias . '&task=remove'); ?>"><?php echo Lang::txt('Remove Repository'); ?></a>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<input type="hidden" name="oldAlias" value="<?php echo $this->escape($this->alias); ?>" />
	<input type="hidden" name="isNew" value="<?php echo $this->isNew ? 'true' : 'false'; ?>" />
	<input type="hidden" name="task" value="save" autocomplete="off" />

	<?php echo Html::input('token'); ?>
</form>
