<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option . '&view=comments&discussion=' . $this->discussion->get('id');

?>
<header id="content-header">
	<h2><?php echo Lang::txt($this->row->isNew() ? 'COM_STORY_ADD_COMMENT' : 'COM_STORY_EDIT_COMMENT'); ?></h2>
	<p class="story-back"><a href="<?php echo Route::url($base); ?>"><?php echo $this->escape($this->discussion->get('title')); ?></a></p>
</header>

<section class="main section">

	<form action="<?php echo Route::url($base); ?>" method="post" id="comment-form" class="story-comment-form">

		<fieldset>
			<div class="grid">
				<div class="col span12">
					<label for="field-subject"><?php echo Lang::txt('COM_STORY_FIELD_SUBJECT'); ?>
						<input type="text" name="fields[subject]" id="field-subject" maxlength="255" value="<?php echo $this->escape($this->row->get('subject')); ?>" />
					</label>
				</div>
			</div>
			<div class="grid">
				<div class="col span12">
					<label for="field-comment"><?php echo Lang::txt('COM_STORY_FIELD_COMMENT'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
						<textarea name="fields[comment]" id="field-comment" rows="12" cols="60"><?php echo $this->escape($this->row->get('comment')); ?></textarea>
					</label>
				</div>
			</div>
<?php if ($this->anonymous && $this->row->isNew()) { ?>
			<div class="grid">
				<div class="col span12">
					<label for="field-anonymous" class="checkbox">
						<input type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" />
						<?php echo Lang::txt('COM_STORY_FIELD_ANONYMOUS'); ?>
					</label>
					<span class="hint"><?php echo Lang::txt('COM_STORY_FIELD_ANONYMOUS_HINT'); ?></span>
				</div>
			</div>
<?php } ?>
		</fieldset>

		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_STORY_POST'); ?>" />
			<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>"><?php echo Lang::txt('JCANCEL'); ?></a>
		</p>

		<input type="hidden" name="fields[id]" value="<?php echo (int) $this->row->get('id'); ?>" />
		<input type="hidden" name="fields[discussion_id]" value="<?php echo (int) $this->discussion->get('id'); ?>" />
		<input type="hidden" name="fields[parent]" value="<?php echo (int) $this->row->get('parent'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="view" value="comments" />
		<input type="hidden" name="task" value="save" />
		<?php echo Html::input('token'); ?>
	</form>

</section>
