<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<ol class="comments" id="<?php echo (isset($this->thread) ? $this->thread : 't') . (isset($this->parent) ? $this->parent : '0'); ?>">
	<?php
	if (isset($this->comments))
	{
		$cls = 'odd';
		if (isset($this->cls))
		{
			$cls = ($this->cls == 'odd') ? 'even' : 'odd';
		}

		$this->depth++;

		foreach ($this->comments as $comment)
		{
			$comment->set('qid', $this->question->get('id'));

			$this->view('_comment')
			     ->set('item_id', $this->item_id)
			     ->set('option', $this->option)
			     ->set('comment', $comment)
			     ->set('config', $this->config)
			     ->set('depth', $this->depth)
			     ->set('question', $this->question)
			     ->set('cls', $cls)
			     ->set('base', $this->base)
			     ->display();
		}
	}
	?>
</ol>