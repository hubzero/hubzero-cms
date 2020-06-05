<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die(); ?>

<form id="<?php echo ($this->params->get('moduleclass_sfx')) ? $this->params->get('moduleclass_sfx') : 'poll' . rand(); ?>" method="post" action="<?php echo Route::url('index.php?option=com_poll'); ?>">
	<fieldset>
		<legend><?php echo $this->escape($poll->title); ?></legend>
		<ul class="poll">
			<?php foreach ($poll->options()->where('text', '!=', '')->order('id', 'asc')->rows() as $option) : ?>
				<li class="poll-option <?php echo $this->params->get('moduleclass_sfx'); ?>">
					<input type="radio" name="voteid" id="voteid<?php echo $option->id; ?>" value="<?php echo $this->escape($option->id); ?>" />
					<label for="voteid<?php echo $option->id; ?>" class="poll-option-text <?php echo $this->params->get('moduleclass_sfx'); ?>">
						<?php echo $this->escape(str_replace('&#039;', "'", $option->text)); ?>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<p>
		<input type="submit" name="task_button" class="button" value="<?php echo Lang::txt('MOD_POLL_VOTE'); ?>" />
		&nbsp;
		<a href="<?php echo Route::url('index.php?option=com_poll&view=poll&id=' . $this->escape($poll->id . ':' . $poll->alias)); ?>"><?php echo Lang::txt('MOD_POLL_RESULTS'); ?></a>
	</p>
	<input type="hidden" name="option" value="com_poll" />
	<input type="hidden" name="task" value="vote" />
	<input type="hidden" name="id" value="<?php echo $this->escape($poll->id); ?>" />
	<?php echo Html::input('token'); ?>
</form>