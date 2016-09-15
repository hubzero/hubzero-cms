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

if (!$this->model->access('content'))
{
	return;
}
?>
<div id="blab" class="miniblog">
	<form id="blogForm" method="post" class="focused" action="<?php echo Route::url($this->model->link()); ?>">
		<fieldset>
			<!-- <textarea name="blogentry" cols="5" rows="5" id="blogentry" placeholder="<?php echo Lang::txt('Got an update?'); ?>"></textarea> -->
			<?php echo $this->editor('blogentry', '', 5, 3, 'blogentry', array('class' => 'minimal no-footer')); ?>
			<p id="blog-submitarea">
				<span id="counter_number_blog" class="leftfloat mini"></span>
				<input type="hidden" name="task" value="view" />
				<input type="hidden" name="active" value="feed" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="managers_only" value="0" />
				<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SHARE_WITH_TEAM'); ?>" id="blog-submit" class="btn" />
			</p>
		</fieldset>
	</form>
</div>