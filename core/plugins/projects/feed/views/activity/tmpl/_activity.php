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

$online = false;
if (isset($this->online) && in_array($a->userid, $this->online))
{
	$online = true;
}
?>
		<div id="li_<?php echo $a->id; ?>" class="activity <?php echo $new ? ' newitem' : ''; ?>" data-recorded="<?php echo $recorded; ?>">

			<div class="activity-actor-picture<?php if ($online) { echo ' tooltips" title="' . Lang::txt('PLG_PROJECTS_FEED_ONLINE'); } ?>">
				<?php if ($showProject) { ?>
					<span class="user-img-wrap">
						<img class="project-image" src="<?php echo Route::url($this->model->link('thumb')); ?>" alt="" />
						<?php if ($online) { ?>
							<span class="online"><?php echo Lang::txt('PLG_PROJECTS_FEED_ONLINE'); ?></span>
						<?php } ?>
					</span>
				<?php } else { ?>
					<a class="user-img-wrap" href="<?php echo Route::url($creator->link()); ?>">
						<img class="blog-author" src="<?php echo $creator->picture($a->admin); ?>" alt="" />
						<?php if ($online) { ?>
							<span class="online"><?php echo Lang::txt('PLG_PROJECTS_FEED_ONLINE'); ?></span>
						<?php } ?>
					</a>
				<?php } ?>
			</div><!-- / .activity-actor-picture -->

			<div class="activity-content">
				<div class="activity-body">
					<div class="activity-details">
						<?php if ($showProject) { ?>
							<span class="project-name">
								<a href="<?php echo Route::url($this->model->link()); ?>"><?php echo \Hubzero\Utility\String::truncate($this->model->get('title'), 65); ?></a>
							</span>
						<?php } ?>
						<span class="activity-actor"><?php echo $a->admin == 1 ? Lang::txt('COM_PROJECTS_ADMIN') : $a->name; ?></span>
						<span class="activity-time"><time datetime="<?php echo Date::of($a->recorded)->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo \Components\Projects\Helpers\Html::showTime($a->recorded, true); ?></time></span>
					</div><!-- / .activity-details -->

					<div class="activity-event">
						<div class="activity-event-content <?php echo $class; if ($a->admin) { echo ' admin-action'; } ?>">
							<?php echo $a->activity; ?>
							<div id="activity-content<?php echo $a->id; ?>">
								<?php echo stripslashes($ebody); ?>
							</div>
						</div>
						<?php echo stripslashes($preview); ?>
					</div><!-- / .activity-event -->

					<?php if ($a->commentable || $deletable || $edit) { ?>
						<div class="activity-options">
							<ul>
								<?php if ($edit && $a->commentable) { ?>
									<?php if ($this->model->access('content')) { ?>
										<li>
											<a class="icon-reply reply tooltips" href="#commentform_<?php echo $a->id; ?>" id="addc_<?php echo $a->id; ?>" title="<?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?>" data-inactive="<?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?>" data-active="<?php echo Lang::txt('COM_PROJECTS_COMMENT_CANCEL'); ?>"><!--
												--><?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?><!--
											--></a>
										</li>
									<?php } ?>
								<?php } ?>
								<?php if ($edit && in_array($class, array('blog', 'quote')) && $this->model->access('manager')) { ?>
									<li id="mo_<?php echo $a->id; ?>">
										<a class="icon-edit edit tooltips" data-form="activity-form<?php echo $a->id; ?>" data-content="activity-content<?php echo $a->id; ?>" href="<?php echo Route::url($this->model->link('feed') .  '&action=edit&tbl=' . $etbl . '&eid=' . $eid);  ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>" data-inactive="<?php echo Lang::txt('JACTION_EDIT'); ?>" data-active="<?php echo Lang::txt('COM_PROJECTS_COMMENT_CANCEL'); ?>"><!--
											--><?php echo Lang::txt('JACTION_EDIT'); ?><!--
										--></a>
									</li>
								<?php } ?>
								<?php if ($deletable) { ?>
									<li id="mo_<?php echo $a->id; ?>">
										<a class="icon-delete delete tooltips" data-confirm="<?php echo Lang::txt('PLG_PROJECTS_BLOG_DELETE_CONFIRMATION'); ?>" href="<?php echo Route::url($this->model->link('feed') .  '&action=delete&tbl=' . $etbl . '&eid=' . $eid);  ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>"><!--
											--><?php echo Lang::txt('JACTION_DELETE'); ?><!--
										--></a>
									</li>
								<?php } ?>
							</ul>
						</div><!-- / .activity-options -->
						<?php if ($edit && in_array($class, array('blog', 'quote')) && $this->model->access('manager')) { ?>
							<div class="commentform editcomment hidden" id="activity-form<?php echo $a->id; ?>">
								<form method="post" action="<?php echo Route::url($this->model->link()); ?>">
									<fieldset>
										<input type="hidden" name="task" value="view" />
										<input type="hidden" name="active" value="feed" />
										<input type="hidden" name="action" value="save" />
										<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
										<input type="hidden" name="eid" value="<?php echo $eid; ?>" />
										<?php echo Html::input('token'); ?>

										<?php echo $this->editor('blogentry', $activity['raw'], 5, 5, 'blogentry' . $a->id, array('class' => 'minimal no-footer')); ?>

										<p class="blog-submit">
											<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SAVE'); ?>" class="btn c-submit" />
										</p>
									</fieldset>
								</form>
							</div>
						<?php } ?>
					<?php } ?>
				</div><!-- / .activity-body -->

				<?php
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
			</div><!-- / .activity-content -->

			<?php
			// Show comments
			$this->view('_comments')
				->set('comments', $comments)
				->set('model', $this->model)
				->set('activity', $a)
				->set('uid', $this->uid)
				->set('edit', $edit)
				->set('online', $this->online)
				->display();
			?>

		</div><!-- / .activity -->
