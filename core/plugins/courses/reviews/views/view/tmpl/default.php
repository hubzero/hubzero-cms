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

$comment = \Components\Courses\Models\Comment::blank();

$edit = Request::getInt('editcomment', 0);

$vote = $comment->votes()
	->whereEquals('created_by', User::get('id'))
	->row();

$this->js();
?>
<?php if ($this->params->get('access-view-comment')) { ?>
		<h3 class="review-title">
			<?php echo Lang::txt('PLG_COURSES_REVIEWS'); ?>
		</h3>
	<?php if ($this->comments->count() > 0) {
		$this->view('list')
		     ->set('option', $this->option)
		     ->set('comments', $this->comments)
		     ->set('obj_type', $this->obj_type)
		     ->set('obj', $this->obj)
		     ->set('params', $this->params)
		     ->set('depth', $this->depth)
		     ->set('url', $this->url)
		     ->set('cls', 'odd')
		     ->display();
	} else if ($this->depth <= 1 && !$this->params->get('access-review-comment')) { ?>
		<div class="no-reviews">
			<?php if ($this->obj->isManager()) { ?>
			<div class="instructions">
				<p><?php echo Lang::txt('PLG_COURSES_REVIEWS_NO_REVIEWS'); ?></p>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo Lang::txt('PLG_COURSES_REVIEWS_REVIEW_MANAGER'); ?></strong></p>
				<p><?php echo Lang::txt('PLG_COURSES_REVIEWS_REVIEW_MANAGER_EXPLANATION'); ?></p>
			</div>
			<?php } else { ?>
			<div class="instructions">
				<p><?php echo Lang::txt('PLG_COURSES_REVIEWS_NO_REVIEWS_BE_FIRST'); ?></p>
				<ol>
					<li><?php echo Lang::txt('PLG_COURSES_REVIEWS_NO_REVIEWS_STEP1'); ?></li>
					<li><?php echo Lang::txt('PLG_COURSES_REVIEWS_NO_REVIEWS_STEP2'); ?></li>
					<li><?php echo Lang::txt('PLG_COURSES_REVIEWS_NO_REVIEWS_STEP3'); ?></li>
				</ol>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo Lang::txt('PLG_COURSES_REVIEWS_HOW_TO_ENROLL'); ?></strong></p>
				<p><?php echo Lang::txt('PLG_COURSES_REVIEWS_HOW_TO_ENROLL_EXPLANATION'); ?></p>
				<p><strong><?php echo Lang::txt('PLG_COURSES_REVIEWS_REVIEW_WITHOUT_ENROLLING'); ?></strong></p>
				<p><?php echo Lang::txt('PLG_COURSES_REVIEWS_REVIEW_WITHOUT_ENROLLING_EXPLANATION'); ?></p>
			</div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if (($this->depth <= 1 && $this->params->get('access-review-comment') && !$vote->get('id')) || $edit) { ?>
		<div class="below section">
			<h3 class="post-comment-title">
				<?php if ($this->depth <= 1 && $this->params->get('access-review-comment')) { ?>
					<?php echo Lang::txt('PLG_COURSES_REVIEWS_POST_A_REVIEW'); ?>
				<?php } else { ?>
					<?php echo Lang::txt('PLG_COURSES_REVIEWS_POST_A_COMMENT'); ?>
				<?php } ?>
			</h3>

			<form method="post" action="<?php echo Route::url($this->url); ?>" id="commentform">
				<p class="comment-member-photo">
					<span class="comment-anchor"></span>
					<?php
					$anonymous = 1;
					$jxuser = \Components\Members\Models\Member::oneOrNew(User::get('id'));
					if (!User::isGuest())
					{
						$anonymous = 0;
					}
					?>
					<img src="<?php echo $jxuser->picture($anonymous); ?>" alt="" />
				</p>
				<fieldset>
				<?php
				if (!User::isGuest())
				{
					$parent = Request::getInt('replyto', 0);

					if ($parent)
					{
						$reply = \Components\Courses\Models\Comment::oneOrNew($parent);

						$name = Lang::txt('COM_KB_ANONYMOUS');
						if (!$reply->get('anonymous'))
						{
							$name = $reply->creator->get('name');
							if (in_array($reply->creator->get('access'), User::getAuthorisedViewLevels()))
							{
								$name = '<a href="' . Route::url($reply->creator->link()) . '">' . $this->escape(stripslashes($reply->creator->get('name'))) . '</a>';
							}
						}
					?>
					<blockquote cite="c<?php echo $this->replyto->get('id'); ?>">
						<p>
							<strong><?php echo $name; ?></strong>
							<span class="comment-date-at"><?php echo Lang::txt('PLG_COURSES_REVIEWS_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $reply->created(); ?>"><?php echo $reply->created('time'); ?></time></span>
							<span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_REVIEWS_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $reply->created(); ?>"><?php echo $reply->created('date'); ?></time></span>
						</p>
						<p><?php echo \Hubzero\Utility\String::truncate(stripslashes($reply->get('content')), 300); ?></p>
					</blockquote>
					<?php
					}
				}

				if ($edit)
				{
					$comment = \Components\Courses\Models\Comment::oneOrNew($edit);
					?>
					<p class="warning">
						<?php echo Lang::txt('PLG_COURSES_REVIEWS_NOTE_EDITING_COMMENT_POSTED'); ?> <br />
						<span class="comment-date-at"><?php echo Lang::txt('PLG_COURSES_REVIEWS_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $comment->created(); ?>"><?php echo $comment->created('time'); ?></time></span>
						<span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_REVIEWS_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $comment->created(); ?>"><?php echo $comment->created('date'); ?></time></span>
					</p>
					<?php
					if ($comment->get('parent'))
					{
						$this->depth = 2;
					}
				}

				$comment->set('parent', $parent);
				?>
				<?php if ($this->depth <= 1) {  // && $this->params->get('access-review-comment') ?>
					<fieldset class="rating">
						<legend><?php echo Lang::txt('PLG_COURSES_REVIEWS_FORM_RATING'); ?>:</legend>

						<input class="option" id="review_rating_5" name="comment[rating]" type="radio" value="5"<?php if ($comment->get('rating') == 5) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_5">
							&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;
							<?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_EXCELLENT'); ?>
						</label>

						<input class="option" id="review_rating_4" name="comment[rating]" type="radio" value="4"<?php if ($comment->get('rating') == 4) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_4">
							&#x272D;&#x272D;&#x272D;&#x272D;&#x2729;
							<?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_VERY_GOOD'); ?>
						</label>

						<input class="option" id="review_rating_3" name="comment[rating]" type="radio" value="3"<?php if ($comment->get('rating') == 3) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_3">
							&#x272D;&#x272D;&#x272D;&#x2729;&#x2729;
							<?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_GOOD'); ?>
						</label>

						<input class="option" id="review_rating_2" name="comment[rating]" type="radio" value="2"<?php if ($comment->get('rating') == 2) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_2">
							&#x272D;&#x272D;&#x2729;&#x2729;&#x2729;
							<?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_FAIR'); ?>
						</label>

						<input class="option" id="review_rating_1" name="comment[rating]" type="radio" value="1"<?php if ($comment->get('rating') == 1) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_1">
							&#x272D;&#x2729;&#x2729;&#x2729;&#x2729;
							<?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_POOR'); ?>
						</label>
					</fieldset>
					<div class="clear"></div>
				<?php } ?>

					<label>
						<?php echo Lang::txt('PLG_COURSES_REVIEWS_YOUR_COMMENTS'); ?>: <span class="required"><?php echo Lang::txt('PLG_COURSES_REVIEWS_REQUIRED'); ?></span>
						<?php echo $this->editor('comment[content]', $this->escape(stripslashes($comment->get('content'))), 35, 20, 'commentcontent', array('class' => 'minimal no-footer')); ?>
					</label>


					<label id="comment-anonymous-label">
						<?php if ($this->params->get('comments_anon', 1)) { ?>
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1"<?php if ($comment->get('anonymous')) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('PLG_COURSES_REVIEWS_POST_ANONYMOUSLY'); ?>
						<?php } else { ?>
							&nbsp; <input class="option" type="hidden" name="comment[anonymous]" id="comment-anonymous" value="0" />
						<?php } ?>
					</label>

					<p class="submit">
						<input type="submit" name="submit" value="<?php echo Lang::txt('PLG_COURSES_REVIEWS_POST_COMMENT'); ?>" />
					</p>

					<input type="hidden" name="comment[id]" value="<?php echo $comment->get('id'); ?>" />
					<input type="hidden" name="comment[item_id]" value="<?php echo $this->obj->get('id'); ?>" />
					<input type="hidden" name="comment[item_type]" value="<?php echo $this->obj_type; ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $comment->get('parent'); ?>" />
					<input type="hidden" name="comment[created_by]" value="<?php echo ($comment->get('id') ? $comment->get('created_by') : User::get('id')); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="action" value="save" />

					<?php echo Html::input('token'); ?>

					<div class="sidenote">
						<p>
							<strong><?php echo Lang::txt('PLG_COURSES_REVIEWS_KEEP_RELEVANT'); ?></strong>
						</p>
					</div>
				</fieldset>
			</form>
			<div class="clear"></div>
		</div><!-- / .section -->
	<?php } ?>
<?php } else { ?>
	<p class="warning">
		<?php echo Lang::txt('PLG_COURSES_REVIEWS_MUST_BE_LOGGED_IN'); ?>
	</p>
<?php } ?>