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

//$assets = array();
//$ids = array();
$likes = 0;
if ($this->rows) 
{
	foreach ($this->rows as $row)
	{
		$likes += $row->item()->get('positive');
		//$ids[] = $row->id;
	}
}

$database = JFactory::getDBO();
$this->juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->name;
?>
<!-- <ul id="page_options">
	<li>
		<a class="board btn" href="<?php echo JRoute::_($base . '&task=boards'); ?>">
			<?php echo JText::_('Collections'); ?>
		</a>
	</li>
</ul> -->

<form method="get" action="<?php echo JRoute::_($base . '&task=board/' . $this->collection->get('alias')); ?>" id="bulletinboard">

	<fieldset class="filters">
		<!-- <label for="fielter-search">Search</label>
		<input type="text" name="search" id="fielter-search" value="<?php //echo $this->escape($this->filters['search']); ?>" />
		<input type="hidden" name="board" value="<?php //echo $this->board->id; ?>" />
		<input type="submit" value="Go" />
		<span class="board count">
			<strong><?php //echo count($this->boards); ?></strong> boards
		</span> -->
		<span class="title count">
			"<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>"
		</span>
		<span class="post count">
			<strong><?php echo $this->rows->total(); ?></strong> posts
		</span>
		<!-- <span class="like count">
			<strong><?php //echo $likes; ?></strong> likes
		</span> -->
<?php if (!$this->juser->get('guest')) { ?>
		<a class="repost btn tooltips" title="Repost :: Repost this collection" href="<?php echo JRoute::_($base . '&task=' . $this->collection->get('alias') . '/repost'); ?>">
			<?php echo JText::_('Repost collection'); ?>
		</a>
	<?php //} ?>
	<?php if ($this->rows && $this->params->get('access-create-item')) { ?>
		<a class="add btn tooltips" title="New post :: Add a new post to this collection" href="<?php echo JRoute::_($base . '&task=post/new&board=' . $this->collection->get('alias')); ?>">
			<?php echo JText::_('New post'); ?>
		</a>
	<?php } //else { ?>
<?php } ?>
		<div class="clear"></div>
	</fieldset>

	<div id="bulletins">
<?php 
if ($this->rows) 
{
	ximport('Hubzero_User_Profile');
	ximport('Hubzero_User_Profile_Helper');

	//$ba = new BulletinboardAsset($database);
	//$assets = $ba->getRecords(array('bulletin_id' => $ids));

	$bt = new CollectionsTags($database);
	//$tags = $bt->getTagsForIds($ids); //$bt->get_tag_cloud(0, 0, $row->id);

	foreach ($this->rows as $row)
	{
		$item = $row->item();

		//$tags = $bt->get_tag_cloud(0, 0, $row->id);

		if ($item->get('state') == 2)
		{
			$item->set('type', 'deleted');
		}
?>
		<div class="bulletin <?php echo $item->get('type'); ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&task=posts/' . $row->get('id')); ?>" data-width="600" data-height="350">
			<div class="content">
<?php
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'members',
				'element' => $this->name,
				'name'    => 'entry',
				'layout'  => '_' . $item->get('type')
			)
		);
		$view->name       = $this->name;
		//$view->juser      = $this->juser;
		$view->option     = $this->option;
		$view->member     = $this->member;
		$view->params     = $this->params;
		//$view->authorized = $this->authorized;
		
		$view->dateFormat = $this->dateFormat;
		$view->timeFormat = $this->timeFormat;
		$view->tz         = $this->tz;
		
		//$view->assets = $item->assets();
		$view->row    = $row;
		$view->board  = $this->collection;
		
		$view->display();
?>
<?php if (count($item->tags()) > 0) { ?>
				<div class="tags-wrap">
					<?php echo $bt->buildCloud($item->tags()); ?>
				</div>
<?php } ?>
				<div class="meta">
					<p class="stats">
						<span class="likes">
							<?php echo JText::sprintf('%s likes', $item->get('positive')); ?>
						</span>
						<span class="comments">
<?php if ($item->get('comments')) { ?>
							<?php echo JText::sprintf('%s comments', $item->get('comments')); ?>
<?php } else { ?>
							<?php echo JText::sprintf('%s comments', 0); ?>
<?php } ?>
						</span>
						<span class="reposts">
<?php if ($item->get('reposts')) { ?>
							<?php echo JText::sprintf('%s reposts', $item->get('reposts')); ?>
<?php } else { ?>
							<?php echo JText::sprintf('%s reposts', 0); ?>
<?php } ?>
						</span>
					</p>
<?php if (!$this->juser->get('guest')) { ?>
					<div class="actions">
<?php if ($item->get('created_by') == $this->juser->get('id')) { ?>
						<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/edit'); ?>">
							<span><?php echo JText::_('Edit'); ?></span>
						</a>
<?php } else { ?>
						<a class="vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->get('id'); ?>" data-text-like="<?php echo JText::_('Like'); ?>" data-text-unlike="<?php echo JText::_('Unlike'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/vote'); ?>">
							<span><?php echo ($item->get('voted')) ? JText::_('Unlike') : JText::_('Like'); ?></span>
						</a>
<?php } ?>
						<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/comment'); ?>">
							<span><?php echo JText::_('Comment'); ?></span>
						</a>
						<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/repost'); ?>">
							<span><?php echo JText::_('Repost'); ?></span>
						</a>
<?php if ($row->get('original') && ($item->get('created_by') == $this->juser->get('id') || $this->params->get('access-delete-item'))) { ?>
						<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/delete'); ?>">
							<span><?php echo JText::_('Delete'); ?></span>
						</a>
<?php } else if ($row->get('created_by') == $this->juser->get('id') || $this->params->get('access-edit-item')) { ?>
						<a class="unpost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/remove'); ?>">
							<span><?php echo JText::_('Remove'); ?></span>
						</a>
<?php } ?>
					</div><!-- / .actions -->
<?php } ?>
				</div><!-- / .meta -->

				<?php $huser = Hubzero_User_Profile::getInstance($item->get('created_by')); ?>
				<div class="convo attribution clearfix">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $item->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $item->get('created_by')); ?>">
							<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>
						</a> 
						posted 
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> <span class="date"><?php echo JHTML::_('date', $item->get('created'), $this->timeFormat, $this->tz); ?></span> 
							<span class="entry-date-on">on</span> <span class="time"><?php echo JHTML::_('date', $item->get('created'), $this->dateFormat, $this->tz); ?></span>
						</span>
					</p>
				</div><!-- / .attribution -->
<?php if ($item->get('created_by') != $this->member->get('uidNumber')) { ?>
				<div class="convo attribution reposted clearfix">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($this->member, 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>">
							<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>
						</a> 
						onto 
						<a href="<?php echo JRoute::_($base . ($this->collection->get('is_default') ? '' : '/' . $this->collection->get('alias'))); ?>">
							<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>
						</a>
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> <span class="date"><?php echo JHTML::_('date', $row->get('created'), $this->timeFormat, $this->tz); ?></span> 
							<span class="entry-date-on">on</span> <span class="time"><?php echo JHTML::_('date', $row->get('created'), $this->dateFormat, $this->tz); ?></span>
						</span>
					</p>
				</div><!-- / .attribution -->
<?php } ?>
			</div><!-- / .content -->
		</div><!-- / .bulletin -->
<?php
	}
}
else
{
?>
		<div id="bb-introduction">
<?php if ($this->params->get('access-create-item')) { ?>
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
					<a class="tooltips" href="<?php echo JRoute::_($base . '&task=post/new&type=image&board=' . $this->collection->get('alias')); ?>" rel="post-image" title="Post an image">Image</a>
				</li>
				<li class="post-file">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&task=post/new&type=file&board=' . $this->collection->get('alias')); ?>" rel="post-file" title="Post a file">File</a>
				</li>
				<li class="post-text">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&task=post/new&type=text&board=' . $this->collection->get('alias')); ?>" rel="post-text" title="Post some text">Text</a>
				</li>
				<li class="post-link">
					<a class="tooltips" href="<?php echo JRoute::_($base . '&task=post/new&type=link&board=' . $this->collection->get('alias')); ?>" rel="post-link" title="Post a link">Link</a>
				</li>
			</ul>
<?php } else { ?>
			<div class="instructions">
				<p>No posts available for this board.</p>
			</div>
<?php } ?>
		</div>
<?php
}
?>
	</div>
</form>