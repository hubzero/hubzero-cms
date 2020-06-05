<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<ul class="comments" id="comments_<?php echo $this->activity->id; ?>">
	<?php
	if ($this->comments && count($this->comments) > 0)
	{
		// Show Comments
		foreach ($this->comments as $comment)
		{
			// Show comments
			$this->view('_comment')
				->set('comment', $comment)
				->set('model', $this->model)
				->set('activity', $this->activity)
				->set('uid', $this->uid)
				->set('edit', $this->edit)
				->set('online', $this->online)
				->display();
		}
	}
	?>
</ul>
