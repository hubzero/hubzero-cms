<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
$juser = $this->juser;
?>
<h3 class="section-header"><a name="projects"></a><?php echo JText::_('PLG_MEMBERS_PROJECTS'); ?></h3>
<div class="aside">
	<div class="container">
		<h3>Create a Project</h3>
		<p class="starter"><span class="starter-point"></span></p>
		<p class="starter">Have a new project? Want to create a dedicated space for project collaboration? Create a project today!</p>
		<p class="add"><a href="/projects/start">Add Project</a></p>
	</div>
	<div class="container">
		<h3>Explore Projects</h3>
		<p class="starter"><span class="starter-point"></span></p>
		<p class="starter"><a href="/projects/browse">Browse</a> other public projects and learn more about project <a href="/projects/features">features</a>.</p>
	</div>
</div><!-- / .aside -->
<div class="subject" id="s-projects">
	<div class="entries-filters">
		<ul class="entries-menu">
			<li>
				<a class="active" href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->juser->get('id').'&active=projects').'?action=all'; ?>"><?php echo JText::_('PLG_MEMBERS_PROJECTS_LIST').' ('.$this->total.')'; ?>
				</a>
			</li>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->juser->get('id').'&active=projects').'?action=updates'; ?>"><?php echo JText::_('PLG_MEMBERS_PROJECTS_UPDATES_FEED'); ?> <?php if($this->newcount) { echo '<span class="s-new">'.$this->newcount.'</span>'; } ?>
				</a>
			</li>
		</ul>
	</div>
	<div id="myprojects">
	<?php 
		if($this->which == 'all')  { 
			// Show owned projects first
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'members',
					'element'=>'projects',
					'name'=>'browse',
					'layout'=>'list'
				)
			);
			$view->option = $this->option;
			$view->rows = $this->owned;
			$view->which = 'owned';
			$view->config = $this->config;
			$view->juser = $this->juser;
			$view->filters = $this->filters;
			echo $view->loadTemplate();
		}
		
		// Show rows
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'members',
				'element'=>'projects',
				'name'=>'browse',
				'layout'=>'list'
			)
		);
		$view->option = $this->option;
		$view->rows = $this->rows;
		$view->config = $this->config;
		$view->juser = $this->juser;
		$view->which = $this->filters['which'];
		$view->filters = $this->filters;
		echo $view->loadTemplate();
	?>
	</div>
</div><!-- / .subject -->
<div class="clear"></div>
