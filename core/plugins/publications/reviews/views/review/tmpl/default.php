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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$review = new PublicationsModelReview($this->review);

if ($review->exists())
{
	$title = Lang::txt('PLG_PUBLICATIONS_REVIEWS_EDIT_YOUR_REVIEW');
}
else
{
	$title = Lang::txt('PLG_PUBLICATIONS_REVIEWS_WRITE_A_REVIEW');
}

?>

<div class="below section">
	<h3 id="reviewform-title">
		<a name="reviewform"></a>
		<?php echo $title; ?>
	</h3>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->review->get('publication_id') . '&active=reviews'); ?>" method="post" id="commentform">
			<p class="comment-member-photo">
				<span class="comment-anchor"></span>
				<?php
				$anon = User::isGuest() ? 1 : 0;
				?>
				<img src="<?php echo $review->creator()->picture($anon); ?>" alt="" />
			</p>
			<fieldset>
				<input type="hidden" name="created" value="<?php echo $review->get('created'); ?>" />
				<input type="hidden" name="reviewid" value="<?php echo $review->get('id'); ?>" />
				<input type="hidden" name="created_by" value="<?php echo $this->review->get('created_by'); ?>" />
				<input type="hidden" name="publication_id" value="<?php echo $this->review->get('publication_id'); ?>" />
				<input type="hidden" name="publication_version_id" value="<?php echo $this->review->get('publication_version_id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="view" />
				<input type="hidden" name="id" value="<?php echo $this->review->get('publication_id'); ?>" />
				<input type="hidden" name="action" value="savereview" />
				<input type="hidden" name="active" value="reviews" />

				<fieldset>
					<legend><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_FORM_RATING'); ?>:</legend>
					<label>
						<input class="option" id="review_rating_1" name="rating" type="radio" value="1"<?php if ($review->get('rating') == 1) { echo ' checked="checked"'; } ?> />
						&#x272D;&#x2729;&#x2729;&#x2729;&#x2729;
						<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_RATING_POOR'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_2" name="rating" type="radio" value="2"<?php if ($review->get('rating') == 2) { echo ' checked="checked"'; } ?> />
						&#x272D;&#x272D;&#x2729;&#x2729;&#x2729;
						<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_RATING_FAIR'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_3" name="rating" type="radio" value="3"<?php if ($review->get('rating') == 3) { echo ' checked="checked"'; } ?> />
						&#x272D;&#x272D;&#x272D;&#x2729;&#x2729;
						<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_RATING_GOOD'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_4" name="rating" type="radio" value="4"<?php if ($review->get('rating') == 4) { echo ' checked="checked"'; } ?> />
						&#x272D;&#x272D;&#x272D;&#x272D;&#x2729;
						<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_RATING_VERY_GOOD'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_5" name="rating" type="radio" value="5"<?php if ($review->get('rating') == 5) { echo ' checked="checked"'; } ?> />
						&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;
						<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_RATING_EXCELLENT'); ?>
					</label>
				</fieldset>

				<label for="review_comments">
					<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_FORM_COMMENTS');
					if ($this->banking)
					{
						echo ' ( <span class="required">' . Lang::txt('PLG_PUBLICATIONS_REVIEWS_REQUIRED').'</span> ' . Lang::txt('PLG_PUBLICATIONS_REVIEWS_FOR_ELIGIBILITY') . ' <a href="' . $this->infolink . '">' . Lang::txt('PLG_PUBLICATIONS_REVIEWS_EARN_POINTS') . '</a> )';
					}
					?>
					<?php
					echo $this->editor('comment', $this->escape($review->content('raw')), 35, 10, 'review_comments', array('class' => 'minimal no-footer'));
					?>
				</label>

				<label id="review-anonymous-label">
					<input class="option" type="checkbox" name="anonymous" id="review-anonymous" value="1"<?php if ($review->get('anonymous') != 0) { echo ' checked="checked"'; } ?> />
					<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_FORM_ANONYMOUS'); ?>
				</label>

				<div class="submitarea">
					<label>
						<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_SUBMIT'); ?>" />
					</label>
				</div>

				<div class="sidenote">
					<p>
						<strong><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_KEEP_POLITE'); ?></strong>
					</p>
				</div>
			</fieldset>
		<div class="clear"></div>
	</form>
</div><!-- / .below section -->
