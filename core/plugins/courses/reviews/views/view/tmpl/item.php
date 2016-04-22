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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$cls = isset($this->cls) ? $this->cls : 'odd';

if (!$this->comment->get('anonymous') && $this->obj->get('created_by') == $this->comment->get('created_by'))
{
	$cls .= ' author';
}

$rtrn = $this->url ? $this->url : Request::getVar('REQUEST_URI', $this->obj->link() . '&active=reviews', 'server');
if (!strstr($rtrn, 'index.php'))
{
	$rtrn .= '?';
}
else
{
	$rtrn .= '&';
}

switch ($this->comment->get('rating'))
{
	case 1:   $rating = ' one-stars';   $strs = '&#x272D;&#x2729;&#x2729;&#x2729;&#x2729;'; break;
	case 2:   $rating = ' two-stars';   $strs = '&#x272D;&#x272D;&#x2729;&#x2729;&#x2729;'; break;
	case 3:   $rating = ' three-stars'; $strs = '&#x272D;&#x272D;&#x272D;&#x2729;&#x2729;'; break;
	case 4:   $rating = ' four-stars';  $strs = '&#x272D;&#x272D;&#x272D;&#x272D;&#x2729;'; break;
	case 5:   $rating = ' five-stars';  $strs = '&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;'; break;
	case 0:
	default:  $rating = ' no-stars';    $strs = '&#x2729;&#x2729;&#x2729;&#x2729;&#x2729;'; break;
}
?>
		<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
			<p class="comment-member-photo">
				<span class="comment-anchor"></span>
				<img src="<?php echo $this->comment->creator()->picture($this->comment->get('anonymous')); ?>" alt="" />
			</p>
			<div class="comment-content">
				<?php
				if ($this->params->get('comments_votable', 1))
				{
					$this->view('vote')
					     ->set('option', $this->option)
					     ->set('item', $this->comment)
					     ->set('url', $this->url)
					     ->display();
				}
				?>
				<p class="comment-title">
					<strong>
					<?php if (!$this->comment->get('anonymous')) { ?>
						<?php if (in_array($this->comment->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
							<a href="<?php echo Route::url($this->comment->creator()->link()); ?>">
								<?php echo $this->escape(stripslashes($this->comment->creator()->get('name'))); ?>
							</a>
						<?php } else { ?>
							<?php echo $this->escape(stripslashes($this->comment->creator()->get('name'))); ?>
						<?php } ?>
					<?php } else { ?>
						<?php echo Lang::txt('PLG_COURSES_REVIEWS_ANONYMOUS'); ?>
					<?php } ?>
					</strong>
					<a class="permalink" href="<?php echo $this->url . '#c' . $this->comment->get('id'); ?>" title="<?php echo Lang::txt('PLG_COURSES_REVIEWS_PERMALINK'); ?>">
						<span class="comment-date-at"><?php echo Lang::txt('PLG_COURSES_REVIEWS_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
						<span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_REVIEWS_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
						<?php if ($this->comment->wasModified()) { ?>
							&mdash; <?php echo Lang::txt('Edited'); ?>
							<span class="comment-date-at"><?php echo Lang::txt('PLG_COURSES_REVIEWS_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('time'); ?></time></span>
							<span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_REVIEWS_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('date'); ?></time></span>
						<?php } ?>
					</a>
				</p>
				<div class="comment-body">
					<?php if ($this->comment->get('rating')) { ?>
						<p class="avgrating <?php echo $rating; ?>">
							<?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_OUT_OF_5_STARS', $this->comment->get('rating')); ?>
						</p>
					<?php } ?>
					<?php
					if ($this->comment->isReported())
					{
						echo '<p class="warning">' . Lang::txt('PLG_COURSES_REVIEWS_REPORTED_AS_ABUSIVE') . '</p>';
					}
					else
					{
						echo $this->comment->content;
					}
					?>
				</div><!-- / .comment-body -->

			<?php if ($this->comment->get('filename')) { ?>
				<div class="attachment">
					<p><?php echo Lang::txt('PLG_COURSES_REVIEWS_ATTACHED_FILE'); ?> <a href="<?php echo Request::base() . 'site/comments/' . $this->comment->get('filename'); ?>"><?php echo $this->escape($this->comment->get('filename')); ?></a></p>
				</div>
			<?php } ?>

			<?php if (!$this->comment->isReported()) { ?>
				<p class="comment-options">
				<?php if (($this->params->get('access-edit-comment') && $this->comment->get('created_by') == User::get('id')) || $this->params->get('access-manage-comment')) { ?>
					<a class="icon-edit edit" href="<?php echo Route::url($rtrn . 'editcomment=' . $this->comment->get('id') . '#post-comment'); ?>"><!--
						--><?php echo Lang::txt('PLG_COURSES_REVIEWS_EDIT'); ?><!--
					--></a>
				<?php } ?>
				<?php if (($this->params->get('access-delete-comment') && $this->comment->get('created_by') == User::get('id')) || $this->params->get('access-manage-comment')) { ?>
					<a class="icon-delete delete" href="<?php echo Route::url($rtrn . 'action=delete&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('PLG_COURSES_REVIEWS_DELETE'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->params->get('access-create-comment') && $this->depth < $this->params->get('comments_depth', 3)) { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('PLG_COURSES_REVIEWS_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_COURSES_REVIEWS_REPLY'); ?>" href="<?php echo Route::url($rtrn . 'replyto=' . $this->comment->get('id') . '#post-comment'); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
						--><?php echo Lang::txt('PLG_COURSES_REVIEWS_REPLY'); ?><!--
					--></a>
				<?php } ?>
					<a class="icon-abuse abuse" href="<?php echo Route::url('index.php?option=com_support&task=reportabuse&category=itemcomment&id=' . $this->comment->get('id') . '&parent=' . $this->obj->get('id')); ?>"><!--
						--><?php echo Lang::txt('PLG_COURSES_REVIEWS_REPORT_ABUSE'); ?><!--
					--></a>
				</p><!-- / .comment-options -->
			<?php } ?>

			<?php if ($this->params->get('access-create-comment') && $this->depth < $this->params->get('comments_depth', 3)) { ?>
				<div class="addcomment hide" id="comment-form<?php echo $this->comment->get('id'); ?>">
					<form action="<?php echo Route::url($this->url); ?>" method="post" enctype="multipart/form-data">
						<fieldset>
							<legend><span><?php echo Lang::txt('PLG_COURSES_REVIEWS_REPLYING_TO', (!$this->comment->get('anonymous') ? $this->comment->creator()->get('name') : Lang::txt('PLG_COURSES_REVIEWS_ANONYMOUS'))); ?></span></legend>

							<input type="hidden" name="comment[id]" value="0" />
							<input type="hidden" name="comment[item_id]" value="<?php echo $this->obj->get('id'); ?>" />
							<input type="hidden" name="comment[item_type]" value="<?php echo $this->obj_type; ?>" />
							<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('id'); ?>" />
							<input type="hidden" name="comment[created]" value="" />
							<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="action" value="save" />

							<?php echo Html::input('token'); ?>

							<label for="comment-<?php echo $this->comment->get('id'); ?>-content">
								<span class="label-text"><?php echo Lang::txt('PLG_COURSES_REVIEWS_ENTER_COMMENTS'); ?></span>
								<?php echo $this->editor('comment[content]', '', 35, 4, 'comment-' . $this->comment->get('id') . '-content', array('class' => 'minimal no-footer')); ?>
							</label>

							<label class="reply-anonymous-label" for="comment-<?php echo $this->comment->get('id'); ?>-anonymous">
								<?php if ($this->params->get('comments_anon', 1)) { ?>
									<input class="option" type="checkbox" name="comment[anonymous]" id="comment-<?php echo $this->comment->get('id'); ?>-anonymous" value="1" />
									<?php echo Lang::txt('PLG_COURSES_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
								<?php } else { ?>
									&nbsp; <input class="option" type="hidden" name="comment[anonymous]" value="0" />
								<?php } ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo Lang::txt('PLG_COURSES_REVIEWS_POST_COMMENT'); ?>" />
							</p>
						</fieldset>
					</form>
				</div><!-- / .addcomment -->
			<?php } ?>
			</div><!-- / .comment-content -->
			<?php
			$this->view('list')
			     ->set('option', $this->option)
			     ->set('comments', $this->comment->replies(array('state' => array(1, 3))))
			     ->set('obj_type', $this->obj_type)
			     ->set('obj', $this->obj)
			     ->set('params', $this->params)
			     ->set('depth', $this->depth)
			     ->set('url', $this->url)
			     ->set('cls', $cls)
			     ->display();
			?>
		</li>