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
$cls = '';
$style = '';
if ($this->category !== null)
{
	$cls .= ' category-' . $this->page->get('category');
	$style = 'border-left-color: #' .  $this->category->get('color');
}
if ($this->version->get('approved') == 0)
{
	$cls .= ' not-approved';
}
?>
<div class="item-container <?php echo $cls; ?>" style="<?php echo $style; ?>">
	<div class="item-title">
		<?php if ($this->page->get('privacy') == 'members') : ?>
			<span class="icon-lock tooltips" title="<?php echo JText::_('COM_GROUPS_PAGES_PAGE_PRIVATE'); ?>"></span>
		<?php endif; ?>
		<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$this->page->get('id')); ?>">
			<?php echo $this->page->get('title'); ?>
		</a>
	</div>

	<div class="item-sub" >
		<span tabindex="-1"><?php echo $this->page->url(); ?></span>
	</div>

	<?php if ($this->checkout) : ?>
		<div class="item-checkout">
			<img width="15" src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($this->checkout->userid); ?>" />
			<?php
				$user = \Hubzero\User\Profile::getInstance($this->checkout->userid);
				echo JText::sprintf('COM_GROUPS_PAGES_PAGE_CHECKED_OUT', $user->get('uidNumber'), $user->get('name'));
			?>
		</div>
	<?php endif; ?>

	<?php if ($this->version->get('approved') == 0) : ?>
		<div class="item-approved">
			<?php echo JText::_('COM_GROUPS_PAGES_PAGE_PENDING_APPROVAL'); ?>
		</div>
	<?php endif; ?>

	<?php if ($this->page->get('home') == 0) : ?>
		<div class="item-state">
			<?php if ($this->page->get('state') == 0) : ?>
				<a class="unpublished tooltips" title="<?php echo JText::_('COM_GROUPS_PAGES_PUBLISH_PAGE'); ?>" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=publish&pageid='.$this->page->get('id')); ?>"><?php echo JText::_('COM_GROUPS_PAGES_PUBLISH_PAGE'); ?></a>
			<?php else : ?>
				<a class="published tooltips" title="<?php echo JText::_('COM_GROUPS_PAGES_UNPUBLISH_PAGE'); ?>" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=unpublish&pageid='.$this->page->get('id')); ?>"><?php echo JText::_('COM_GROUPS_PAGES_UNPUBLISH_PAGE'); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="item-preview">
		<a class="tooltips page-preview" title="<?php echo JText::_('COM_GROUPS_PAGES_PREVIEW_PAGE'); ?>" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=preview&pageid='.$this->page->get('id')); ?>"><?php echo JText::_('COM_GROUPS_PAGES_PREVIEW_PAGE'); ?></a>
	</div>

	<div class="item-controls btn-group dropdown">
		<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$this->page->get('id')); ?>" class="btn"><?php echo JText::_('COM_GROUPS_PAGES_MANAGE_PAGE'); ?></a>
		<span class="btn dropdown-toggle"></span>
		<ul class="dropdown-menu">
			<li><a class="icon-edit" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$this->page->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_EDIT_PAGE_BACK'); ?></a></li>
			<li><a class="icon-search page-preview" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=preview&pageid='.$this->page->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_PREVIEW_PAGE'); ?></a></li>
			<?php if ($this->page->get('home') == 0) : ?>
				<?php if ($this->page->get('state') == 0) : ?>
					<li><a class="icon-ban-circle" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=publish&pageid='.$this->page->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_PUBLISH_PAGE'); ?></a></li>
				<?php else : ?>
					<li><a class="icon-success" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=unpublish&pageid='.$this->page->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_UNPUBLISH_PAGE'); ?></a></li>
				<?php endif; ?>
			<?php endif; ?>
			<li class="divider"></li>
			<li><a class="icon-history page-history" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=versions&pageid='.$this->page->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_VERSION_HISTORY_PAGE'); ?></a></li>
			

			<?php if ($this->page->get('home') == 0) : ?>
				<li class="divider"></li>
				<li><a class="icon-delete" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=delete&pageid='.$this->page->get('id')); ?>"> <?php echo JText::_('COM_GROUPS_PAGES_DELETE_PAGE'); ?></a></li>
			<?php endif; ?>
		</ul>
	</div>

	<?php if ($this->page->get('home') == 0) : ?>
		<div class="item-mover"></div>
	<?php endif; ?>
</div>