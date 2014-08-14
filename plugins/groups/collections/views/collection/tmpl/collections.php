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

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>

<ul id="page_options">
	<li>
		<a class="icon-info btn popup" href="<?php echo JRoute::_('index.php?option=com_help&component=collections&page=index'); ?>">
			<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_GETTING_STARTED'); ?></span>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo JRoute::_($base); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('option', $this->option)
	     ->set('group', $this->group)
	     ->set('params', $this->params)
	     ->set('name', $this->name)
	     ->set('active', 'collections')
	     ->set('collections', $this->rows->total())
	     ->set('posts', $this->posts)
	     ->set('followers', $this->followers)
	     ->set('following', ($this->params->get('access-can-follow') ? $this->following : 0))
	     ->display();
	?>

	<?php if (!$this->juser->get('guest')) { ?>
		<p class="guest-options">
			<?php if ($this->params->get('access-manage-collection')) { ?>
				<a class="icon-config config btn" href="<?php echo JRoute::_($base . '&scope=settings'); ?>">
					<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_SETTINGS'); ?></span>
				</a>
			<?php } ?>

			<?php if ($this->model->isFollowing()) { ?>
				<a class="icon-unfollow unfollow btn" data-text-follow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo JRoute::_($base . '&scope=unfollow'); ?>">
					<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL'); ?></span>
				</a>
			<?php } else { ?>
				<a class="icon-follow follow btn" data-text-follow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo JRoute::_($base . '&scope=follow'); ?>">
					<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL'); ?></span>
				</a>
			<?php } ?>
		</p>
	<?php } ?>

	<?php if ($this->rows->total() > 0) { ?>
		<div id="posts" data-base="<?php echo rtrim(JURI::base(true), '/'); ?>">
			<?php if (!$this->juser->get('guest')) { ?>
				<?php if ($this->params->get('access-create-collection') && !JRequest::getInt('no_html', 0)) { ?>
					<div class="post new-collection">
						<a class="add" href="<?php echo JRoute::_($base . '&scope=new'); ?>">
							<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION'); ?></span>
						</a>
					</div>
				<?php } ?>
			<?php } ?>
			<?php foreach ($this->rows as $row) { ?>
				<div class="post collection <?php echo ($row->get('access') == 4) ? 'private' : 'public'; ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>">
					<div class="content">
						<?php
							$this->view('default_collection', 'post')
							     ->set('row', $row)
							     ->set('collection', $row)
							     ->display();
						?>
						<div class="meta">
							<p class="stats">
								<span class="likes">
									<?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_POST_LIKES', $row->get('positive', 0)); ?>
								</span>
								<span class="reposts">
									<?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_POST_POSTS', $row->get('posts', 0)); ?>
								</span>
							</p>
							<div class="actions">
								<?php if (!$this->juser->get('guest')) { ?>
									<?php if ($row->isFollowing()) { ?>
										<a class="unfollow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $row->get('alias') . '/unfollow'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW'); ?></span>
										</a>
									<?php } else { ?>
										<a class="follow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $row->get('alias') . '/follow'); ?>">
											<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?></span>
										</a>
									<?php } ?>
									<?php if ($this->params->get('access-manage-collection')) { ?>
										<?php if ($this->params->get('access-edit-collection')) { ?>
											<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $row->get('alias') . '/edit'); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_EDIT'); ?>">
												<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_EDIT'); ?></span>
											</a>
										<?php } ?>
										<?php if ($this->params->get('access-delete-collection')) { ?>
											<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $row->get('alias') . '/delete'); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_DELETE'); ?>">
												<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_DELETE'); ?></span>
											</a>
										<?php } ?>
									<?php } else { ?>
											<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $row->get('alias') . '/collect'); ?>">
												<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
											</a>
									<?php } ?>
								<?php } else { ?>
									<a class="repost tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&scope=' . $row->get('alias') . '/collect', false, true)), false); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
										<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
									</a>
									<a class="follow tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&scope=' . $row->get('alias') . '/follow', false, true)), false); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
										<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW'); ?></span>
									</a>
								<?php } ?>
							</div><!-- / .actions -->
						</div><!-- / .meta -->
					</div><!-- / .content -->
				</div><!-- / .post -->
			<?php } ?>
		</div><!-- / #posts -->
		<?php if ($this->total > $this->filters['limit']) { echo $this->pageNav->getListFooter(); } ?>
		<div class="clear"></div>
	<?php } else { ?>
		<div id="collection-introduction">
			<?php if ($this->params->get('access-create-collection')) { ?>
				<div class="instructions">
					<ol>
						<li><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_STEP1'); ?></li>
						<li><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_STEP2'); ?></li>
						<li><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_STEP3'); ?></li>
					</ol>
					<div class="new-collection">
						<a class="add" href="<?php echo JRoute::_($base . '&scope=new'); ?>">
							<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION'); ?></span>
						</a>
					</div>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_ABOUT_TITLE'); ?></strong></p>
					<p><?php echo JText::_('PLG_GROUPS_COLLECTIONS_INSTRUCT_COLLECTION_ABOUT_DESC'); ?></p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p><?php echo JText::_('PLG_GROUPS_COLLECTIONS_NO_COLLECTIONS_FOUND'); ?></p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
	<?php } ?>
</form>