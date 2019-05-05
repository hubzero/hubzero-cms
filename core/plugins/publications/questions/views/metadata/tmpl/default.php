<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<p class="answer">
	<?php if ($this->publication->alias) : ?>
		<a href="<?php echo Route::url($this->publication->link('questions')); ?>">
	<?php else : ?>
		<a href="<?php echo Route::url($this->publication->link('questions')); ?>">
	<?php endif; ?>
		<?php
			if ($this->count == 1)
			{
				echo Lang::txt('PLG_PUBLICATION_QUESTIONS_NUM_QUESTION', $this->count);
			}
			else
			{
				echo Lang::txt('PLG_PUBLICATION_QUESTIONS_NUM_QUESTIONS', $this->count);
			}
		?>
	</a>
	(<a href="<?php echo Route::url($this->publication->link('questions') . '&action=new#ask'); ?>"><?php echo Lang::txt('PLG_PUBLICATION_QUESTIONS_ASK_A_QUESTION'); ?></a>)
</p>
