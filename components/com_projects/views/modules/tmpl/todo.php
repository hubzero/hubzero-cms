<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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
<div class="sidebox">
		<h4 class="todo"><a href="<?php echo JRoute::_('index.php?option='.$this->option . a . 'alias=' . $this->project->alias . a . 'active=todo'); ?>" class="hlink" title="<?php echo JText::_('COM_PROJECTS_VIEW') . ' ' . strtolower(JText::_('COM_PROJECTS_PROJECT')) . ' ' . strtolower(JText::_('COM_PROJECTS_TAB_TODO')); ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_TAB_TODO')); ?></a>
<?php if (count($this->items) > 0) { ?>
	<span><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . 'active=todo'); ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_SEE_ALL')); ?> </a></span>
<?php } ?>
</h4>
<?php if (count($this->items) == 0) { ?>
	<div class="noitems">
		<p><?php echo JText::_('COM_PROJECTS_NO_TODOS'); ?></p>
		<p class="addnew"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . 'active=todo'); ?>"><?php echo JText::_('COM_PROJECTS_ADD_TODO'); ?></a></p>
	</div>
<?php } else { ?>
	<ul>
		<?php foreach($this->items as $todo) { 
			$overdue = '';
			$due = '';
			if($todo->duedate && $todo->duedate != '0000-00-00 00:00:00' && $todo->duedate <= date( 'Y-m-d H:i:s') ) {
				$overdue = ' urgency';
				$due = JText::_('COM_PROJECTS_OVERDUE').' '.JText::_('COM_PROJECTS_BY').' '.ProjectsHtml::timeAgo($todo->duedate);
			}
			else if($todo->duedate && $todo->duedate != '0000-00-00 00:00:00') {
				$due = JText::_('COM_PROJECTS_DUE').' '.JText::_('COM_PROJECTS_IN').' '.ProjectsHtml::timeFromNow($todo->duedate);
			}						
		?>
	<li>
		 <a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias. a . 'active=todo' . a . 'action=view').'/?todoid='.$todo->id; ?>" title="<?php echo htmlentities($todo->content); ?>">
		<?php echo Hubzero_View_Helper_Html::shortenText($todo->content, 35, 0); ?></a>
		 <span class="block faded mini">
			<?php if($todo->assignedname) { ?>
			<span><?php echo ProjectsHtml::shortenName($todo->assignedname); ?></span> | 
			<?php } ?>	
			<?php if($due) { ?>
			<span class="duetd<?php echo $overdue; ?>"><?php echo $due; ?></span> | 
			<?php } ?>
			<span><?php echo $todo->comments.' '; echo $todo->comments == 1 ? strtolower(JText::_('COM_PROJECTS_COMMENT')) : JText::_('COM_PROJECTS_COMMENTS'); ?></span>
		</span>
	   </li><?php } ?>
	</ul><?php } ?>
</div>