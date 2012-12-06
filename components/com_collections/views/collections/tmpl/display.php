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
if ($this->rows->total() > 0) 
{
	foreach ($this->rows as $row)
	{
		$likes += $row->get('positive', 0);
		//$ids[] = $row->id;
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

<form method="get" action="<?php echo JRoute::_($base . '&scope=boards/' . $this->collection->get('alias')); ?>" id="bulletinboard">
	<!-- <fieldset class="filters">
<?php if (!$this->filters['trending']) { ?>
		<span class="post count">
			"<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>" has <strong><?php echo $this->rows->total(); ?></strong> posts
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
if ($this->rows->total() > 0) 
{
	ximport('Hubzero_User_Profile');
	ximport('Hubzero_User_Profile_Helper');

	//$ba = new BulletinboardAsset($database);
	//$assets = $ba->getRecords(array('bulletin_id' => $ids));

	//$bt = new BulletinboardTags($database);
	//$tags = $bt->getTagsForIds($ids); //$bt->get_tag_cloud(0, 0, $row->id);

	foreach ($this->rows as $row)
	{
		$item = $row->item();
		$huser = Hubzero_User_Profile::getInstance($row->get('created_by'));

		//$tags = $bt->get_tag_cloud(0, 0, $row->id);

		if ($item->get('state') == 2)
		{
			$item->set('type', 'deleted');
		}
?>
		<div class="bulletin <?php echo $row->get('type'); ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->get('id')); ?>" data-width="600" data-height="350">
			<div class="content">
<?php
		/*$view = new JView(
			array(
				'name'    => 'posts',
				'layout'  => 'display_' . $item->get('type')
			)
		);
		//$view->juser      = $this->juser;
		$view->option     = $this->option;
		$view->config     = $this->config;
		//$view->authorized = $this->authorized;
		
		$view->dateFormat = $this->dateFormat;
		$view->timeFormat = $this->timeFormat;
		$view->tz         = $this->tz;
		
		//$view->assets = $assets;
		$view->row   = $row;
		$view->collection = $this->collection;
		
		$view->display();*/
?>
<?php if (count($item->tags()) > 0) { ?>
				<div class="tags-wrap">
					<?php echo $item->tags('render'); ?>
				</div>
<?php } ?>
				<div class="meta">
					<p class="stats">
						<span class="likes">
							<?php echo JText::sprintf('%s likes', $row->get('positive', 0)); ?>
						</span>
						<span class="comments">
							<?php echo JText::sprintf('%s comments', $row->get('comments', 0)); ?>
						</span>
						<span class="reposts">
							<?php echo JText::sprintf('%s reposts', $row->get('reposts')); ?>
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
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->creator()->get('id')); ?>" title="<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->creator()->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>
						</a> 
						onto 
						<a href="<?php echo JRoute::_($base . '&controller=boards&id=' . $row->get('collection_id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('board_title'))); ?>
						</a>
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> <span class="date"><?php echo JHTML::_('date', $row->get('created'), $this->timeFormat, $this->tz); ?></span> 
							<span class="entry-date-on">on</span> <span class="time"><?php echo JHTML::_('date', $row->get('created'), $this->dateFormat, $this->tz); ?></span>
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
</form>