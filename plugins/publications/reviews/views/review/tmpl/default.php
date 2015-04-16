<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if ($this->review->id)
{
	$title = Lang::txt('PLG_PUBLICATION_REVIEWS_EDIT_YOUR_REVIEW');
}
else
{
	$title = Lang::txt('PLG_PUBLICATION_REVIEWS_WRITE_A_REVIEW');
}
?>

<div class="below section">
	<h3 id="reviewform-title">
		<a name="reviewform"></a>
		<?php echo $title; ?>
	</h3>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->review->publication_id . '&active=reviews'); ?>" method="post" id="commentform">
			<p class="comment-member-photo">
				<?php
				if (!$this->juser->get('guest'))
				{
					$jxuser = new \Hubzero\User\Profile();
					$jxuser->load($this->juser->get('id'));
					$thumb = plgPublicationsReviews::getMemberPhoto($jxuser, 0);
				}
				else
				{
					$thumb = DS . ltrim(Component::params('com_members')->get('defaultpic'), DS);
					$thumb = plgPublicationsReviews::thumbit($thumb);
				}
				?>
				<img src="<?php echo $thumb; ?>" alt="" />
			</p>
			<fieldset>
				<input type="hidden" name="created" value="<?php echo $this->review->created; ?>" />
				<input type="hidden" name="reviewid" value="<?php echo $this->review->id; ?>" />
				<input type="hidden" name="created_by" value="<?php echo $this->review->created_by; ?>" />
				<input type="hidden" name="publication_id" value="<?php echo $this->review->publication_id; ?>" />
				<input type="hidden" name="publication_version_id" value="<?php echo $this->review->publication_version_id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="view" />
				<input type="hidden" name="id" value="<?php echo $this->review->publication_id; ?>" />
				<input type="hidden" name="action" value="savereview" />
				<input type="hidden" name="active" value="reviews" />

				<fieldset>
					<legend><?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_FORM_RATING'); ?>:</legend>
					<label>
						<input class="option" id="review_rating_1" name="rating" type="radio" value="1"<?php if ($this->review->rating == 1) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/1.gif" alt="<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_1_STAR'); ?>" />
						<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_POOR'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_2" name="rating" type="radio" value="2"<?php if ($this->review->rating == 2) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/2.gif" alt="<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_2_STARS'); ?>" />
						<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_FAIR'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_3" name="rating" type="radio" value="3"<?php if ($this->review->rating == 3) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/3.gif" alt="<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_3_STARS'); ?>" />
						<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_GOOD'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_4" name="rating" type="radio" value="4"<?php if ($this->review->rating == 4) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/4.gif" alt="<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_4_STARS'); ?>" />
						<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_VERY_GOOD'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_5" name="rating" type="radio" value="5"<?php if ($this->review->rating == 5) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/5.gif" alt="<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_5_STARS'); ?>" />
						<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_RATING_EXCELLENT'); ?>
					</label>
				</fieldset>

				<label for="review_comments">
					<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_FORM_COMMENTS');
					if ($this->banking)
					{
						echo ' ( <span class="required">'.Lang::txt('PLG_PUBLICATION_REVIEWS_REQUIRED').'</span> '.Lang::txt('PLG_PUBLICATION_REVIEWS_FOR_ELIGIBILITY').' <a href="'.$this->infolink.'">'.Lang::txt('PLG_PUBLICATION_REVIEWS_EARN_POINTS').'</a> )';
					}
					?>
					<?php
					echo JFactory::getEditor()->display('comment', $this->review->comment, '', '', 35, 10, false, 'review_comments', null, null, array('class' => 'minimal no-footer'));
					?>
				</label>

				<label id="review-anonymous-label">
					<input class="option" type="checkbox" name="anonymous" id="review-anonymous" value="1"<?php if ($this->review->anonymous != 0) { echo ' checked="checked"'; } ?> />
					<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_FORM_ANONYMOUS'); ?>
				</label>
				<div class="submitarea">
					<label>
						<input type="submit" lass="btn btn-success" value="<?php echo Lang::txt('PLG_PUBLICATION_REVIEWS_SUBMIT'); ?>" />
					</label>
				</div>
				<div class="sidenote">
					<p>
						<strong>Please keep comments relevant to this entry. Comments deemed inappropriate may be removed.</strong>
					</p>
					<p>
						URLs (starting with http://) or email addresses will automatically be linked.
					</p>
				</div>
			</fieldset>
		<div class="clear"></div>
	</form>
</div><!-- / .below section -->
