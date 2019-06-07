<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->depth == 0 && $this->config->get('access-edit-thread'))
{
	$stick = $this->base . '&unit=' . $this->unit . '&b=' . $this->lecture . '&thread=' . $this->post->get('thread') . '&action=sticky&sticky=';
	?>
	<div class="sticky-thread-controls<?php echo ($this->post->get('sticky')) ? ' stuck' : ''; ?>" data-thread="<?php echo $this->post->get('thread'); ?>">
		<p>
			<a class="sticky-toggle"
				href="<?php echo Route::url($stick . ($this->post->get('sticky') ? 0 : 1)); ?>"
				data-stick-href="<?php echo Route::url($stick . '1'); ?>"
				data-unstick-href="<?php echo Route::url($stick . '0'); ?>"
				data-stick-txt="<?php echo Lang::txt('Make sticky'); ?>"
				data-unstick-txt="<?php echo Lang::txt('Make not sticky'); ?>">
				<?php echo ($this->post->get('sticky')) ? Lang::txt('Make not sticky') : Lang::txt('Make sticky'); ?>
			</a>
			<span class="hint">
				<?php echo Lang::txt('Sticky discussions are viewable by all sections'); ?>
			</span>
		</p>
	</div>
	<?php
}
?>
<ol class="comments" id="t<?php echo $this->parent; ?>">
	<?php
	if ($this->comments && is_array($this->comments))
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
			$this->view('comment')
			     ->set('option', $this->option)
			     ->set('comment', $comment)
			     ->set('post', $this->post)
			     ->set('unit', $this->unit)
			     ->set('lecture', $this->lecture)
			     ->set('config', $this->config)
			     ->set('depth', $this->depth)
			     ->set('cls', $cls)
			     ->set('base', $this->base)
			     ->set('attach', $this->attach)
			     ->set('search', $this->search)
			     ->set('course', $this->course)
			     ->display();
		}
	}
	?>
</ol>