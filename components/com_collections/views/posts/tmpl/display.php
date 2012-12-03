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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_User_Profile');
ximport('Hubzero_User_Profile_Helper');

$database = JFactory::getDBO();
$this->juser = JFactory::getUser();
$bt = new BulletinboardTags($database);

$creator = Hubzero_User_Profile::getInstance($this->row->created_by);
$huser = Hubzero_User_Profile::getInstance($this->post->created_by);

$tags = $bt->get_tag_cloud(0, 0, $this->row->id);

$base = 'index.php?option=' . $this->option . '&id=' . $this->juser->get('id') . '&active=bulletinboard';

if ($this->row->state == 2)
{
	$this->row->type = 'deleted';
}
?>
<ul id="page_options">
	<li>
		<a class="board btn" href="<?php echo JRoute::_($base . '&task=boards'); ?>">
			<?php echo JText::_('Boards'); ?>
		</a>
	</li>
</ul>

<div class="bulletin full <?php echo $this->row->type; ?>" id="b<?php echo $this->row->id; ?>" data-id="<?php echo $this->row->id; ?>" data-closeup-url="<?php echo JRoute::_($base . '&task=posts/' . $this->row->id); ?>" data-width="600" data-height="350">
	<div class="content">
		<div class="creator attribution clearfix">
			<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->row->created_by); ?>" title="<?php echo $this->escape(stripslashes($huser->get('name'))); ?>" class="img-link">
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($huser->get('name'))); ?>" />
			</a>
			<p>
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->row->created_by); ?>">
					<?php echo $this->escape(stripslashes($huser->get('name'))); ?>
				</a> created this post
				<br />
				<span class="entry-date">
					<span class="entry-date-at">@</span> <span class="date"><?php echo JHTML::_('date', $this->row->created, $this->timeFormat, $this->tz); ?></span> 
					<span class="entry-date-on">on</span> <span class="time"><?php echo JHTML::_('date', $this->row->created, $this->dateFormat, $this->tz); ?></span>
				</span>
			</p>
		</div><!-- / .attribution -->
<?php
$view = new JView(
	array(
		'name'    => 'posts',
		'layout'  => 'display_' . $this->row->type
	)
);

$view->name       = $this->name;
//$view->juser      = $this->juser;
$view->option     = $this->option;
//$view->member     = $this->member;
$view->config     = $this->config;
//$view->authorized = $this->authorized;

$view->dateFormat = $this->dateFormat;
$view->timeFormat = $this->timeFormat;
$view->tz         = $this->tz;

$view->row   = $this->row;
$view->board = $this->board;

$view->display();
?>
		<div class="meta">
			<p class="stats">
				<span class="likes">
					<?php echo JText::sprintf('%s likes', $this->row->positive); ?>
				</span>
				<span class="comments">
<?php if (isset($this->row->comments) && $this->row->comments) { ?>
					<?php echo JText::sprintf('%s comments', $this->row->comments); ?>
<?php } else { ?>
					<?php echo JText::sprintf('%s comments', 0); ?>
<?php } ?>
				</span>
				<span class="reposts">
<?php if ($this->row->reposts) { ?>
					<?php echo JText::sprintf('%s reposts', $this->row->reposts); ?>
<?php } else { ?>
					<?php echo JText::sprintf('%s reposts', 0); ?>
<?php } ?>
				</span>
			</p>
			<div class="actions">
<?php if ($this->row->created_by == $this->juser->get('id')) { ?>
				<a class="edit" data-id="<?php echo $this->row->id; ?>" href="<?php echo JRoute::_($base . '&task=posts/' . $this->row->id . '/edit'); ?>">
					<span><?php echo JText::_('Edit'); ?></span>
				</a>
<?php } else { ?>
				<a class="vote <?php echo ($this->row->voted) ? 'unlike' : 'like'; ?>" data-id="<?php echo $this->row->id; ?>" data-text-like="<?php echo JText::_('Like'); ?>" data-text-unlike="<?php echo JText::_('Unlike'); ?>" href="<?php echo JRoute::_($base . '&task=posts/' . $this->row->id . '/vote'); ?>">
					<span><?php echo ($this->row->voted) ? JText::_('Unlike') : JText::_('Like'); ?></span>
				</a>
<?php } ?>
				<a class="comment" data-id="<?php echo $this->row->id; ?>" href="<?php echo JRoute::_($base . '&task=posts/' . $this->row->id . '/comment'); ?>">
					<span><?php echo JText::_('Comment'); ?></span>
				</a>
				<a class="repost" data-id="<?php echo $this->row->id; ?>" href="<?php echo JRoute::_($base . '&task=posts/' . $this->row->id . '/repost'); ?>">
					<span><?php echo JText::_('Repost'); ?></span>
				</a>
<?php if ($this->post->original && $this->row->created_by == $this->juser->get('id') || $this->config->get('access-delete-bulletin')) { ?>
				<a class="delete" data-id="<?php echo $this->row->id; ?>" href="<?php echo JRoute::_($base . '&task=posts/' . $this->row->id . '/delete'); ?>">
					<span><?php echo JText::_('Delete'); ?></span>
				</a>
<?php } else if ($this->post->created_by == $this->juser->get('id') || $this->config->get('access-edit-bulletin')) { ?>
				<a class="unpost" data-id="<?php echo $this->row->id; ?>" href="<?php echo JRoute::_($base . '&task=posts/' . $this->row->id . '/unpost'); ?>">
					<span><?php echo JText::_('Unpost'); ?></span>
				</a>
<?php } ?>
			</div><!-- / .actions -->
		</div><!-- / .meta -->
<?php //if ($this->row->created_by != $this->post->created_by) { ?>
		<div class="convo attribution clearfix">
			<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->row->created_by); ?>" title="<?php echo $this->escape(stripslashes($huser->get('name'))); ?>" class="img-link">
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($huser->get('name'))); ?>" />
			</a>
			<p>
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->row->created_by); ?>">
					<?php echo $this->escape(stripslashes($huser->get('name'))); ?>
				</a> 
				onto 
				<a href="<?php echo JRoute::_($base); ?>">
					<?php echo $this->escape(stripslashes($this->board->title)); ?>
				</a>
				<br />
				<span class="entry-date">
					<span class="entry-date-at">@</span> <span class="date"><?php echo JHTML::_('date', $this->post->created, $this->timeFormat, $this->tz); ?></span> 
					<span class="entry-date-on">on</span> <span class="time"><?php echo JHTML::_('date', $this->post->created, $this->dateFormat, $this->tz); ?></span>
				</span>
			</p>
		</div><!-- / .attribution -->
<?php 
if ($this->comments) 
{ 
	foreach ($this->comments as $comment)
	{
		$cuser = Hubzero_User_Profile::getInstance($comment->created_by);
?>
		<div class="commnts">
			<div class="comment convo clearfix" id="c<?php echo $comment->id; ?>">
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $comment->created_by); ?>" class="img-link">
					<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($cuser, $comment->anonymous); ?>" class="profile user_image" alt="Profile picture of <?php echo $this->escape(stripslashes($cuser->get('name'))); ?>" />
				</a>
				<p>
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $comment->created_by); ?>"><?php echo $this->escape(stripslashes($cuser->get('name'))); ?></a> 
					<br />
					<span class="entry-date">
						<span class="entry-date-at">@</span> <span class="date"><?php echo JHTML::_('date', $comment->created, $this->timeFormat, $this->tz); ?></span> 
						<span class="entry-date-on">on</span> <span class="time"><?php echo JHTML::_('date', $comment->created, $this->dateFormat, $this->tz); ?></span>
					</span>
				</p>
				<blockquote>
					<p><?php echo stripslashes($comment->content); ?></p>
				</blockquote>
			</div>
		</div>
<?php 
	}
} 
$now = date('Y-m-d H:i:s', time());
?>
		<div class="commnts">
			<div class="comment convo clearfix" comment-id="84653874123656611">
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id')); ?>" class="img-link">
					<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($this->juser, 0); ?>" class="profile user_image" alt="Profile picture of <?php echo $this->escape(stripslashes($this->juser->get('name'))); ?>" />
				</a>
				<p>
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id')); ?>"><?php echo $this->escape(stripslashes($this->juser->get('name'))); ?></a> 
					<br />
					<span class="entry-date">
						<span class="entry-date-at">@</span> <span class="date"><?php echo JHTML::_('date', $now, $this->timeFormat, $this->tz); ?></span> 
						<span class="entry-date-on">on</span> <span class="time"><?php echo JHTML::_('date', $now, $this->dateFormat, $this->tz); ?></span>
					</span>
				</p>
				<fieldset>
					<textarea name="comment[content]" cols="35" rows="5"></textarea>
				</fieldset>
			</div>
		</div>
	</div><!-- / .content -->
</div><!-- / .bulletin -->