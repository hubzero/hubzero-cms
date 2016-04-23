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

$this->css()
     ->js();
?>

<?php if ($this->params->get('access-view-comment')) { ?>
	<section class="below section">
		<div class="section-inner">
			<div class="subject thread">

				<?php if ($this->params->get('access-create-comment')) { ?>
					<h3 class="post-comment-title" id="post-comment">
						<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_POST_A_COMMENT'); ?>
					</h3>
					<form method="post" action="<?php echo Route::url($this->url); ?>" id="commentform" enctype="multipart/form-data">
						<p class="comment-member-photo">
							<?php
							$edit = 0;
							// Make sure editing capaibilites are available before even accepting an ID to edit
							if ($this->params->get('access-edit-comment') || $this->params->get('access-manage-comment'))
							{
								$edit = Request::getInt('commentedit', 0);
							}

							// Load the comment
							$comment = \Plugins\Hubzero\Comments\Models\Comment::oneOrNew($edit);
							// If the comment exists and the editor is NOT the creator and the editor is NOT a manager...
							if ($comment->get('id') && $comment->get('created_by') != User::get('id') && !$this->params->get('access-manage-comment'))
							{
								// Disallow editing
								$comment = \Plugins\Hubzero\Comments\Models\Comment::blank();
							}

							if ($comment->isNew())
							{
								$comment->set('parent', Request::getInt('commentreply', 0));
								$comment->set('created_by', (!User::isGuest() ? User::get('id') : 0));
								$comment->set('anonymous', (!User::isGuest() ? 0 : 1));
							}
							?>
							<img src="<?php echo $comment->creator->picture($comment->get('anonymous')); ?>" alt="" />
						</p>
						<fieldset>
							<?php
							if (!User::isGuest())
							{
								if ($replyto = Request::getInt('commentreply', 0))
								{
									$reply = \Plugins\Hubzero\Comments\Models\Comment::oneOrNew($replyto);

									$name = Lang::txt('COM_KB_ANONYMOUS');
									if (!$reply->get('anonymous'))
									{
										$name = $this->escape(stripslashes($repy->creator->get('name')));
										if (in_array($reply->creator->get('access'), User::getAuthorisedViewLevels()))
										{
											$name = '<a href="' . Route::url($reply->creator->link()) . '">' . $name . '</a>';
										}
									}
									?>
									<blockquote cite="c<?php echo $reply->get('id'); ?>">
										<p>
											<strong><?php echo $name; ?></strong>
											<span class="comment-date-at"><?php echo Lang::txt('COM_ANSWERS_AT'); ?></span>
											<span class="time"><time datetime="<?php echo $reply->created(); ?>"><?php echo $reply->created('time'); ?></time></span>
											<span class="comment-date-on"><?php echo Lang::txt('COM_ANSWERS_ON'); ?></span>
											<span class="date"><time datetime="<?php echo $reply->created(); ?>"><?php echo $reply->created('date'); ?></time></span>
										</p>
										<p><?php echo $reply->content; ?></p>
									</blockquote>
									<?php
								}
							}
							?>
							<label for="commentcontent">
								<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_YOUR_COMMENTS'); ?>:
								<?php
								if (!User::isGuest())
								{
									echo $this->editor('comment[content]', $this->escape($comment->get('content')), 35, 15, 'commentcontent', array('class' => 'minimal no-footer'));
								}
								?>
							</label>

							<label for="comment_file">
								<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_ATTACH_FILE'); ?>
								<input type="file" name="comment_file" id="comment_file" />
							</label>

							<label id="comment-anonymous-label">
								<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1"<?php if ($comment->get('anonymous')) { echo ' checked="checked"'; } ?> />
								<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_POST_ANONYMOUSLY'); ?>
							</label>

							<p class="submit">
								<input type="submit" class="btn btn-success" name="submit" value="<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_POST_COMMENT'); ?>" />
							</p>

							<input type="hidden" name="comment[id]" value="<?php echo $comment->get('id'); ?>" />
							<input type="hidden" name="comment[item_id]" value="<?php echo $this->obj_id; ?>" />
							<input type="hidden" name="comment[item_type]" value="<?php echo $this->obj_type; ?>" />
							<input type="hidden" name="comment[parent]" value="<?php echo $comment->get('parent'); ?>" />
							<input type="hidden" name="comment[created_by]" value="<?php echo $comment->get('created_by'); ?>" />
							<input type="hidden" name="comment[state]" value="<?php echo $comment->get('state', 1); ?>" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="action" value="commentsave" />

							<?php echo Html::input('token'); ?>

							<div class="sidenote">
								<p>
									<strong><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_KEEP_RELEVANT'); ?></strong>
								</p>
							</div>
						</fieldset>
					</form>
				<?php } ?>

				<?php if ($this->params->get('comments_locked', 0) == 1) { ?>
					<p class="info"><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_LOCKED'); ?></p>
				<?php } ?>

				<h3 class="post-comment-title">
					<?php echo Lang::txt('PLG_HUBZERO_COMMENTS'); ?>
				</h3>
				<?php if ($this->comments->count()) {
					$this->view('list')
					     ->set('option', $this->option)
					     ->set('comments', $this->comments)
					     ->set('obj_type', $this->obj_type)
					     ->set('obj_id', $this->obj_id)
					     ->set('obj', $this->obj)
					     ->set('params', $this->params)
					     ->set('depth', $this->depth)
					     ->set('url', $this->url)
					     ->set('cls', 'odd')
					     ->display();
				} else if ($this->depth <= 1) { ?>
					<p class="no-comments">
						<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_NO_COMMENTS'); ?>
					</p>
				<?php } ?>

			</div><!-- / .subject -->
			<div class="aside">
			</div><!-- / .aside -->
		</div>
	</section><!-- / .below section -->
<?php } else { ?>
	<p class="warning">
		<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_MUST_BE_LOGGED_IN'); ?>
	</p>
<?php } ?>