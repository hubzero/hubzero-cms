<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_STORY_SUBMIT'); ?></h2>
	<p class="story-submit-intro"><?php echo Lang::txt('COM_STORY_SUBMIT_INTRO'); ?></p>
</header>

<section class="main section">

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&view=submissions'); ?>" method="post" id="submission-form">

		<fieldset>
			<div class="input-wrap">
				<label for="field-subject"><?php echo Lang::txt('COM_STORY_FIELD_SUBJECT'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[subject]" id="field-subject" size="60" maxlength="255" value="<?php echo $this->escape($this->row->get('subject')); ?>" required />
			</div>

			<div class="input-wrap">
				<label for="field-url"><?php echo Lang::txt('COM_STORY_FIELD_URL'); ?></label>
				<input type="url" name="fields[url]" id="field-url" size="60" value="<?php echo $this->escape($this->row->get('url')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_URL_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-body"><?php echo Lang::txt('COM_STORY_FIELD_WHY'); ?></label>
				<textarea name="fields[body]" id="field-body" rows="8" cols="60"><?php echo $this->escape($this->row->get('body')); ?></textarea>
				<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_WHY_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-topic"><?php echo Lang::txt('COM_STORY_FIELD_TOPIC'); ?></label>
				<select name="fields[topic_id]" id="field-topic">
					<option value="0"><?php echo Lang::txt('COM_STORY_FIELD_NONE'); ?></option>
<?php foreach ($this->topics as $topic) { ?>
					<option value="<?php echo (int) $topic->get('id'); ?>"<?php
						echo ($this->row->get('topic_id') == $topic->get('id')) ? ' selected="selected"' : '';
					?>><?php echo $this->escape($topic->get('title')); ?></option>
<?php } ?>
				</select>
			</div>
		</fieldset>

		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_STORY_SEND_IT'); ?>" />
		</p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="view" value="submissions" />
		<input type="hidden" name="task" value="save" />
		<?php echo Html::input('token'); ?>
	</form>

</section>
