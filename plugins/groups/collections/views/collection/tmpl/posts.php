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
		<a class="icon-unfollow unfollow btn" data-text-follow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo JRoute::_($base . '&scope=unfollow'); ?>">
			<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL'); ?></span>
		</a>
		<?php } else { ?>
		<a class="icon-follow follow btn" data-text-follow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo JRoute::_($base . '&scope=follow'); ?>">
			<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_FOLLOW_ALL'); ?></span>
		</a>
		<?php } ?>
	</li>
</ul>
<?php } ?>

<form method="get" action="<?php echo JRoute::_($base . '&scope=posts'); ?>" id="collections">

	<fieldset class="filters">
		<ul>
			<li>
				<a class="collections count" href="<?php echo JRoute::_($base . '&scope=all'); ?>">
					<span><?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_STATS_COLLECTIONS', '<strong>' . $this->collections . '</strong>'); ?></span>
				</a>
			</li>
			<li>
				<a class="posts active count" href="<?php echo JRoute::_($base . '&scope=posts'); ?>">
					<span><?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_STATS_POSTS', '<strong>' . $this->posts . '</strong>'); ?></span>
				</a>
			</li>
			<li>
				<a class="followers count" href="<?php echo JRoute::_($base . '&scope=followers'); ?>">
					<span><?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_STATS_FOLLOWERS', '<strong>' . $this->followers . '</strong>'); ?></span>
				</a>
			</li>
		<?php if ($this->params->get('access-can-follow')) { ?>
			<li>
				<a class="following count" href="<?php echo JRoute::_($base . '&scope=following'); ?>">
					<span><?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_STATS_FOLLOWING', '<strong>' . $this->following . '</strong>'); ?></span>
				</a>
			</li>
		<?php } ?>
		</ul>
		<?php if ($this->params->get('access-create-collection')) { ?>
		<p>
			<a class="icon-add add btn tooltips" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_NEW_POST_TITLE'); ?>" href="<?php echo JRoute::_($base . '&scope=post/new'); ?>">
				<?php echo JText::_('PLG_GROUPS_COLLECTIONS_NEW_POST'); ?>
			</a>
		</p>
		<?php } ?>
		<div class="clear"></div>
	</fieldset>

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
		<div class="post <?php echo $item->type(); ?>" id="b<?php echo $row->get('id'); ?>" data-id="<?php echo $row->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&task=post/' . $row->get('id')); ?>" data-width="600" data-height="350">
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
						<?php if (!$this->juser->get('guest')) { ?>
							<?php if ($item->get('created_by') == $this->juser->get('id')) { ?>
									<a class="edit" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/edit'); ?>">
										<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_EDIT'); ?></span>
									</a>
							<?php } else { ?>
									<a class="vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $row->get('id'); ?>" data-text-like="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_LIKE'); ?>" data-text-unlike="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_UNLIKE'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/vote'); ?>">
										<span><?php echo ($item->get('voted')) ? JText::_('PLG_GROUPS_COLLECTIONS_UNLIKE') : JText::_('PLG_GROUPS_COLLECTIONS_LIKE'); ?></span>
									</a>
							<?php } ?>
									<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/comment'); ?>">
										<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COMMENT'); ?></span>
									</a>
									<a class="repost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/collect'); ?>">
										<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
									</a>
							<?php if ($row->get('original') && ($item->get('created_by') == $this->juser->get('id') || $this->params->get('access-delete-item'))) { ?>
									<a class="delete" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/delete'); ?>">
										<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_DELETE'); ?></span>
									</a>
							<?php } else if ($row->get('created_by') == $this->juser->get('id') || $this->params->get('access-edit-item')) { ?>
									<a class="unpost" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/remove'); ?>">
										<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_REMOVE'); ?></span>
									</a>
							<?php } ?>
						<?php } else { ?>
								<a class="vote like tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&scope=post/' . $row->get('id') . '/vote', false, true)), false); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_LIKE'); ?>">
									<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_LIKE'); ?></span>
								</a>
								<a class="comment" data-id="<?php echo $row->get('id'); ?>" href="<?php echo JRoute::_($base . '&scope=post/' . $row->get('id') . '/comment'); ?>">
									<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COMMENT'); ?></span>
								</a>
								<a class="repost tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&scope=post/' . $row->get('id') . '/collect', false, true)), false); ?>" title="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
									<span><?php echo JText::_('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
								</a>
						<?php } ?>
					</div><!-- / .actions -->
				</div><!-- / .meta -->

			<?php /*if ($row->original() || $item->get('created_by') != $this->member->get('uidNumber')) { ?>
				<div class="convo attribution clearfix">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $item->get('created_by')); ?>" title="<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>" class="img-link">
						<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($item->creator(), 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $item->get('created_by')); ?>">
							<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>
						</a>
						onto
						<a href="<?php echo JRoute::_($row->link()); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
						<br />
						<span class="entry-date">
							<span class="entry-date-at">@</span> <span class="date"><time datetime="<?php echo $item->get('created'); ?>"><?php echo JHTML::_('date', $item->get('created'), JText::_('TIME_FORMAT_HZ1')); ?></time></span>
							<span class="entry-date-on">on</span> <span class="time"><time datetime="<?php echo $item->get('created'); ?>"><?php echo JHTML::_('date', $item->get('created'), JText::_('DATE_FORMAT_HZ1')); ?></time></span>
						</span>
					</p>
				</div><!-- / .attribution -->
			<?php } ?>
			<?php if (!$row->original()) {*/ ?>
				<div class="convo attribution reposted clearfix">
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
							<span class="date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date') ?></time></span>
						</span>
					</p>
				</div><!-- / .attribution -->
			<?php //} ?>
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