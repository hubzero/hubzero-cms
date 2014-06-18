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

$this->css();
?>
<section class="main section" id="s-projects">
	<div class="subject">
		<div class="entries-filters">
			<ul class="entries-menu">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$cn.'&active=projects&action=all'); ?>"><?php echo JText::_('PLG_GROUPS_PROJECTS_LIST').' ('.$this->projectcount.')'; ?>
					</a>
				</li>
				<li>
					<a class="active" href="<?php echo JRoute::_('index.php?option=com_groups&cn='.$cn.'&active=projects&action=updates'); ?>"><?php echo JText::_('PLG_GROUPS_PROJECTS_UPDATES_FEED'); ?> <?php if ($this->newcount) { echo '<span class="s-new">'.$this->newcount.'</span>'; } ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="container">
			<div id="project-updates">
				<div id="latest_activity" class="infofeed">
				<?php
					// Display item list
					$this->view('default', 'activity')
					     ->set('option', $this->option)
					     ->set('activities', $this->activities)
					     ->set('limit', $this->limit)
					     ->set('total', $this->total)
					     ->set('filters', $this->filters)
					     ->set('uid', $this->uid)
					     ->set('database', $this->database)
					     ->set('config', $this->config)
					     ->set('gid', $gid)
					     ->display();
					?>
				</div> <!-- / .infofeed -->
			</div>
		</div>
	</div><!-- / .subject -->
	<aside class="aside">
		<div class="container">
			<h3>Create a Project</h3>
			<p>Have a new project? Want to create a dedicated space for project collaboration? Create a project today!</p>
			<p><a class="icon-add btn" href="/projects/start?gid=<?php echo $gid; ?>">Add Project</a></p>
		</div>
		<div class="container">
			<h3>Your Projects</h3>
			<p>View a list of all the projects you collaborate on.</p>
			<p>Go to <a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->uid . '&active=projects'); ?>">your projects</a></p>
		</div>
	</aside><!-- /.aside -->
</section>