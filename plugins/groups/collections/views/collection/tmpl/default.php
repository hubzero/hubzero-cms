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

$base = 'index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->name;
?>
<ul id="page_options">
	<li>
		<a class="board btn" href="<?php echo JRoute::_($base . '&scope=boards'); ?>">
			<?php echo JText::_('Boards'); ?>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo JRoute::_($base . '&scope=boards/' . $this->board->id); ?>" id="bulletinboard">

	<fieldset class="filters">
		<!-- <label for="fielter-search">Search</label>
		<input type="text" name="search" id="fielter-search" value="<?php echo $this->escape($this->filters['search']); ?>" />
		<input type="hidden" name="board" value="<?php echo $this->board->id; ?>" />
		<input type="submit" value="Go" /> -->
		<span class="post count">
			"<?php echo $this->escape(stripslashes($this->board->title)); ?>" has <strong><?php echo count($this->rows); ?></strong> posts
		</span>
<?php if ($this->rows && $this->params->get('access-create-bulletin')) { ?>
		<a class="add btn" href="<?php echo JRoute::_($base . '&scope=posts/new&boards=' . $this->board->id); ?>">
			<?php echo JText::_('New post'); ?>
		</a>
<?php } ?>
		<div class="clear"></div>
	</fieldset>

	<div id="bulletins">
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
		<div class="bulletin <?php echo $row->type; ?>" id="b<?php echo $row->id; ?>" data-id="<?php echo $row->id; ?>" data-closeup-url="<?php echo JRoute::_($base . '&scope=posts/' . $row->id); ?>" data-width="600" data-height="350">
			<div class="content">
<?php
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'groups',
				'element' => $this->name,
				'name'    => 'entry',
				'layout'  => '_' . $row->type
			)
		);
		$view->name       = $this->name;
		//$view->juser      = $this->juser;
		$view->option     = $this->option;
		$view->group      = $this->group;
		$view->params     = $this->params;
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
						<a class="edit" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&scope=posts/' . $row->id . '/edit'); ?>">
							<span><?php echo JText::_('Edit'); ?></span>
						</a>
<?php } else { ?>
						<a class="vote <?php echo ($row->voted) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->id; ?>" data-text-like="<?php echo JText::_('Like'); ?>" data-text-unlike="<?php echo JText::_('Unlike'); ?>" href="<?php echo JRoute::_($base . '&scope=posts/' . $row->id . '/vote'); ?>">
							<span><?php echo ($row->voted) ? JText::_('Unlike') : JText::_('Like'); ?></span>
						</a>
<?php } ?>
						<a class="comment" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&scope=posts/' . $row->id . '/comment'); ?>">
							<span><?php echo JText::_('Comment'); ?></span>
						</a>
						<a class="repost" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&scope=posts/' . $row->id . '/repost'); ?>">
							<span><?php echo JText::_('Repost'); ?></span>
						</a>
<?php if ($row->original && ($row->created_by == $this->juser->get('id') || $this->params->get('access-delete-bulletin'))) { ?>
						<a class="delete" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&scope=posts/' . $row->id . '/delete'); ?>">
							<span><?php echo JText::_('Delete'); ?></span>
						</a>
<?php } else if ($row->poster == $this->juser->get('id') || $this->params->get('access-edit-bulletin')) { ?>
						<a class="unpost" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&scope=posts/' . $row->post_id . '/unpost'); ?>">
							<span><?php echo JText::_('Unpost'); ?></span>
						</a>
<?php } ?>
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
						<a href="<?php echo JRoute::_($base); ?>">
							<?php echo $this->escape(stripslashes($this->board->title)); ?>
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
<?php if ($this->params->get('access-create-bulletin')) { ?>
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
					<a class="tooltips" href="<?php echo JRoute::_($base . '&scope=posts/new&type=image&board=' . $this->board->id); ?>" rel="post-image" title="Post an image">Image</a>
				</li>
				<li class="post-file">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&scope=posts/new&type=file&board=' . $this->board->id); ?>" rel="post-file" title="Post a file">File</a>
				</li>
				<li class="post-text">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&scope=posts/new&type=text&board=' . $this->board->id); ?>" rel="post-text" title="Post some text">Text</a>
				</li>
				<li class="post-link">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&scope=posts/new&type=link&board=' . $this->board->id); ?>" rel="post-link" title="Post a link">Link</a>
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