<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS') . ': ' . $text, 'tools');
Toolbar::apply();
Toolbar::save();
Toolbar::spacer();
Toolbar::cancel();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_TOOLS_FIELD_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_TOOLS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-path"><?php echo Lang::txt('COM_TOOLS_FIELD_UUID'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[path]" id="field-path" value="<?php echo $this->escape(stripslashes($this->row->get('path'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-introtext"><?php echo Lang::txt('COM_TOOLS_FIELD_DESCRIPTION'); ?>:</label>
					<textarea name="fields[introtext]" id="field-introtext" cols="35" rows="5"><?php echo $this->escape($this->row->get('introtext')); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TOOLS_FIELD_ID'); ?></th>
						<td><?php echo $this->row->get('id', 0); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TOOLS_FIELD_STATUS'); ?></th>
						<td><?php echo $this->row->get('status'); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="fields[type]" value="<?php echo $this->row->get('type'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo Html::input('token'); ?>
</form>