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

$item = $this->post->item();

$database = JFactory::getDBO();
$this->juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->name;

ximport('Hubzero_Wiki_Parser');

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => 'collections',
	'pagename' => 'collections',
	'pageid'   => 0,
	'filepath' => '',
	'domain'   => 'collection'
);

$p =& Hubzero_Wiki_Parser::getInstance();

if ($item->get('state') == 2)
{
	$item->set('type', 'deleted');
}
?>

<div class="post full <?php echo $item->get('type'); ?>" id="b<?php echo $this->post->get('id'); ?>" data-id="<?php echo $this->post->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&task=post/' . $this->post->get('id')); ?>" data-width="600" data-height="350">
	<div class="content">
		<div class="creator attribution clearfix">
			<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $item->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>" class="img-link">
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($item->creator(), 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>" />
			</a>
			<p>
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $item->get('created_by')); ?>">
					<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>
				</a> created this post
				<br />
				<span class="entry-date">
					<span class="entry-date-at">@</span> <span class="time"><?php echo JHTML::_('date', $item->get('created'), $this->timeFormat, $this->tz); ?></span> 
					<span class="entry-date-on">on</span> <span class="date"><?php echo JHTML::_('date', $item->get('created'), $this->dateFormat, $this->tz); ?></span>
				</span>
			</p>
		</div><!-- / .attribution -->
<?php
$type = $item->get('type');
if (!in_array($type, array('collection', 'deleted', 'image', 'file', 'text', 'link')))
{
	$type = 'link';
}

$view = new Hubzero_Plugin_View(
	array(
		'folder'  => 'members',
		'element' => $this->name,
		'name'    => 'post',
		'layout'  => 'default_' . $type
	)
);

$view->actual = true;
$view->name       = $this->name;
//$view->juser      = $this->juser;
$view->option     = $this->option;
$view->member     = $this->member;
$view->params     = $this->params;
//$view->authorized = $this->authorized;

$view->dateFormat = $this->dateFormat;
$view->timeFormat = $this->timeFormat;
$view->tz         = $this->tz;

$view->row        = $this->post;
//$view->collection = $this->collection;
$view->parser     = $p;
$view->wikiconfig = $wikiconfig;

$view->display();
?>
	<?php if (count($item->tags()) > 0) { ?>
		<div class="tags-wrap">
			<?php echo $item->tags('render'); ?>
		</div>
	<?php } ?>
		<div class="meta">
			<p class="stats">
				<span class="likes">
					<?php echo JText::sprintf('%s likes', $item->get('positive', 0)); ?>
				</span>
				<span class="comments">
					<?php echo JText::sprintf('%s comments', $item->get('comments', 0)); ?>
				</span>
				<span class="reposts">
					<?php echo JText::sprintf('%s reposts', $item->get('reposts', 0)); ?>
				</span>
			</p>
	<?php /*if (!$this->juser->get('guest')) { ?>
			<div class="actions">
		<?php if ($item->get('created_by') == $this->juser->get('id')) { ?>
				<a class="edit" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $this->post->get('id') . '/edit'); ?>">
					<span><?php echo JText::_('Edit'); ?></span>
				</a>
		<?php } else { ?>
				<a class="vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $this->post->get('id'); ?>" data-text-like="<?php echo JText::_('Like'); ?>" data-text-unlike="<?php echo JText::_('Unlike'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $this->post->get('id') . '/vote'); ?>">
					<span><?php echo ($item->get('voted')) ? JText::_('Unlike') : JText::_('Like'); ?></span>
				</a>
		<?php } ?>
				<a class="comment" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $this->post->get('id') . '/comment'); ?>">
					<span><?php echo JText::_('Comment'); ?></span>
				</a>
				<a class="repost" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $this->post->get('id') . '/collect'); ?>">
					<span><?php echo JText::_('Collect'); ?></span>
				</a>
		<?php if ($this->post->get('original') && ($item->get('created_by') == $this->juser->get('id') || $this->params->get('access-delete-item'))) { ?>
				<a class="delete" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $this->post->get('id') . '/delete'); ?>">
					<span><?php echo JText::_('Delete'); ?></span>
				</a>
		<?php } else if ($this->post->get('created_by') == $this->juser->get('id') || $this->params->get('access-edit-item')) { ?>
				<a class="unpost" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $this->post->get('id') . '/remove'); ?>">
					<span><?php echo JText::_('Remove'); ?></span>
				</a>
		<?php } ?>
			</div><!-- / .actions -->
	<?php }*/ ?>
		</div><!-- / .meta -->
<?php //if ($this->post->created_by != $this->post->created_by) { ?>
		<div class="convo attribution clearfix">
			<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->post->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($this->post->creator()->get('name'))); ?>" class="img-link">
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($this->post->creator(), 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($this->post->creator()->get('name'))); ?>" />
			</a>
			<p>
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->post->get('created_by')); ?>">
					<?php echo $this->escape(stripslashes($this->post->creator()->get('name'))); ?>
				</a> 
				onto 
				<a href="<?php echo JRoute::_($base . '&task=' . $this->collection->get('alias')); ?>">
					<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>
				</a>
				<br />
				<span class="entry-date">
					<span class="entry-date-at">@</span> <span class="time"><?php echo JHTML::_('date', $this->post->get('created'), $this->timeFormat, $this->tz); ?></span> 
					<span class="entry-date-on">on</span> <span class="date"><?php echo JHTML::_('date', $this->post->get('created'), $this->dateFormat, $this->tz); ?></span>
				</span>
			</p>
		</div><!-- / .attribution -->
<?php 
if ($item->get('comments')) 
{ 
	?>
		<div class="commnts">
	<?php
	foreach ($item->comments() as $comment)
	{
		$cuser = Hubzero_User_Profile::getInstance($comment->created_by);
?>
		
			<div class="comment convo clearfix" id="c<?php echo $comment->id; ?>">
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $comment->created_by); ?>" class="img-link">
					<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($cuser, $comment->anonymous); ?>" class="profile user_image" alt="Profile picture of <?php echo $this->escape(stripslashes($cuser->get('name'))); ?>" />
				</a>
				<p>
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $comment->created_by); ?>"><?php echo $this->escape(stripslashes($cuser->get('name'))); ?></a> 
					said
					<br />
					<span class="entry-date">
						<span class="entry-date-at">@</span> <span class="time"><?php echo JHTML::_('date', $comment->created, $this->timeFormat, $this->tz); ?></span> 
						<span class="entry-date-on">on</span> <span class="date"><?php echo JHTML::_('date', $comment->created, $this->dateFormat, $this->tz); ?></span>
					</span>
				</p>
				<blockquote>
					<p><?php echo stripslashes($comment->content); ?></p>
				</blockquote>
			</div>
<?php 
	}
	?>
		</div>
	<?php
} 
$now = date('Y-m-d H:i:s', time());
?>
		<div class="commnts">
			<div class="comment convo clearfix">
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id')); ?>" class="img-link">
					<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($this->juser, 0); ?>" class="profile user_image" alt="Profile picture of <?php echo $this->escape(stripslashes($this->juser->get('name'))); ?>" />
				</a>
				<p>
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id')); ?>"><?php echo $this->escape(stripslashes($this->juser->get('name'))); ?></a> 
					will say
					<br />
					<span class="entry-date">
						<span class="entry-date-at">@</span> <span class="time"><?php echo JHTML::_('date', $now, $this->timeFormat, $this->tz); ?></span> 
						<span class="entry-date-on">on</span> <span class="date"><?php echo JHTML::_('date', $now, $this->dateFormat, $this->tz); ?></span>
					</span>
				</p>
				<form action="<?php echo JRoute::_($base . '&task=post/' . $this->post->get('id') . '/savecomment'); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[item_id]" value="<?php echo $item->get('id'); ?>" />
						<input type="hidden" name="comment[item_type]" value="collection" />
						<input type="hidden" name="comment[state]" value="1" />

						<textarea name="comment[content]" cols="35" rows="3"></textarea>
						<input type="submit" class="comment-submit" value="<?php echo JText::_('Save'); ?>" />
					</fieldset>
				</form>
			</div>
		</div>
	</div><!-- / .content -->
</div><!-- / .bulletin -->