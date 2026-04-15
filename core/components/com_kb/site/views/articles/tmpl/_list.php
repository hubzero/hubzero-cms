<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<?php if ($this->comments && count($this->comments) > 0): ?>
<ol class="comments" id="t<?php echo isset($this->parent) ? $this->parent : '0'; ?>">
<?php
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
</ol>
<?php endif; ?>
