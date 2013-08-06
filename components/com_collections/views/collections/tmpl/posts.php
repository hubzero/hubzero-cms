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
	<h2><?php echo JText::_('Collections'); ?></h2>
</div>

<div id="content-header-extra">
	<ul>
		<li>
			<a class="icon-info about btn" href="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=about'); ?>">
				<span><?php echo JText::_('Getting started'); ?></span>
			</a>
		</li>
	</ul>
</div>

<form method="get" action="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=' . $this->task); ?>" id="collections">
	<fieldset class="filters">
		<div class="filters-inner">
			<ul>
				<li>
					<a class="collections count" href="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=all'); ?>">
						<span><?php echo JText::sprintf('<strong>%s</strong> collections', $this->collections); ?></span>
					</a>
				</li>
				<li>
					<a class="posts count active" href="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=posts'); ?>">
						<span><?php echo JText::sprintf('<strong>%s</strong> posts', $this->total); ?></span>
					</a>
				</li>
			</ul>
			<div class="clear"></div>
			<p>
				<label for="filter-search">
					<span><?php echo JText::_('Search'); ?></span>
					<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Search posts'); ?>" />
				</label>
				<input type="submit" class="filter-submit" value="<?php echo JText::_('Go'); ?>" />
			</p>
		</div><!-- / .filters-inner -->
	</fieldset>

	<div class="main section">
		<div id="posts">
<?php 
if ($this->rows->total() > 0) 
{
	ximport('Hubzero_User_Profile');
	ximport('Hubzero_User_Profile_Helper');

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

	foreach ($this->rows as $row)
	{
		$item = $row->item();

		if ($item->get('state') == 2)
		{
			$item->set('type', 'deleted');
		}
		$type = $item->get('type');
		if (in_array($type, array('image', 'text')))
		{
			$type = 'file';
		}
		if (!in_array($type, array('collection', 'deleted', 'file', 'link')))
		{
			$type = 'link';
		}
?>
		<div class="post <?php echo $type; ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&controller=posts&post=' . $row->get('id')); ?>" data-width="600" data-height="350">
			<div class="content">
				<?php
					$view = new JView(
						array(
							'name'    => 'posts',
							'layout'  => 'display_' . $type
						)
					);
					$view->option     = $this->option;
					$view->params     = $this->config;
					$view->dateFormat = $this->dateFormat;
					$view->timeFormat = $this->timeFormat;
					$view->tz         = $this->tz;
					$view->row        = $row;
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
				<?php if (!$this->juser->get('guest')) { ?>
					<div class="actions">
				<?php if ($row->get('created_by') == $this->juser->get('id')) { ?>
						<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&post=' . $row->get('id') . '&task=edit'); ?>">
							<span><?php echo JText::_('Edit'); ?></span>
						</a>
				<?php } else { ?>
						<a class="vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $item->get('id'); ?>" data-text-like="<?php echo JText::_('Like'); ?>" data-text-unlike="<?php echo JText::_('Unlike'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&post=' . $row->get('id') . '&task=vote'); ?>">
							<span><?php echo ($item->get('voted')) ? JText::_('Unlike') : JText::_('Like'); ?></span>
						</a>
				<?php } ?>
						<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&post=' . $row->get('id') . '&task=comment'); ?>">
							<span><?php echo JText::_('Comment'); ?></span>
						</a>
						<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&post=' . $row->get('id') . '&task=collect'); ?>">
							<span><?php echo JText::_('Collect'); ?></span>
						</a>
				<?php /*if ($row->get('original') && ($row->get('created_by') == $this->juser->get('id') || $this->config->get('access-delete-item'))) { ?>
						<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&post=' . $row->get('id') . '&task=delete'); ?>">
							<span><?php echo JText::_('Delete'); ?></span>
						</a>
				<?php }*/ /*else if ($row->poster == $this->juser->get('id') || $this->config->get('access-edit-bulletin')) { ?>
						<a class="unpost" data-id="<?php echo $row->id; ?>" href="<?php echo JRoute::_($base . '&controller=posts&id=' . $row->post_id . '&task=unpost'); ?>">
							<span><?php echo JText::_('Unpost'); ?></span>
						</a>
				<?php }*/ ?>
					</div><!-- / .actions -->
					<?php } ?>
				</div><!-- / .meta -->
				<div class="convo attribution clearfix">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->creator()->get('uidNumber') . '&active=collections'); ?>" title="<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($row->creator(), 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->creator()->get('uidNumber') . '&active=collections'); ?>">
							<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>
						</a> 
						onto 
						<a href="<?php echo JRoute::_($row->link()); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> <span class="time"><?php echo JHTML::_('date', $row->get('created'), $this->timeFormat, $this->tz); ?></span> 
							<span class="entry-date-on">on</span> <span class="date"><?php echo JHTML::_('date', $row->get('created'), $this->dateFormat, $this->tz); ?></span>
						</span>
					</p>
				</div><!-- / .attribution -->
			</div><!-- / .content -->
		</div><!-- / .post -->
<?php
	}
}
else
{
?>
		<div id="collections-introduction">
	<?php if ($this->config->get('access-create-bulletin')) { ?>
			<div class="instructions">
				<ol>
					<li>Find an image, file, link or text you want to share.</li>
					<li>Click on the appropriate type of post.</li>
					<li>Add anything extra you want (tags are nice).</li>
					<li>Done!</li>
				</ol>
			</div>
	<?php } else { ?>
			<div class="instructions">
				<p>No bulletins available for this board.</p>
			</div>
	<?php } ?>
		</div><!-- / #collections-introduction -->
<?php
}
?>
		</div><!-- / #posts -->
		<?php if ($this->total > $this->filters['limit']) { echo $this->pageNav->getListFooter(); } ?>
		<div class="clear"></div>
	</div><!-- / .main section -->
</form>