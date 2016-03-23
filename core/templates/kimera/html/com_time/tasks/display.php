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
		<?php /*<div class="container">
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
		</div>*/ ?>

		<div class="wrapper">
			<div class="entris">
				<div class="bucket">
					<?php foreach ($this->tasks as $task) : ?>
						<div class="task<?php if ($task->active == 0) { echo ' inactive'; } ?>">
							<div class="contents">
								<div class="details">
									<div class="hub"><?php echo $task->hub->name; ?></div>
									<div class="task"><?php echo String::highlight($task->name, $this->filters['search'], array('html' => true)); ?></div>
									<!-- <div class="description"><?php echo \Hubzero\Utility\String::truncate($task->description, 200); ?></div> -->
									<div class="priority priority<?php echo $task->priority; ?>"><span><?php
											switch ($task->priority)
											{
												case 0: echo 'Unknown'; break;
												case 1: echo 'Trivial'; break;
												case 2: echo 'Minor'; break;
												case 3: echo 'Normal'; break;
												case 4: echo 'Major'; break;
												case 5: echo 'Critical'; break;
											}
									?></span></div>
								</div>
							<?php if ($task->active) { ?>
								<div class="schedule">
									<?php if ($task->start_date == '0000-00-00' && $task->end_date == '0000-00-00') { ?>
										<span class="no-date">Unscheduled</span>
									<?php } else { ?>
										<span class="start-date"><?php echo ($task->start_date != '0000-00-00') ? Date::of($task->start_date)->toLocal('m/d/y') : ''; ?></span> &mdash;
										<span class="end-date"><?php echo ($task->end_date != '0000-00-00') ? Date::of($task->end_date)->toLocal('m/d/y') : 'On-going'; ?></span>
										<?php if ($task->end_date != '0000-00-00')
										{
											echo '<span class="status';
											if ($task->end_date > Date::of('now')->format('Y-m-d'))
											{
												echo ' on-time">On-time';
											}
											else
											{
												echo ' over-due">Over-due';
											}
											echo '</span>';
										}
									}
									?>
								</div>
								<div class="meta">
									<div class="grid">
										<div class="col span8">
											<!--
											<span class="opt icon-paperclip">0</span>
											<span class="opt icon-note">0</span>
											-->
											<span class="opt icon-hours"><?php echo 5; //$task->hours(); ?> hrs</span>
										</div>
										<div class="col span4 omega">
											<a data-id="<?php echo $task->id; ?>" href="<?php echo Route::url($this->base . '&task=edit&id=' . $task->id); ?>" class="assigned <?php echo $task->assignee->name ? 'yes' : 'no';?>" data-hint="Click to change assignee"><?php echo $task->assignee->name ? 'assigned' : 'unassigned';?></a>
											<form id="assigner<?php echo $task->id; ?>" action="<?php echo Route::url($this->base . '&task=save&id=' . $task->id); ?>" method="post" class="assigner hide">
												<fieldset>
													<label for="assignee_id"><?php echo Lang::txt('COM_TIME_TASKS_ASSIGNEE'); ?>:</label>
													<select name="assignee_id" id="assignee_id">
														<option value="0"><?php echo Lang::txt('COM_TIME_NO_ASSIGNEE'); ?></option>
														<?php if ($group = \Hubzero\User\Group::getInstance(Component::params('com_time')->get('accessgroup', 'time'))) : ?>
															<?php foreach ($group->get('members') as $member) : ?>
																<option value="<?php echo $member; ?>" <?php echo ($task->assignee_id == $member) ? 'selected="selected"': '';?>>
																	<?php echo User::getInstance($member)->get('name'); ?>
																</option>
															<?php endforeach; ?>
														<?php endif; ?>
													</select>
													<input type="submit" class="btn" value="Save" />
													<input type="hidden" name="id" value="<?php echo $task->id; ?>" />
													<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
													<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
													<input type="hidden" name="task" value="save" />
												</fieldset>
											</form>
										</div>
									</div>
								</div>

							<!-- <div class="tds <?php if ($task->active == 0) { echo "in"; } ?>active">
								<a href="<?php echo Route::url($this->base . '&task=toggleactive&id=' . $task->id); ?>"></a>
							</div>
							<a href="<?php echo Route::url($this->base . '&task=edit&id=' . $task->id); ?>">
									<?php echo String::highlight($task->name, $this->filters['search'], array('html' => true)); ?>
								</a>
							<div class="tds">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_HUB_NAME'); ?>:</div>
								<div class="small-content">
									<?php echo $task->hub->name; ?>
								</div>
							</div>
							<div class="tds">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_PRIORITY'); ?>:</div>
								<div class="small-content">
									<?php echo $task->priority; ?>
								</div>
							</div>
							<div class="tds">
							<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_ASSIGNEE_SHORT'); ?>:</div>
								<div class="small-content">
									<?php echo $task->assignee->name; ?>
								</div>
							</div>
							<div class="tds">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_LIAISON_SHORT'); ?>:</div>
								<div class="small-content">
									<?php echo $task->liaison->name; ?>
								</div>
							</div>
							<div class="tds">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_START_DATE'); ?>:</div>
								<div class="small-content">
									<?php echo ($task->start_date != '0000-00-00') ? Date::of($task->start_date)->toLocal('m/d/y') : ''; ?>
								</div>
							</div>
							<div class="tds">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_TASKS_END_DATE'); ?>:</div>
								<div class="small-content">
									<?php echo ($task->end_date != '0000-00-00') ? Date::of($task->end_date)->toLocal('m/d/y') : ''; ?>
								</div>
							</div> -->
							<?php } else { ?>
							<div class="meta">
								<div class="grid">
									<div class="col span6">
										<span class="opt completed icon-check">Completed</span>
									</div>
									<div class="col span6 omega">
										<a href="<?php echo Route::url($this->base . '&task=toggleactive&id=' . $task->id); ?>">Reactivate</a>
									</div>
								</div>
							</div>
						<?php } ?>
							</div>
						</div>
					<?php endforeach; ?>
					<?php if (!$this->tasks->count()) : ?>
						<div class="trs">
							<div class="td no_tasks"><?php echo Lang::txt('COM_TIME_TASKS_NONE_TO_DISPLAY'); ?></div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
			<form action="<?php echo Route::url($this->base); ?>">
				<?php echo $this->tasks->pagination; ?>
			</form>

	</section>
</div>
<script>
jQuery(document).ready(function($){
	$('a.assigned').on('click', function(e){
		e.preventDefault();

		$(this).toggleClass('active');
		$(this).next().toggle();
	});
});
</script>