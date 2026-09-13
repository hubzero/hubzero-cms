<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt($this->row->isNew() ? 'COM_KARMA_GATE_NEW' : 'COM_KARMA_GATE_EDIT'), 'karma.png');
Toolbar::apply();
Toolbar::save();
Toolbar::cancel();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=gates'); ?>" method="post" name="adminForm" id="item-form" class="main section">

	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_GATE_DETAILS'); ?></legend>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_KARMA_FIELD_GATE_ALIAS'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[alias]" id="field-alias" size="40" value="<?php echo $this->escape($this->row->get('alias')); ?>" required />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_GATE_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_KARMA_FIELD_TITLE'); ?>:</label>
					<input type="text" name="fields[title]" id="field-title" size="40" value="<?php echo $this->escape($this->row->get('title')); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_KARMA_FIELD_DESCRIPTION'); ?>:</label>
					<textarea name="fields[description]" id="field-description" rows="3" cols="50"><?php echo $this->escape($this->row->get('description')); ?></textarea>
				</div>

				<div class="input-wrap">
					<label for="field-scale_id"><?php echo Lang::txt('COM_KARMA_FIELD_SCALE'); ?>:</label>
					<select name="fields[scale_id]" id="field-scale_id">
<?php foreach ($this->scales as $scale) { ?>
						<option value="<?php echo $this->escape($scale->get('id')); ?>"<?php if ($this->row->get('scale_id') == $scale->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($scale->get('title')); ?></option>
<?php } ?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-bands"><?php echo Lang::txt('COM_KARMA_FIELD_BANDS'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[bands]" id="field-bands" size="50" value="<?php echo $this->escape($this->row->get('bands')); ?>" required />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_BANDS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-default_value"><?php echo Lang::txt('COM_KARMA_FIELD_DEFAULT_VALUE'); ?>:</label>
					<input type="text" name="fields[default_value]" id="field-default_value" value="<?php echo $this->escape($this->row->get('default_value')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_DEFAULT_VALUE_HINT'); ?></span>
				</div>
			</fieldset>
		</div>

		<div class="col span5 omega">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_GATE_PREVIEW'); ?></legend>

<?php if ($this->preview) { ?>
				<p class="hint"><?php echo Lang::txt('COM_KARMA_GATE_PREVIEW_INTRO'); ?></p>

				<table class="adminlist">
					<thead>
						<tr>
							<th scope="col"><?php echo Lang::txt('COM_KARMA_PREVIEW_KARMA'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_KARMA_PREVIEW_ANSWER'); ?></th>
						</tr>
					</thead>
					<tbody>
<?php foreach ($this->preview as $line) { ?>
						<tr>
							<td><?php echo $this->escape($line['karma']); ?> <span class="hint"><?php echo $this->escape($line['note']); ?></span></td>
							<td><strong><?php echo $this->escape($line['value']); ?></strong></td>
						</tr>
<?php } ?>
					</tbody>
				</table>
<?php } else { ?>
				<p class="hint"><?php echo Lang::txt('COM_KARMA_GATE_PREVIEW_EMPTY'); ?></p>
<?php } ?>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="gates" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
