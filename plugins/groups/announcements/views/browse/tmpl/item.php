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

// Default to unpublished
$class = 'unpublished';

// Is the announcement available?
// Checks that the announcement is:
//   * exists
//   * published (not deleted)
//   * publish up before now
//   * publish down after now
if ($this->announcement->isAvailable())
{
	$class = 'published';
}

// Is it high priority?
if ($this->announcement->get('priority'))
{
	$class .= ' high';
}

// Is it sticky?
if ($this->announcement->get('sticky'))
{
	$class .= ' sticky';
}

//did the user already close this
$closed = JRequest::getWord('group_announcement_' . $this->announcement->get('id'), '', 'cookie');
if ($closed == 'closed' && $this->showClose == true)
{
	return;
}
?>

<div class="announcement-container <?php echo $class; ?>">
	<?php if (strstr($class, 'unpublished')) : ?>
		<span class="unpublished-message"><?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_NOT_ACTIVE'); ?></span>
	<?php endif; ?>
	<div class="announcement">
		<?php echo $this->announcement->content('parsed'); ?>
		<dl class="entry-meta">
			<dt class="entry-id">
				<?php echo $this->announcement->get('id'); ?>
			</dt>
		<?php if ($this->authorized == 'manager') : ?>
			<dd class="entry-author">
				<?php
					$profile = $this->announcement->creator();
					if (is_object($profile) && $profile->get('name') != '')
					{
						echo $this->escape($profile->get('name'));
					}
				?>
			</dd>
		<?php endif; ?>
			<dd class="time">
				<time datetime="<?php echo $this->announcement->published(); ?>">
					<?php echo $this->announcement->published('time'); ?>
				</time>
			</dd>
			<dd class="date">
				<time datetime="<?php echo $this->announcement->published(); ?>">
					<?php echo $this->announcement->published('date'); ?>
				</time>
			</dd>
		<?php if ($this->authorized == 'manager' && !$this->showClose) : ?>
			<dd class="entry-options">
				<?php if ($this->juser->get('id') == $this->announcement->get('created_by')) : ?>
					<a class="icon-edit edit" href="<?php echo JRoute::_($this->announcement->link('edit')); ?>" title="<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_EDIT'); ?>">
						<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_EDIT'); ?>
					</a>
					<a class="icon-delete delete" href="<?php echo JRoute::_($this->announcement->link('delete')); ?>" data-confirm="<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_CONFIRM_DELETE'); ?>" title="<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_DELETE'); ?>">
						<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_DELETE'); ?>
					</a>
				<?php endif; ?>
			</dd>
		<?php endif; ?>
		</dl>
	<?php if ($this->showClose) : ?>
		<a class="close" href="<?php echo JRoute::_($this->announcement->link()); ?>" data-id="<?php echo $this->announcement->get('id'); ?>" data-duration="30" title="<?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_CLOSE_TITLE'); ?>">
			<span><?php echo JText::_('PLG_GROUPS_ANNOUNCEMENTS_CLOSE'); ?></span>
		</a>
	<?php endif; ?>
	</div>
</div>