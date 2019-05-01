<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('license');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_LICENSES') . ': ' . $text, 'resources');
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

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_RESOURCES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" class="required" size="35" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT'); ?>">
					<label for="field-name"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[name]" id="field-name" size="35" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" /><br />
					<span class="hint"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-url"><?php echo Lang::txt('COM_RESOURCES_FIELD_URL'); ?>:</label><br />
					<input type="text" name="fields[url]" id="field-url" size="35" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->url)); ?>" /><br />
				</div>

				<div class="input-wrap">
					<label for="field-text"><?php echo Lang::txt('COM_RESOURCES_FIELD_CONTENT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<textarea name="fields[text]" id="field-text" cols="45" rows="15"><?php echo $this->escape(stripslashes($this->row->text)); ?></textarea>
				</div>

				<input type="hidden" name="fields[ordering]" value="<?php echo $this->row->ordering; ?>" />
				<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
		</div>
		<div class="col span4">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_RESOURCES_FIELD_ID'); ?></th>
						<td><?php echo $this->row->id; ?></td>
					</tr>
				<?php if ($this->row->id) { ?>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_RESOURCES_FIELD_ORDERING'); ?></th>
						<td><?php echo $this->row->ordering; ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>