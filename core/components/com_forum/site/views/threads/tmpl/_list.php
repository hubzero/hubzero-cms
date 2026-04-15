<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$hash_map = array();

foreach ($this->likes as $like)
{
	$postId = $like->postId;

	if (isset($hash_map[$postId])) 
	{
		$hash_map[$postId][] = $like;
	} 
	else 
	{
		$hash_map[$postId] = array($like);
	}
}


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
	if (!isset($this->search))
	{
		$this->search = '';
	}
	$this->depth++;
?>
<ol class="comments" id="t<?php echo $this->parent; ?>">
<?php foreach ($this->comments as $comment): ?>
<?php
	$postId = $comment->get('id');
	$likesByPostId = isset($hash_map[$postId]) ? $hash_map[$postId] : [];
	$this->view('_comment')
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('comment', $comment)
	     ->set('like', $likesByPostId)
	     ->set('likes', $this->likes)
	     ->set('thread', $this->thread)
	     ->set('config', $this->config)
	     ->set('depth', $this->depth)
	     ->set('cls', $cls)
	     ->set('filters', $this->filters)
	     ->set('category', $this->category)
	     ->display();
?>
<?php endforeach; ?>
</ol>
<?php endif; ?>

