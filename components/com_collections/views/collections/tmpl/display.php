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

$likes = 0;
if ($this->rows->total() > 0) 
{
	foreach ($this->rows as $row)
	{
		$likes += $row->get('positive', 0);
	}
}

$this->juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option;
?>
<div id="content-header">
	<h2><?php echo JText::_('Collections'); ?></h2>
</div>

<ul class="sub-menu">
	<li<?php if ($this->task == 'popular') { echo ' class="active"'; } ?>>
		<a href="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=popular'); ?>">
			<span><?php echo JText::_('Popular posts'); ?></span>
		</a>
	</li>
	<li<?php if ($this->task == 'recent') { echo ' class="active"'; } ?>>
		<a href="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=recent'); ?>">
			<span><?php echo JText::_('Recent posts'); ?></span>
		</a>
	</li>
	<li<?php if ($this->task == 'spotlight') { echo ' class="active"'; } ?>>
		<a href="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=spotlight'); ?>">
			<span><?php echo JText::_('Collection Spotlight'); ?></span>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=' . $this->task); ?>" id="collections">
	<div class="main section">
		<div id="posts" data-base="<?php echo JURI::base(true); ?>">
<?php 
if ($this->rows->total() > 0) 
{
	foreach ($this->rows as $row)
	{
		$item = $row->item();
?>
		<div class="post <?php echo $item->get('type'); ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->get('id')); ?>" data-width="600" data-height="350">
			<div class="content">
				<?php
					$view = new JView(
						array(
							'name'    => 'posts',
							'layout'  => 'display_' . $item->type()
						)
					);
					$view->option     = $this->option;
					$view->params     = $this->config;
					$view->row        = $row;
					$view->collection = $this->collection;
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
					<div class="actions">
				<?php if ($row->get('created_by') == $this->juser->get('id')) { ?>
						<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->get('id') . '&task=edit'); ?>">
							<span><?php echo JText::_('Edit'); ?></span>
						</a>
				<?php } else { ?>
						<a class="vote <?php echo ($row->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->get('id'); ?>" data-text-like="<?php echo JText::_('Like'); ?>" data-text-unlike="<?php echo JText::_('Unlike'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->get('id') . '&task=vote'); ?>">
							<span><?php echo ($row->get('voted')) ? JText::_('Unlike') : JText::_('Like'); ?></span>
						</a>
				<?php } ?>
						<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->get('id') . '&task=comment'); ?>">
							<span><?php echo JText::_('Comment'); ?></span>
						</a>
						<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->get('id') . '&task=repost'); ?>">
							<span><?php echo JText::_('Repost'); ?></span>
						</a>
				<?php if ($row->get('original') && ($row->get('created_by') == $this->juser->get('id') || $this->config->get('access-delete-bulletin'))) { ?>
						<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->get('id') . '&task=delete'); ?>">
							<span><?php echo JText::_('Delete'); ?></span>
						</a>
				<?php } /*else if ($row->poster == $this->juser->get('id') || $this->config->get('access-edit-bulletin')) { ?>
						<a class="unpost" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->post_id . '&task=unpost'); ?>">
							<span><?php echo JText::_('Unpost'); ?></span>
						</a>
				<?php }*/ ?>
					</div><!-- / .actions -->
				</div><!-- / .meta -->
				<div class="convo attribution clearfix">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->creator('id') . '&active=collections'); ?>" title="<?php echo $this->escape(stripslashes($row->creator('name'))); ?>" class="img-link">
						<img src="<?php echo $row->creator()->getPicture(); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($row->creator('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->creator('id') . '&active=collections'); ?>">
							<?php echo $this->escape(stripslashes($row->creator('name'))); ?>
						</a> 
						onto 
						<a href="<?php echo JRoute::_($row->link()); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> <span class="time"><?php echo JHTML::_('date', $row->get('created'), JText::_('TIME_FORMAT_HZ1')); ?></span> 
							<span class="entry-date-on">on</span> <span class="date"><?php echo JHTML::_('date', $row->get('created'), JText::_('DATE_FORMAT_HZ1')); ?></span>
						</span>
					</p>
				</div><!-- / .attribution -->
			</div><!-- / .content -->
		</div><!-- / .bulletin -->
<?php
	}
}
else
{
?>
		<div id="bb-introduction">
	<?php if ($this->config->get('access-create-bulletin')) { ?>
			<div class="instructions">
				<ol>
					<li>Find an image, file, link or text you want to share.</li>
					<li>Click on the appropriate type of post.</li>
					<li>Add anything extra you want (tags are nice).</li>
					<li>Done!</li>
				</ol>
			</div>
			<ul class="post-type">
				<li class="post-image">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&controller=posts&task=new&type=image&board=' . $this->collection->get('alias')); ?>" rel="post-image" title="Post an image">Image</a>
				</li>
				<li class="post-file">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&controller=posts&task=new&type=file&board=' . $this->collection->get('alias')); ?>" rel="post-file" title="Post a file">File</a>
				</li>
				<li class="post-text">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&controller=posts&task=new&type=text&board=' . $this->collection->get('alias')); ?>" rel="post-text" title="Post some text">Text</a>
				</li>
				<li class="post-link">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&controller=posts&task=new&type=link&board=' . $this->collection->get('alias')); ?>" rel="post-link" title="Post a link">Link</a>
				</li>
			</ul>
	<?php } else { ?>
			<div class="instructions">
				<p>No bulletins available for this board.</p>
			</div>
	<?php } ?>
		</div>
<?php
}
?>
		</div>
		<?php if ($this->total > $this->filters['limit']) { echo $this->pageNav->getListFooter(); } ?>
		<div class="clear"></div>
	</div>
</form>