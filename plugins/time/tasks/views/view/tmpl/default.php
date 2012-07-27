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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Set some ordering variables
$start   = ($this->filters['start']) ? '&start='.$this->filters['start'] : '';
$imgasc  = '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'sort_asc.png">';
$imgdesc = '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'sort_desc.png">';
$sortcol = $this->mainframe->getUserStateFromRequest("$this->option.$this->active.orderby", 'orderby', 'name');
$dir     = $this->mainframe->getUserStateFromRequest("$this->option.$this->active.orderdir", 'orderdir', 'asc');
$newdir  = ($dir == 'asc') ? 'desc' : 'asc';
?>

<div id="dialog-confirm"></div>

<div id="plg_time_tasks">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks&action=new'); ?>">
					<?php echo JText::_('PLG_TIME_TASKS_NEW'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="container">
		<?php
			if(!empty($this->filters['hub']) ||
				($this->filters['priority'] != NULL) ||
				!empty($this->filters['aname']) ||
				!empty($this->filters['lname']))
			{
				echo '<div id="applied-filters">';
				echo '<p>Filters:</p>';
				echo '<ul class="filters-list">';
				if(!empty($this->filters['hub']))
				{
					echo '<li><a href="' .
						JRoute::_('index.php?option='.$this->option.'&active=tasks&hub=0') .
						'" class="filters-x">x</a><i>Hub</i>: ' . $this->filter_hub->name . '</li>';
				}
				if($this->filters['priority'] != NULL)
				{
					echo '<li><a href="' .
						JRoute::_('index.php?option='.$this->option.'&active=tasks&priority=') .
						'" class="filters-x">x</a><i>Priority</i>: ' . $this->filters['priority'] . '</li>';
				}
				if(!empty($this->filters['aname']))
				{
					echo '<li><a href="' .
						JRoute::_('index.php?option='.$this->option.'&active=tasks&aname=0') .
						'" class="filters-x">x</a><i>Assignee</i>: ' . $this->filter_assignee->name . '</li>';
				}
				if(!empty($this->filters['lname']))
				{
					echo '<li><a href="' .
						JRoute::_('index.php?option='.$this->option.'&active=tasks&lname=0') .
						'" class="filters-x">x</a><i>Liaison</i>: ' . $this->filter_liaison->name . '</li>';
				}
				echo '</ul>';
				echo '</div>';
			}
		?>
		<table class="entries">
			<caption><?php echo JText::_('PLG_TIME_TASKS_CAPTION'); ?></caption>
			<thead>
				<tr>
					<td></td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=name&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_NAME'); ?>
						</a>
						<?php if($sortcol == 'name') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=hname&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_HUB_NAME'); ?>
						</a>
						<?php if($sortcol == 'hname') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
						<div class="filters">
							<div class="filters-fix"></div>
							<div class="filters-inner">
								<label for="user"><?php echo JText::_('PLG_TIME_TASKS_FILTER_HUB'); ?>:</label>
								<?php echo $this->hlist; ?>
							</div>
						</div>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=priority&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_PRIORITY'); ?>
						</a>
						<?php if($sortcol == 'priority') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
						<div class="filters">
							<div class="filters-fix"></div>
							<div class="filters-inner">
								<label for="priority"><?php echo JText::_('PLG_TIME_TASKS_FILTER_PRIORITY'); ?>:</label>
								<?php echo $this->priority_list; ?>
							</div>
						</div>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=aname&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_ASSIGNEE_SHORT'); ?>
						</a>
						<?php if($sortcol == 'aname') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
						<div class="filters">
							<div class="filters-fix"></div>
							<div class="filters-inner">
								<label for="assignee"><?php echo JText::_('PLG_TIME_TASKS_FILTER_ASSIGNEE'); ?>:</label>
								<?php echo $this->alist; ?>
							</div>
						</div>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=lname&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_LIAISON_SHORT'); ?>
						</a>
						<?php if($sortcol == 'lname') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
						<div class="filters">
							<div class="filters-fix"></div>
							<div class="filters-inner">
								<label for="liaison"><?php echo JText::_('PLG_TIME_TASKS_FILTER_LIAISON'); ?>:</label>
								<?php echo $this->llist; ?>
							</div>
						</div>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=start_date&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_START_DATE'); ?>
						</a>
						<?php if($sortcol == 'start_date') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=end_date&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_END_DATE'); ?>
						</a>
						<?php if($sortcol == 'end_date') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
				</tr>
			</thead>
			<tbody>
				<?php if(count($this->tasks) > 0) {
					foreach($this->tasks as $task) { ?>
					<tr<?php if($task->active == 0) { echo ' class="inactive"'; } ?>>
						<td class="<?php if($task->active == 0){ echo "in"; }?>active">
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks&action=toggleactive&id='.$task->id); ?>"></a>
						</td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records&' .
								'q[column]=task_id&q[operator]=e&q[value]='.$task->id); ?>">
								<?php echo $task->name; ?>
							</a>
						</td>
						<td><?php echo $task->hname; ?></td>
						<td style="text-align:center;"><?php echo $task->priority; ?></td>
						<td><?php echo $task->aname; ?></td>
						<td><?php echo $task->lname; ?></td>
						<td><?php echo ($task->start_date != '0000-00-00') ? JHTML::_('date', $task->start_date, '%m/%d/%Y', 0) : ''; ?></td>
						<td><?php echo ($task->end_date != '0000-00-00') ? JHTML::_('date', $task->end_date, '%m/%d/%Y', 0) : ''; ?>
							<div class="modifiers">
								<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks&action=edit&id='.$task->id); ?>"></a>
								<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks&action=delete&id='.$task->id); ?>"></a>
							</div>
						</td>
					</tr>
					<?php } // close foreach
				} else { // else count > 0 ?>
					<tr>
						<td colspan="9" class="no_tasks"><?php echo JText::_('PLG_TIME_TASKS_NONE_TO_DISPLAY'); ?></td>
					</tr>
				<?php } // close else ?>
			</tbody>
		</table>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks'); ?>">
			<?php echo $this->pageNav; ?>
		</form>
	</div>
</div>