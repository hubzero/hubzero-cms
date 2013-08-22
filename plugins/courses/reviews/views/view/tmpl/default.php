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
defined('_JEXEC') or die('Restricted access');

$dateformat = '%d %b %Y';
$timeformat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$timeformat = 'H:i p';
	$tz = true;
}

$comment = new Hubzero_Item_Comment($this->database);

$edit = JRequest::getInt('editcomment', 0);

?>
<?php if ($this->params->get('access-view-comment')) { ?>
	<!-- <div class="below section"> -->
		<h3 class="review-title">
			<a name="reviews"></a>
			<?php echo JText::_('PLG_COURSES_REVIEWS'); ?>
		</h3>
	<?php if ($this->comments) {
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => 'reviews',
				'name'    => 'view',
				'layout'  => 'list'
			)
		);
		$view->option     = $this->option;
		$view->comments   = $this->comments;
		$view->obj_type   = $this->obj_type;
		$view->obj        = $this->obj;
		$view->params     = $this->params;
		$view->depth      = $this->depth;
		$view->url        = $this->url;
		$view->cls        = 'odd';
		$view->display();
	} else if ($this->depth <= 1) { ?>
		<div class="no-reviews">
			<div class="instructions">
				<p><?php echo JText::_('PLG_COURSES_REVIEWS_NO_REVIEWS'); ?></p>
				<ol>
					<li><?php echo JText::_('Enroll in the course.'); ?></li>
					<li><?php echo JText::_('Get your learn on!'); ?></li>
					<li><?php echo JText::_('Come back and tell us about the experience.'); ?></li>
				</ol>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo JText::_('How do I enroll?'); ?></strong></p>
				<p><?php echo JText::_('Find the description page for the course you want to take (hint: you\'re on one right now). Look for an "enroll" button and click it. If you don\'t see the button, the course currently isn\'t active.'); ?></p>
				<p><strong><?php echo JText::_('Can I review without enrolling?'); ?></strong></p>
				<p><?php echo JText::_('Sorry, no. To review a course you must have taken the course.'); ?></p>
			</div>
		</div>
	<?php } ?>
	<!-- </div>/ .below section -->

	<?php if (($this->depth <= 1 && $this->params->get('access-review-comment') && !$comment->hasRated($this->obj->get('id'), $this->obj_type, $this->juser->get('id'))) 
		|| $edit) { ?>
	<?php //if ($this->params->get('access-create-comment')) { ?>
	<div class="below section">
		<h3 class="post-comment-title">
			<a name="post-comment"></a>
		<?php if ($this->depth <= 1 && $this->params->get('access-review-comment')) { ?>
			<?php echo JText::_('PLG_COURSES_REVIEWS_POST_A_REVIEW'); ?>
		<?php } else { ?>
			<?php echo JText::_('PLG_COURSES_REVIEWS_POST_A_COMMENT'); ?>
		<?php } ?>
		</h3>

			<form method="post" action="<?php echo JRoute::_($this->url); ?>" id="commentform">
				<p class="comment-member-photo">
					<span class="comment-anchor"><a name="post-comment"></a></span>
					<?php
					ximport('Hubzero_User_Profile');
					ximport('Hubzero_User_Profile_Helper');
					
					$anonymous = 1;
					if (!$this->juser->get('guest')) 
					{
						$jxuser = new Hubzero_User_Profile();
						$jxuser->load($this->juser->get('id'));
						$anonymous = 0;
					}
					?>
					<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, $anonymous); ?>" alt="" />
				</p>
				<fieldset>
				<?php
				if (!$this->juser->get('guest')) 
				{
					if (($replyto = JRequest::getInt('replyto', 0))) 
					{
						$reply = new Hubzero_Item_Comment($this->database);
						$reply->load($replyto);
						
						ximport('Hubzero_View_Helper_Html');
						
						$name = JText::_('COM_KB_ANONYMOUS');
						if (!$reply->anonymous) 
						{
							//$xuser =& JUser::getInstance( $reply->created_by );
							$xuser = new Hubzero_User_Profile();
							$xuser->load($reply->created_by);
							if (is_object($xuser) && $xuser->get('name')) 
							{
								$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $reply->created_by) . '">' . $this->escape(stripslashes($xuser->get('name'))) . '</a>';
							}
						}
					?>
					<blockquote cite="c<?php echo $this->replyto->id ?>">
						<p>
							<strong><?php echo $name; ?></strong> 
							@ <span class="time"><?php echo JHTML::_('date', $reply->created, $timeformat, $tz); ?></span> 
							on <span class="date"><?php echo JHTML::_('date', $reply->created, $dateformat, $tz); ?></span>
						</p>
						<p><?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($reply->content), 300, 0); ?></p>
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
						$comment = new Hubzero_Item_Comment($this->database);
					}*/
					?>
					<p class="warning">
						<strong>Note:</strong> You are editing a comment originally posted <br />
						<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, $timeformat, $tz); ?></time></span> 
						<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, $dateformat, $tz); ?></time></span>
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
						<?php
						if (!$this->juser->get('guest')) 
						{
							ximport('Hubzero_Wiki_Editor');
							$editor =& Hubzero_Wiki_Editor::getInstance();
							echo $editor->display('comment[content]', 'commentcontent', $comment->content, 'minimal', '40', '20');
						/*} else {
							$rtrn = JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'#post-comment');
							?>
							<p class="warning">
								You must <a href="/login?return=<?php echo base64_encode($rtrn); ?>">log in</a> to post comments.
							</p>
							<?php
						*/
						}
						?>
					</label>


					<label id="comment-anonymous-label">
					<?php if ($this->params->get('comments_anon', 1)) { ?>
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1"<?php if ($comment->anonymous) { echo ' checked="checked"'; } ?> />
						<?php echo JText::_('Post anonymously'); ?>
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

					<div class="sidenote">
						<p>
							<strong><?php echo JText::_('PLG_COURSES_REVIEWS_KEEP_RELEVANT'); ?></strong>
						</p>
						<p>
							Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="<?php echo JRoute::_('index.php?option=com_wiki&pagename=Help:WikiFormatting'); ?>" class="popup">Wiki syntax</a> is supported.
						</p>
					</div>
				</fieldset>
			</form>
		<!-- </div>/ .subject -->
		<div class="clear"></div>
	</div><!-- / .section -->
	<?php } ?>
<?php } else { ?>
	<p class="warning">
		<?php echo JText::_('PLG_COURSES_REVIEWS_MUST_BE_LOGGED_IN'); ?>
	</p>
<?php } ?>