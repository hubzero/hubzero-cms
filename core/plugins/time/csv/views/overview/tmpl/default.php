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

// No direct access
defined('_HZEXEC_') or die();

\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui');

$this->css()
     ->js();

$options = array();
$base    = 'index.php?option=com_time&controller=reports';

// If no incoming fields vars selected, we'll assume we should show all
$all  = true;
foreach (Request::query() as $key => $value)
{
	if (strpos($key, 'fields-') !== false)
	{
		$all = false;
	}
}
?>

<div class="plg_time_csv">
	<?php if ($this->records->count()) : ?>
		<a target="_blank" href="<?php echo Route::url($base . '&' . JURI::getInstance()->getQuery() . '&method=download'); ?>">
			<div class="download btn icon-save">Download</div>
		</a>
	<?php endif; ?>
	<div class="filters">
		<form action="<?php echo Route::url($base); ?>">
			<input type="hidden" name="report_type" value="csv" />
			<div class="grouping">
				<label for="hub_id"><?php echo Lang::txt('PLG_TIME_CSV_HUB_NAME'); ?>: </label>
				<select name="hub_id" id="hub_id">
					<option value=""><?php echo Lang::txt('PLG_TIME_CSV_NO_HUB_SELECTED'); ?></option>
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
				<label for="start_date"><?php echo Lang::txt('PLG_TIME_CSV_START_DATE'); ?>: </label>
				<input type="text" id="start_date" name="start_date" class="hadDatepicker" value="<?php echo $this->start; ?>" />
			</div>
			<div class="grouping">
				<label for="end_date"><?php echo Lang::txt('PLG_TIME_CSV_END_DATE'); ?>: </label>
				<input type="text" id="end_date" name="end_date" class="hadDatepicker" value="<?php echo $this->end; ?>" />
			</div>
			<div class="grouping">
				<div><?php echo Lang::txt('PLG_TIME_CSV_FIELDS'); ?>:</div>
				<input type="checkbox" name="fields-hub" value="1" <?php echo ($hub = Request::getInt('fields-hub', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-hub"><?php echo Lang::txt('PLG_TIME_CSV_HUB'); ?></label>
				<br />
				<input type="checkbox" name="fields-task" value="1" <?php echo ($task = Request::getInt('fields-task', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-task"><?php echo Lang::txt('PLG_TIME_CSV_TASK'); ?></label>
				<br />
				<input type="checkbox" name="fields-user" value="1" <?php echo ($user = Request::getInt('fields-user', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-user"><?php echo Lang::txt('PLG_TIME_CSV_USER'); ?></label>
				<br />
				<input type="checkbox" name="fields-date" value="1" <?php echo ($date = Request::getInt('fields-date', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-date"><?php echo Lang::txt('PLG_TIME_CSV_DATE'); ?></label>
				<br />
				<input type="checkbox" name="fields-time" value="1" <?php echo ($time = Request::getInt('fields-time', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-time"><?php echo Lang::txt('PLG_TIME_CSV_TIME'); ?></label>
				<br />
				<input type="checkbox" name="fields-description" value="1" <?php echo ($description = Request::getInt('fields-description', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-description"><?php echo Lang::txt('PLG_TIME_CSV_DESCRIPTION'); ?></label>
			</div>
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_TIME_CSV_FILTER'); ?>" />
			<a href="<?php echo Route::url($base . '&report_type=csv'); ?>">
				<button class="btn btn-warning" type="button">
					<?php echo Lang::txt('PLG_TIME_CSV_CLEAR'); ?>
				</button>
			</a>
		</form>
	</div>
	<?php if ($this->records->count()) : ?>
		<h3>Preview</h3>
		<div class="preview">
			<div class="preview-header">
				<?php if ($hub) : ?>
					<div class="preview-field hname">
						<?php echo Lang::txt('PLG_TIME_CSV_HUB'); ?>
					</div>
				<?php endif; ?>
				<?php if ($task) : ?>
					<div class="preview-field pname">
						<?php echo Lang::txt('PLG_TIME_CSV_TASK'); ?>
					</div>
				<?php endif; ?>
				<?php if ($user) : ?>
					<div class="preview-field uname">
						<?php echo Lang::txt('PLG_TIME_CSV_USER'); ?>
					</div>
				<?php endif; ?>
				<?php if ($date) : ?>
					<div class="preview-field date">
						<?php echo Lang::txt('PLG_TIME_CSV_DATE'); ?>
					</div>
				<?php endif; ?>
				<?php if ($time) : ?>
					<div class="preview-field time">
						<?php echo Lang::txt('PLG_TIME_CSV_TIME'); ?>
					</div>
				<?php endif; ?>
				<?php if ($description) : ?>
					<div class="preview-field description">
						<?php echo Lang::txt('PLG_TIME_CSV_DESCRIPTION'); ?>
					</div>
				<?php endif; ?>
			</div>
			<?php foreach ($this->records as $record) : ?>
				<?php if ($this->permissions->can('view.report', 'hub', $record->task->hub_id)) : ?>
					<div class="preview-row">
						<?php if ($hub) : ?>
							<div class="preview-field hname">
								<?php echo $record->task->hub->name; ?>
							</div>
						<?php endif; ?>
						<?php if ($task) : ?>
							<div class="preview-field pname">
								<?php echo $record->task->name; ?>
							</div>
						<?php endif; ?>
						<?php if ($user) : ?>
							<div class="preview-field uname">
								<?php echo $record->user->name; ?>
							</div>
						<?php endif; ?>
						<?php if ($date) : ?>
							<div class="preview-field date">
								<?php echo Date::of($record->date)->toLocal(); ?>
							</div>
						<?php endif; ?>
						<?php if ($time) : ?>
							<div class="preview-field time">
								<?php echo $record->time; ?>
							</div>
						<?php endif; ?>
						<?php if ($description) : ?>
							<div class="preview-field description">
								<?php echo $record->description; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p class="warning no-data"><?php echo Lang::txt('PLG_TIME_CSV_NO_DATA_AVAILABLE'); ?></p>
	<?php endif; ?>
</div>