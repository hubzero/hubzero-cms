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

	$juser = JFactory::getUser();

	$cls = isset($this->cls) ? $this->cls : 'odd';

	if (!($this->comment instanceof plgHubzeroCommentsModelComment))
	{
		$this->comment = new plgHubzeroCommentsModelComment($this->comment);
	}
	$this->comment->set('option', $this->option);
	$this->comment->set('item_id', $this->obj->id);
	$this->comment->set('item_type', $this->obj_type);

	if (isset($this->obj->created_by) && $this->obj->created_by == $this->comment->get('created_by')) 
	{
		$cls .= ' author';
	}

	$rtrn = $this->url ? $this->url : JRequest::getVar('REQUEST_URI', 'index.php?option=' . $this->option . '&id=' . $this->obj->id . '&active=comments', 'server');

	$this->comment->set('url', $rtrn);
?>
		<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
			<p class="comment-member-photo">
				<span class="comment-anchor"></span>
				<img src="<?php echo $this->comment->creator()->getPicture($xuser, $this->comment->get('anonymous')); ?>" alt="" />
			</p>
			<div class="comment-content">
				<?php
					if ($this->params->get('comments_votable', 1))
					{
						$view = new \Hubzero\Plugin\View(
							array(
								'folder'  => 'hubzero',
								'element' => 'comments',
								'name'    => 'view',
								'layout'  => 'vote'
							)
						);
						$view->option = $this->option;
						$view->item   = $this->comment;
						$view->url    = $this->url;
						$view->display();
					}
				?>

				<p class="comment-title">
					<strong>
					<?php if (!$this->comment->get('anonymous')) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->comment->get('created_by')); ?>">
							<?php echo $this->escape(stripslashes($this->comment->creator('name'))); ?>
						</a>
					<?php } else { ?>
						<?php echo JText::_('PLG_HUBZERO_COMMENTS_ANONYMOUS'); ?>
					<?php } ?>
					</strong> 
					<a class="permalink" href="<?php echo $this->comment->link(); ?>" title="<?php echo JText::_('PLG_HUBZERO_COMMENTS_PERMALINK'); ?>">
						<span class="comment-date-at"><?php echo JText::_('PLG_HUBZERO_COMMENTS_AT'); ?></span> 
						<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span> 
						<span class="comment-date-on"><?php echo JText::_('PLG_HUBZERO_COMMENTS_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
					</a>
				</p>

				<div class="comment-body">
				<?php
					if ($this->comment->isReported()) 
					{
						echo '<p class="warning">' . JText::_('PLG_HUBZERO_COMMENTS_REPORTED_AS_ABUSIVE') . '</p>';
					} 
					else 
					{
						echo $this->comment->content('parsed');
					}
				?>
				</div><!-- / .comment-body -->

			<?php if ($this->comment->get('filename') && !$this->comment->isReported()) : ?>
				<div class="comment-attachment">
					<p><?php echo JText::_('PLG_HUBZERO_COMMENTS_ATTACHED_FILE'); ?> <?php echo '<a href="' . rtrim(JURI::getInstance()->base(true), '/') . 'site/comments/' . $this->escape($this->comment->get('filename')) . '" target="_blank">' . $this->escape($this->comment->get('filename')) . '</a>'; ?></p>
				</div><!-- / .comment-attachment -->
			<?php endif; ?>

			<?php if (!$this->comment->isReported()) { ?>
				<p class="comment-options">
				<?php if ($this->params->get('access-create-comment') && $this->depth < $this->params->get('comments_depth', 3)) { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo JText::_('PLG_HUBZERO_COMMENTS_CANCEL'); ?>" data-txt-inactive="<?php echo JText::_('PLG_HUBZERO_COMMENTS_REPLY'); ?>"href="<?php echo JRoute::_($this->comment->link('reply')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!-- 
						--><?php echo JText::_('PLG_HUBZERO_COMMENTS_REPLY'); ?><!-- 
					--></a>
				<?php } ?>
					<a class="icon-abuse abuse" href="<?php echo JRoute::_($this->comment->link('abuse')); ?>"><!-- 
						--><?php echo JText::_('PLG_HUBZERO_COMMENTS_REPORT_ABUSE'); ?><!-- 
					--></a>
				<?php if (($this->params->get('access-edit-comment') && $this->comment->get('created_by') == $juser->get('id')) || $this->params->get('access-manage-comment')) { ?>
					<a class="icon-edit edit" href="<?php echo JRoute::_($this->comment->link('edit')); ?>"><!-- 
						--><?php echo JText::_('PLG_HUBZERO_COMMENTS_EDIT'); ?><!-- 
					--></a>
				<?php } ?>
				<?php if (($this->params->get('access-delete-comment') && $this->comment->get('created_by') == $juser->get('id')) || $this->params->get('access-manage-comment')) { ?>
					<a class="icon-delete delete" href="<?php echo JRoute::_($this->comment->link('delete')); ?>"><!-- 
						--><?php echo JText::_('PLG_HUBZERO_COMMENTS_DELETE'); ?><!-- 
					--></a>
				<?php } ?>
				</p><!-- / .comment-options -->
			<?php } ?>
			<?php if ($this->params->get('access-create-comment') && $this->depth < $this->params->get('comments_depth', 3)) { ?>
				<div class="addcomment hide" id="comment-form<?php echo $this->comment->get('id'); ?>">
					<form action="<?php echo JRoute::_($this->comment->link('base')); ?>" method="post">
						<fieldset>
							<legend>
								<span><?php echo JText::sprintf('PLG_HUBZERO_COMMENTS_REPLYING_TO', (!$this->comment->get('anonymous') ? $this->comment->get('name') : JText::_('PLG_HUBZERO_COMMENTS_ANONYMOUS'))); ?></span>
							</legend>

							<input type="hidden" name="comment[id]" value="0" />
							<input type="hidden" name="comment[item_id]" value="<?php echo $this->escape($this->comment->get('item_id')); ?>" />
							<input type="hidden" name="comment[item_type]" value="<?php echo $this->escape($this->comment->get('item_type')); ?>" />
							<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('id'); ?>" />
							<input type="hidden" name="comment[created]" value="" />
							<input type="hidden" name="comment[created_by]" value="<?php echo $this->escape($juser->get('id')); ?>" />
							<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
							<input type="hidden" name="action" value="save" />

							<?php echo JHTML::_('form.token'); ?>

							<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
								<span class="label-text"><?php echo JText::_('PLG_HUBZERO_COMMENTS_ENTER_COMMENTS'); ?></span>
								<?php
								echo \JFactory::getEditor()->display('comment[content]', '', '', '', 35, 4, false, 'comment_' . $this->comment->get('id') . '_content', null, null, array('class' => 'minimal no-footer'));
								?>
							</label>

							<label class="reply-anonymous-label" for="comment-<?php echo $this->comment->id; ?>-anonymous">
								<input class="option" type="checkbox" name="comment[anonymous]" id="comment-<?php echo $this->comment->id; ?>-anonymous" value="1" /> 
								<?php echo JText::_('PLG_HUBZERO_COMMENTS_POST_COMMENT_ANONYMOUSLY'); ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo JText::_('PLG_HUBZERO_COMMENTS_POST_COMMENT'); ?>" /> 
							</p>
						</fieldset>
					</form>
				</div><!-- / .addcomment -->
			<?php } ?>
			</div><!-- / .comment-content -->

			<?php
				if ($this->comment->replies) 
				{
					$view = new \Hubzero\Plugin\View(
						array(
							'folder'  => 'hubzero',
							'element' => 'comments',
							'name'    => 'view',
							'layout'  => 'list'
						)
					);
					$view->option     = $this->option;
					$view->comments   = $this->comment->get('replies');
					$view->obj_type   = $this->obj_type;
					$view->obj        = $this->obj;
					$view->params     = $this->params;
					$view->depth      = $this->depth;
					$view->url        = $this->url;
					$view->cls        = $cls;
					$view->display();
				}
			?>
		</li>