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

$base = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->name;

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
<?php if (!$this->juser->get('guest') && !$this->params->get('access-create-collection')) { ?>
	<li>
		<?php if ($this->model->isFollowing()) { ?>
			<a class="unfollow btn" data-text-follow="<?php echo JText::_('Follow All'); ?>" data-text-unfollow="<?php echo JText::_('Unfollow All'); ?>" href="<?php echo JRoute::_($base . '&task=unfollow'); ?>">
				<span><?php echo JText::_('Unfollow All'); ?></span>
			</a>
		<?php } else { ?>
			<a class="follow btn" data-text-follow="<?php echo JText::_('Follow All'); ?>" data-text-unfollow="<?php echo JText::_('Unfollow All'); ?>" href="<?php echo JRoute::_($base . '&task=follow'); ?>">
				<span><?php echo JText::_('Follow All'); ?></span>
			</a>
		<?php } ?>
	</li>
<?php } ?>
</ul>

<form method="get" action="<?php echo JRoute::_($base); ?>" id="collections">
	<fieldset class="filters">
		<ul>
		<?php if ($this->params->get('access-manage-collection')) { ?>
			<li>
				<a class="livefeed tooltips" href="<?php echo JRoute::_($base); ?>" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FEED_TITLE'); ?>">
					<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FEED'); ?></span>
				</a>
			</li>
		<?php } ?>
			<li>
				<a class="collections count active" href="<?php echo JRoute::_($base . '&task=all'); ?>">
					<span><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_COLLECTIONS', $this->rows->total()); ?></span>
				</a>
			</li>
			<li>
				<a class="posts count" href="<?php echo JRoute::_($base . '&task=posts'); ?>">
					<span><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_POSTS', $this->posts); ?></span>
				</a>
			</li>
			<li>
				<a class="followers count" href="<?php echo JRoute::_($base . '&task=followers'); ?>">
					<span><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_FOLLOWERS', $this->followers); ?></span>
				</a>
			</li>
			<li>
				<a class="following count" href="<?php echo JRoute::_($base . '&task=following'); ?>">
					<span><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_FOLLOWNG', $this->following); ?></span>
				</a>
			</li>
		</ul>
	<?php if (!$this->juser->get('guest')) { ?>
		<?php if ($this->params->get('access-create-collection')) { ?>
		<p>
			<a class="icon-add add btn" href="<?php echo JRoute::_($base . '&task=new'); ?>">
				<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_NEW_COLLECTION'); ?></span>
			</a>
		</p>
		<?php } ?>
	<?php } ?>
		<div class="clear"></div>
	</fieldset>

<?php if ($this->rows->total() > 0) { ?>
	<div id="posts">
	<?php foreach ($this->rows as $row) { ?>
		<div class="post collection <?php echo ($row->get('access') == 4) ? 'private' : 'public'; echo ($row->get('is_default')) ? ' default' : ''; ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>">
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
							<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_LIKES', $row->get('positive', 0)); ?>
						</span>
						<span class="reposts">
							<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_POSTS', $row->get('posts', 0)); ?>
						</span>
					</p>
					<div class="actions">
						<?php if (!$this->juser->get('guest')) { ?>
							<?php if ($row->get('object_type') == 'member' && $row->get('object_id') == $this->juser->get('id')) { ?>
								<?php if ($this->params->get('access-edit-collection')) { ?>
									<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=' . $row->get('alias') . '/edit'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_EDIT'); ?></span>
									</a>
								<?php } ?>
								<?php if ($this->params->get('access-delete-collection')) { //!$row->get('is_default') && ?>
									<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=' . $row->get('alias') . '/delete'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_DELETE'); ?></span>
									</a>
								<?php } ?>
							<?php } else { ?>
									<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&task=' . $row->get('alias') . '/collect'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
									</a>
								<?php if ($row->isFollowing()) { ?>
									<a class="unfollow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($base . '&task=' . $row->get('alias') . '/unfollow'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?></span>
									</a>
								<?php } else { ?>
									<a class="follow" data-id="<?php echo $row->get('id'); ?>" data-text-follow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($base . '&task=' . $row->get('alias') . '/follow'); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?></span>
									</a>
								<?php } ?>
							<?php } ?>
						<?php } else { ?>
							<a class="repost tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&task=' . $row->get('alias') . '/collect', false, true)), false); ?>" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
								<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
							</a>
							<a class="follow tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&task=' . $row->get('alias') . '/follow', false, true)), false); ?>" title="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
								<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?></span>
							</a>
						<?php } ?>
					</div><!-- / .actions -->
				</div><!-- / .meta -->
				<?php if ($row->get('object_type') == 'member' && $row->get('object_id') != $this->juser->get('id')) { ?>
				<div class="convo attribution clearfix">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($row->creator(), 0); ?>" alt="<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $this->escape(stripslashes($row->creator()->get('name')))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>">
							<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>
						</a>
						<br />
						<span class="entry-date">
							<span class="entry-date-at"><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_AT'); ?></span>
							<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span>
							<span class="entry-date-on"><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_ON'); ?></span>
							<span class="time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
						</span>
					</p>
				</div><!-- / .attribution -->
				<?php } ?>
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
					<li><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COLLECTION_INSTRUCTIONS_STEP1'); ?></li>
					<li><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COLLECTION_INSTRUCTIONS_STEP2'); ?></li>
					<li><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_COLLECTION_INSTRUCTIONS_STEP3'); ?></li>
				</ol>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_WHAT_IS_COLLECTION'); ?></strong></p>
				<p><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_WHAT_IS_COLLECTION_EXPLANATION'); ?></p>
			</div>
		<?php } else { ?>
			<div class="instructions">
				<p><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_NONE'); ?></p>
			</div><!-- / .instructions -->
		<?php } ?>
		</div><!-- / #collection-introduction -->
<?php } ?>
</form>