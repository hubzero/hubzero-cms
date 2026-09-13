<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Models\Story;

$canDo = \Components\Story\Helpers\Permissions::getActions('component');

$states = array(
	Story::STATE_DRAFT     => Lang::txt('COM_STORY_STATE_DRAFT'),
	Story::STATE_QUEUED    => Lang::txt('COM_STORY_STATE_QUEUED'),
	Story::STATE_PUBLISHED => Lang::txt('COM_STORY_STATE_PUBLISHED'),
	Story::STATE_ARCHIVED  => Lang::txt('COM_STORY_STATE_ARCHIVED'),
	Story::STATE_TRASHED   => Lang::txt('COM_STORY_STATE_TRASHED')
);

Toolbar::title(Lang::txt($this->row->isNew() ? 'COM_STORY_NEW' : 'COM_STORY_EDIT'), 'story.png');
Toolbar::apply();
Toolbar::save();
Toolbar::cancel();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=stories'); ?>" method="post" name="adminForm" id="item-form" class="main section">

	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_STORY_THE_STORY'); ?></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_STORY_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" size="60" value="<?php echo $this->escape($this->row->get('title')); ?>" required />
				</div>

				<div class="input-wrap">
					<label for="field-kicker"><?php echo Lang::txt('COM_STORY_FIELD_KICKER'); ?>:</label>
					<input type="text" name="fields[kicker]" id="field-kicker" size="60" value="<?php echo $this->escape($this->row->get('kicker')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_KICKER_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_STORY_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="field-alias" size="60" value="<?php echo $this->escape($this->row->get('alias')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-intro"><?php echo Lang::txt('COM_STORY_FIELD_INTRO'); ?>:</label>
					<?php echo $this->editor('text[intro]', $this->text->get('intro'), 60, 8, 'field-intro'); ?>
					<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_INTRO_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-body"><?php echo Lang::txt('COM_STORY_FIELD_BODY'); ?>:</label>
					<?php echo $this->editor('text[body]', $this->text->get('body'), 60, 16, 'field-body'); ?>
				</div>

				<div class="input-wrap">
					<label for="field-related"><?php echo Lang::txt('COM_STORY_FIELD_RELATED'); ?>:</label>
					<textarea name="text[related]" id="field-related" rows="4" cols="60"><?php echo $this->escape($this->text->get('related')); ?></textarea>
				</div>
			</fieldset>
		</div>

		<div class="col span4 omega">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_STORY_FILING'); ?></legend>

				<div class="input-wrap">
					<label for="field-section_id"><?php echo Lang::txt('COM_STORY_FIELD_SECTION'); ?>:</label>
					<select name="fields[section_id]" id="field-section_id">
						<option value="0"><?php echo Lang::txt('COM_STORY_FIELD_NONE'); ?></option>
<?php foreach ($this->sections as $section) { ?>
						<option value="<?php echo $section->get('id'); ?>"<?php if ($this->row->get('section_id') == $section->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($section->get('title')); ?></option>
<?php } ?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-topic_id"><?php echo Lang::txt('COM_STORY_FIELD_TOPIC'); ?>:</label>
					<select name="fields[topic_id]" id="field-topic_id">
						<option value="0"><?php echo Lang::txt('COM_STORY_FIELD_NONE'); ?></option>
<?php foreach ($this->topics as $topic) { ?>
						<option value="<?php echo $topic->get('id'); ?>"<?php if ($this->row->get('topic_id') == $topic->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($topic->get('title')); ?></option>
<?php } ?>
					</select>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_STORY_PUBLISHING'); ?></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_STORY_COL_STATE'); ?>:</label>
					<select name="fields[state]" id="field-state">
<?php foreach ($states as $value => $label) { ?>
						<option value="<?php echo $value; ?>"<?php if ((int) $this->row->get('state') === (int) $value) { echo ' selected="selected"'; } ?>><?php echo $this->escape($label); ?></option>
<?php } ?>
					</select>
<?php if (!$canDo->get('story.publish')) { ?>
					<span class="hint"><?php echo Lang::txt('COM_STORY_CANNOT_PUBLISH_HINT'); ?></span>
<?php } ?>
				</div>

				<div class="input-wrap">
					<label for="field-publish_up"><?php echo Lang::txt('COM_STORY_FIELD_PUBLISH_UP'); ?>:</label>
					<input type="text" name="fields[publish_up]" id="field-publish_up" value="<?php echo $this->escape($this->row->get('publish_up')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_PUBLISH_UP_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-publish_down"><?php echo Lang::txt('COM_STORY_FIELD_PUBLISH_DOWN'); ?>:</label>
					<input type="text" name="fields[publish_down]" id="field-publish_down" value="<?php echo $this->escape($this->row->get('publish_down')); ?>" />
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="stories" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
