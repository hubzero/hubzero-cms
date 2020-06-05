<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<ol class="comments" id="t<?php echo $this->parent; ?>">
<?php
if ($this->comments)
{
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

	foreach ($this->comments as $comment)
	{
		$this->view('_comment')
		     ->set('option', $this->option)
		     ->set('group', $this->group)
		     ->set('comment', $comment)
		     ->set('thread', $this->thread)
		     ->set('config', $this->config)
		     ->set('depth', $this->depth)
		     ->set('cls', $cls)
		     ->set('filters', $this->filters)
		     ->set('category', $this->category)
		     ->display();
	}
}
?>
</ol>