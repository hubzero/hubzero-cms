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
defined('_JEXEC') or die( 'Restricted access' );

// build array of positions
$positions = array();
foreach ($this->modules as $module)
{
	if (!in_array($module->get('position'), $positions) && $module->get('position') != '')
	{
		$positions[] = $module->get('position');
	}
}
?>
<ul class="toolbar toolbar-modules">
	<li class="new">
		<a class="btn icon-add" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=add'); ?>">
			<?php echo JText::_('COM_GROUPS_PAGES_NEW_MODULE'); ?>
		</a>
	</li>
	<li class="filter">
		<select>
			<option value=""><?php echo JText::_('COM_GROUPS_PAGES_MODULE_FILTER'); ?></option>
			<?php foreach ($positions as $position) : ?>
				<option value="<?php echo $position; ?>"><?php echo $position; ?></option>
			<?php endforeach; ?>
		</select>
	</li>
	<li class="filter-search-divider"><?php echo JText::_('COM_GROUPS_PAGES_MODULE_OR'); ?></li>
	<li class="search">
		<input type="text" placeholder="<?php echo JText::_('COM_GROUPS_PAGES_MODULE_SEARCH'); ?>" />
	</li>
</ul>

<ul class="item-list modules">
	<?php if ($this->modules->count() > 0) : ?>
		<?php foreach ($this->modules as $module) : ?>
			<?php
			$class = 'position-' . $module->get('position');
			if ($module->get('approved') == 0)
			{
				$class .= ' not-approved';
			}
			?>
			<li class="<?php echo $class; ?>">
				<div class="item-container">
					<div class="item-title">
						<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=edit&moduleid='.$module->get('id')); ?>">
							<?php echo $module->get('title'); ?>
						</a>

						<?php
							$pages = array();
							$menus = $module->menu('list');
							foreach ($menus as $menu)
							{
								$pages[] = $menu->getPageTitle();
							}
						?>
					</div>

					<div class="item-sub">
						<?php echo JText::sprintf('COM_GROUPS_PAGES_MODULE_INCLUDED_ON', implode(', ', $pages)); ?>
					</div>

					<div class="item-position">
						<span><?php echo JText::_('COM_GROUPS_PAGES_MODULE_POSITION'); ?>:</span>
						<?php echo $module->get('position'); ?>
					</div>

					<?php if ($module->get('approved') == 0) : ?>
						<div class="item-approved">
							<?php echo JText::_('COM_GROUPS_PAGES_MODULE_PENDING_APPROVAL'); ?>
						</div>
					<?php endif; ?>

					<div class="item-state">
						<?php if ($module->get('state') == 0) : ?>
							<a class="unpublished tooltips" title="<?php echo JText::_('COM_GROUPS_PAGES_PUBLISH_MODULE'); ?>" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=publish&moduleid='.$module->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_PUBLISH_MODULE'); ?></a>
						<?php else : ?>
							<a class="published tooltips" title="<?php echo JText::_('COM_GROUPS_PAGES_UNPUBLISH_MODULE'); ?>" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=unpublish&moduleid='.$module->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_UNPUBLISH_MODULE'); ?></a>
						<?php endif; ?>
					</div>

					<div class="item-controls btn-group dropdown">
						<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=edit&moduleid='.$module->get('id')); ?>" class="btn">
							<?php echo JText::_('COM_GROUPS_PAGES_MANAGE_MODULE'); ?>
						</a>
						<span class="btn dropdown-toggle"></span>
						<ul class="dropdown-menu">
							<li><a class="icon-edit" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=edit&moduleid='.$module->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_EDIT_MODULE'); ?></a></li>
							<li class="divider"></li>
							<?php if ($module->get('state') == 0) : ?>
								<li><a class="icon-ban-circle" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=publish&moduleid='.$module->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_PUBLISH_MODULE'); ?></a></li>
							<?php else : ?>
								<li><a class="icon-success" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=unpublish&moduleid='.$module->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_UNPUBLISH_MODULE'); ?></a></li>
							<?php endif; ?>
							<li class="divider"></li>
							<li><a class="icon-delete" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=delete&moduleid='.$module->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_DELETE_MODULE'); ?></a></li>
						</ul>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	<?php else : ?>
		<li class="no-results">
			<p><?php echo JText::_('COM_GROUPS_PAGES_NO_MODULES'); ?></p>
		</li>
	<?php endif; ?>
</ul>