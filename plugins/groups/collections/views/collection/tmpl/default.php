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

$this->juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>

<?php if (!$this->juser->get('guest') && !$this->params->get('access-create-item')) { ?>
<ul id="page_options">
	<li>
		<?php if ($this->model->isFollowing()) { ?>
		<a class="icon-unfollow unfollow btn" data-text-follow="<?php echo JText::_('Follow All'); ?>" data-text-unfollow="<?php echo JText::_('Unfollow All'); ?>" href="<?php echo JRoute::_($base . '&scope=unfollow'); ?>">
			<span><?php echo JText::_('Unfollow All'); ?></span>
		</a>
		<?php } else { ?>
		<a class="icon-follow follow btn" data-text-follow="<?php echo JText::_('Follow All'); ?>" data-text-unfollow="<?php echo JText::_('Unfollow All'); ?>" href="<?php echo JRoute::_($base . '&scope=follow'); ?>">
			<span><?php echo JText::_('Follow All'); ?></span>
		</a>
		<?php } ?>
	</li>
</ul>
<?php } ?>

<form method="get" action="<?php echo JRoute::_($base . '&scope=' . $this->collection->get('alias')); ?>" id="collections">

	<p class="overview">
		<span class="title count">
			"<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>" 
		</span>
		<span class="posts count">
			<?php echo JText::sprintf('<strong>%s</strong> posts', $this->rows->total()); ?>
		</span>
<?php if (!$this->juser->get('guest')) { ?>
	<?php if ($this->rows && $this->params->get('access-create-item')) { ?>
		<a class="icon-add add btn tooltips" title="<?php echo JText::_('New post :: Add a new post to this collection'); ?>" href="<?php echo JRoute::_($base . '&scope=post/new&board=' . $this->collection->get('alias')); ?>">
			<span><?php echo JText::_('New post'); ?></span>
		</a>
	<?php } //else { ?>
		<?php if ($this->collection->isFollowing()) { ?>
			<a class="unfollow btn tooltips" data-text-follow="<?php echo JText::_('Follow'); ?>" data-text-unfollow="<?php echo JText::_('Unfollow'); ?>" title="<?php echo JText::_('Unfollow :: Unfollow this collection'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $this->collection->get('alias') . '/unfollow'); ?>">
				<span><?php echo JText::_('Unfollow'); ?></span>
			</a>
		<?php } else { ?>
			<a class="follow btn tooltips" data-text-follow="<?php echo JText::_('Follow'); ?>" data-text-unfollow="<?php echo JText::_('Unfollow'); ?>" title="<?php echo JText::_('Follow :: Follow this collection'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $this->collection->get('alias') . '/follow'); ?>">
				<span><?php echo JText::_('Follow'); ?></span>
			</a>
		<?php } ?>
		<!-- <a class="repost btn tooltips" title="<?php echo JText::_('Repost :: Collect this collection'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $this->collection->get('alias') . '/collect'); ?>">
			<span><?php echo JText::_('Collect'); ?></span>
		</a> -->
	<?php //} ?>
<?php } ?>
		<span class="clear"></span>
	</p>

<?php 
if ($this->rows->total() > 0) 
{
	?>
	<div id="posts">
	<?php
	foreach ($this->rows as $row)
	{
		$item = $row->item();
?>
		<div class="post <?php echo $item->type(); ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id')); ?>" data-width="600" data-height="350">
			<div class="content">
			<?php
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'  => 'groups',
						'element' => $this->name,
						'name'    => 'post',
						'layout'  => 'default_' . $item->type()
					)
				);
				$view->name       = $this->name;
				$view->option     = $this->option;
				$view->group      = $this->group;
				$view->params     = $this->params;
				$view->row        = $row;
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
				<?php if (!$this->juser->get('guest')) { ?>
					<div class="actions">
					<?php if ($item->get('created_by') != $this->juser->get('id')) { ?>
						<a class="vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->get('id'); ?>" data-text-like="<?php echo JText::_('Like'); ?>" data-text-unlike="<?php echo JText::_('Unlike'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/vote'); ?>">
							<span><?php echo ($item->get('voted')) ? JText::_('Unlike') : JText::_('Like'); ?></span>
						</a>
					<?php } ?>
						<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/comment'); ?>">
							<span><?php echo JText::_('Comment'); ?></span>
						</a>
						<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/collect'); ?>">
							<span><?php echo JText::_('Collect'); ?></span>
						</a>
					<?php if ($item->get('created_by') == $this->juser->get('id') || $this->params->get('access-manage-collection')) { ?>
						<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/edit'); ?>" title="<?php echo JText::_('Edit'); ?>">
							<span><?php echo JText::_('Edit'); ?></span>
						</a>
					<?php } ?>
					<?php if ($row->get('original') && ($item->get('created_by') == $this->juser->get('id') || $this->params->get('access-manage-collection'))) { ?>
						<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/delete'); ?>" title="<?php echo JText::_('Delete'); ?>">
							<span><?php echo JText::_('Delete'); ?></span>
						</a>
					<?php } else if ($row->get('created_by') == $this->juser->get('id') || $this->params->get('access-manage-collection')) { ?>
						<a class="unpost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/remove'); ?>">
							<span><?php echo JText::_('Remove'); ?></span>
						</a>
					<?php } ?>
					</div><!-- / .actions -->
				<?php } ?>
				</div><!-- / .meta -->
			<?php /*if ($row->original() || $item->get('created_by') != $this->juser->get('id')) { ?>
				<div class="convo attribution clearfix">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $item->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo $item->creator()->getPicture(); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($item->creator('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $item->get('created_by')); ?>">
							<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>
						</a> 
						posted 
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> 
							<span class="time"><time datetime="<?php echo $item->created(); ?>"><?php echo $item->created('time'); ?></time></span> 
							<span class="entry-date-on">on</span> 
							<span class="date"><time datetime="<?php echo $item->created(); ?>"><?php echo $item->created('date'); ?></time></span>
						</span>
					</p>
				</div><!-- / .attribution -->
			<?php }*/ ?>
			<?php //if (!$row->original()) {//if ($item->get('created_by') != $this->member->get('uidNumber')) { ?>
				<div class="convo attribution reposted clearfix">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($row->creator('name'))); ?>" class="img-link">
						<img src="<?php echo $row->creator()->getPicture(); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($row->creator('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>">
							<?php echo $this->escape(stripslashes($row->creator('name'))); ?>
						</a> 
						onto 
						<a href="<?php echo JRoute::_($base . ($this->collection->get('is_default') ? '' : '/' . $this->collection->get('alias'))); ?>">
							<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>
						</a>
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> 
							<span class="time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span> 
							<span class="entry-date-on">on</span> 
							<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
						</span>
					</p>
				</div><!-- / .attribution -->
			<?php //} ?>
			</div><!-- / .content -->
		</div><!-- / .bulletin -->
	<?php } ?>
	</div><!-- / #posts -->
	<?php if ($this->posts > $this->filters['limit']) { echo $this->pageNav->getListFooter(); } ?>
	<div class="clear"></div>
<?php } else { ?>
		<div id="collection-introduction">
	<?php if ($this->params->get('access-create-item')) { ?>
			<div class="instructions">
				<ol>
					<li><?php echo JText::_('Find images, files, links or text you want to share.'); ?></li>
					<li><?php echo JText::_('Click on "New post" button.'); ?></li>
					<li><?php echo JText::_('Add anything extra you want (tags are nice).'); ?></li>
					<li><?php echo JText::_('Done!'); ?></li>
				</ol>
			</div><!-- / .instructions -->
		<?php } else { ?>
			<div class="instructions">
				<p><?php echo JText::_('No posts available for this collection.'); ?></p>
			</div><!-- / .instructions -->
		<?php } ?>
		</div><!-- / #collection-introduction -->
<?php } ?>
</form>