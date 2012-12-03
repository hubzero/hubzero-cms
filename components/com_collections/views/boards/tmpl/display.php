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

//import helper class
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Document');

$assets = array();
$ids = array();
$likes = 0;
if ($this->rows) 
{
	foreach ($this->rows as $row)
	{
		$likes += $row->positive;
		$ids[] = $row->id;
	}
}

$database = JFactory::getDBO();
$this->juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option;
?>
<div id="content-header">
	<h2><?php echo JText::_('Bulletin Boards'); ?></h2>
</div>

<div id="content-header-extra">
	<ul>
		<li>
			<a class="board btn" href="<?php echo JRoute::_($base . '&controller=boards'); ?>">
				<span><?php echo JText::_('Boards'); ?></span>
			</a>
		</li>
	</ul>
</div>

<div id="sub-menu">
	<ul>
		<li<?php if ($this->task == 'popular') { echo ' class="active"'; } ?>>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=popular'); ?>">
				<span><?php echo JText::_('Popular posts'); ?></span>
			</a>
		</li>
		<li<?php if ($this->task == 'recent') { echo ' class="active"'; } ?>>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=recent'); ?>">
				<span><?php echo JText::_('Recent posts'); ?></span>
			</a>
		</li>
		<li<?php if ($this->task == 'spotlight') { echo ' class="active"'; } ?>>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=spotlight'); ?>">
				<span><?php echo JText::_('Spotlight'); ?></span>
			</a>
		</li>
	</ul>
	<div class="clear"></div>
</div>

<form method="get" action="<?php echo JRoute::_($base . '&scope=boards/' . $this->board->id); ?>" id="bulletinboard">
	<!-- <fieldset class="filters">
<?php if (!$this->filters['trending']) { ?>
		<span class="post count">
			"<?php echo $this->escape(stripslashes($this->board->title)); ?>" has <strong><?php echo count($this->rows); ?></strong> posts
		</span>
<?php } else { ?>
		<span class="post count">
			<a href="#"><?php echo JText::_('Recent'); ?></a>
		</span>
		<span class="post count">
			<a href="#"><?php echo JText::_('Popular'); ?></a>
		</span>
<?php } ?>
<?php /*if ($this->rows && $this->config->get('access-create-bulletin')) { ?>
		<a class="add btn" href="<?php echo JRoute::_($base . '&scope=posts/new&boards=' . $this->board->id); ?>">
			<?php echo JText::_('New post'); ?>
		</a>
<?php }*/ ?>
		<div class="clear"></div>
	</fieldset> -->
<div class="main section" id="bulletins">
<?php 
if ($this->rows) 
{
	ximport('Hubzero_User_Profile');
	ximport('Hubzero_User_Profile_Helper');

	$ba = new BulletinboardAsset($database);
	$assets = $ba->getRecords(array('bulletin_id' => $ids));

	$bt = new BulletinboardTags($database);
	$tags = $bt->getTagsForIds($ids); //$bt->get_tag_cloud(0, 0, $row->id);

	foreach ($this->rows as $row)
	{
		$huser = Hubzero_User_Profile::getInstance($row->poster);

		//$tags = $bt->get_tag_cloud(0, 0, $row->id);

		if ($row->state == 2)
		{
			$row->type = 'deleted';
		}
?>
		<div class="bulletin <?php echo $row->type; ?>" id="b<?php echo $row->id; ?>" data-id="<?php echo $row->id; ?>" data-closeup-url="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->id); ?>" data-width="600" data-height="350">
			<div class="content">
<?php
		$view = new JView(
			array(
				'name'    => 'posts',
				'layout'  => 'display_' . $row->type
			)
		);
		//$view->juser      = $this->juser;
		$view->option     = $this->option;
		$view->config     = $this->config;
		//$view->authorized = $this->authorized;
		
		$view->dateFormat = $this->dateFormat;
		$view->timeFormat = $this->timeFormat;
		$view->tz         = $this->tz;
		
		$view->assets = $assets;
		$view->row   = $row;
		$view->board = $this->board;
		
		$view->display();
?>
<?php if (isset($tags[$row->id])) { ?>
				<div class="tags-wrap">
					<?php echo $bt->buildCloud($tags[$row->id]); ?>
				</div>
<?php } ?>
				<div class="meta">
					<p class="stats">
						<span class="likes">
							<?php echo JText::sprintf('%s likes', $row->positive); ?>
						</span>
						<span class="comments">
<?php if (isset($row->comments) && $row->comments) { ?>
							<?php echo JText::sprintf('%s comments', $row->comments); ?>
<?php } else { ?>
							<?php echo JText::sprintf('%s comments', 0); ?>
<?php } ?>
						</span>
						<span class="reposts">
<?php if ($row->reposts) { ?>
							<?php echo JText::sprintf('%s reposts', $row->reposts); ?>
<?php } else { ?>
							<?php echo JText::sprintf('%s reposts', 0); ?>
<?php } ?>
						</span>
					</p>
					<div class="actions">
<?php if ($row->created_by == $this->juser->get('id')) { ?>
						<a class="edit" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->post_id . '&task=edit'); ?>">
							<span><?php echo JText::_('Edit'); ?></span>
						</a>
<?php } else { ?>
						<a class="vote <?php echo (isset($row->voted) && $row->voted) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->post_id; ?>" data-text-like="<?php echo JText::_('Like'); ?>" data-text-unlike="<?php echo JText::_('Unlike'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->post_id . '&task=vote'); ?>">
							<span><?php echo (isset($row->voted) && $row->voted) ? JText::_('Unlike') : JText::_('Like'); ?></span>
						</a>
<?php } ?>
						<a class="comment" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->post_id . '&task=comment'); ?>">
							<span><?php echo JText::_('Comment'); ?></span>
						</a>
						<a class="repost" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->post_id . '&task=repost'); ?>">
							<span><?php echo JText::_('Repost'); ?></span>
						</a>
<?php if ($row->original && ($row->created_by == $this->juser->get('id') || $this->config->get('access-delete-bulletin'))) { ?>
						<a class="delete" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->post_id . '&task=delete'); ?>">
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
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->poster); ?>" title="<?php echo $this->escape(stripslashes($row->name)); ?>" class="img-link">
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($row->name)); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->poster); ?>">
							<?php echo $this->escape(stripslashes($row->name)); ?>
						</a> 
						onto 
						<a href="<?php echo JRoute::_($base . '&controller=boards&id=' . $row->board_id); ?>">
							<?php echo $this->escape(stripslashes($row->board_title)); ?>
						</a>
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> <span class="date"><?php echo JHTML::_('date', $row->posted, $this->timeFormat, $this->tz); ?></span> 
							<span class="entry-date-on">on</span> <span class="time"><?php echo JHTML::_('date', $row->posted, $this->dateFormat, $this->tz); ?></span>
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
					<a class="tooltips" href="<?php echo JRoute::_($base . '&controller=posts&task=new&type=image&board=' . $this->board->id); ?>" rel="post-image" title="Post an image">Image</a>
				</li>
				<li class="post-file">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&controller=posts&task=new&type=file&board=' . $this->board->id); ?>" rel="post-file" title="Post a file">File</a>
				</li>
				<li class="post-text">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&controller=posts&task=new&type=text&board=' . $this->board->id); ?>" rel="post-text" title="Post some text">Text</a>
				</li>
				<li class="post-link">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&controller=posts&task=new&type=link&board=' . $this->board->id); ?>" rel="post-link" title="Post a link">Link</a>
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
</form>