<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<ol id="steps" class="steps">
	<li class="setup-step<?php if ($this->step == 0) { echo ' active'; } else if ($this->project->setup_stage >= 1) { echo ' completed'; } ?>"><?php if ($this->project->setup_stage > 0 && $this->step != 0) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&task=setup&section=describe'); ?>"><?php } ?><?php echo Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT'); ?><?php if ($this->project->setup_stage > 0 && $this->step != 0) { ?></a><?php } ?></li>
	<li <?php if ($this->step == 1) { echo 'class="active"'; } elseif ($this->project->setup_stage >= 2) { echo 'class="completed"'; } else { echo 'class="coming"'; } ?>><?php if ($this->project->setup_stage >= 1 && $this->step != 1) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&task=setup&section=team'); ?>"><?php } ?><?php echo Lang::txt('COM_PROJECTS_ADD_TEAM'); ?><?php if ($this->project->setup_stage >= 1 && $this->step != 1) { ?></a><?php } ?></li>
	<?php if ($this->step == 2) { ?>
	<li class="active"><?php echo Lang::txt('COM_PROJECTS_SETUP_ONE_LAST_THING'); ?></li>
	<?php } ?>
	<li class="coming"><?php echo Lang::txt('COM_PROJECTS_READY_TO_GO'); ?></li>
</ol>
