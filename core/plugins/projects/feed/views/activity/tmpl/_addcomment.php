<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Add Comment
if ($this->model->access('content')) { ?>
<div class="commentform addcomment hidden" id="commentform_<?php echo $this->activity->log->get('id'); ?>">
	<form action="<?php echo Route::url($this->model->link('feed')); ?>" method="post">
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="active" value="feed" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="parent_activity" value="<?php echo $this->activity->log->get('id'); ?>" />
			<?php echo Html::input('token'); ?>

			<label class="comment-show">
				<?php echo $this->editor('comment', '', 5, 3, 'ca_' . $this->activity->log->get('id'), array('class' => 'commentarea minimal no-footer')); ?>
			</label>
			<p class="blog-submit"><input type="submit" class="btn c-submit" id="cs_<?php echo $this->activity->log->get('id'); ?>" value="<?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?>" /></p>
		</fieldset>
	</form>
</div>
<?php }
