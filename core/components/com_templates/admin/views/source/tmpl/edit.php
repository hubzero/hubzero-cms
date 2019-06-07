<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Request::setVar('hidemainmenu', true);

$canDo = Components\Templates\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_EDIT_FILE'), 'thememanager');

// Can save the item.
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}

Toolbar::cancel();
Toolbar::divider();
Toolbar::help('source');

Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$editor = Hubzero\Html\Editor::getInstance();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_FILENAME', $this->file->get('name'), $this->file->template()->element); ?></legend>

		<div class="input-wrap">
			<label for="field-source"><?php echo Lang::txt('COM_TEMPLATES_FIELD_SOURCE_LABEL'); ?></label>
			<div class="editor-border">
				<textarea name="fields[source]" id="field-source" rows="40" cols="80"><?php echo $this->escape($this->file->source()); ?></textarea>
			</div>
		</div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>
	</fieldset>

	<input type="hidden" name="fields[extension_id]" id="field-extension_id" value="<?php echo $this->escape($this->file->get('extension_id')); ?>" />
	<input type="hidden" name="fields[filename]" id="field-filename" value="<?php echo $this->escape($this->file->get('name')); ?>" />
</form>
