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

$this->js('jquery.masonry.js', 'com_collections')
     ->js('jquery.infinitescroll.js', 'com_collections')
     ->js();

$juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>

<ul id="page_options">
	<li>
		<a class="icon-info btn popup" href="<?php echo JRoute::_('index.php?option=com_help&component=collections&page=index'); ?>">
			<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_GETTING_STARTED'); ?></span>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo JRoute::_($base . '&scope=' . $this->collection->get('alias', 'posts')); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('option', $this->option)
	     ->set('group', $this->group)
	     ->set('params', $this->params)
	     ->set('name', $this->name)
	     ->set('active', ($this->collection->exists() ? '' : 'posts'))
	     ->set('collections', $this->collections)
	     ->set('posts', $this->posts)
	     ->set('followers', $this->followers) //->set('following', $this->following)
	     ->display();
	?>

	<?php if (!$juser->get('guest') && $this->params->get('access-manage-collection')) { ?>
		<p class="guest-options">
			<a class="icon-config config btn" href="<?php echo JRoute::_($base . '&scope=settings'); ?>">
				<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS'); ?></span>
			</a>
		</p>
	<?php } ?>

	<?php if ($this->collection->exists()) { ?>
		<p class="overview">
			<span class="title count">
				"<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>"
			</span>
			<span class="posts count">
				<?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_STATS_POSTS', '<strong>' . $this->count . '</strong>'); ?>
			</span>
			<?php if (!$juser->get('guest')) { ?>
				<?php if ($this->collection->isFollowing()) { ?>
					<a class="unfollow btn tooltips" data-text-follow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW'); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW_TITLE'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $this->collection->get('alias') . '/unfollow'); ?>">
						<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW'); ?></span>
					</a>
				<?php } else { ?>
					<a class="follow btn tooltips" data-text-follow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW'); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW_TITLE'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $this->collection->get('alias') . '/follow'); ?>">
						<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?></span>
					</a>
				<?php } ?>
				<!-- <a class="repost btn tooltips" title="<?php echo JText::_('Repost :: Collect this collection'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $this->collection->get('alias') . '/collect'); ?>">
					<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
				</a> -->
			<?php } ?>
			<span class="clear"></span>
		</p>
	<?php } ?>

	<?php if ($this->rows->total() > 0) { ?>
		<div id="posts" data-base="<?php echo rtrim(JURI::base(true), '/'); ?>">
			<?php if ($this->params->get('access-create-item') && !JRequest::getInt('no_html', 0)) { ?>
				<div class="post new-post">
					<a class="icon-add add" href="<?php echo JRoute::_($base . '&scope=post/new&board=' . $this->collection->get('alias')); ?>">
						<?php echo JText::_('PLG_GROUPS_COLLECTIONS_NEW_POST'); ?>
					</a>
				</div>
			<?php } ?>
			<?php
			foreach ($this->rows as $row)
			{
				$item = $row->item();
			?>
				<div class="post <?php echo $item->type(); ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id')); ?>">
					<div class="content">
						<?php
							$this->view('default_' . $item->type(), 'post')
							     ->set('name', $this->name)
							     ->set('option', $this->option)
							     ->set('group', $this->group)
							     ->set('params', $this->params)
							     ->set('row', $row)
							     ->display();
						?>
						<?php if (count($item->tags()) > 0) { ?>
							<div class="tags-wrap">
								<?php echo $item->tags('render'); ?>
							</div>
						<?php } ?>
						<div class="meta">
							<p class="stats">
								<span class="likes">
									<?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_POST_LIKES', $item->get('positive', 0)); ?>
								</span>
								<span class="comments">
									<?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_POST_COMMENTS', $item->get('comments', 0)); ?>
								</span>
								<span class="reposts">
									<?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_POST_REPOSTS', $item->get('reposts', 0)); ?>
								</span>
							</p>
							<div class="actions">
								<?php if (!$juser->get('guest')) { ?>
									<?php if ($item->get('created_by') != $juser->get('id')) { ?>
										<a class="vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->get('id'); ?>" data-text-like="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_LIKE'); ?>" data-text-unlike="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNLIKE'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/vote'); ?>">
											<span><?php echo ($item->get('voted')) ? JText::_('PLG_GROUPS_COLLECTIONS_UNLIKE') : JText::_('PLG_GROUPS_COLLECTIONS_LIKE'); ?></span>
										</a>
									<?php } ?>
										<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&scope=post/' . $row->get('id') . '/comment'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COMMENT'); ?></span>
										</a>
										<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/collect'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
										</a>
									<?php if ($item->get('created_by') == $juser->get('id') || $this->params->get('access-manage-collection')) { ?>
										<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/edit'); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_EDIT'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_EDIT'); ?></span>
										</a>
									<?php } ?>
									<?php if ($row->get('original') && ($item->get('created_by') == $juser->get('id') || $this->params->get('access-manage-collection'))) { ?>
										<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/delete'); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_DELETE'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_DELETE'); ?></span>
										</a>
									<?php } else if ($row->get('created_by') == $juser->get('id') || $this->params->get('access-manage-collection')) { ?>
										<a class="unpost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/remove'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_REMOVE'); ?></span>
										</a>
									<?php } ?>
								<?php } else { ?>
										<a class="vote like tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&scope=post/' . $row->get('id') . '/vote', false, true)), false); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_LIKE'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_LIKE'); ?></span>
										</a>
										<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&scope=post/' . $row->get('id') . '/comment'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COMMENT'); ?></span>
										</a>
										<a class="repost tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&scope=post/' . $row->get('id') . '/collect', false, true)), false); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
										</a>
								<?php } ?>
							</div><!-- / .actions -->
						</div><!-- / .meta -->
						<div class="convo attribution reposted">
							<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($row->creator('name'))); ?>" class="img-link">
								<img src="<?php echo $row->creator()->getPicture(); ?>" alt="<?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_PROFILE_PICTURE', $this->escape(stripslashes($row->creator('name')))); ?>" />
							</a>
							<p>
								<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>">
									<?php echo $this->escape(stripslashes($row->creator('name'))); ?>
								</a>
								onto
								<a href="<?php echo JRoute::_($row->link()); ?>">
									<?php echo $this->escape(stripslashes($row->get('title'))); ?>
								</a>
								<br />
								<span class="entry-date">
									<span class="entry-date-at"><?php echo JText::_('PLG_GROUPS_COLLECTIONS_DATE_AT'); ?></span>
									<span class="time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span>
									<span class="entry-date-on"><?php echo JText::_('PLG_GROUPS_COLLECTIONS_DATE_ON'); ?></span>
									<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
								</span>
							</p>
						</div><!-- / .attribution -->
					</div><!-- / .content -->
				</div><!-- / .post -->
			<?php } ?>
		</div><!-- / #posts -->
		<?php if ($this->posts > $this->filters['limit']) { echo $this->pageNav->getListFooter(); } ?>
		<div class="clear"></div>
	<?php } else { ?>
		<div id="collection-introduction">
			<?php if ($this->params->get('access-create-item')) { ?>
				<div class="instructions">
					<ol>
						<li><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP1'); ?></li>
						<li><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP2'); ?></li>
						<li><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP3'); ?></li>
						<li><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP4'); ?></li>
					</ol>
					<div class="new-post">
						<a class="icon-add add" href="<?php echo JRoute::_($base . '&scope=post/new&board=' . $this->collection->get('alias')); ?>">
							<?php echo JText::_('PLG_GROUPS_COLLECTIONS_NEW_POST'); ?>
						</a>
					</div>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_TITLE'); ?></strong></p>
					<p><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_DESC'); ?><p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p><?php echo JText::_('PLG_GROUPS_COLLECTIONS_NO_POSTS_FOUND'); ?></p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
	<?php } ?>
</form>