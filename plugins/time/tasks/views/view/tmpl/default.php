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

$dateFormat = '%m/%d/%Y';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'm/d/Y';
	$tz = null;
}

// Set some ordering variables
$start   = ($this->filters['start']) ? '&start='.$this->filters['start'] : '';
$imgasc  = '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'sort_asc.png">';
$imgdesc = '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'sort_desc.png">';
$sortcol = $this->mainframe->getUserStateFromRequest("$this->option.$this->active.orderby", 'orderby', 'name');
$dir     = $this->mainframe->getUserStateFromRequest("$this->option.$this->active.orderdir", 'orderdir', 'asc');
$newdir  = ($dir == 'asc') ? 'desc' : 'asc';
?>

<div id="plg_time_tasks">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="add icon-add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks&action=new'); ?>">
					<?php echo JText::_('PLG_TIME_TASKS_NEW'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="container">
		<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option.'&active='.$this->active); ?>">
			<div class="search-box">
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active='.$this->active.'&search='); ?>">
					<span class="clear-button"><?php echo JText::_('PLG_TIME_TASKS_CLEAR'); ?></span>
				</a>
				<input class="search-submit" type="submit" value="<?php echo JText::_('PLG_TIME_TASKS_SEARCH'); ?>" />
				<fieldset class="search-text">
					<input id="search-input" type="text" name="search" placeholder="<?php echo JText::_('PLG_TIME_TASKS_SEARCH_EXPLANATION'); ?>" value="<?php 
							echo (is_array($this->filters['search']) && !empty($this->filters['search'][0])) ? implode(" ", $this->filters['search']) : ''; ?>" />
				</fieldset>
			</div><!-- / .search-box -->
		</form>
		<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option.'&active='.$this->active); ?>">
			<div id="add-filters">
				<p>Filter results:
					<select name="q[column]" id="filter-column">
						<?php foreach ($this->cols as $c) { ?>
							<option value="<?php echo $c['raw']; ?>"><?php echo $c['human']; ?></option>
						<?php } // end foreach $cols ?>
					</select>
					<?php echo $this->operators; ?>
					<select name="q[value]" id="filter-value">
					</select>
					<input id="filter-submit" type="submit" value="<?php echo JText::_('+ Add filter'); ?>" />
					<input type="hidden" value="time_tasks" id="filter-table" />
				</p>
			</div><!-- / .filters -->
		</form>
		<?php
			if(!empty($this->filters['q']) || (is_array($this->filters['search']) && !empty($this->filters['search'][0])))
			{
				echo '<div id="applied-filters">';
				echo '<p>Applied filters:</p>';
				echo '<ul class="filters-list">';
				if(!empty($this->filters['q']))
				{
					foreach($this->filters['q'] as $q)
					{
						echo '<li><a href="' .
							JRoute::_('index.php?option='.$this->option.'&active='.$this->active.'&q[column]=' . $q['column'] .
								'&q[operator]=' . $q['operator'] . '&q[value]=' . $q['value'] . '&q[delete]') .
							'" class="filters-x">x</a><i>' . $q['human_column'] . ' ' . $q['human_operator'] . '</i>: ' .
							$q['human_value'] . '</li>';
					}
				}
				if(is_array($this->filters['search']) && !empty($this->filters['search'][0]))
				{
					echo '<li><a href="' .
						JRoute::_('index.php?option='.$this->option.'&active='.$this->active.'&search=') .
						'" class="filters-x">x</a><i>Search</i>: ' . implode(" ", $this->filters['search']) . '</li>';
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
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=priority&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_PRIORITY'); ?>
						</a>
						<?php if($sortcol == 'priority') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=aname&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_ASSIGNEE_SHORT'); ?>
						</a>
						<?php if($sortcol == 'aname') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=lname&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_TASKS_LIAISON_SHORT'); ?>
						</a>
						<?php if($sortcol == 'lname') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
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
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks&action=edit&id='.$task->id); ?>">
								<?php echo $task->name; ?>
							</a>
						</td>
						<td><?php echo $task->hname; ?></td>
						<td style="text-align:center;"><?php echo $task->priority; ?></td>
						<td><?php echo $task->aname; ?></td>
						<td><?php echo $task->lname; ?></td>
						<td><?php echo ($task->start_date != '0000-00-00') ? JHTML::_('date', $task->start_date, $dateFormat, $tz) : ''; ?></td>
						<td><?php echo ($task->end_date != '0000-00-00') ? JHTML::_('date', $task->end_date, $dateFormat, $tz) : ''; ?></td>
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
