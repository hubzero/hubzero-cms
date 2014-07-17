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

\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css('tasks')
     ->js('tasks');

$app = JFactory::getApplication();

// Set some ordering variables
$start   = ($this->filters['start']) ? '&start=' . $this->filters['start'] : '';
$sortcol = $app->getUserStateFromRequest("{$this->option}.{$this->controller}.orderby",  'orderby',  'name');
$dir     = $app->getUserStateFromRequest("{$this->option}.{$this->controller}.orderdir", 'orderdir', 'asc');
$newdir  = ($dir == 'asc') ? 'desc' : 'asc';
$base    = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_tasks">
		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="add icon-add btn" href="<?php echo JRoute::_($base . '&task=new'); ?>">
						<?php echo JText::_('COM_TIME_TASKS_NEW'); ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="container">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
				<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<form method="get" action="<?php echo JRoute::_($base); ?>">
				<div class="search-box">
					<a href="<?php echo JRoute::_($base . '&search='); ?>">
						<button type="button" class="clear-button btn btn-warning"><?php echo JText::_('COM_TIME_TASKS_CLEAR'); ?></button>
					</a>
					<input class="search-submit btn btn-success" type="submit" value="<?php echo JText::_('COM_TIME_TASKS_SEARCH'); ?>" />
					<fieldset class="search-text">
						<input id="search-input" type="text" name="search" placeholder="<?php echo JText::_('COM_TIME_TASKS_SEARCH_EXPLANATION'); ?>" value="<?php
								echo (is_array($this->filters['search']) && !empty($this->filters['search'][0])) ? implode(" ", $this->filters['search']) : ''; ?>" />
					</fieldset>
				</div>
			</form>
			<form method="get" action="<?php echo JRoute::_($base); ?>">
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
						<input class="btn btn-success" id="filter-submit" type="submit" value="<?php echo JText::_('+ Add filter'); ?>" />
						<input type="hidden" value="time_tasks" id="filter-table" />
					</p>
				</div><!-- / .filters -->
			</form>
			<?php if (!empty($this->filters['q']) || (is_array($this->filters['search']) && !empty($this->filters['search'][0]))) : ?>
				<div id="applied-filters">
					<p>Applied filters:</p>
					<ul class="filters-list">
						<?php if (!empty($this->filters['q'])) : ?>
							<?php foreach ($this->filters['q'] as $q) : ?>
								<li>
									<a href="<?php echo JRoute::_($base . '&q[column]=' . $q['column'] .
										'&q[operator]=' . $q['operator'] . '&q[value]=' . $q['value'] . '&q[delete]'); ?>"
										class="filters-x">x
									</a>
									<i><?php echo $q['human_column'] . ' ' . $q['human_operator']; ?></i>: <?php echo $q['human_value']; ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if (is_array($this->filters['search']) && !empty($this->filters['search'][0])) : ?>
							<li>
								<a href="<?php echo JRoute::_($base . '&search='); ?>" class="filters-x">x</a>
								<i>Search</i>: <?php echo implode(" ", $this->filters['search']); ?>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>
			<table class="entries">
				<caption><?php echo JText::_('COM_TIME_TASKS_CAPTION'); ?></caption>
				<thead>
					<tr>
						<td></td>
						<td>
							<a <?php if ($sortcol == 'name') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=name&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_TASKS_NAME'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'hname') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=hname&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_TASKS_HUB_NAME'); ?>
							</a>
						<td>
							<a <?php if ($sortcol == 'priority') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=priority&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_TASKS_PRIORITY'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'aname') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=aname&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_TASKS_ASSIGNEE_SHORT'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'lname') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=lname&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_TASKS_LIAISON_SHORT'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'start_date') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=start_date&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_TASKS_START_DATE'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'end_date') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=end_date&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_TASKS_END_DATE'); ?>
							</a>
						</td>
					</tr>
				</thead>
				<tbody>
					<?php if (count($this->tasks) > 0) : ?>
						<?php foreach ($this->tasks as $task) : ?>
						<tr<?php if ($task->active == 0) { echo ' class="inactive"'; } ?>>
							<td class="<?php if ($task->active == 0) { echo "in"; } ?>active">
								<a href="<?php echo JRoute::_($base . '&task=toggleactive&id=' . $task->id); ?>"></a>
							</td>
							<td>
								<a href="<?php echo JRoute::_($base . '&task=edit&id=' . $task->id); ?>">
									<?php echo $task->name; ?>
								</a>
							</td>
							<td><?php echo $task->hname; ?></td>
							<td style="text-align:center;"><?php echo $task->priority; ?></td>
							<td><?php echo $task->aname; ?></td>
							<td><?php echo $task->lname; ?></td>
							<td><?php echo ($task->start_date != '0000-00-00') ? JHTML::_('date', $task->start_date, 'm/d/y', null) : ''; ?></td>
							<td><?php echo ($task->end_date != '0000-00-00') ? JHTML::_('date', $task->end_date, 'm/d/y', null) : ''; ?></td>
						</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="9" class="no_tasks"><?php echo JText::_('COM_TIME_TASKS_NONE_TO_DISPLAY'); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<form action="<?php echo JRoute::_($base); ?>">
				<?php echo $this->pageNav; ?>
			</form>
		</div>
	</section>
</div>