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

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->css('overview')
     ->css('fullcalendar')
     ->js('overview');

\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect.css');
\Hubzero\Document\Assets::addSystemScript('flot/jquery.flot.min');
\Hubzero\Document\Assets::addSystemScript('flot/jquery.flot.pie.min');
\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemScript('moment.min');
\Hubzero\Document\Assets::addSystemScript('jquery.fullcalendar.min');

HTML::behavior('core');

$utc   = Date::of('now');
$now   = Date::of($utc)->toLocal(Lang::txt('g:00a'));
$then  = Date::of(strtotime($now . ' + 1 hour'))->toLocal(Lang::txt('g:00a'));
$start = Date::of($utc)->toLocal(Lang::txt('G'));
$end   = Date::of(strtotime($now . ' + 1 hour'))->toLocal(Lang::txt('G'));

?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_overview">
		<div class="overview-container">
			<div class="calendar"></div>
			<div class="details">
				<div class="details-inner">
					<div class="details-explanation">
						<p>
							Drag and select a time-range from the calendar on the left to create a new time entry,
							or click an existing entry to edit.
						</p>
					</div>
					<form action="<?php echo Route::url('/api/time/postRecord'); ?>" class="details-data" method="POST">
						<div class="grouping" id="hub-group">
							<label for="hub_id">
								<?php echo Lang::txt('COM_TIME_OVERVIEW_HUB'); ?>:
								<span class="hub-error error-message"><?php echo Lang::txt('COM_TIME_OVERVIEW_PLEASE_SELECT_HUB'); ?></span>
							</label>
							<select name="hub_id" id="hub_id" tabindex="1">
								<option value=""><?php echo Lang::txt('COM_TIME_NO_HUB'); ?></option>
								<?php foreach (Hub::all()->ordered() as $hub) : ?>
									<option value="<?php echo $hub->id; ?>">
										<?php echo $hub->name; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="grouping" id="task-group">
							<label for="task">
								<?php echo Lang::txt('COM_TIME_OVERVIEW_TASK'); ?>:
								<span class="task-error error-message"><?php echo Lang::txt('COM_TIME_OVERVIEW_PLEASE_SELECT_TASK'); ?></span>
							</label>
							<select name="task_id" id="task_id" tabindex="2">
								<option value=""><?php echo Lang::txt('COM_TIME_RECORDS_NO_HUB_SELECTED'); ?></option>
								<?php foreach ($tasks = Task::all()->ordered() as $task) : ?>
									<option value="<?php echo $task->id; ?>">
										<?php echo $task->name; ?>
									</option>
								<?php endforeach; ?>
								<?php if (!$tasks->count()) : ?>
									<option value=""><?php echo Lang::txt('COM_TIME_RECORDS_NO_TASKS_AVAILABLE'); ?></option>
								<?php endif; ?>
							</select>
						</div>

						<div class="grouping" id="description-group">
							<label for="description"><?php echo Lang::txt('COM_TIME_OVERVIEW_DESCRIPTION'); ?>:</label>
							<textarea name="description" id="description" rows="6" cols="50" tabIndex="3"></textarea>
						</div>

						<input type="hidden" name="id" class="details-id" value="" />
						<input type="hidden" name="start" class="details-start" value="" />
						<input type="hidden" name="end" class="details-end" value="" />

						<p class="submit">
							<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_TIME_OVERVIEW_SAVE'); ?>" tabIndex="4" />
							<a href="#" class="details-cancel">
								<button type="button" class="btn btn-secondary">
									<?php echo Lang::txt('COM_TIME_OVERVIEW_CANCEL'); ?>
								</button>
							</a>
						</p>
					</form>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="plots-container">
			<div class="hourly-wrap">
				<div class="section-header"><h3><?php echo Lang::txt('COM_TIME_OVERVIEW_HOURS_THIS_WEEK'); ?></h3></div>
				<div class="hourly">
					<div class="pie-half1">
					<div class="pie-half2">
						<div class="inner-pie">
							<div class="hours">0hrs</div>
						</div>
					</div>
					</div>
				</div>
			</div>
			<div class="week-overview-wrap">
				<div class="section-header"><h3><?php echo Lang::txt('COM_TIME_OVERVIEW_DAILY_HOURS'); ?></h3></div>
				<div class="week-overview"></div>
			</div>
		</div>
	</section>
</div>