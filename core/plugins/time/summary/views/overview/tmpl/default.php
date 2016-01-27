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

use Components\Time\Models\Hub;
use Components\Time\Models\Task;

// No direct access
defined('_HZEXEC_') or die();

\Hubzero\Document\Assets::addSystemScript('select2');
\Hubzero\Document\Assets::addSystemScript('jquery.flot.min', 'flot');
\Hubzero\Document\Assets::addSystemStylesheet('select2');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui');

$this->css()
     ->js();

HTML::behavior('core');

$base = 'index.php?option=com_time&controller=reports';
?>

<div class="plg_time_summary">
	<div class="filters">
		<form action="<?php echo Route::url($base); ?>">
			<input type="hidden" name="report_type" value="summary" />
			<div class="grouping">
				<label for="hub_id"><?php echo Lang::txt('PLG_TIME_SUMMARY_HUB_NAME'); ?>: </label>
				<select name="hub_id" id="hub_id">
					<option value=""><?php echo Lang::txt('PLG_TIME_SUMMARY_NO_HUB_SELECTED'); ?></option>
					<?php foreach (Hub::all()->order('name', 'asc') as $hub) : ?>
						<?php if ($this->permissions->can('view.report', 'hub', $hub->id)) : ?>
							<option value="<?php echo $hub->id; ?>" <?php echo ($hub->id == $this->hub_id) ? 'selected="selected"' : ''; ?>>
								<?php echo $hub->name; ?>
							</option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="grouping">
				<label for="task_id"><?php echo Lang::txt('PLG_TIME_SUMMARY_TASK_NAME'); ?>: </label>
				<select name="task_id" id="task_id">
					<option value=""><?php echo Lang::txt('PLG_TIME_SUMMARY_NO_TASK_SELECTED'); ?></option>
					<?php $tasks = Task::all()->order('name', 'asc'); ?>
					<?php if ($this->hub_id) $tasks->whereEquals('hub_id', $this->hub_id); ?>
					<?php foreach ($tasks as $task) : ?>
						<?php if ($this->permissions->can('view.report', 'hub', $task->hub_id)) : ?>
							<option value="<?php echo $task->id; ?>" <?php echo ($task->id == $this->task_id) ? 'selected="selected"' : ''; ?>>
								<?php echo $task->name; ?>
							</option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="grouping">
				<label for="start_date"><?php echo Lang::txt('PLG_TIME_SUMMARY_START_DATE'); ?>: </label>
				<input type="text" id="start_date" name="start_date" class="hadDatepicker" value="<?php echo $this->start; ?>" />
			</div>
			<div class="grouping">
				<label for="end_date"><?php echo Lang::txt('PLG_TIME_SUMMARY_END_DATE'); ?>: </label>
				<input type="text" id="end_date" name="end_date" class="hadDatepicker" value="<?php echo $this->end; ?>" />
			</div>
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_TIME_SUMMARY_FILTER'); ?>" />
			<a href="<?php echo Route::url($base . '&report_type=summary'); ?>">
				<button class="btn btn-warning" type="button">
					<?php echo Lang::txt('PLG_TIME_SUMMARY_CLEAR'); ?>
				</button>
			</a>
		</form>
	</div>
	<?php if (count($this->hubs) > 0) : ?>
		<div class="charts">
			<div class="overview">
				<h3>Overview</h3>
				<div class="tasks-bar"></div>
			</div>
			<div class="report">
			<h3>Report</h3>
				<div class="reports">
					<?php foreach ($this->hubs as $hub) : ?>
						<div class="report-hub">
							<div class="clickable">
								<div class="report-hub-name"><?php echo $hub['name']; ?></div>
								<div class="report-hub-hours">
									<?php echo (isset($hub['total']) ? $hub['total'] : '0') . ' hour' . (isset($hub['total']) && $hub['total'] != 1 ? 's' : ''); ?>
								</div>
								<div class="report-filler">blah</div>
							</div>
							<div class="respondable">
								<?php if (isset($hub['tasks']) && count($hub['tasks']) > 0) : ?>
									<?php foreach ($hub['tasks'] as $task) : ?>
										<div class="report-task">
											<div class="report-task-name"><?php echo $task['name']; ?></div>
											<div class="report-task-hours">
												<?php echo (isset($task['total']) ? $task['total'] : '0') . ' hour' . (isset($task['total']) && $task['total'] != 1 ? 's' : ''); ?>
											</div>
											<div class="report-filler">blah</div>
										</div>
										<?php if (isset($task['records']) && count($task['records']) > 0) : ?>
											<?php foreach ($task['records'] as $record) : ?>
												<div class="report-record">
													<div class="report-record-name">
														<?php echo $record->user->name . ' - '; ?>
														<?php echo $record->description ?: '[no description available]'; ?>
													</div>
													<div class="report-record-hours"><?php echo $record->time . ' hour' . ($record->time != 1 ? 's' : ''); ?></div>
													<div class="report-filler">blah</div>
												</div>
											<?php endforeach; ?>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php else : ?>
		<p class="warning no-data">No data available for these parameters.</p>
	<?php endif; ?>
</div>