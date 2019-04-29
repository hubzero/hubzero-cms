<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('PLG_RESOURCES') . ': '.Lang::txt('PLG_RESOURCES_SPONSOR').': ' . $text, 'resources');
Toolbar::save();
Toolbar::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	<?php echo $this->editor()->save('field-description'); ?>

	// form field validation
	if ($('#title').val() == '') {
		alert( '<?php echo Lang::txt('PLG_RESOURCES_SPONSORS_MISSING_TITLE'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="title"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="title" size="30" maxlength="100" value="<?php echo $this->escape($this->row->title); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ALIAS_HINT'); ?>">
					<label for="alias"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="alias" size="30" maxlength="100" value="<?php echo $this->escape($this->row->alias); ?>" /><br />
					<span class="hint"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_DESCRIPTION'); ?>:</label></td>
					<?php echo $this->editor('fields[description]', stripslashes($this->row->description), 45, 10, 'field-description'); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_ID'); ?></th>
						<td><?php echo $this->row->id; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_CREATED'); ?></th>
						<td><?php echo $this->row->created; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_CREATOR'); ?></th>
						<td><?php echo $this->row->created_by; ?></td>
					</tr>
					<?php if ($this->row->modified) { ?>
						<tr>
							<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_MODIFIED'); ?></th>
							<td><?php echo $this->row->modified; ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_FIELD_MODIFIER'); ?></th>
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