<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$comment = new \Hubzero\Item\Comment($this->database);

$edit = JRequest::getInt('editcomment', 0);

$this->js();
?>
<?php if ($this->params->get('access-view-comment')) { ?>
		<h3 class="review-title">
			<?php echo JText::_('PLG_COURSES_REVIEWS'); ?>
		</h3>
	<?php if ($this->comments->total() > 0) {
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
				<p><?php echo JText::_('PLG_COURSES_REVIEWS_NO_REVIEWS'); ?></p>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo JText::_('PLG_COURSES_REVIEWS_REVIEW_MANAGER'); ?></strong></p>
				<p><?php echo JText::_('PLG_COURSES_REVIEWS_REVIEW_MANAGER_EXPLANATION'); ?></p>
			</div>
			<?php } else { ?>
			<div class="instructions">
				<p><?php echo JText::_('PLG_COURSES_REVIEWS_NO_REVIEWS_BE_FIRST'); ?></p>
				<ol>
					<li><?php echo JText::_('PLG_COURSES_REVIEWS_NO_REVIEWS_STEP1'); ?></li>
					<li><?php echo JText::_('PLG_COURSES_REVIEWS_NO_REVIEWS_STEP2'); ?></li>
					<li><?php echo JText::_('PLG_COURSES_REVIEWS_NO_REVIEWS_STEP3'); ?></li>
				</ol>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo JText::_('PLG_COURSES_REVIEWS_HOW_TO_ENROLL'); ?></strong></p>
				<p><?php echo JText::_('PLG_COURSES_REVIEWS_HOW_TO_ENROLL_EXPLANATION'); ?></p>
				<p><strong><?php echo JText::_('PLG_COURSES_REVIEWS_REVIEW_WITHOUT_ENROLLING'); ?></strong></p>
				<p><?php echo JText::_('PLG_COURSES_REVIEWS_REVIEW_WITHOUT_ENROLLING_EXPLANATION'); ?></p>
			</div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if (($this->depth <= 1 && $this->params->get('access-review-comment') && !$comment->hasRated($this->obj->get('id'), $this->obj_type, $this->juser->get('id'))) || $edit) { ?>
	<div class="below section">
		<h3 class="post-comment-title">
		<?php if ($this->depth <= 1 && $this->params->get('access-review-comment')) { ?>
			<?php echo JText::_('PLG_COURSES_REVIEWS_POST_A_REVIEW'); ?>
		<?php } else { ?>
			<?php echo JText::_('PLG_COURSES_REVIEWS_POST_A_COMMENT'); ?>
		<?php } ?>
		</h3>

			<form method="post" action="<?php echo JRoute::_($this->url); ?>" id="commentform">
				<p class="comment-member-photo">
					<span class="comment-anchor"></span>
					<?php
					$anonymous = 1;
					if (!$this->juser->get('guest'))
					{
						$jxuser = new \Hubzero\User\Profile();
						$jxuser->load($this->juser->get('id'));
						$anonymous = 0;
					}
					?>
					<img src="<?php echo $jxuser->getPicture($anonymous); ?>" alt="" />
				</p>
				<fieldset>
				<?php
				if (!$this->juser->get('guest'))
				{
					if (($replyto = JRequest::getInt('replyto', 0)))
					{
						$reply = new \Hubzero\Item\Comment($this->database);
						$reply->load($replyto);

						$name = JText::_('COM_KB_ANONYMOUS');
						if (!$reply->anonymous)
						{
							$xuser = new \Hubzero\User\Profile();
							$xuser->load($reply->created_by);
							if (is_object($xuser) && $xuser->get('name'))
							{
								$name = '<a href="' . JRoute::_($xuser->getLink()) . '">' . $this->escape(stripslashes($xuser->get('name'))) . '</a>';
							}
						}
					?>
					<blockquote cite="c<?php echo $this->replyto->id; ?>">
						<p>
							<strong><?php echo $name; ?></strong>
							<span class="comment-date-at"><?php echo JText::_('PLG_COURSES_REVIEWS_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, JText::_('TIME_FORMAt_HZ1')); ?></time></span>
							<span class="comment-date-on"><?php echo JText::_('PLG_COURSES_REVIEWS_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, JText::_('DATE_FORMAt_HZ1')); ?></time></span>
						</p>
						<p><?php echo \Hubzero\Utility\String::truncate(stripslashes($reply->content), 300); ?></p>
					</blockquote>
					<?php
					}
				}

				$comment->parent = JRequest::getInt('replyto', 0);
				if ($edit)
				{
					$comment->load($edit);
					/*if ($comment->created_by != $this->juser->get('id'))
					{
						$comment = new \Hubzero\Item\Comment($this->database);
					}*/
					?>
					<p class="warning">
						<?php echo JText::_('PLG_COURSES_REVIEWS_NOTE_EDITING_COMMENT_POSTED'); ?> <br />
						<span class="comment-date-at"><?php echo JText::_('PLG_COURSES_REVIEWS_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, JText::_('TIME_FORMAt_HZ1')); ?></time></span>
						<span class="comment-date-on"><?php echo JText::_('PLG_COURSES_REVIEWS_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, JText::_('DATE_FORMAt_HZ1')); ?></time></span>
					</p>
					<?php
					if ($comment->parent)
					{
						$this->depth = 2;
					}
				}
				?>
				<?php if ($this->depth <= 1) {  // && $this->params->get('access-review-comment') ?>
					<fieldset class="rating">
						<legend><?php echo JText::_('PLG_COURSES_REVIEWS_FORM_RATING'); ?>:</legend>

						<input class="option" id="review_rating_5" name="comment[rating]" type="radio" value="5"<?php if ($comment->rating == 5) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_5">
							&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;
							<?php echo JText::_('PLG_COURSES_REVIEWS_RATING_EXCELLENT'); ?>
						</label>

						<input class="option" id="review_rating_4" name="comment[rating]" type="radio" value="4"<?php if ($comment->rating == 4) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_4">
							&#x272D;&#x272D;&#x272D;&#x272D;&#x2729;
							<?php echo JText::_('PLG_COURSES_REVIEWS_RATING_VERY_GOOD'); ?>
						</label>

						<input class="option" id="review_rating_3" name="comment[rating]" type="radio" value="3"<?php if ($comment->rating == 3) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_3">
							&#x272D;&#x272D;&#x272D;&#x2729;&#x2729;
							<?php echo JText::_('PLG_COURSES_REVIEWS_RATING_GOOD'); ?>
						</label>

						<input class="option" id="review_rating_2" name="comment[rating]" type="radio" value="2"<?php if ($comment->rating == 2) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_2">
							&#x272D;&#x272D;&#x2729;&#x2729;&#x2729;
							<?php echo JText::_('PLG_COURSES_REVIEWS_RATING_FAIR'); ?>
						</label>

						<input class="option" id="review_rating_1" name="comment[rating]" type="radio" value="1"<?php if ($comment->rating == 1) { echo ' checked="checked"'; } ?> />
						<label for="review_rating_1">
							&#x272D;&#x2729;&#x2729;&#x2729;&#x2729;
							<?php echo JText::_('PLG_COURSES_REVIEWS_RATING_POOR'); ?>
						</label>
					</fieldset>
					<div class="clear"></div>
				<?php } ?>

					<label>
						<?php echo JText::_('PLG_COURSES_REVIEWS_YOUR_COMMENTS'); ?>: <span class="required"><?php echo JText::_('PLG_COURSES_REVIEWS_REQUIRED'); ?></span>
						<?php echo $this->editor('comment[content]', $this->escape(stripslashes($comment->content)), 35, 20, 'commentcontent', array('class' => 'minimal no-footer')); ?>
					</label>


					<label id="comment-anonymous-label">
					<?php if ($this->params->get('comments_anon', 1)) { ?>
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1"<?php if ($comment->anonymous) { echo ' checked="checked"'; } ?> />
						<?php echo JText::_('PLG_COURSES_REVIEWS_POST_ANONYMOUSLY'); ?>
					<?php } else { ?>
						&nbsp; <input class="option" type="hidden" name="comment[anonymous]" id="comment-anonymous" value="0" />
					<?php } ?>
					</label>

					<p class="submit">
						<input type="submit" name="submit" value="<?php echo JText::_('PLG_COURSES_REVIEWS_POST_COMMENT'); ?>" />
					</p>

					<input type="hidden" name="comment[id]" value="<?php echo $comment->id; ?>" />
					<input type="hidden" name="comment[item_id]" value="<?php echo $this->obj->get('id'); ?>" />
					<input type="hidden" name="comment[item_type]" value="<?php echo $this->obj_type; ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $comment->parent; ?>" />
					<input type="hidden" name="comment[created_by]" value="<?php echo ($comment->id ? $comment->created_by : $this->juser->get('id')); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="action" value="save" />

					<?php echo JHTML::_('form.token'); ?>

					<div class="sidenote">
						<p>
							<strong><?php echo JText::_('PLG_COURSES_REVIEWS_KEEP_RELEVANT'); ?></strong>
						</p>
					</div>
				</fieldset>
			</form>
		<div class="clear"></div>
	</div><!-- / .section -->
	<?php } ?>
<?php } else { ?>
	<p class="warning">
		<?php echo JText::_('PLG_COURSES_REVIEWS_MUST_BE_LOGGED_IN'); ?>
	</p>
<?php } ?>