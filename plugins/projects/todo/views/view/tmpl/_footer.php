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
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="id" id="pid" value="<?php echo $this->project->id; ?>" />
<input type="hidden" name="alias" value="<?php echo $this->project->alias; ?>" />
<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
<input type="hidden" name="active" value="todo" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="todoid" id="todoid" value="0" />
<input type="hidden" name="page" id="tdpage" value="list" />
<input type="hidden" name="list" id="list" value="<?php echo $this->filters['todolist']; ?>" />
<input type="hidden" name="state" id="tdstate" value="<?php echo $this->filters['state']; ?>" />
<input type="hidden" name="mine" value="<?php echo $this->filters['mine']; ?>" />
<input type="hidden" name="sortby" value="<?php echo $this->filters['sortby']; ?>" />
<input type="hidden" name="sortdir" value="<?php echo $this->filters['sortdir']; ?>" />
<?php if ($this->filters['state'] == 0) { ?>
	<p class="tips js"><?php echo ucfirst(JText::_('PLG_PROJECTS_TODO_REORDER_INSTRUCT')); ?></p>
<?php } ?>
<?php if ($this->filters['todolist']) { ?>
<div class="container-footer">
	<span class="listoptions"><a href="<?php echo JRoute::_($url . '&action=delete&dl=' . $this->filters['todolist']); ?>" id="del-<?php echo $this->filters['todolist']; ?>" class="dellist"><?php echo JText::_('PLG_PROJECTS_TODO_DELETE_TODO_LIST'); ?></a></span>
	<div class="confirmaction" id="confirm-<?php echo $this->filters['todolist']; ?>"><?php echo JText::_('PLG_PROJECTS_TODO_DELETE_ARE_YOU_SURE'); ?>
		<ul>
			<li><a href="<?php echo JRoute::_($url . '&action=delete&dl=' . $this->filters['todolist'] . '&all=1'); ?>"><?php echo JText::_('PLG_PROJECTS_TODO_DELETE_ALL_ITEMS'); ?></a></li>
			<li><a href="<?php echo JRoute::_($url . '&action=delete&dl=' . $this->filters['todolist']); ?>"><?php echo JText::_('PLG_PROJECTS_TODO_DELETE_LEAVE_ITEMS'); ?></a></li>
			<li><a href="<?php echo JRoute::_($url . '&list=' . $this->filters['todolist']); ?>" id="cnl-<?php echo $this->filters['todolist']; ?>"><?php echo JText::_('PLG_PROJECTS_TODO_DELETE_CANCEL'); ?></a></li>
		</ul>
	</div>
</div>
<?php } ?>