<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt($this->row->isNew() ? 'COM_STORY_SECTION_NEW' : 'COM_STORY_SECTION_EDIT'), 'story.png');
Toolbar::apply();
Toolbar::save();
Toolbar::cancel();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=sections'); ?>" method="post" name="adminForm" id="item-form" class="main section">

	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_STORY_SECTION_DETAILS'); ?></legend>

		<div class="input-wrap">
			<label for="field-title"><?php echo Lang::txt('COM_STORY_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
			<input type="text" name="fields[title]" id="field-title" size="50" value="<?php echo $this->escape($this->row->get('title')); ?>" required />
		</div>

		<div class="input-wrap">
			<label for="field-alias"><?php echo Lang::txt('COM_STORY_FIELD_ALIAS'); ?>:</label>
			<input type="text" name="fields[alias]" id="field-alias" size="50" value="<?php echo $this->escape($this->row->get('alias')); ?>" />
			<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_ALIAS_HINT'); ?></span>
		</div>

		<div class="input-wrap">
			<label for="field-description"><?php echo Lang::txt('COM_STORY_FIELD_DESCRIPTION'); ?>:</label>
			<textarea name="fields[description]" id="field-description" rows="4" cols="50"><?php echo $this->escape($this->row->get('description')); ?></textarea>
		</div>

		<div class="input-wrap">
			<label for="field-ordering"><?php echo Lang::txt('COM_STORY_COL_ORDERING'); ?>:</label>
			<input type="number" name="fields[ordering]" id="field-ordering" value="<?php echo (int) $this->row->get('ordering'); ?>" />
		</div>

		<div class="input-wrap">
			<label for="field-state"><?php echo Lang::txt('COM_STORY_COL_STATE'); ?>:</label>
			<select name="fields[state]" id="field-state">
				<option value="1"<?php if ($this->row->get('state', 1)) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
				<option value="0"<?php if (!$this->row->get('state', 1)) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
			</select>
		</div>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_STORY_SECTION_COMMENTING'); ?></legend>

		<div class="input-wrap">
			<label for="field-allow-anonymous"><?php echo Lang::txt('COM_STORY_FIELD_ALLOW_ANONYMOUS'); ?>:</label>
			<select name="params[allow_anonymous]" id="field-allow-anonymous">
				<option value="0"<?php if (!$this->row->params->get('allow_anonymous', 0)) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JNO'); ?></option>
				<option value="1"<?php if ($this->row->params->get('allow_anonymous', 0)) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JYES'); ?></option>
			</select>
			<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_ALLOW_ANONYMOUS_HINT'); ?></span>
		</div>
	</fieldset>

	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="sections" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
