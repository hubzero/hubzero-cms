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

$base = $this->member->getLink() . '&active=' . $this->name;

if (!$this->collection->get('layout'))
{
	$this->collection->set('layout', 'grid');
}
$viewas = JRequest::getWord('viewas', $this->collection->get('layout'));

$this->css()
     ->js('jquery.masonry', 'com_collections')
     ->js('jquery.infinitescroll', 'com_collections')
     ->js();
?>

<ul id="page_options">
	<li>
		<a class="icon-info btn popup" href="<?php echo JRoute::_('index.php?option=com_help&component=collections&page=index'); ?>">
			<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_GETTING_STARTED'); ?></span>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo JRoute::_($base . '&task=' . $this->collection->get('alias')); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('params', $this->params)
	     ->set('option', $this->option)
	     ->set('member', $this->member)
	     ->set('name', $this->name)
	     ->set('active', ($this->collection->exists() ? '' : 'posts'))
	     ->set('collections', $this->collections)
	     ->set('posts', $this->posts)
	     ->set('followers', $this->followers)
	     ->set('following', $this->following)
	     ->display();
	?>

	<?php if ($this->collection->exists()) { ?>
		<p class="overview">
			<span class="title count">
				"<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>"
			</span>
			<span class="posts count">
				<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_POSTS', $this->total); ?>
			</span>
			<?php if (!$this->juser->get('guest')) { ?>
				<?php if (!$this->params->get('access-create-item')) { ?>
					<?php if ($this->collection->isFollowing()) { ?>
						<a class="unfollow btn tooltips" data-text-follow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?>" data-text-unfollow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?>" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_TITLE'); ?>" href="<?php echo JRoute::_($base . '&task=' . $this->collection->get('alias') . '/unfollow'); ?>">
							<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?></span>
						</a>
					<?php } else { ?>
						<a class="follow btn tooltips" data-text-follow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?>" data-text-unfollow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?>" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_TITLE'); ?>" href="<?php echo JRoute::_($base . '&task=' . $this->collection->get('alias') . '/follow'); ?>">
							<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?></span>
						</a>
					<?php } ?>
					<a class="repost btn tooltips" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COLLECT_TITLE'); ?>" href="<?php echo JRoute::_($base . '&task=' . $this->collection->get('alias') . '/collect'); ?>">
						<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
					</a>
				<?php } ?>
			<?php } ?>
			<span class="view-options">
				<a href="<?php echo JRoute::_($base . '&viewas=grid'); ?>" class="icon-grid<?php if ($viewas == 'grid') { echo ' selected'; } ?>" data-view="view-grid" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_GRID_VIEW'); ?>"><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_GRID_VIEW'); ?></a>
				<a href="<?php echo JRoute::_($base . '&viewas=list'); ?>" class="icon-list<?php if ($viewas == 'list') { echo ' selected'; } ?>" data-view="view-list" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_LIST_VIEW'); ?>"><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_LIST_VIEW'); ?></a>
			</span>
		</p>
	<?php } ?>

	<?php if ($this->rows->total() > 0) { ?>
		<div id="posts" data-base="<?php echo rtrim(JURI::base(true), '/'); ?>" data-update="<?php echo JRoute::_('index.php?option=com_collections&controller=posts&task=reorder&' . JUtility::getToken() . '=1'); ?>" class="view-<?php echo $viewas; ?>">
			<?php if ($this->params->get('access-create-collection') && !JRequest::getInt('no_html', 0)) { ?>
				<div class="post new-post" id="post_0">
					<a class="icon-add add" href="<?php echo JRoute::_($base . '&task=post/new&board=' . $this->collection->get('alias')); ?>">
						<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_NEW_POST'); ?>
					</a>
				</div>
			<?php } ?>
		<?php
		foreach ($this->rows as $row)
		{
			$item = $row->item();
			?>
			<div class="post <?php echo $item->type(); ?>" id="post_<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&task=post/' . $row->get('id')); ?>">
				<div class="content">
					<?php if (!$this->juser->get('guest') && $this->params->get('access-create-item')) { ?>
						<div class="sort-handle tooltips" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_GRAB_TO_REORDER'); ?>"></div>
					<?php } ?>
					<?php
						$this->view('default_' . $item->type(), 'post')
						     ->set('name', $this->name)
						     ->set('option', $this->option)
						     ->set('member', $this->member)
						     ->set('params', $this->params)
						     ->set('row', $row)
						     ->display();
					?>
					<?php if ($tags = $item->tags('cloud')) { ?>
						<div class="tags-wrap">
							<?php echo $tags; ?>
						</div>
					<?php } ?>
					<div class="meta" data-metadata-url="<?php echo JRoute::_('index.php?option=com_collections&controller=posts&task=metadata&post=' . $row->get('id')); ?>">
						<p class="stats">
							<span class="likes">
								<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)); ?>
							</span>
							<span class="comments">
								<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)); ?>
							</span>
							<span class="reposts">
								<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)); ?>
							</span>
						</p>
						<div class="actions">
							<?php if (!$this->juser->get('guest')) { ?>
								<?php //if ($item->get('created_by') == $this->juser->get('id')) { ?>
								<?php if ($row->get('created_by') == $this->juser->get('id')) { ?>
									<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/edit'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_EDIT'); ?></span>
									</a>
								<?php } else { ?>
									<a class="vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->get('id'); ?>" data-text-like="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_LIKE'); ?>" data-text-unlike="<?php echo JText::_('Unlike'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/vote'); ?>">
										<span><?php echo ($item->get('voted')) ? JText::_('PLG_MEMBERS_COLLECTIONS_UNLIKE') : JText::_('PLG_MEMBERS_COLLECTIONS_LIKE'); ?></span>
									</a>
								<?php } ?>
									<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&task=post/' . $row->get('id') . '/comment'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
									</a>
									<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/collect'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
									</a>
								<?php if ($row->get('original') && ($item->get('created_by') == $this->juser->get('id') || $this->params->get('access-delete-item'))) { ?>
									<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/delete'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_DELETE'); ?></span>
									</a>
								<?php } else if ($row->get('created_by') == $this->juser->get('id') || $this->params->get('access-edit-item')) { ?>
									<a class="unpost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $row->get('id') . '/remove'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_REMOVE'); ?></span>
									</a>
								<?php } ?>
							<?php } else { ?>
									<a class="vote like tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&task=post/' . $row->get('id') . '/vote', false, true)), false); ?>" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_LIKE'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_LIKE'); ?></span>
									</a>
									<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&task=post/' . $row->get('id') . '/comment'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
									</a>
									<a class="repost tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&task=post/' . $row->get('id') . '/collect', false, true)), false); ?>" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
									</a>
							<?php } ?>
						</div><!-- / .actions -->
					</div><!-- / .meta -->
					<div class="convo attribution reposted">
						<?php
						$name = $this->escape(stripslashes($row->creator('name')));
						if ($row->creator('public')) { ?>
							<a href="<?php echo JRoute::_($row->creator()->getLink()); ?>" title="<?php echo $name; ?>" class="img-link">
								<img src="<?php echo $row->creator()->getPicture(); ?>" alt="<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</a>
						<?php } else { ?>
							<span class="img-link">
								<img src="<?php echo $row->creator()->getPicture(); ?>" alt="<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</span>
						<?php } ?>
						<p>
							<?php if ($row->creator('public')) { ?>
								<a href="<?php echo JRoute::_($row->creator()->getLink()); ?>">
									<?php echo $name; ?>
								</a>
							<?php } else { ?>
								<?php echo $name; ?>
							<?php } ?>
							<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_ONTO'); ?>
							<a href="<?php echo JRoute::_($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->get('title'))); ?>
							</a>
							<br />
							<span class="entry-date">
								<span class="entry-date-at"><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span>
								<span class="entry-date-on"><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
							</span>
						</p>
					</div><!-- / .attribution -->

				</div><!-- / .content -->
			</div><!-- / .post -->
		<?php } ?>
		</div><!-- / #posts -->
		<?php if ($this->total > $this->filters['limit']) { echo $this->pageNav->getListFooter(); } ?>
		<div class="clear"></div>
	<?php } else { ?>
		<div id="collection-introduction">
			<?php if ($this->params->get('access-create-item')) { ?>
				<div class="instructions">
					<ol>
						<li><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP1'); ?></li>
						<li><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP2'); ?></li>
						<li><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP3'); ?></li>
						<li><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP4'); ?></li>
					</ol>
					<div class="new-post">
						<a class="icon-add add" href="<?php echo JRoute::_($base . '&task=post/new&board=' . $this->collection->get('alias')); ?>">
							<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_NEW_POST'); ?>
						</a>
					</div>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_WHAT_IS_POST'); ?></strong></p>
					<p><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_WHAT_IS_POST_EXPLANATION'); ?><p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_EMPTY_COLLECTION'); ?></p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
	<?php } ?>
</form>