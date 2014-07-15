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
<ul class="toolbar toolbar-pages">
	<li class="new">
		<a class="btn icon-add" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=add'); ?>">
			<?php echo JText::_('New Page'); ?>
		</a>
	</li>
	<li class="filter">
		<select>
			<option value="">- Filter By Category &mdash;</option>
			<?php foreach ($this->categories as $category) : ?>
				<option data-color="#<?php echo $category->get('color'); ?>" value="<?php echo $category->get('id'); ?>"><?php echo $category->get('title'); ?></option>
			<?php endforeach; ?>
		</select>
	</li>
	<li class="filter-search-divider">Or</li>
	<li class="search">
		<input type="text" placeholder="Search Pages...." />
	</li>
</ul>

<ul class="item-list pages" data-url="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=reorder&no_html=1'); ?>">
	<?php if ($this->pages->count() > 0) : ?>
		<?php foreach ($this->pages as $key => $page) : ?>
			<?php
				$class    = '';
				$category = $this->categories->fetch('id', $page->get('category'));
				if ($page->versions()->count() < 1)
				{
					continue;
				}
				$version  = $page->versions()->first();
				
				if ($category !== null)
				{
					$class .= ' category-' . $page->get('category');
				}
				if ($version->get('approved') == 0)
				{
					$class .= ' not-approved';
				}
				
				//get file check outs
				$checkout = GroupsHelperPages::getCheckout($page->get('id'));
			?>
			<li id="<?php echo $page->get('id'); ?>" class="<?php echo $class; ?>">
				<div class="item-container" <?php if ($category) : ?>style="border-color: #<?php echo $category->get('color'); ?>"<?php endif; ?>>
					<div class="item-title">
						<?php if ($page->get('privacy') == 'members') : ?>
							<span class="icon-lock tooltips" title="Private to Group Members"></span>
						<?php endif; ?>
						<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$page->get('id')); ?>">
							<?php echo $page->get('title'); ?>
						</a>
					</div>
					
					<div class="item-sub" >
						<span tabindex="-1"><?php echo DS . 'groups' . DS . $this->group->get('cn') . DS .$page->get('alias'); ?></span>
					</div>
					
					<?php if ($checkout) : ?>
						<div class="item-checkout">
							<img width="15" src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($checkout->userid); ?>" />
							<?php
								$user = \Hubzero\User\Profile::getInstance($checkout->userid);
								echo JText::sprintf('<a href="/members/%s">%s</a> is currently editing', $user->get('uidNumber'), $user->get('name'));
							?>
						</div>
					<?php endif; ?>
					
					<?php if ($version->get('approved') == 0) : ?>
						<div class="item-approved">
							<?php echo JText::_('Pending Approval'); ?>
						</div>
					<?php endif; ?>
					
					<div class="item-state">
						<?php if ($page->get('state') == 0) : ?>
							<a class="unpublished tooltips" title="<?php echo JText::_('Publish Page'); ?>" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=publish&pageid='.$page->get('id')); ?>"><?php echo JText::_('Publish Page'); ?></a>
						<?php else : ?>
							<a class="published tooltips" title="<?php echo JText::_('Unpublish Page'); ?>" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=unpublish&pageid='.$page->get('id')); ?>"><?php echo JText::_('Unpublish Page'); ?></a>
						<?php endif; ?>
					</div>
					
					<div class="item-home">
						<?php if (!$page->get('home')) : ?>
							<a class="tooltips" title="<?php echo JText::_('Make Home Page'); ?>" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=sethome&pageid='.$page->get('id')); ?>"><?php echo JText::_('Make Home Page'); ?></a>
						<?php else : ?>
							<a class="homepage tooltips" title="<?php echo JText::_('Remove as Home Page'); ?>" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=sethome&pageid='.$page->get('id')); ?>"><?php echo JText::_('Remove as Home Page'); ?></a>
						<?php endif; ?>
					</div>
			
					<div class="item-order">
						<div class="order-num">
							<?php echo ($page->get('ordering') + 0); ?>
						</div>
						<div class="order-grabber"></div>
					</div>
			
					<div class="item-controls btn-group dropdown">
						<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$page->get('id')); ?>" class="btn"><?php echo JText::_('Manage Page'); ?></a>
						<span class="btn dropdown-toggle"></span>
						<ul class="dropdown-menu">
							<li><a class="icon-edit" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$page->get('id')); ?>"> <?php echo JText::_('Edit Page'); ?></a></li>
							<li><a class="icon-search page-preview" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=preview&pageid='.$page->get('id')); ?>"> <?php echo JText::_('Preview Page'); ?></a></li>
							<?php if (!$page->get('home')) : ?>
								<li><a class="icon-home" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=sethome&pageid='.$page->get('id')); ?>"> <?php echo JText::_('Make Home Page'); ?></a></li>
							<?php else : ?>
								<li><a class="icon-home" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=sethome&pageid='.$page->get('id')); ?>"> <?php echo JText::_('Remove as Home Page'); ?></a></li>
							<?php endif; ?>
							<li class="divider"></li>
							<?php if ($page->get('state') == 0) : ?>
								<li><a class="icon-ban-circle" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=publish&pageid='.$page->get('id')); ?>"> <?php echo JText::_('Publish Page'); ?></a></li>
							<?php else : ?>
								<li><a class="icon-success" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=unpublish&pageid='.$page->get('id')); ?>"> <?php echo JText::_('Un-publish Page'); ?></a></li>
							<?php endif; ?>
							<li class="divider"></li>
							<li><a class="icon-history page-history" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=versions&pageid='.$page->get('id')); ?>"> <?php echo JText::_('Version History'); ?></a></li>
							<li class="divider"></li>
							<li><a class="icon-delete" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=delete&pageid='.$page->get('id')); ?>"> <?php echo JText::_('Delete Page'); ?></a></li>
						</ul>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	<?php else : ?>
		<li class="no-results">
			<p><?php echo JText::_('Currently this group does not have any content pages.'); ?></p>
		</li>
	<?php endif; ?>

	<div class="item-list-loader"></div>
</ul>