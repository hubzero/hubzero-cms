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

$this->dateFormat  = '%d %b %Y';
$this->timeFormat  = '%I:%M %p';
$this->tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$this->dateFormat  = 'd M Y';
	$this->timeFormat  = 'h:i a';
	$this->tz = true;
}

ximport('Hubzero_User_Profile_Helper');

$base = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->name;
?>

<form method="get" action="<?php echo JRoute::_($base . '&task=followers'); ?>" id="collections">

	<fieldset class="filters">
		<ul>
			<li>
				<a class="collections count" href="<?php echo JRoute::_($base . '&task=all'); ?>">
					<?php echo JText::sprintf('<strong>%s</strong> collections', $this->collections); ?>
				</a>
			</li>
			<li>
				<a class="posts count" href="<?php echo JRoute::_($base . '&task=posts'); ?>">
					<?php echo JText::sprintf('<strong>%s</strong> posts', $this->posts); ?>
				</a>
			</li>
			<li>
				<a class="followers count active" href="<?php echo JRoute::_($base . '&task=followers'); ?>">
					<?php echo JText::sprintf('<strong>%s</strong> followers', $this->total); ?>
				</a>
			</li>
			<li>
				<a class="following count" href="<?php echo JRoute::_($base . '&task=following'); ?>">
					<?php echo JText::sprintf('<strong>%s</strong> following', $this->following); ?>
				</a>
			</li>
		</ul>
		<div class="clear"></div>
	</fieldset>

<?php if ($this->rows->total() > 0) { ?>
	<div class="container">
		<table class="followers entries" summary="<?php echo JText::_('PLG_MEMBERS_COLLECTIONS_TBL_SUMMARY'); ?>">
			<caption>
				<?php echo JText::_('People following you'); ?>
			</caption>
			<tbody>
	<?php foreach ($this->rows as $row) { ?>
				<tr>
				<?php if ($row->get('follower_type') == 'member') { ?>
					<th class="entry-img">
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($row->get('follower_id'), 0); ?>" width="40" height="40" alt="Profile picture of <?php echo $this->escape(stripslashes($row->follower()->get('name'))); ?>" />
					</th>
					<td>
						<a class="entry-title" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('follower_id')); ?>">
							<?php echo $this->escape(stripslashes($row->follower()->get('name'))); ?>
						</a>
				<?php } else if ($row->get('follower_type') == 'group') { ?>
					<th class="entry-img">
						<img src="<?php echo $row->follower()->get('logo'); ?>" width="40" height="40" alt="Profile picture of <?php echo $this->escape(stripslashes($row->follower()->get('description'))); ?>" />
					</th>
					<td>
						<a class="entry-title" href="<?php echo JRoute::_('index.php?option=com_groups&gid=' . $row->follower()->get('cn')); ?>">
							<?php echo $this->escape(stripslashes($row->follower()->get('name'))); ?>
						</a>
				<?php } ?>
						<br />
						<span class="entry-details">
							<span class="follower count"><?php echo JText::sprintf('<strong>%s</strong> followers', $row->count('followers')); ?></span>
							<span class="following count"><?php echo JText::sprintf('<strong>%s</strong> following', $row->count('following')); ?></span>
						</span>
					</td>
					<td>
						<time datetime="<?php echo $row->get('created'); ?>"><?php echo JHTML::_('date', $row->get('created'), $this->dateFormat, $this->tz); ?></time>
					</td>
				</tr>
	<?php } ?>
			</tbody>
		</table>
	</div><!-- / .container -->
<?php } else { ?>
		<div id="collection-introduction">
			<div class="instructions">
	<?php if ($this->params->get('access-manage-collection')) { ?>
				<p><?php echo JText::_('You currently do not have anyone following you or any of your collections.'); ?></p>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong>What are followers?</strong></p>
				<p>"Followers" are members that have decided to receive all public posts you make or all posts in one of your collections.<p>
				<p>Followers cannot see of your private collections or posts made to private collections.<p>
			</div>
	<?php } else { ?>
				<p>
					<?php echo JText::_('This member is not following anyone or any collections.'); ?>
				</p>
			</div><!-- / .instructions -->
	<?php } ?>
		</div><!-- / #collection-introduction -->
<?php } ?>
		<div class="clear"></div>

</form>