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
?>
<ul class="toolbar toolbar-categories">
	<li class="new">
		<a class="btn icon-add" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=add'); ?>">
			<?php echo JText::_('COM_GROUPS_PAGES_NEW_CATEGORY'); ?>
		</a>
	</li>
</ul>

<ul class="item-list categories">
	<?php if ($this->categories->count() > 0) : ?>
		<?php foreach ($this->categories as $category) : ?>
			<li>
				<div class="item-container">
					<div class="item-title">
						<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=edit&categoryid='.$category->get('id')); ?>">
							<?php echo $category->get('title'); ?>
						</a>
					</div>

					<div class="item-sub">
						<?php echo JText::sprintf('COM_GROUPS_PAGES_CATEGORY_X_PAGES', $category->getPages('count')); ?>
					</div>

					<div class="item-color" style="background-color: #<?php echo $category->get('color'); ?>"></div>

					<div class="item-controls btn-group dropdown">
						<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=edit&categoryid='.$category->get('id')); ?>" class="btn">
							<?php echo JText::_('COM_GROUPS_PAGES_MANAGE_CATEGORY'); ?>
						</a>
						<span class="btn dropdown-toggle"></span>
						<ul class="dropdown-menu">
							<li><a class="icon-edit" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=edit&categoryid='.$category->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_EDIT_CATEGORY'); ?></a></li>
							<li class="divider"></li>
							<li><a class="icon-delete" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=delete&categoryid='.$category->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_DELETE_CATEGORY'); ?></a></li>
						</ul>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	<?php else : ?>
		<li class="no-results">
			<p><?php echo JText::_('COM_GROUPS_PAGES_NO_CATEGORIES'); ?></p>
		</li>
	<?php endif; ?>
</ul>