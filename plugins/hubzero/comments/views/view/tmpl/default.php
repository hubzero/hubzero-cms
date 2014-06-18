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

$this->js();
?>

<?php if ($this->params->get('access-view-comment')) { ?>
	<section class="below section">
		<div class="section-inner">
			<div class="subject thread">
				<h3 class="post-comment-title">
					<?php echo JText::_('PLG_HUBZERO_COMMENTS'); ?>
				</h3>
				<?php if ($this->comments) {
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
				} else if ($this->depth <= 1) { ?>
					<p class="no-comments">
						<?php echo JText::_('PLG_HUBZERO_COMMENTS_NO_COMMENTS'); ?>
					</p>
				<?php } ?>

			<?php if ($this->params->get('access-create-comment')) { ?>
				<h3 class="post-comment-title" id="post-comment">
					<?php echo JText::_('PLG_HUBZERO_COMMENTS_POST_A_COMMENT'); ?>
				</h3>
				<form method="post" action="<?php echo JRoute::_($this->url); ?>" id="commentform" enctype="multipart/form-data">
					<p class="comment-member-photo">
						<?php
							$anonymous = 1;
							if (!$this->juser->get('guest'))
							{
								$jxuser = new \Hubzero\User\Profile();
								$jxuser->load($this->juser->get('id'));
								$anonymous = 0;
							}
						?>
						<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($jxuser, $anonymous); ?>" alt="" />
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
									$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $reply->created_by) . '">' . $this->escape(stripslashes($xuser->get('name'))) . '</a>';
								}
							}
							?>
						<blockquote cite="c<?php echo $this->replyto->id ?>">
							<p>
								<strong><?php echo $name; ?></strong>
								<span class="comment-date-at"><?php echo JText::_('COM_ANSWERS_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, JText::_('TIME_FORMAT_HZ1')); ?></time></span>
								<span class="comment-date-on"><?php echo JText::_('COM_ANSWERS_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
							</p>
							<p><?php echo \Hubzero\Utility\String::truncate(stripslashes($reply->content), 300); ?></p>
						</blockquote>
							<?php
						}
					}

					$comment = new \Hubzero\Item\Comment($this->database);
					$comment->parent = JRequest::getInt('replyto', 0);
					if (($edit = JRequest::getInt('editcomment', 0)))
					{
						$comment->load($edit);
					}
					?>
						<label for="commentcontent">
							<?php echo JText::_('PLG_HUBZERO_COMMENTS_YOUR_COMMENTS'); ?>:
							<?php
								if (!$this->juser->get('guest'))
								{
									echo \JFactory::getEditor()->display('comment[content]', '', '', '', 35, 15, false, 'commentcontent', null, null, array('class' => 'minimal no-footer'));
								}
							?>
						</label>

						<label for="commentFile">
							<?php echo JText::_('PLG_HUBZERO_COMMENTS_ATTACH_FILE'); ?>
							<input type="file" name="commentFile" id="commentFile" />
						</label>

						<label id="comment-anonymous-label">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1"<?php if ($comment->anonymous) { echo ' checked="checked"'; } ?> />
							<?php echo JText::_('PLG_HUBZERO_COMMENTS_POST_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" class="btn btn-success" name="submit" value="<?php echo JText::_('PLG_HUBZERO_COMMENTS_POST_COMMENT'); ?>" />
						</p>

						<input type="hidden" name="comment[id]" value="<?php echo $comment->id; ?>" />
						<input type="hidden" name="comment[item_id]" value="<?php echo $this->obj->id; ?>" />
						<input type="hidden" name="comment[item_type]" value="<?php echo $this->obj_type; ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo $comment->parent; ?>" />
						<input type="hidden" name="comment[created_by]" value="<?php echo ($comment->created_by) ? $comment->created_by  : $this->juser->get('id'); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="action" value="save" />

						<?php echo JHTML::_('form.token'); ?>

						<div class="sidenote">
							<p>
								<strong><?php echo JText::_('PLG_HUBZERO_COMMENTS_KEEP_RELEVANT'); ?></strong>
							</p>
							<p>
								<?php echo JText::_('PLG_HUBZERO_COMMENTS_FORMATTING_HELP'); ?>
							</p>
						</div>
					</fieldset>
				</form>
			<?php } ?>
			</div><!-- / .subject -->
			<div class="aside">
			</div><!-- / .aside -->
		</div>
	</section><!-- / .below section -->
<?php } else { ?>
	<p class="warning">
		<?php echo JText::_('PLG_HUBZERO_COMMENTS_MUST_BE_LOGGED_IN'); ?>
	</p>
<?php } ?>