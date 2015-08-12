<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2015 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

<div id="s-projects">
	<div class="entries-filters">
		<ul class="entries-menu">
			<li>
				<a href="<?php echo Route::url('index.php?option=com_members&id=' . $this->uid . '&active=projects&action=all'); ?>">
					<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_LIST') . ' (' . $this->projectcount . ')'; ?>
				</a>
			</li>
			<li>
				<a class="active" href="<?php echo Route::url('index.php?option=com_members&id=' . $this->uid . '&active=projects&action=updates'); ?>">
					<?php echo Lang::txt('PLG_MEMBERS_PROJECTS_UPDATES_FEED'); ?> <?php if ($this->newcount) { echo '<span class="s-new">' . $this->newcount . '</span>'; } ?>
				</a>
			</li>
		</ul>
	</div>
	<div id="project-updates">
		<?php
		echo $this->content;
		?>
	</div>
</div><!-- / .subject -->
<div class="clear"></div>
