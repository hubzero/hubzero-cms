<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<?php
if (isset($this->comments) && count($this->comments))
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
<?php
	foreach ($this->comments as $comment)
	{
		$this->view('_comment')
		     ->set('option', $this->option)
		     ->set('comment', $comment)
		     ->set('config', $this->config)
		     ->set('depth', $this->depth)
		     ->set('cls', $cls)
		     ->set('base', $this->base)
		     ->display();
	}
?>
</ol>
<?php
}
?>
