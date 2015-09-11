<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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