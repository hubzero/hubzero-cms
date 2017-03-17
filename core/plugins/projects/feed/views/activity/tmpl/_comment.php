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

$comment = $this->comment;
$newComment = false;
if ($this->model->member())
{
	$newComment = $this->model->member()->lastvisit && $this->model->member()->lastvisit <= $comment->created
		? true : false;
}

// Is user allowed to delete item?
$deletable = ($comment->created_by == $this->uid or $this->model->member()->role == 1) ? 1 : 0;

$longComment = stripslashes($comment->comment);
$longComment = str_replace('<!-- {FORMAT:HTML} -->', '', $longComment);

$shorten = (strlen($longComment) > 250) ? 1 : 0;
$shortComment = $shorten
	? \Hubzero\Utility\String::truncate($longComment, 250, array('html' => true)) : $longComment;

$longComment  = \Components\Projects\Helpers\Html::replaceUrls($longComment, 'external');
$shortComment = \Components\Projects\Helpers\Html::replaceUrls($shortComment, 'external');

// Emotions (new)
$longComment  = \Components\Projects\Helpers\Html::replaceEmoIcons($longComment);
$shortComment = \Components\Projects\Helpers\Html::replaceEmoIcons($shortComment);

$creator = User::getInstance($comment->created_by);

$online = false;
if (isset($this->online) && in_array($comment->created_by, $this->online))
{
	$online = true;
}
?>
	<li class="activity <?php echo $newComment ? ' newitem' : ''; ?>" id="c_<?php echo $comment->id; ?>">

		<div class="activity-actor-picture<?php if ($online) { echo ' tooltips" title="' . Lang::txt('PLG_PROJECTS_FEED_ONLINE'); } ?>">
			<span class="user-img-wrap">
				<img class="comment-author" src="<?php echo $creator->picture($comment->admin); ?>" alt="" />
				<?php if ($online) { ?>
					<span class="online"><?php echo Lang::txt('PLG_PROJECTS_FEED_ONLINE'); ?></span>
				<?php } ?>
			</span>
		</div><!-- / .activity-actor-picture -->

		<div class="activity-content">
			<div class="activity-body">

				<div class="activity-details">
					<span class="activity-actor"><?php echo $comment->admin == 1 ? Lang::txt('COM_PROJECTS_ADMIN') : $comment->author; ?></span>
					<span class="activity-time"><time datetime="<?php echo Date::of($comment->created)->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo \Components\Projects\Helpers\Html::showTime($comment->created, true); ?></time></span>
				</div><!-- / .activity-details -->

				<div class="activity-event">
					<div class="activity-event-content" id="activity-event-content<?php echo $comment->id; ?>">
						<?php
						echo '<div class="body">' . $shortComment;
						if ($shorten)
						{
							echo ' <a href="#fullbodyc' . $comment->id . '" class="more-content">' . Lang::txt('COM_PROJECTS_MORE') . '</a>';
						}
						echo '</div>';
						if ($shorten)
						{
							$fragment = ltrim(Hubzero\Utility\Uri::getInstance()->toString(['fragment']), '#');
							$cls = ($fragment == 'fullbodyc' . $comment->id ? '' : ' hidden');

							echo '<div class="fullbody' . $cls . '" id="fullbodyc' . $comment->id . '">' . $longComment . '</div>' ;
						}
						?>
					</div>
				</div>

				<?php if ($this->edit && $deletable && $this->model->access('content')) { ?>
					<div class="activity-options">
						<ul>
							<?php if ($this->edit && $this->model->access('manager')) { ?>
								<li id="pu_<?php echo $comment->id; ?>_edit">
									<a class="icon-edit edit tooltips" data-form="comment-form<?php echo $comment->id; ?>" data-content="activity-event-content<?php echo $comment->id; ?>" href="<?php echo Route::url($this->model->link('feed') .'&action=editcomment&cid=' . $comment->id);  ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>"><!--
										--><?php echo Lang::txt('JACTION_EDIT'); ?><!--
									--></a>
								</li>
							<?php } ?>
							<?php if ($deletable) { ?>
								<li id="pu_<?php echo $comment->id; ?>_delete">
									<a class="icon-delete delete tooltips" data-confirm="<?php echo Lang::txt('PLG_PROJECTS_BLOG_DELETE_CONFIRMATION'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>" href="<?php echo Route::url($this->model->link('feed') .'&action=deletecomment&cid=' . $comment->id); ?>"><!--
										--><?php echo Lang::txt('JACTION_DELETE'); ?><!--
									--></a>
								</li>
							<?php } ?>
						</ul>
					</div><!-- / .activity-options -->
				<?php } ?>

				<?php if ($this->edit && $this->model->access('manager')) { ?>
					<div class="commentform editcomment hidden" id="comment-form<?php echo $comment->id; ?>">
						<form method="post" action="<?php echo Route::url($this->model->link()); ?>">
							<fieldset>
								<input type="hidden" name="task" value="view" />
								<input type="hidden" name="active" value="feed" />
								<input type="hidden" name="action" value="savecomment" />
								<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
								<input type="hidden" name="parent_activity" value="<?php echo $this->activity->id; ?>" />
								<input type="hidden" name="cid" value="<?php echo $comment->id; ?>" />
								<?php echo Html::input('token'); ?>

								<?php echo $this->editor('comment', $comment->comment, 5, 5, 'comment' . $comment->id, array('class' => 'minimal no-footer')); ?>

								<p class="blog-submit">
									<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SAVE'); ?>" class="btn c-submit" />
								</p>
							</fieldset>
						</form>
					</div>
				<?php } ?>

			</div><!-- / .activity-body -->
		</div><!-- / .activity-content -->
	</li>
