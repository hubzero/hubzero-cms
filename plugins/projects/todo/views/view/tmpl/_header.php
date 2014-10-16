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

$url = 'index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=todo';

?>
<div id="plg-header">
	<h3 class="todo"><?php if ($this->listName or $this->filters['assignedto'] or $this->filters['state'] == 1) { ?> <a href="<?php echo JRoute::_($url); ?>"> <?php } ?><?php echo $this->title; ?><?php if ($this->listName or $this->filters['assignedto'] or $this->filters['state'] == 1) { ?></a><?php } ?>
	<?php if ($this->listName) { ?> &raquo; <a href="<?php echo JRoute::_($url).'/?list='.$this->filters['todolist']; ?>"><span class="indlist <?php echo 'pin_'.$this->filters['todolist'] ?>"><?php echo $this->listName; ?></span></a> <?php } ?>
	<?php if ($this->filters['assignedto']) { ?> &raquo; <span class="indlist mytodo"><a href="<?php echo JRoute::_($url).'/?mine=1'; ?>"><?php echo ucfirst(JText::_('PLG_PROJECTS_TODO_MY_TODOS')); ?></a></span> <?php } ?>
	<?php if ($this->filters['state']) { ?> &raquo; <span class="indlist completedtd"><?php echo ucfirst(JText::_('PLG_PROJECTS_TODO_COMPLETED')); ?></span> <?php } ?>
	</h3>
</div>