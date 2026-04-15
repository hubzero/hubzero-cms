<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Only emit <ol> when there are comments (ARIA: list requires listitem children)
$hasComments = false;
if (!empty($this->comments))
{
	if (is_countable($this->comments))
	{
		$hasComments = count($this->comments) > 0;
	}
	elseif ($this->comments instanceof \Traversable)
	{
		foreach ($this->comments as $_c) { $hasComments = true; break; }
	}
}
if ($hasComments):
	$cls = 'odd';
	if (isset($this->cls))
	{
		$cls = ($this->cls == 'odd') ? 'even' : 'odd';
	}
	$this->depth++;
?>
<ol class="comments" id="t<?php echo isset($this->parent) ? $this->parent : '0'; ?>">
	<?php foreach ($this->comments as $comment): ?>
	<?php
		$comment->set('page_id', $this->page->get('id'));
		$this->view('_comment', 'comments')
			->set('option', $this->option)
			->set('comment', $comment)
			->set('config', $this->config)
			->set('depth', $this->depth)
			->set('page', $this->page)
			->set('version', $this->version)
			->set('cls', $cls)
			->display();
	?>
	<?php endforeach; ?>
</ol>
<?php endif; ?>
