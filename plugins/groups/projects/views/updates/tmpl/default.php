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
$cn = $this->group->get('cn');
$gid = $this->group->get('gidNumber');
?>
<div class="main section" id="s-projects">
	<div class="aside">
		<div class="container">
			<h3>Create a Project</h3>
			<p class="starter"><span class="starter-point"></span></p>
			<p class="starter">Have a new project? Want to create a dedicated space for project collaboration? Create a project today!</p>
			<p class="add"><a href="/projects/start?gid=<?php echo $gid; ?>">Add Project</a></p>
		</div>
		<div class="container">
			<h3>Your Projects</h3>
			<p class="starter"><span class="starter-point"></span></p>
			<p class="starter">View a list of all the projects you collaborate on.</p>
			<p class="starter">Go to <a href="/members/<?php echo $this->uid; ?>/projects">your projects</a></p>
		</div>
	</div><!-- /.aside -->
	
	<div class="subject">
		<div class="entries-filters">
			<ul class="entries-menu">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$cn.'&active=projects').'?action=all'; ?>"><?php echo JText::_('PLG_GROUPS_PROJECTS_LIST').' ('.$this->projectcount.')'; ?>
					</a>
				</li>
				<li>
					<a class="active" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$cn.'&active=projects').'?action=updates'; ?>"><?php echo JText::_('PLG_GROUPS_PROJECTS_UPDATES_FEED'); ?> <?php if($this->newcount) { echo '<span class="s-new">'.$this->newcount.'</span>'; } ?>
					</a>
				</li>
			</ul>
		</div>
	<div class="container">
		<div id="project-updates">
			<div id="latest_activity" class="infofeed">
			<?php 
				// Display item list
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'groups',
						'element'=>'projects',
						'name'=>'activity'
					)
				);
				$view->option = $this->option;
				$view->activities = $this->activities;
				$view->limit = $this->limit;
				$view->total = $this->total;
				$view->filters = $this->filters;
				$view->uid = $this->uid;
				$view->database = $this->database;
				$view->config = $this->config;
				$view->gid = $gid;
				echo $view->loadTemplate();
				?>
			</div> <!-- / .infofeed -->
		
		</div>
  </div>
</div><!-- / .subject -->
<div class="clear"></div>
</div>