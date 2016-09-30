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

$activity    = $this->activity;
$a           = $activity['activity'];
$class       = $activity['class'];
$deletable   = empty($this->edit) ? $activity['deletable'] : false;
$etbl        = $activity['etbl'];
$eid         = $activity['eid'];
$ebody       = $activity['body'];
$comments    = $activity['comments'];
$preview     = $activity['preview'];
$showProject = isset($this->showProject) ? $this->showProject : false;
$edit        = isset($this->edit) ? $this->edit : true;

$creator = User::getInstance($a->userid);

$new = false;
if ($this->model->member())
{
	$new = $this->model->member()->lastvisit
		&& $this->model->member()->lastvisit <= $a->recorded
		? true : false;
}

$recorded = $a->recorded;
if ($comments)
{
	foreach ($comments as $comment)
	{
		if ($comment->created > $recorded)
		{
			$recorded = $comment->created;
		}
	}
}
?>
		<div id="li_<?php echo $a->id; ?>" class="activity-item <?php echo $new ? ' newitem' : ''; ?>" data-recorded="<?php echo $recorded; ?>">
			<div id="tr_<?php echo $a->id; ?>" class="item-control">
				<?php if ($deletable) { ?>
					<span class="m_options">
						<span id="mo_<?php echo $a->id; ?>">
							<a class="icon-delete delete" data-confirm="<?php echo Lang::txt('Permanently delete this entry?'); ?>" href="<?php echo Route::url($this->model->link('feed') .  '&action=delete&tbl=' . $etbl . '&eid=' . $eid);  ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
						</span>
					</span>
				<?php } ?>
				<div class="blog-item">
					<?php if ($showProject) { ?>
						<img class="project-image" src="<?php echo Route::url($this->model->link('thumb')); ?>" alt="" />
					<?php } else { ?>
						<img class="blog-author" src="<?php echo $creator->picture($a->admin); ?>" alt="" />
					<?php } ?>
					<div class="blog-content">
						<?php if ($showProject) { ?>
							<span class="project-name">
								<a href="<?php echo Route::url($this->model->link()); ?>"><?php echo \Hubzero\Utility\String::truncate($this->model->get('title'), 65); ?></a>
							</span>
						<?php } ?>
						<span class="actor"><?php echo $a->admin == 1 ? Lang::txt('COM_PROJECTS_ADMIN') : $a->name; ?></span>
						<span class="item-time">&middot; <?php echo \Components\Projects\Helpers\Html::showTime($a->recorded, true); ?></span>
						<?php  if ($edit && $a->commentable) { ?>
							<?php if ($this->model->access('content')) { ?>
								<span class="item-comment">&middot; <a href="#commentform_<?php echo $a->id; ?>" id="addc_<?php echo $a->id; ?>" data-inactive="<?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?>" data-active="<?php echo Lang::txt('COM_PROJECTS_COMMENT_CANCEL'); ?>" class="showc"><?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?></a></span>
							<?php } ?>
						<?php } ?>
						<div class="<?php echo $class; ?> activity <?php if ($a->admin) { echo ' admin-action'; } ?>">
							 <?php echo $a->activity; ?><?php echo stripslashes($ebody); ?>
						</div>
						<?php echo stripslashes($preview); ?>
					</div>
				</div>
			</div>

			<?php
			// Show comments
			$this->view('_comments')
				->set('comments', $comments)
				->set('model', $this->model)
				->set('activity', $a)
				->set('uid', $this->uid)
				->set('edit', $edit)
				->display();

			// Add comment
			if ($edit && $this->model->access('content'))
			{
				$this->view('_addcomment')
					->set('comments', $comments)
					->set('model', $this->model)
					->set('activity', $a)
					->set('uid', $this->uid)
					->set('etbl', $etbl)
					->set('eid', $eid)
					->display();
			}
			?>
		</div>