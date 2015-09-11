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

// No direct access
defined('_HZEXEC_') or die();

require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'task.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'hub.php';

use Components\Time\Models\Task;
use Components\Time\Models\Hub;

Html::behavior('core');

$this->css()
     ->js();
?>

<fieldset>
	<legend><?php echo Lang::txt('PLG_SUPPORT_TIME'); ?></legend>

	<div class="plg_support_time">
		<div class="grid">
			<div class="col span6">
				<div class="time-group">
					<span><?php echo Lang::txt('PLG_SUPPORT_TIME_TIME'); ?>:</span>
					<div>
						<select name="htime">
							<?php for ($i=0; $i < 24; $i++) : ?>
								<option value="<?php echo $i; ?>">
									<?php echo $i; ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>
					<div>
						<select name="mtime">
							<option value="0" >:00</option>
							<option value="25">:15</option>
							<option value="5" >:30</option>
							<option value="75">:45</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col span6 omega">
				<label for="date">
					<?php echo Lang::txt('PLG_SUPPORT_TIME_DATE'); ?>:
					<input type="text" name="date" value="<?php echo Date::format('Y-m-d') ?>" size="10" />
				</label>
			</div>
		</div>
		<div class="clear"></div>

		<div class="grouping">
			<label for="hub_id"><?php echo Lang::txt('PLG_SUPPORT_TIME_HUB'); ?>:
				<select name="hub_id" id="hub_id">
					<option value=""><?php echo Lang::txt('PLG_SUPPORT_TIME_NO_HUB_SELECTED'); ?></option>
					<?php foreach (Hub::all()->order('name', 'asc') as $hub) : ?>
						<option value="<?php echo $hub->id; ?>">
							<?php echo $hub->name; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>

			<label for="task_id"><?php echo Lang::txt('PLG_SUPPORT_TIME_TASK'); ?>:
				<select name="task_id" id="task_id">
					<option value=""><?php echo Lang::txt('PLG_SUPPORT_TIME_NO_HUB_SELECTED'); ?></option>
					<?php foreach ($tasks = Task::all()->order('name', 'asc') as $task) : ?>
						<option value="<?php echo $task->id; ?>">
							<?php echo $task->name; ?>
						</option>
					<?php endforeach; ?>
					<?php if (!$tasks->count()) : ?>
						<option value=""><?php echo Lang::txt('PLG_SUPPORT_TIME_NO_TASKS_AVAILABLE'); ?></option>
					<?php endif; ?>
				</select>
			</label>
		</div>
		<div class="clear"></div>

		<input type="hidden" name="records[id]" value="0" />
	</div>
</fieldset>