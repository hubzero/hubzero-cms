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

$database = JFactory::getDBO();
$this->juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->name;

$this->css()
     ->js();
?>

<ul id="page_options">
	<li>
		<a class="icon-info btn popup" href="<?php echo JRoute::_('index.php?option=com_help&component=collections&page=index'); ?>">
			<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_GETTING_STARTED'); ?></span>
		</a>
	</li>
	<?php if (!$this->juser->get('guest') && !$this->params->get('access-create-item')) { ?>
		<li>
			<?php if ($this->model->isFollowing()) { ?>
				<a class="unfollow btn" data-text-follow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo JRoute::_($base . '&task=unfollow'); ?>">
					<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL'); ?></span>
				</a>
			<?php } else { ?>
				<a class="follow btn" data-text-follow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL'); ?>" data-text-unfollow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_ALL'); ?>" href="<?php echo JRoute::_($base . '&task=follow'); ?>">
					<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_ALL'); ?></span>
				</a>
			<?php } ?>
		</li>
	<?php } ?>
</ul>

<form method="get" action="<?php echo JRoute::_($base . '&task=following'); ?>" id="collections">
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
				<a class="collections count" href="<?php echo JRoute::_($base . '&task=all'); ?>">
					<span><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_COLLECTIONS', $this->collections); ?></span>
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
				<a class="following count active" href="<?php echo JRoute::_($base . '&task=following'); ?>">
					<span><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_FOLLOWNG', $this->rows->total()); ?></span>
				</a>
			</li>
		</ul>
		<div class="clear"></div>
	</fieldset>

	<?php if ($this->rows->total() > 0) { ?>
		<div class="container">
			<table class="following entries">
				<tbody>
					<?php foreach ($this->rows as $row) { ?>
						<tr class="<?php echo $row->get('following_type'); ?>">
							<th>
								<?php if ($row->following()->image()) { ?>
									<img src="<?php echo $row->following()->image(); ?>" width="40" height="40" alt="<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $this->escape(stripslashes($row->following()->title()))); ?>" />
								<?php } else { ?>
									<span class="entry-id">
										<?php echo $row->get('following_id'); ?>
									</span>
								<?php } ?>
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_($row->following()->link()); ?>">
									<?php echo $this->escape(stripslashes($row->following()->title())); ?>
								</a>
								<?php if ($row->get('following_type') == 'collection') { ?>
									<?php echo JText::sprintf('by %s', $this->escape(stripslashes($row->following()->creator('name')))); ?>
								<?php } ?>
								<br />
								<span class="entry-details">
									<span class="follower count"><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWERS', $row->count('followers')); ?></span>
									<?php if ($row->get('following_type') != 'collection') { ?>
										<span class="following count"><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWING', $row->count('following')); ?></span>
									<?php } ?>
								</span>
							</td>
							<td>
								<?php if ($this->params->get('access-manage-collection')) { ?>
									<a class="unfollow btn" data-id="<?php echo $row->get('following_id'); ?>" data-text-follow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($row->following()->link('unfollow')); ?>">
										<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_UNFOLLOW'); ?></span>
									</a>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php echo $this->pageNav->getListFooter(); ?>
			<div class="clear"></div>
		</div><!-- / .container -->
	<?php } else { ?>
		<div id="collection-introduction">
			<?php if ($this->params->get('access-manage-collection')) { ?>
				<div class="instructions">
					<ol>
						<li><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP1', JRoute::_('index.php?option=com_collections')); ?></li>
						<li><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP2'); ?></li>
						<li><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP3'); ?></li>
					</ol>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_IS_FOLLOWING'); ?></strong></p>
					<p><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING_EXPLANATION'); ?></p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p>
						<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_NOT_FOLLOWING_ANYONE'); ?>
					</p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
	<?php } ?>
	<div class="clear"></div>
</form>