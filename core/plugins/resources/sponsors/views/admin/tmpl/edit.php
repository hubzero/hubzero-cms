<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('PLG_RESOURCES') . ': '.Lang::txt('PLG_RESOURCES_SPONSOR').': ' . $text, 'resources');
Toolbar::save();
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm" class="editform form-validate" data-invalid-msg="<?php echo Lang::txt('PLG_RESOURCES_SPONSORS_MISSING_TITLE'); ?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="title"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="title" class="required" maxlength="100" value="<?php echo $this->escape($this->row->title); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ALIAS_HINT'); ?>">
					<label for="alias"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="alias" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" /><br />
					<span class="hint"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_DESCRIPTION'); ?>:</label></td>
					<?php echo $this->editor('fields[description]', stripslashes($this->row->description), 45, 10, 'field-description', array('class' => 'required minimal', 'buttons' => false)); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ID'); ?></th>
						<td><?php echo $this->row->id; ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_CREATED'); ?></th>
						<td><time datetime="<?php echo $this->row->created; ?>"><?php echo Date::of($this->row->created)->toLocal(); ?></time></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_CREATOR'); ?></th>
						<td><?php echo $this->row->created_by; ?></td>
					</tr>
					<?php if ($this->row->modified) { ?>
						<tr>
							<th scope="row"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_MODIFIED'); ?></th>
							<td><time datetime="<?php echo $this->row->modified; ?>"><?php echo Date::of($this->row->modified)->toLocal(); ?></time></td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_MODIFIER'); ?></th>
							<td><?php echo $this->row->modified_by; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="plugin" value="sponsors" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>