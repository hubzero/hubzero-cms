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

$a = $this->activity;

// Add Comment
if ($a->commentable && $this->model->access('content')) { ?>
<div class="addcomment hidden" id="commentform_<?php echo $a->id; ?>">
	<form action="<?php echo Route::url($this->model->link('feed')); ?>" method="post">
		<fieldset>
			<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="action" value="savecomment" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="active" value="feed" />
			<input type="hidden" name="itemid" value="<?php echo $this->eid; ?>" />
			<input type="hidden" name="tbl" value="<?php echo $this->etbl; ?>" />
			<input type="hidden" name="parent_activity" value="<?php echo $a->id; ?>" />
			<img class="comment-author" src="<?php echo User::getInstance($this->uid)->picture(); ?>" alt="" />
			<label class="comment-show">
				<!-- <textarea name="comment" rows="4" cols="50" class="commentarea" placeholder="<?php echo Lang::txt('PLG_PROJECTS_BLOG_ENTER_COMMENT'); ?>" id="ca_<?php echo $a->id; ?>"></textarea> -->
				<?php echo $this->editor('comment', '', 5, 3, 'ca_' . $a->id, array('class' => 'commentarea minimal no-footer')); ?>
			</label>
			<p class="blog-submit"><input type="submit" class="btn c-submit" id="cs_<?php echo $a->id; ?>" value="<?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?>" /></p>
		</fieldset>
	</form>
</div>
<?php } ?>