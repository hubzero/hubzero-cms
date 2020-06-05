<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->model->access('content'))
{
	return;
}
?>
<div id="blab" class="miniblog">
	<form id="blogForm" method="post" class="focused" action="<?php echo Route::url($this->model->link()); ?>">
		<fieldset>
			<?php echo $this->editor('comment', '', 5, 3, 'ca_base', array('class' => 'minimal no-footer')); ?>
			<p id="blog-submitarea">
				<span id="counter_number_blog" class="leftfloat mini"></span>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="view" />
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="active" value="feed" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="parent_activity" value="0" />
				<?php echo Html::input('token'); ?>

				<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SHARE_WITH_TEAM'); ?>" id="blog-submit" class="btn" />
			</p>
		</fieldset>
	</form>
</div>