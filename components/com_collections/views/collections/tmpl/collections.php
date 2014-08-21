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

$juser = JFactory::getUser();
$base  = 'index.php?option=' . $this->option;
$mode  = JRequest::getWord('mode', 'grid');

$this->css()
     ->js('jquery.masonry')
     ->js('jquery.infinitescroll')
     ->js();
?>
<header id="content-header">
	<h2><?php echo JText::_('COM_COLLECTIONS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-info btn popup" href="<?php echo JRoute::_('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">
				<span><?php echo JText::_('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
			</a>
		</p>
	</div>
</header>

<form method="get" action="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=' . $this->task); ?>" id="collections">
	<?php
	$this->view('_submenu')
	     ->set('option', $this->option)
	     ->set('active', 'collections')
	     ->set('collections', $this->total)
	     ->set('posts', $this->posts)
	     ->display();
	?>

	<fieldset class="filters">
		<div class="input-group">
			<span class="input-cell">
				<label for="filter-search">
					<span><?php echo JText::_('COM_COLLECTIONS_SEARCH_LABEL'); ?></span>
					<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_COLLECTIONS_SEARCH_PLACEHOLDER'); ?>" />
				</label>
			</span>
			<span class="input-cell">
				<input type="submit" class="btn" value="<?php echo JText::_('COM_COLLECTIONS_GO'); ?>" />
			</span>
		</div>
	</fieldset>

	<section class="main section">
		<?php if ($this->rows->total() > 0) { ?>
			<div id="posts" data-base="<?php echo JURI::base(true); ?>" class="view-as <?php echo $mode; ?>">
				<?php if (!$juser->get('guest') && !JRequest::getInt('no_html', 0)) { ?>
					<div class="post new-post">
						<a class="icon-add add" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=collections&task=new'); ?>">
							<?php echo JText::_('COM_COLLECTIONS_NEW_COLLECTION'); ?>
						</a>
					</div>
				<?php } ?>
				<?php foreach ($this->rows as $row) { ?>
					<div class="post collection" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&controller=collection&id=' . $row->get('id')); ?>">
						<div class="content">
							<?php
								$this->view('display_collection', 'posts')
								     ->set('option', $this->option)
								     ->set('params', $this->config)
								     ->set('row', $row)
								     ->display();
							?>
							<div class="meta">
								<p class="stats">
									<span class="likes">
										<?php echo JText::sprintf('COM_COLLECTIONS_NUM_LIKES', $row->count('likes')); ?>
									</span>
									<?php /*<span class="reposts">
										<?php echo JText::sprintf('COM_COLLECTIONS_NUM_REPOSTS', $row->count('reposts')); ?>
									</span>*/ ?>
									<span class="posts">
										<?php echo JText::sprintf('COM_COLLECTIONS_NUM_POSTS', $row->count('posts')); ?>
									</span>
								</p>
								<div class="actions">
									<?php if (!$juser->get('guest')) { ?>
										<?php if ($row->get('object_type') == 'member' && $row->get('object_id') == $juser->get('id')) { ?>
												<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($row->link() . '/edit'); ?>">
													<span><?php echo JText::_('COM_COLLECTIONS_EDIT'); ?></span>
												</a>
												<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($row->link() . '/delete'); ?>">
													<span><?php echo JText::_('COM_COLLECTIONS_DELETE'); ?></span>
												</a>
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
									<?php } else { ?>
										<a class="repost tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&controller=posts&board=' . $row->get('id') . '&task=collect', false, true)), false); ?>" title="<?php echo JText::_('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
											<span><?php echo JText::_('COM_COLLECTIONS_COLLECT'); ?></span>
										</a>
										<a class="follow tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($row->link() . '/follow')), false); ?>" title="<?php echo JText::_('COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
											<span><?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?></span>
										</a>
									<?php } ?>
								</div><!-- / .actions -->
							</div><!-- / .meta -->
							<div class="convo attribution">
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
					</div><!-- / .post -->
				<?php } ?>
			</div>
			<?php if ($this->total > $this->filters['limit']) { echo $this->pageNav->getListFooter(); } ?>
			<div class="clear"></div>
		<?php } else { ?>
			<div id="collection-introduction">
				<?php if ($this->config->get('access-create-post')) { ?>
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
		<?php } ?>
	</section>
</form>