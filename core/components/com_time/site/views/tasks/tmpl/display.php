<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Components\Time\Helpers\Filters;
use Hubzero\Utility\String;

// No direct access.
defined('_HZEXEC_') or die();

\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css('tasks')
     ->js('tasks');

// Set some ordering variables
$sortcol = $this->tasks->orderBy;
$dir     = $this->tasks->orderDir;
$newdir  = ($dir == 'asc') ? 'desc' : 'asc';

HTML::behavior('core');
?>

<div id="dialog-confirm"></div>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="add icon-add btn" href="<?php echo Route::url($this->base . '&task=new'); ?>">
					<?php echo Lang::txt('COM_TIME_TASKS_NEW'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_tasks">
		<div class="container">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
				<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<form method="get" action="<?php echo Route::url($this->base); ?>">
				<div class="search-box">
					<a href="<?php echo Route::url($this->base . '&search='); ?>">
						<button type="button" class="clear-button btn btn-warning"><?php echo Lang::txt('COM_TIME_TASKS_CLEAR'); ?></button>
					</a>
					<input class="search-submit btn btn-success" type="submit" value="<?php echo Lang::txt('COM_TIME_TASKS_SEARCH'); ?>" />
					<fieldset class="search-text">
						<input id="search-input" type="text" name="search" placeholder="<?php echo Lang::txt('COM_TIME_TASKS_SEARCH_EXPLANATION'); ?>" value="<?php
								echo (is_array($this->filters['search']) && !empty($this->filters['search'][0])) ? implode(" ", $this->filters['search']) : ''; ?>" />
					</fieldset>
				</div>
			</form>
			<form method="get" action="<?php echo Route::url($this->base); ?>">
				<div id="add-filters">
					<p>Filter results:
						<select name="q[column]" id="filter-column">
							<?php foreach (Filters::getColumnNames('time_tasks', array("id", "description")) as $c) : ?>
								<option value="<?php echo $c['raw']; ?>"><?php echo $c['human']; ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo Filters::buildSelectOperators(); ?>
						<select name="q[value]" id="filter-value">
						</select>
						<input class="btn btn-success" id="filter-submit" type="submit" value="<?php echo Lang::txt('+ Add filter'); ?>" />
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
									<a href="<?php echo Route::url($this->base . '&q[column]=' . $q['column'] .
										'&q[operator]=' . $q['operator'] . '&q[value]=' . $q['value'] . '&q[delete]'); ?>"
										class="filters-x">x
									</a>
									<i><?php echo $q['human_column'] . ' ' . $q['human_operator']; ?></i>: <?php echo $q['human_value']; ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if (is_array($this->filters['search']) && !empty($this->filters['search'][0])) : ?>
							<li>
								<a href="<?php echo Route::url($this->base . '&search='); ?>" class="filters-x">x</a>
								<i>Search</i>: <?php echo implode(" ", $this->filters['search']); ?>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>
			<div class="actions">
				<a class="action merge" href="#" data-target="<?php echo Route::url($this->base . '&task=merge'); ?>">
					<?php echo Lang::txt('COM_TIME_TASKS_MERGE'); ?>
				</a>
				<a class="action edit" href="#" data-target="<?php echo Route::url($this->base . '&task=edit'); ?>">
					<?php echo Lang::txt('COM_TIME_TASKS_EDIT'); ?>
				</a>
				<a class="action delete" href="#" data-target="<?php echo Route::url($this->base . '&task=delete'); ?>">
					<?php echo Lang::txt('COM_TIME_TASKS_DELETE'); ?>
				</a>
			</div>
			<div class="entries table">
				<div class="thead">
					<div class="tr">
						<div class="th"></div>
						<div class="th">
							<a <?php if ($sortcol == 'name') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=name&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_TASKS_NAME'); ?>
							</a>
						</div>
						<div class="th">
							<a <?php if ($sortcol == 'hub.name') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=hub.name&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_TASKS_HUB_NAME'); ?>
							</a>
						</div>
						<div class="th">
							<a <?php if ($sortcol == 'priority') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=priority&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_TASKS_PRIORITY'); ?>
							</a>
						</div>
						<div class="th">
							<a <?php if ($sortcol == 'assignee.name') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=assignee.name&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_TASKS_ASSIGNEE_SHORT'); ?>
							</a>
						</div>
						<div class="th">
							<a <?php if ($sortcol == 'liaison.name') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=liaison.name&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_TASKS_LIAISON_SHORT'); ?>
							</a>
						</div>
						<div class="th">
							<a <?php if ($sortcol == 'start_date') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=start_date&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_TASKS_START_DATE'); ?>
							</a>
						</div>
						<div class="th">
							<a <?php if ($sortcol == 'end_date') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=end_date&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_TASKS_END_DATE'); ?>
							</a>
						</div>
					</div>
				</div>
				<div class="tbody">
					<?php foreach ($this->tasks as $task) : ?>
						<div class="tr<?php if ($task->active == 0) { echo ' inactive'; } ?>" data-id="<?php echo $task->id; ?>" data-name="<?php echo $task->name; ?>">
							<div class="td <?php if ($task->active == 0) { echo "in"; } ?>active">
								<a href="<?php echo Route::url($this->base . '&task=toggleactive&id=' . $task->id); ?>"></a>
							</div>
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_NAME'); ?>:</div>
								<div class="small-content">
									<a class="not-selectable" href="<?php echo Route::url($this->base . '&task=edit&id=' . $task->id); ?>">
										<?php echo String::highlight($task->name, $this->filters['search'], array('html' => true)); ?>
									</a>
								</div>
							</div>
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_HUB_NAME'); ?>:</div>
								<div class="small-content">
									<?php echo $task->hub->name; ?>
								</div>
							</div>
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_PRIORITY'); ?>:</div>
								<div class="small-content">
									<?php echo $task->priority; ?>
								</div>
							</div>
							<div class="td">
							<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_ASSIGNEE_SHORT'); ?>:</div>
								<div class="small-content">
									<?php echo $task->assignee->name; ?>
								</div>
							</div>
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_LIAISON_SHORT'); ?>:</div>
								<div class="small-content">
									<?php echo $task->liaison->name; ?>
								</div>
							</div>
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_START_DATE'); ?>:</div>
								<div class="small-content">
									<?php echo ($task->start_date != '0000-00-00') ? Date::of($task->start_date)->toLocal('m/d/y') : ''; ?>
								</div>
							</div>
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_END_DATE'); ?>:</div>
								<div class="small-content">
									<?php echo ($task->end_date != '0000-00-00') ? Date::of($task->end_date)->toLocal('m/d/y') : ''; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
					<?php if (!$this->tasks->count()) : ?>
						<div class="tr">
							<div class="td no_tasks"><?php echo Lang::txt('COM_TIME_TASKS_NONE_TO_DISPLAY'); ?></div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<form action="<?php echo Route::url($this->base); ?>">
				<?php echo $this->tasks->pagination; ?>
			</form>
		</div>
	</section>
</div>