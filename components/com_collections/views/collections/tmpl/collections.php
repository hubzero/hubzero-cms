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
	<h2><?php echo JText::_('COM_COLLECTIONS'); ?></h2>
</div>

<div id="content-header-extra">
	<ul>
		<li>
			<a class="icon-info about btn" href="<?php echo JRoute::_($base . '&task=about'); ?>">
				<span><?php echo JText::_('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
			</a>
		</li>
	</ul>
</div>

<form method="get" action="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=' . $this->task); ?>" id="collections">
	<fieldset class="filters">
		<div class="filters-inner">
			<ul>
				<li>
					<a class="collections count active" href="<?php echo JRoute::_($base . '&task=all'); ?>">
						<span><?php echo JText::sprintf('COM_COLLECTIONS_HEADER_NUM_COLLECTIONS', $this->total); ?></span>
					</a>
				</li>
				<li>
					<a class="posts count" href="<?php echo JRoute::_($base . '&task=posts'); ?>">
						<span><?php echo JText::sprintf('COM_COLLECTIONS_HEADER_NUM_POSTS', $this->posts); ?></span>
					</a>
				</li>
			</ul>
			<div class="clear"></div>
			<p>
				<label for="filter-search">
					<span><?php echo JText::_('COM_COLLECTIONS_SEARCH_LABEL'); ?></span>
					<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_COLLECTIONS_SEARCH_PLACEHOLDER'); ?>" />
				</label>
				<input type="submit" class="filter-submit" value="<?php echo JText::_('COM_COLLECTIONS_GO'); ?>" />
			</p>
		</div><!-- / .filters-inner -->
	</fieldset>

	<div class="main section">
		<div id="posts" data-base="<?php echo JURI::base(true); ?>">
<?php 
if ($this->rows->total() > 0) 
{
	foreach ($this->rows as $row)
	{
		$item = $row->item();
?>
		<div class="post <?php echo $item->type(); ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&controller=collection&id=' . $row->get('id')); ?>" data-width="600" data-height="350">
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
							<?php echo JText::sprintf('COM_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)); ?>
						</span>
						<span class="reposts">
							<?php echo JText::sprintf('COM_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)); ?>
						</span>
						<span class="posts">
							<?php echo JText::sprintf('COM_COLLECTIONS_NUM_POSTS', $row->get('posts', 0)); ?>
						</span>
					</p>
				<?php if (!$this->juser->get('guest')) { ?>
					<div class="actions">
						<?php if ($row->get('object_type') == 'member' && $row->get('object_id') == $this->juser->get('id')) { ?>
							<?php //if ($this->juser->get('id') == $item->get('object_id')) { ?>
								<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($row->link() . '/edit'); ?>">
									<span><?php echo JText::_('COM_COLLECTIONS_EDIT'); ?></span>
								</a>
							<?php //} ?>
							<?php //if ($this->juser->get('id') == $item->get('object_id')) { ?>
								<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($row->link() . '/delete'); ?>">
									<span><?php echo JText::_('COM_COLLECTIONS_DELETE'); ?></span>
								</a>
							<?php // } ?>
						<?php } else { ?>
								<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&board=' . $row->get('id') . '&task=collect'); ?>">
									<span><?php echo JText::_('COM_COLLECTIONS_COLLECT'); ?></span>
								</a>
							<?php if ($row->isFollowing()) { ?>
								<a class="unfollow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($row->link() . '/unfollow'); ?>">
									<span><?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?></span>
								</a>
							<?php } else { ?>
								<a class="follow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($row->link() . '/follow'); ?>">
									<span><?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?></span>
								</a>
							<?php } ?>
						<?php } ?>
					</div><!-- / .actions -->
				<?php } ?>
				</div><!-- / .meta -->
				<div class="convo attribution clearfix">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->creator('id') . '&active=collections'); ?>" title="<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo $row->creator()->getPicture(); ?>" alt="<?php echo JText::sprintf('COM_COLLECTIONS_PROFILE_PICTURE', $this->escape(stripslashes($row->creator()->get('name')))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->creator('id') . '&active=collections'); ?>">
							<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>
						</a> 
						<br />
						<span class="entry-date">
							<span class="entry-date-at"><?php echo JText::_('COM_COLLECTIONS_AT'); ?></span> 
							<span class="time"><?php echo JHTML::_('date', $row->get('created'), JText::_('TIME_FORMAT_HZ1')); ?></span> 
							<span class="entry-date-on"><?php echo JText::_('COM_COLLECTIONS_ON'); ?></span> 
							<span class="date"><?php echo JHTML::_('date', $row->get('created'), JText::_('DATE_FORMAT_HZ1')); ?></span>
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
					<li><?php echo JText::_('COM_COLLECTIONS_INSTRUCTIONS_STEP1'); ?></li>
					<li><?php echo JText::_('COM_COLLECTIONS_INSTRUCTIONS_STEP2'); ?></li>
					<li><?php echo JText::_('COM_COLLECTIONS_INSTRUCTIONS_STEP3'); ?></li>
					<li><?php echo JText::_('COM_COLLECTIONS_INSTRUCTIONS_STEP4'); ?></li>
				</ol>
			</ul>
	<?php } else { ?>
			<div class="instructions">
				<p><?php echo JText::_('COM_COLLECTIONS_NO_COLLECTIONS_FOUND'); ?></p>
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