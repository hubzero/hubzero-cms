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

<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="icon-group group btn popup 1200x600" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&controller=media&task=filebrowser&tmpl=component&path=/uploads'); ?>">
				<?php echo JText::_('Upload Images/Files'); ?>
			</a>
			<a class="icon-group group btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn')); ?>">
				<?php echo JText::_('Back to Group'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	
	<?php foreach ($this->notifications as $notification) : ?>
		<p class="<?php echo $notification['type']; ?>">
			<?php echo $notification['message']; ?>
		</p>
	<?php endforeach; ?>
	
	<div class="group-page-manager">
		
		<ul class="tabs clearfix">
			<li><a href="#pages"><?php echo JText::_('Manage Pages'); ?></a></li>
			<li><a href="#categories"><?php echo JText::_('Manage Page Categories'); ?></a></li>
			<?php if ($this->group->isSuperGroup() || $this->config->get('page_modules', 0) == 1) : ?>
				<li><a href="#modules"><?php echo JText::_('Manage Modules'); ?></a></li>
			<?php endif ;?>
		</ul>
		
		<form action="index.php" method="post" id="hubForm" class="full">
			<fieldset>
				<!-- <legend><?php echo JText::_('Manage Pages'); ?></legend> -->
				<?php
					$view = new JView(array(
						'name'   => 'pages',
						'layout' => 'list'
					));
					$view->group      = $this->group;
					$view->categories = $this->categories;
					$view->pages      = $this->pages;
					$view->display();
				?>
			</fieldset>

			<fieldset>
				<!-- <legend><?php echo JText::_('Manage Page Categories'); ?></legend> -->
				<?php
					$view = new JView(array(
						'name'   => 'categories',
						'layout' => 'list'
					));
					$view->group      = $this->group;
					$view->categories = $this->categories;
					$view->display();
				?>
			</fieldset>
			
			<?php if ($this->group->isSuperGroup() || $this->config->get('page_modules', 0) == 1) : ?>
				<fieldset>
					<!-- <legend><?php echo JText::_('Manage Modules'); ?></legend> -->
					<?php
						$view = new JView(array(
							'name'   => 'modules',
							'layout' => 'list'
						));
						$view->group   = $this->group;
						$view->modules = $this->modules;
						$view->display();
					?>
				</fieldset>
			<?php endif; ?>
		</form>
		
	</div>
	
</div>
