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

	ximport('Hubzero_User_Profile');
	ximport('Hubzero_User_Profile_Helper');

	$dateformat = '%d %b %Y';
	$timeformat = '%I:%M %p';
	$tz = 0;
	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		$dateformat = 'd M Y';
		$timeformat = 'H:i p';
		$tz = true;
	}

	$juser = JFactory::getUser();

	$cls = isset($this->cls) ? $this->cls : 'odd';

	JPluginHelper::importPlugin('hubzero');
	$dispatcher =& JDispatcher::getInstance();
	$wikiconfig = array(
		'option'   => $this->option,
		'scope'    => '',
		'pagename' => $this->obj->get('alias'),
		'pageid'   => $this->obj->get('id'),
		'filepath' => '',
		'domain'   => '' 
	);
	$result = $dispatcher->trigger('onGetWikiParser', array($wikiconfig, true));
	$p = (is_array($result) && !empty($result)) ? $result[0] : null;

	if (!$this->comment->anonymous && $this->obj->get('created_by') == $this->comment->created_by) 
	{
		$cls .= ' author';
	}

	$xuser = new Hubzero_User_Profile();
	$xuser->load($this->comment->created_by);
	
	$rtrn = $this->url ? $this->url : JRequest::getVar('REQUEST_URI', 'index.php?option=' . $this->option . '&gid=' . $this->obj->get('alias') . '&active=reviews', 'server');
	if (!strstr($rtrn, 'index.php'))
	{
		$rtrn .= '?';
	}
	else 
	{
		$rtrn .= '&';
	}

	switch ($this->comment->rating)
	{
		case 1:   $rating = ' one-stars';   $strs = '&#x272D;&#x2729;&#x2729;&#x2729;&#x2729;'; break;
		case 2:   $rating = ' two-stars';   $strs = '&#x272D;&#x272D;&#x2729;&#x2729;&#x2729;'; break;
		case 3:   $rating = ' three-stars'; $strs = '&#x272D;&#x272D;&#x272D;&#x2729;&#x2729;'; break;
		case 4:   $rating = ' four-stars';  $strs = '&#x272D;&#x272D;&#x272D;&#x272D;&#x2729;'; break;
		case 5:   $rating = ' five-stars';  $strs = '&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;'; break;
		case 0:
		default:  $rating = ' no-stars';    $strs = '&#x2729;&#x2729;&#x2729;&#x2729;&#x2729;'; break;
	}
?>
		<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->id; ?>">
			<p class="comment-member-photo">
				<span class="comment-anchor"><a name="#c<?php echo $this->comment->id; ?>"></a></span>
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($xuser, $this->comment->anonymous); ?>" alt="" />
			</p>
			<div class="comment-content">
				<?php
				if ($this->params->get('comments_votable', 1))
				{
					$view = new Hubzero_Plugin_View(
						array(
							'folder'  => 'courses',
							'element' => 'reviews',
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
					<?php if (!$this->comment->anonymous) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->comment->created_by); ?>">
							<?php echo $this->escape(stripslashes($this->comment->name)); ?>
						</a>
					<?php } else { ?>
						<?php echo JText::_('PLG_COURSES_REVIEWS_ANONYMOUS'); ?>
					<?php } ?>
					</strong> 
					<a class="permalink" href="<?php echo $this->url . '#c' . $this->comment->id; ?>" title="<?php echo JText::_('PLG_COURSES_REVIEWS_PERMALINK'); ?>">
						<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $this->comment->created; ?>"><?php echo JHTML::_('date', $this->comment->created, $timeformat, $tz); ?></time></span> 
						<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $this->comment->created; ?>"><?php echo JHTML::_('date', $this->comment->created, $dateformat, $tz); ?></time></span>
						<?php if ($this->comment->modified && $this->comment->modified != '0000-00-00 00:00:00') { ?>
							&mdash; <?php echo JText::_('Edited'); ?>
							<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $this->comment->created; ?>"><?php echo JHTML::_('date', $this->comment->created, $timeformat, $tz); ?></time></span> 
							<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $this->comment->created; ?>"><?php echo JHTML::_('date', $this->comment->created, $dateformat, $tz); ?></time></span>
						<?php } ?>
					</a>
				</p>
				<div class="comment-body">
					<?php if ($this->comment->rating) { ?>
						<p class="avgrating <?php echo $rating; ?>">
							<?php echo JText::sprintf('%s out of 5 stars', $this->comment->rating); ?>
						</p>
					<?php } ?>
				<?php
				if ($this->comment->state == 3) 
				{
					echo '<p class="warning">' . JText::_('PLG_COURSES_REVIEWS_REPORTED_AS_ABUSIVE') . '</p>';
				} 
				else 
				{
					echo (is_object($p)) ? $p->parse(stripslashes($this->comment->content)) : nl2br($this->escape(stripslashes($this->comment->content)));
				}
				?>
				</div><!-- / .comment-body -->

			<?php if ($this->comment->filename) { ?>
				<div class="attachment">
					<p>Attached file: <?php echo '<a href="' . JURI::base() . 'site' . DS . 'comments' . DS . $this->comment->filename . '" target="_blank">' . $this->comment->filename . '</a>'; ?></p>
				</div>
			<?php } ?>

			<?php if ($this->comment->state != 3) { ?>
				<p class="comment-options">
				<?php if ($this->params->get('access-create-comment') && $this->depth < $this->params->get('comments_depth', 3)) { ?>
					<a class="icon-reply reply" href="<?php echo JRoute::_($rtrn . 'replyto=' . $this->comment->id . '#post-comment'); ?>" rel="comment-form<?php echo $this->comment->id; ?>"><!-- 
						--><?php echo JText::_('PLG_COURSES_REVIEWS_REPLY'); ?><!-- 
					--></a>
				<?php } ?>
					<a class="icon-abuse abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=itemcomment&id=' . $this->comment->id . '&parent=' . $this->obj->id); ?>"><!-- 
						--><?php echo JText::_('PLG_COURSES_REVIEWS_REPORT_ABUSE'); ?><!-- 
					--></a>
				<?php if (($this->params->get('access-edit-comment') && $this->comment->created_by == $juser->get('id')) || $this->params->get('access-manage-comment')) { ?>
					<a class="icon-edit edit" href="<?php echo JRoute::_($rtrn . 'editcomment=' . $this->comment->id . '#post-comment'); ?>"><!-- 
						--><?php echo JText::_('PLG_COURSES_REVIEWS_EDIT'); ?><!-- 
					--></a>
				<?php } ?>
				<?php if (($this->params->get('access-delete-comment') && $this->comment->created_by == $juser->get('id')) || $this->params->get('access-manage-comment')) { ?>
					<a class="icon-delete delete" href="<?php echo JRoute::_($rtrn . 'action=delete&comment=' . $this->comment->id); ?>"><!-- 
						--><?php echo JText::_('PLG_COURSES_REVIEWS_DELETE'); ?><!-- 
					--></a>
				<?php } ?>
				</p><!-- / .comment-options -->
			<?php } ?>

			<?php if ($this->params->get('access-create-comment') && $this->depth < $this->params->get('comments_depth', 3)) { ?>
				<div class="addcomment hide" id="comment-form<?php echo $this->comment->id; ?>">
					<form action="<?php echo JRoute::_($this->url); ?>" method="post" enctype="multipart/form-data">
						<fieldset>
							<legend><span><?php echo JText::sprintf('PLG_COURSES_REVIEWS_REPLYING_TO', (!$this->comment->anonymous ? $this->comment->name : JText::_('PLG_COURSES_REVIEWS_ANONYMOUS'))); ?></span></legend>

							<input type="hidden" name="comment[id]" value="0" />
							<input type="hidden" name="comment[item_id]" value="<?php echo $this->obj->get('id'); ?>" />
							<input type="hidden" name="comment[item_type]" value="<?php echo $this->obj_type; ?>" />
							<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->id; ?>" />
							<input type="hidden" name="comment[created]" value="" />
							<input type="hidden" name="comment[created_by]" value="<?php echo $juser->get('id'); ?>" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="action" value="save" />

							<label for="comment-<?php echo $this->comment->id; ?>-content">
								<span><?php echo JText::_('PLG_COURSES_REVIEWS_ENTER_COMMENTS'); ?></span>
								<textarea name="comment[content]" id="comment-<?php echo $this->comment->id; ?>-content" rows="4" cols="50" placeholder="<?php echo JText::_('PLG_COURSES_REVIEWS_ENTER_COMMENTS'); ?>"></textarea>
							</label>

							<label class="reply-anonymous-label" for="comment-<?php echo $this->comment->id; ?>-anonymous">
						<?php if ($this->params->get('comments_anon', 1)) { ?>
								<input class="option" type="checkbox" name="comment[anonymous]" id="comment-<?php echo $this->comment->id; ?>-anonymous" value="1" /> 
								<?php echo JText::_('PLG_COURSES_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
						<?php } else { ?>
								&nbsp; <input class="option" type="hidden" name="comment[anonymous]" value="0" /> 
						<?php } ?>
							</label>

							<!-- <label for="commentFile">
								<?php echo JText::_('PLG_COURSES_REVIEWS_ATTACH_FILE'); ?>:
								<input type="file" name="commentFile" id="commentFile" />
							</label> -->

							<p class="submit">
								<input type="submit" value="<?php echo JText::_('PLG_COURSES_REVIEWS_POST_COMMENT'); ?>" /> 
								<a class="cancelreply" href="<?php echo JRoute::_($this->url . '#c' . $this->comment->id); ?>">
									<?php echo JText::_('PLG_COURSES_REVIEWS_CANCEL'); ?>
								</a>
							</p>
						</fieldset>
					</form>
				</div><!-- / .addcomment -->
			<?php } ?>
			</div><!-- / .comment-content -->
			<?php
			if ($this->comment->replies) 
			{
				$view = new Hubzero_Plugin_View(
					array(
						'folder'  => 'courses',
						'element' => 'reviews',
						'name'    => 'view',
						'layout'  => 'list'
					)
				);
				$view->option     = $this->option;
				$view->comments   = $this->comment->replies;
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