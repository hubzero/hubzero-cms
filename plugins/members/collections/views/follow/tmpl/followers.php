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
     ->js();
?>

<ul id="page_options">
	<li>
		<a class="icon-info btn popup" href="<?php echo JRoute::_('index.php?option=com_help&component=collections&page=index'); ?>">
			<span><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_GETTING_STARTED'); ?></span>
		</a>
	</li>
</ul>

<form method="get" action="<?php echo JRoute::_($base . '&task=followers'); ?>" id="collections">
	<?php
	$this->view('_submenu', 'collection')
	     ->set('option', $this->option)
	     ->set('member', $this->member)
	     ->set('params', $this->params)
	     ->set('name', $this->name)
	     ->set('active', 'followers')
	     ->set('collections', $this->collections)
	     ->set('posts', $this->posts)
	     ->set('followers', $this->total)
	     ->set('following', $this->following)
	     ->display();
	?>

	<?php if ($this->rows->total() > 0) { ?>
		<div class="container">
			<table class="followers entries">
				<caption>
					<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOWING_YOU'); ?>
				</caption>
				<tbody>
				<?php foreach ($this->rows as $row) { ?>
					<tr class="<?php echo $row->get('follower_type'); ?>">
						<th class="entry-img">
							<img src="<?php echo $row->follower()->image(); ?>" width="40" height="40" alt="<?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $this->escape(stripslashes($row->follower()->title()))); ?>" />
						</th>
						<td>
							<a class="entry-title" href="<?php echo JRoute::_($row->follower()->link()); ?>">
								<?php echo $this->escape(stripslashes($row->follower()->title())); ?>
							</a>
							<br />
							<span class="entry-details">
								<span class="follower count"><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWERS', $row->count('followers')); ?></span>
								<span class="following count"><?php echo JText::sprintf('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWING', $row->count('following')); ?></span>
							</span>
						</td>
						<td>
							<time datetime="<?php echo $row->get('created'); ?>"><?php echo JHTML::_('date', $row->get('created'), JText::_('DATE_FORMAT_HZ1')); ?></time>
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
					<p><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOWING_YOU_NONE'); ?></p>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_ARE_FOLLOWERS'); ?></strong></p>
					<p><?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_ARE_FOLLOWERS_EXPLANATION'); ?><p>
				</div>
			<?php } else { ?>
				<div class="instructions">
					<p>
						<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_FOLLOW_MEMBER_HAS_NO_FOLLOWERS'); ?>
					</p>
				</div><!-- / .instructions -->
			<?php } ?>
		</div><!-- / #collection-introduction -->
	<?php } ?>
	<div class="clear"></div>
</form>