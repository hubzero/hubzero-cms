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

$this->page->pagename = $this->page->stripNamespace();

$juser =& JFactory::getUser();
?>
	<div id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
		<h2><?php echo $this->page->stripNamespace($this->page->getTitle()); ?></h2>
	</div><!-- /#content-header -->

	<?php if (!$juser->get('guest') && $this->config->get('access-create')) { ?>
	<div id="<?php echo ($this->sub) ? 'sub-content-header-extra' : 'content-header-extra'; ?>">
		<ul id="<?php echo ($this->sub) ? 'page_options' : 'useroptions'; ?>">
			<li>
				<a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->page->scope . '&' . ($this->sub ? 'action' : 'task') . '=new'); ?>">
					<?php echo JText::_('COM_WIKI_NEW_PAGE'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
	<?php } ?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>

<?php
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'page',
		'layout'    => 'submenu'
	));
	$view->option = $this->option;
	$view->controller = $this->controller;
	$view->page   = $this->page;
	$view->task   = $this->task;
	$view->config = $this->config;
	$view->sub    = $this->sub;
	$view->display();
?>

	<div class="main section">

<?php
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'special',
		'layout'    => strtolower($this->layout)
	));
	$view->option = $this->option;
	$view->controller = $this->controller;
	$view->page   = $this->page;
	$view->task   = $this->task;
	$view->config = $this->config;
	$view->sub    = $this->sub;
	//$view->revision = $this->revision;
	$view->display()
?>

	</div><!-- / .main section -->