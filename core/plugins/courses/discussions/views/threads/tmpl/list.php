<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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