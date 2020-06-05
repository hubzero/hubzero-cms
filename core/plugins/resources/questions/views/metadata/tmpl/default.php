<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<p class="answer">
	<a href="<?php echo Route::url($this->resource->link() . '&active=questions'); ?>">
	<?php
		if ($this->count == 1)
		{
			echo Lang::txt('PLG_RESOURCES_QUESTIONS_NUM_QUESTION', $this->count);
		}
		else
		{
			echo Lang::txt('PLG_RESOURCES_QUESTIONS_NUM_QUESTIONS', $this->count);
		}
	?>
	</a>
	(<a href="<?php echo Route::url($this->resource->link() . '&active=questions&action=new'); ?>"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION'); ?></a>)
</p>
