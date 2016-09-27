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

if ($this->review->get('id'))
{
	$title = Lang::txt('PLG_RESOURCES_REVIEWS_EDIT_YOUR_REVIEW');
}
else
{
	$title = Lang::txt('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW');
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->review->get('resource_id') . '&active=reviews'); ?>" method="post" id="commentform">
	<section class="below section">
		<h3 id="reviewform-title">
			<?php echo $title; ?>
		</h3>
		<p class="comment-member-photo">
			<span class="comment-anchor"></span>
			<?php
			$anon = 1;
			if (!User::isGuest())
			{
				$anon = 0;
			}
			?>
			<img src="<?php echo $this->review->creator->picture($anon); ?>" alt="" />
		</p>
		<fieldset>
			<input type="hidden" name="review[created]" value="<?php echo $this->review->get('created'); ?>" />
			<input type="hidden" name="review[id]" value="<?php echo $this->review->get('id'); ?>" />
			<input type="hidden" name="review[user_id]" value="<?php echo $this->review->get('user_id'); ?>" />
			<input type="hidden" name="review[resource_id]" value="<?php echo $this->review->get('resource_id'); ?>" />
			<input type="hidden" name="review[state]" value="<?php echo $this->review->get('state'); ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="id" value="<?php echo $this->review->get('resource_id'); ?>" />
			<input type="hidden" name="action" value="savereview" />
			<input type="hidden" name="active" value="reviews" />

			<?php echo Html::input('token'); ?>

			<?php if ($this->banking) {	?>
				<p class="help"><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_DID_YOU_KNOW_YOU_CAN'); ?> <a href="<?php echo $this->infolink; ?>"><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_EARN_POINTS'); ?></a> <?php echo Lang::txt('PLG_RESOURCES_REVIEWS_FOR_REVIEWS'); ?>? <?php echo Lang::txt('PLG_RESOURCES_REVIEWS_EARN_POINTS_EXP'); ?></p>
			<?php } ?>

			<fieldset>
				<legend><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_FORM_RATING'); ?>:</legend>
				<label>
					<input class="option" id="review_rating_1" name="review[rating]" type="radio" value="1"<?php if ($this->review->get('rating') == 1) { echo ' checked="checked"'; } ?> />
					&#x272D;&#x2729;&#x2729;&#x2729;&#x2729;
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_RATING_POOR'); ?>
				</label>
				<label>
					<input class="option" id="review_rating_2" name="review[rating]" type="radio" value="2"<?php if ($this->review->get('rating') == 2) { echo ' checked="checked"'; } ?> />
					&#x272D;&#x272D;&#x2729;&#x2729;&#x2729;
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_RATING_FAIR'); ?>
				</label>
				<label>
					<input class="option" id="review_rating_3" name="review[rating]" type="radio" value="3"<?php if ($this->review->get('rating') == 3) { echo ' checked="checked"'; } ?> />
					&#x272D;&#x272D;&#x272D;&#x2729;&#x2729;
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_RATING_GOOD'); ?>
				</label>
				<label>
					<input class="option" id="review_rating_4" name="review[rating]" type="radio" value="4"<?php if ($this->review->get('rating') == 4) { echo ' checked="checked"'; } ?> />
					&#x272D;&#x272D;&#x272D;&#x272D;&#x2729;
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_RATING_VERY_GOOD'); ?>
				</label>
				<label>
					<input class="option" id="review_rating_5" name="review[rating]" type="radio" value="5"<?php if ($this->review->get('rating') == 5) { echo ' checked="checked"'; } ?> />
					&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_RATING_EXCELLENT'); ?>
				</label>
			</fieldset>

			<label for="review_comments">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_FORM_COMMENTS');
				if ($this->banking)
				{
					echo ' ( <span class="required">'.Lang::txt('PLG_RESOURCES_REVIEWS_REQUIRED').'</span> '.Lang::txt('PLG_RESOURCES_REVIEWS_FOR_ELIGIBILITY').' <a href="'.$this->infolink.'">'.Lang::txt('PLG_RESOURCES_REVIEWS_EARN_POINTS').'</a> )';
				}
				?>
				<?php
				echo $this->editor('review[comment]', $this->escape($this->review->get('comment')), 35, 10, 'review_comments', array('class' => 'minimal no-footer'));
				?>
			</label>

			<label id="comment-anonymous-label">
				<input class="option" type="checkbox" name="review[anonymous]" id="review-anonymous" value="1"<?php if ($this->review->get('anonymous') != 0) { echo ' checked="checked"'; } ?> />
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_FORM_ANONYMOUS'); ?>
			</label>

			<p class="submit">
				<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_SUBMIT'); ?>" />
			</p>

			<div class="sidenote">
				<p>
					<strong><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_KEEP_POLITE'); ?></strong>
				</p>
				<p>
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_CONTENT_FORMATTING_NOTES'); ?>
				</p>
			</div>
		</fieldset>
	</section><!-- / .below section -->
</form>
