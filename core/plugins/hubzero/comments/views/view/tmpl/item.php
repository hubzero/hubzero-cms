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

	$this->comment->set('option', $this->option);
	$this->comment->set('item_id', $this->obj_id);
	$this->comment->set('item_type', $this->obj_type);

	if ($this->obj->get('created_by') == $this->comment->get('created_by'))
	{
		$cls .= ' author';
	}

	if ($mark = $this->params->get('onCommentMark'))
	{
		if ($mark instanceof Closure)
		{
			$marked = (string) $mark($this->comment);
			$cls .= ($marked ? ' ' . $marked : '');
		}
	}

	$rtrn = $this->url ? $this->url : Request::getVar('REQUEST_URI', 'index.php?option=' . $this->option . '&id=' . $this->obj_id . '&active=comments', 'server');

	$this->comment->set('url', $rtrn);
?>
		<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
			<p class="comment-member-photo">
				<img src="<?php echo $this->comment->creator()->getPicture($this->comment->get('anonymous')); ?>" alt="" />
			</p>
			<div class="comment-content">
				<?php
				if ($this->params->get('comments_votable', 1))
				{
					$this->view('vote')
					     ->set('option', $this->option)
					     ->set('item', $this->comment)
					     ->set('params', $this->params)
					     ->set('url', $this->url)
					     ->display();
				}
				?>

				<p class="comment-title">
					<strong>
						<?php if (!$this->comment->get('anonymous')) { ?>
							<?php if ($this->comment->creator()->get('public')) { ?>
								<a href="<?php echo Route::url($this->comment->creator()->getLink()); ?>"><!--
									--><?php echo $this->escape(stripslashes($this->comment->creator()->get('name'))); ?><!--
								--></a>
							<?php } else { ?>
								<?php echo $this->escape(stripslashes($this->comment->creator()->get('name'))); ?>
							<?php } ?>
						<?php } else { ?>
							<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_ANONYMOUS'); ?>
						<?php } ?>
					</strong>
					<a class="permalink" href="<?php echo $this->comment->link(); ?>" title="<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_PERMALINK'); ?>">
						<span class="comment-date-at"><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
						<span class="comment-date-on"><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
					</a>
				</p>

				<div class="comment-body">
					<?php
					if ($this->comment->isReported())
					{
						echo '<p class="warning">' . Lang::txt('PLG_HUBZERO_COMMENTS_REPORTED_AS_ABUSIVE') . '</p>';
					}
					else
					{
						echo $this->comment->content;
					}
					?>
				</div><!-- / .comment-body -->

				<?php //if (!$this->comment->isReported() && $this->comment->files()->count()) { ?>
					<div class="comment-attachments">
						<?php
						foreach ($this->comment->files()->rows() as $attachment)
						{
							if (!trim($attachment->get('description')))
							{
								$attachment->set('description', $attachment->get('filename'));
							}

							if ($attachment->isImage())
							{
								if ($attachment->width() > 400)
								{
									$html = '<p><a href="' . Route::url($attachment->link()) . '"><img src="' . Route::url($attachment->link()) . '" alt="' . $attachment->get('description') . '" width="400" /></a></p>';
								}
								else
								{
									$html = '<p><img src="' . Route::url($attachment->link()) . '" alt="' . $attachment->get('description') . '" /></p>';
								}
							}
							else
							{
								$html = '<p class="attachment"><a href="' . Route::url($attachment->link()) . '" title="' . $attachment->get('description') . '">' . $attachment->get('description') . '</a></p>';
							}

							echo $html;
						}
						?>
					</div><!-- / .comment-attachments -->
				<?php //} ?>

				<?php if (!$this->comment->isReported()) { ?>
					<p class="comment-options">
						<?php if (($this->params->get('access-delete-comment') && $this->comment->get('created_by') == User::get('id')) || $this->params->get('access-manage-comment')) { ?>
							<a class="icon-delete delete" href="<?php echo Route::url($this->comment->link('delete')); ?>" data-txt-confirm="<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_CONFIRM'); ?>"><!--
								--><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_DELETE'); ?><!--
							--></a>
						<?php } ?>
						<?php if (($this->params->get('access-edit-comment') && $this->comment->get('created_by') == User::get('id')) || $this->params->get('access-manage-comment')) { ?>
							<a class="icon-edit edit" href="<?php echo Route::url($this->comment->link('edit')); ?>"><!--
								--><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_EDIT'); ?><!--
							--></a>
						<?php } ?>
						<?php if ($this->params->get('access-create-comment') && $this->depth < $this->params->get('comments_depth', 3)) { ?>
							<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_REPLY'); ?>"href="<?php echo Route::url($this->comment->link('reply')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
								--><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_REPLY'); ?><!--
							--></a>
						<?php } ?>
							<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_REPORTED_AS_ABUSIVE'); ?>" href="<?php echo Route::url($this->comment->link('report')); ?>"><!--
								--><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_REPORT_ABUSE'); ?><!--
							--></a>
					</p><!-- / .comment-options -->
				<?php } ?>
				<?php if ($this->params->get('access-create-comment') && $this->depth < $this->params->get('comments_depth', 3)) { ?>
					<div class="addcomment hide" id="comment-form<?php echo $this->comment->get('id'); ?>">
						<form action="<?php echo Route::url($this->comment->link('base')); ?>" method="post" enctype="multipart/form-data">
							<fieldset>
								<legend>
									<span><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_REPLYING_TO', (!$this->comment->get('anonymous') ? $this->comment->get('name') : Lang::txt('PLG_HUBZERO_COMMENTS_ANONYMOUS'))); ?></span>
								</legend>

								<input type="hidden" name="comment[id]" value="0" />
								<input type="hidden" name="comment[item_id]" value="<?php echo $this->escape($this->comment->get('item_id')); ?>" />
								<input type="hidden" name="comment[item_type]" value="<?php echo $this->escape($this->comment->get('item_type')); ?>" />
								<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('id'); ?>" />
								<input type="hidden" name="comment[created]" value="" />
								<input type="hidden" name="comment[created_by]" value="<?php echo $this->escape(User::get('id')); ?>" />
								<input type="hidden" name="comment[state]" value="1" />
								<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
								<input type="hidden" name="action" value="commentsave" />

								<?php echo Html::input('token'); ?>

								<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
									<span class="label-text"><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_ENTER_COMMENTS'); ?></span>
									<?php
									echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
									?>
								</label>

								<label class="comment-<?php echo $this->comment->get('id'); ?>-file" for="comment-<?php echo $this->comment->get('id'); ?>-file">
									<span class="label-text"><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_ATTACH_FILE'); ?>:</span>
									<input type="file" name="comment_file" id="comment-<?php echo $this->comment->get('id'); ?>-file" />
								</label>

								<label class="reply-anonymous-label" for="comment-<?php echo $this->comment->get('id'); ?>-anonymous">
									<input class="option" type="checkbox" name="comment[anonymous]" id="comment-<?php echo $this->comment->get('id'); ?>-anonymous" value="1" />
									<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_POST_COMMENT_ANONYMOUSLY'); ?>
								</label>

								<p class="submit">
									<input type="submit" value="<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_POST_COMMENT'); ?>" />
								</p>
							</fieldset>
						</form>
					</div><!-- / .addcomment -->
				<?php } ?>
			</div><!-- / .comment-content -->

			<?php
			if ($this->depth < $this->params->get('comments_depth', 3))
			{
				$replies = $this->comment->replies(array('state' => array(1, 3)));

				if ($replies->count())
				{
					$this->view('list')
					     ->set('option', $this->option)
					     ->set('comments', $replies)
					     ->set('obj_type', $this->obj_type)
					     ->set('obj_id', $this->obj_id)
					     ->set('obj', $this->obj)
					     ->set('params', $this->params)
					     ->set('depth', $this->depth)
					     ->set('url', $this->url)
					     ->set('cls', $cls)
					     ->display();
				}
			}
			?>
		</li>