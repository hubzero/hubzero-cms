<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>
<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_PROJECTS'); ?></h3>

<ul id="page_options" class="pluginOptions">
	<li>
		<a class="icon-add add btn showinbox"  href="<?php echo Route::url('index.php?option=com_projects&task=start'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_ADD'); ?>
		</a>
	</li>
</ul>

<ul class="sub-menu">
	<li class="active">
		<a href="<?php echo Route::url('index.php?option=com_members&id=' . $this->user->get('id') . '&active=projects&action=all'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_LIST') . ' (' . $this->total . ')'; ?>
		</a>
	</li>
	<li>
		<a href="<?php echo Route::url('index.php?option=com_members&id=' . $this->user->get('id') . '&active=projects&action=updates'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_UPDATES_FEED'); ?> <?php if ($this->newcount) { echo '<span class="s-new">' . $this->newcount . '</span>'; } ?>
		</a>
	</li>
</ul>

<div id="s-projects">
	<div class="container">
		<?php
		if ($this->which == 'all')
		{
			// Show owned projects first
			$this->view('list')
			     ->set('option', $this->option)
			     ->set('rows', $this->owned)
			     ->set('which', 'owned')
			     ->set('config', $this->config)
			     ->set('user', $this->user)
			     ->set('filters', $this->filters)
			     ->display();

			echo '</div><div class="container">';
		}

		// Show rows
		$this->view('list')
		     ->set('option', $this->option)
		     ->set('rows', $this->rows)
		     ->set('config', $this->config)
		     ->set('user', $this->user)
		     ->set('which', $this->filters['which'])
		     ->set('filters', $this->filters)
		     ->display();
		?>
	</div>
</div>
