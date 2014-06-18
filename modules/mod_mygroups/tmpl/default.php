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

$juser = JFactory::getUser();
$groups = $this->groups;
$total = count($this->groups);
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>
	<ul class="module-nav grouped">
		<li><a class="icon-browse" href="<?php echo JRoute::_('index.php?option=com_groups&task=browse'); ?>"><?php echo JText::_('MOD_MYGROUPS_ALL_GROUPS'); ?></a></li>
		<li><a class="icon-plus" href="<?php echo JRoute::_('index.php?option=com_groups&task=new'); ?>"><?php echo JText::_('MOD_MYGROUPS_NEW_GROUP'); ?></a></li>
	</ul>  

	<?php if ($groups && $total > 0) { ?>
		<ul class="compactlist mygroups">
			<?php
			$i = 0;
			foreach ($groups as $group)
			{
				if ($group->published && $i < $this->limit)
				{
					$status = $this->getStatus($group);
					?>
					<li class="group">
						<a href="<?php echo JRoute::_('index.php?option=com_groups&cn=' . $group->cn); ?>"><?php echo $this->escape(stripslashes($group->description)); ?></a>
						<span><span class="<?php echo $status; ?> status"><?php echo JText::_('MOD_MYGROUPS_STATUS_' . strtoupper($status)); ?></span></span>
						<?php if (!$group->approved): ?>
							<br />
							<span class="status pending-approval"><?php echo JText::_('MOD_MYGROUPS_GROUP_STATUS_PENDING'); ?></span>
						<?php endif; ?>
						<?php if ($group->regconfirmed && !$group->registered) : ?>
							<span class="actions">
								<a class="action-accept" href="<?php echo JRoute::_('index.php?option=com_groups&cn=' . $group->cn . '&task=accept'); ?>">
									<?php echo JText::_('MOD_MYGROUPS_ACTION_ACCEPT'); ?>
								</a>
							</span>
						<?php endif; ?>
					</li>
					<?php
					$i++;
				}
			}
			?>
		</ul>
	<?php } else { ?>
		<p><em><?php echo JText::_('MOD_MYGROUPS_NO_GROUPS'); ?></em></p>
	<?php } ?>

	<?php if ($total > $this->limit) { ?>
		<p class="note"><?php echo JText::sprintf('MOD_MYGROUPS_YOU_HAVE_MORE', $this->limit, $total, JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=groups')); ?></p>
	<?php } ?>
</div>

