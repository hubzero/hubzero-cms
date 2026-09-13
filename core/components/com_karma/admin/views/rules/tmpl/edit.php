<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt($this->row->isNew() ? 'COM_KARMA_RULE_NEW' : 'COM_KARMA_RULE_EDIT'), 'karma.png');
Toolbar::apply();
Toolbar::save();
Toolbar::cancel();

$emitter = isset($this->declared[$this->row->get('alias')]) ? $this->declared[$this->row->get('alias')] : null;

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=rules'); ?>" method="post" name="adminForm" id="item-form" class="main section">

	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_RULE_DETAILS'); ?></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_KARMA_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" size="40" value="<?php echo $this->escape($this->row->get('title')); ?>" required />
				</div>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_KARMA_FIELD_RULE_ALIAS'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[alias]" id="field-alias" size="40" value="<?php echo $this->escape($this->row->get('alias')); ?>" required />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_RULE_ALIAS_HINT'); ?></span>
<?php if ($this->row->get('alias')) { ?>
	<?php if ($emitter) { ?>
					<p class="hint"><?php echo Lang::txt('COM_KARMA_RULE_EMITTED_BY', $this->escape($emitter)); ?></p>
	<?php } else { ?>
					<p class="warning"><?php echo Lang::txt('COM_KARMA_RULE_NO_SOURCE_LONG'); ?></p>
	<?php } ?>
<?php } ?>
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_KARMA_FIELD_DESCRIPTION'); ?>:</label>
					<textarea name="fields[description]" id="field-description" rows="4" cols="50"><?php echo $this->escape($this->row->get('description')); ?></textarea>
				</div>

				<div class="input-wrap">
					<label for="field-scale_id"><?php echo Lang::txt('COM_KARMA_FIELD_SCALE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<select name="fields[scale_id]" id="field-scale_id" required>
						<option value=""><?php echo Lang::txt('COM_KARMA_FIELD_SCALE_SELECT'); ?></option>
<?php foreach ($this->scales as $scale) { ?>
						<option value="<?php echo $this->escape($scale->get('id')); ?>"<?php if ($this->row->get('scale_id') == $scale->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($scale->get('title')); ?></option>
<?php } ?>
					</select>
				</div>
			</fieldset>
		</div>

		<div class="col span5 omega">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_RULE_WORTH'); ?></legend>

				<div class="input-wrap">
					<label for="field-delta"><?php echo Lang::txt('COM_KARMA_FIELD_DELTA'); ?>:</label>
					<input type="text" name="fields[delta]" id="field-delta" value="<?php echo $this->escape($this->row->get('delta')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_DELTA_HINT'); ?></span>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_RULE_CAPS'); ?></legend>

				<p class="hint"><?php echo Lang::txt('COM_KARMA_RULE_CAPS_INTRO'); ?></p>

				<div class="input-wrap">
					<label for="field-daily_cap"><?php echo Lang::txt('COM_KARMA_FIELD_DAILY_CAP'); ?>:</label>
					<input type="number" name="fields[daily_cap]" id="field-daily_cap" min="0" value="<?php echo $this->escape($this->row->get('daily_cap', 0)); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_CAP_ZERO'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-per_source_cap"><?php echo Lang::txt('COM_KARMA_FIELD_SOURCE_CAP'); ?>:</label>
					<input type="number" name="fields[per_source_cap]" id="field-per_source_cap" min="0" value="<?php echo $this->escape($this->row->get('per_source_cap', 0)); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_CAP_ZERO'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-requires_karma"><?php echo Lang::txt('COM_KARMA_FIELD_REQUIRES_KARMA'); ?>:</label>
					<input type="number" name="fields[requires_karma]" id="field-requires_karma" value="<?php echo $this->escape($this->row->get('requires_karma', 0)); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_REQUIRES_KARMA_HINT'); ?></span>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_KARMA_SCALE_PUBLISHING'); ?></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_KARMA_FIELD_STATE'); ?>:</label>
					<select name="fields[state]" id="field-state">
						<option value="1"<?php if ($this->row->get('state', 1)) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="0"<?php if (!$this->row->get('state', 1)) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					</select>
					<span class="hint"><?php echo Lang::txt('COM_KARMA_FIELD_RULE_STATE_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="rules" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
