<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css();
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="stats-overview">
		<tbody>
			<tr>
				<td class="public">
					<a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=0&amp;policy=" title="<?php echo JText::_('MOD_GROUPS_VISIBLE_TITLE'); ?>">
						<?php echo $this->escape($this->visible); ?>
						<span><?php echo JText::_('MOD_GROUPS_VISIBLE'); ?></span>
					</a>
				</td>
				<td class="protected">
					<a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=1&amp;policy=" title="<?php echo JText::_('MOD_GROUPS_HIDDEN_TITLE'); ?>">
						<?php echo $this->escape($this->hidden); ?>
						<span><?php echo JText::_('MOD_GROUPS_HIDDEN'); ?></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="stats-overview">
		<tbody>
			<tr>
				<td class="closed">
					<a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=&amp;policy=closed&amp;approved=" title="<?php echo JText::_('MOD_GROUPS_CLOSED_TITLE'); ?>">
						<?php echo $this->escape($this->closed); ?>
						<span><?php echo JText::_('MOD_GROUPS_CLOSED'); ?></span>
					</a>
				</td>
				<td class="invite">
					<a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=&amp;policy=invite&amp;approved=" title="<?php echo JText::_('MOD_GROUPS_INVITE_TITLE'); ?>">
						<?php echo $this->escape($this->invite); ?>
						<span><?php echo JText::_('MOD_GROUPS_INVITE'); ?></span>
					</a>
				</td>
				<td class="restricted">
					<a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=&amp;policy=restricted&amp;approved=" title="<?php echo JText::_('MOD_GROUPS_RESTRICTED_TITLE'); ?>">
						<?php echo $this->escape($this->restricted); ?>
						<span><?php echo JText::_('MOD_GROUPS_RESTRICTED'); ?></span>
					</a>
				</td>
				<td class="open">
					<a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;discoverability=&amp;policy=open&amp;approved=" title="<?php echo JText::_('MOD_GROUPS_OPEN_TITLE'); ?>">
						<?php echo $this->escape($this->open); ?>
						<span><?php echo JText::_('MOD_GROUPS_OPEN'); ?></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="stats-overview">
		<tbody>
			<tr>
				<td class="approved">
					<a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;approved=1&amp;discoverability=&amp;policy=" title="<?php echo JText::_('MOD_GROUPS_PUBLISHED_TITLE'); ?>">
						<?php echo $this->escape($this->approved); ?>
						<span><?php echo JText::_('MOD_GROUPS_PUBLISHED'); ?></span>
					</a>
				</td>
				<td class="pending">
					<a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;approved=0&amp;discoverability=&amp;policy=" title="<?php echo JText::_('MOD_GROUPS_PENDING_TITLE'); ?>">
						<?php echo $this->escape($this->pending); ?>
						<span><?php echo JText::_('MOD_GROUPS_PENDING'); ?></span>
					</a>
				</td>
				<td class="newest">
					<a href="index.php?option=com_groups&amp;controller=manage&amp;type=<?php echo $this->type; ?>&amp;created=pastday&amp;discoverability=&amp;policy=&amp;approved=" title="<?php echo JText::_('MOD_GROUPS_NEW_TITLE'); ?>">
						<?php echo $this->escape($this->pastDay); ?>
						<span><?php echo JText::_('MOD_GROUPS_NEW'); ?></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>