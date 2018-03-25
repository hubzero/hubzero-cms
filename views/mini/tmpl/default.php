<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>
<div class="sidebox<?php if (count($this->items) == 0) { echo ' suggestions'; } ?>">
	<h4>
		<a href="<?php echo Route::url($this->model->link('publications')); ?>" class="hlink" title="<?php echo Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . strtolower(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS')); ?></a>
		<?php if (count($this->items) > 0) { ?>
			<span><a href="<?php echo Route::url($this->model->link('publications')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_SEE_ALL')); ?> </a></span>
		<?php } ?>
	</h4>
	<?php if (count($this->items) == 0) { ?>
		<p class="s-publications"><a href="<?php echo Route::url($this->model->link('publications') . '&action=start'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION'); ?></a></p>
	<?php } else { ?>
		<ul>
			<?php foreach ($this->items as $pub) {
				$status = $pub->getStatusName();
			?>
			<li>
				<span class="pub-thumb"><img src="<?php echo Route::url($pub->link('thumb')); ?>" alt=""/></span>
				<span class="pub-details">
					<a href="<?php echo Route::url($pub->link('editversion')); ?>" title="<?php echo $this->escape($pub->get('title')); ?>"><?php echo \Hubzero\Utility\Str::truncate(stripslashes($pub->get('title')), 100); ?></a>
					 <span class="block faded mini">
						<span>v. <?php echo $pub->get('version_label'); ?> (<?php echo $status; ?>)</span>
					</span>
				</span>
			</li>
			<?php } ?>
		</ul>
	<?php } ?>
</div>
