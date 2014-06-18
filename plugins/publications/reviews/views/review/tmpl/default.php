<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if ($this->review->id) {
	$title = JText::_('PLG_PUBLICATION_REVIEWS_EDIT_YOUR_REVIEW');
} else {
	$title = JText::_('PLG_PUBLICATION_REVIEWS_WRITE_A_REVIEW');
}
?>

<div class="below section">
	<h3 id="reviewform-title">
		<a name="reviewform"></a>
		<?php echo $title; ?>
	</h3>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->review->publication_id.'&active=reviews'); ?>" method="post" id="commentform">
			<p class="comment-member-photo">
<?php
			if (!$this->juser->get('guest')) {
				$jxuser = new \Hubzero\User\Profile();
				$jxuser->load( $this->juser->get('id') );
				$thumb = plgPublicationsReviews::getMemberPhoto($jxuser, 0);
			} else {
				$config = JComponentHelper::getParams( 'com_members' );
				$thumb = $config->get('defaultpic');
				if (substr($thumb, 0, 1) != DS) {
					$thumb = DS.$dfthumb;
				}
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
					<legend><?php echo JText::_('PLG_PUBLICATION_REVIEWS_FORM_RATING'); ?>:</legend>
					<label>
						<input class="option" id="review_rating_1" name="rating" type="radio" value="1"<?php if ($this->review->rating == 1) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/1.gif" alt="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_1_STAR'); ?>" />
						<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_POOR'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_2" name="rating" type="radio" value="2"<?php if ($this->review->rating == 2) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/2.gif" alt="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_2_STARS'); ?>" />
						<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_FAIR'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_3" name="rating" type="radio" value="3"<?php if ($this->review->rating == 3) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/3.gif" alt="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_3_STARS'); ?>" />
						<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_GOOD'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_4" name="rating" type="radio" value="4"<?php if ($this->review->rating == 4) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/4.gif" alt="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_4_STARS'); ?>" />
						<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_VERY_GOOD'); ?>
					</label>
					<label>
						<input class="option" id="review_rating_5" name="rating" type="radio" value="5"<?php if ($this->review->rating == 5) { echo ' checked="checked"'; } ?> />
						<img src="/components/<?php echo $this->option; ?>/assets/img/stars/5.gif" alt="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_5_STARS'); ?>" />
						<?php echo JText::_('PLG_PUBLICATION_REVIEWS_RATING_EXCELLENT'); ?>
					</label>
				</fieldset>

				<label for="review_comments">
					<?php echo JText::_('PLG_PUBLICATION_REVIEWS_FORM_COMMENTS');
					if ($this->banking) {
						echo ' ( <span class="required">'.JText::_('PLG_PUBLICATION_REVIEWS_REQUIRED').'</span> '.JText::_('PLG_PUBLICATION_REVIEWS_FOR_ELIGIBILITY').' <a href="'.$this->infolink.'">'.JText::_('PLG_PUBLICATION_REVIEWS_EARN_POINTS').'</a> )';
					}
					?>
					<?php
					echo JFactory::getEditor()->display('comment', $this->review->comment, '', '', 35, 10, false, 'review_comments', null, null, array('class' => 'minimal no-footer'));
					?>
				</label>

				<label id="review-anonymous-label">
					<input class="option" type="checkbox" name="anonymous" id="review-anonymous" value="1"<?php if ($this->review->anonymous != 0) { echo ' checked="checked"'; } ?> />
					<?php echo JText::_('PLG_PUBLICATION_REVIEWS_FORM_ANONYMOUS'); ?>
				</label>
				<label>
					<input type="submit" value="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_SUBMIT'); ?>" />
				</label>
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
