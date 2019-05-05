<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<ol class="comments" id="t<?php echo isset($this->parent) ? $this->parent : '0'; ?>">
<?php
if ($this->comments)
{
	$cls = 'odd';
	if (isset($this->cls))
	{
		$cls = ($this->cls == 'odd') ? 'even' : 'odd';
	}

	$this->depth++;

	foreach ($this->comments as $comment)
	{
		$comment->set('entry_id', $this->article->get('id'));

		$this->view('_comment')
		     ->set('option', $this->option)
		     ->set('comment', $comment)
		     ->set('depth', $this->depth)
		     ->set('article', $this->article)
		     ->set('cls', $cls)
		     ->set('base', $this->base)
		     ->display();
	}
}
?>
</ol>