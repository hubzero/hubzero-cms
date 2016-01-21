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
use Components\Time\Models\Liaison;

// No direct access.
defined('_HZEXEC_') or die();

$this->js('jquery.fancyselect', 'system')
     ->css('jquery.fancyselect', 'system')
     ->css('jquery.ui.css', 'system');

$this->css()
     ->css('tasks')
     ->js('time');
?>

<div id="dialog-confirm"></div>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a class="icon-reply btn" href="<?php echo Route::url($this->base . $this->start); ?>">
					<?php echo Lang::txt('COM_TIME_TASKS_ALL_TASKS'); ?>
				</a>
			</li>
			<li class="last">
				<a class="delete icon-delete btn" href="<?php echo Route::url($this->base . '&task=delete&id=' . $this->row->id); ?>">
					<?php echo Lang::txt('COM_TIME_TASKS_DELETE'); ?>
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
			<form action="<?php echo Route::url($this->base . '&task=save'); ?>" method="post">
				<div class="grouping" id="name-group">
					<label for="name"><?php echo Lang::txt('COM_TIME_TASKS_NAME'); ?>:</label>
					<input type="text" name="name" id="name" value="<?php echo $this->escape($this->row->name); ?>" size="50" />
				</div>

				<div class="grouping" id="active-group">
					<label><?php echo Lang::txt('COM_TIME_TASKS_ACTIVE'); ?>:</label>
					<input type="radio" name="active" id="active_yes" value="1" <?php if ($this->row->active == 1 || $this->row->active === NULL) { echo "checked"; } ?> />Yes
					<input type="radio" name="active" id="active_no" value="0" <?php if ($this->row->active !== NULL && $this->row->active == 0) { echo "checked"; } ?> />No
				</div>

				<div class="grouping" id="hub-group">
					<label for="hub_id"><?php echo Lang::txt('COM_TIME_TASKS_HUB_NAME'); ?>:</label>
					<select name="hub_id" id="hub_id">
						<option value=""><?php echo Lang::txt('COM_TIME_NO_HUB'); ?></option>
						<?php foreach (Hub::whereEquals('active', 1)->order('name', 'asc') as $hub) : ?>
							<option <?php echo ($hub->id == $this->row->hub->id) ? 'selected="selected" ': ''; ?>value="<?php echo $hub->id; ?>">
								<?php echo $hub->name; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="grouping" id="startdate-group">
					<label for="startdate"><?php echo Lang::txt('COM_TIME_TASKS_START_DATE'); ?>:</label>
					<input type="text" name="start_date" id="startdate" class="hadDatepicker" value="<?php echo $this->escape($this->row->start_date); ?>" size="10" />
				</div>

				<div class="grouping" id="enddate-group">
					<label for="enddate"><?php echo Lang::txt('COM_TIME_TASKS_END_DATE'); ?>:</label>
					<input type="text" name="end_date" id="enddate" class="hadDatepicker" value="<?php echo $this->escape($this->row->end_date); ?>" size="10" />
				</div>

				<div class="grouping" id="priority-group">
					<label for="priority"><?php echo Lang::txt('COM_TIME_TASKS_PRIORITY'); ?>:</label>
					<select name="priority" id="priority">
						<option <?php echo ($this->row->get('priority', 3) == 0) ? 'selected="selected"': ''; ?>value="0">(0) Unknown</option>
						<option <?php echo ($this->row->get('priority', 3) == 1) ? 'selected="selected"': ''; ?>value="1">(1) Trivial</option>
						<option <?php echo ($this->row->get('priority', 3) == 2) ? 'selected="selected"': ''; ?>value="2">(2) Minor</option>
						<option <?php echo ($this->row->get('priority', 3) == 3) ? 'selected="selected"': ''; ?>value="3">(3) Normal</option>
						<option <?php echo ($this->row->get('priority', 3) == 4) ? 'selected="selected"': ''; ?>value="4">(4) Major</option>
						<option <?php echo ($this->row->get('priority', 3) == 5) ? 'selected="selected"': ''; ?>value="5">(5) Critical</option>
					</select>
				</div>

				<div class="grouping" id="assignee-group">
					<label for="assignee_id"><?php echo Lang::txt('COM_TIME_TASKS_ASSIGNEE'); ?>:</label>
					<select name="assignee_id" id="assignee_id">
						<option value="0"><?php echo Lang::txt('COM_TIME_NO_ASSIGNEE'); ?></option>
						<?php if ($group = \Hubzero\User\Group::getInstance($this->config->get('accessgroup', 'time'))) : ?>
							<?php foreach ($group->get('members') as $member) : ?>
								<option value="<?php echo $member; ?>" <?php echo ($this->row->assignee_id == $member) ? 'selected="selected"': '';?>>
									<?php echo User::getInstance($member)->get('name'); ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<div class="grouping" id="liaison-group">
					<label for="liaison_id"><?php echo Lang::txt('COM_TIME_TASKS_LIAISON'); ?>:</label>
					<select name="liaison_id" id="liaison_id">
						<option value="0"><?php echo Lang::txt('COM_TIME_NO_LIAISON'); ?></option>
						<?php foreach (Liaison::all() as $liaison) : ?>
							<option value="<?php echo $liaison->user_id; ?>" <?php echo ($this->row->liaison_id == $liaison->user_id) ? 'selected="selected"': '';?>>
								<?php echo $liaison->name; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="grouping" id="description-group">
					<label for="description"><?php echo Lang::txt('COM_TIME_TASKS_DESCRIPTION'); ?>:</label>
					<textarea name="description" id="description" rows="6" cols="50"><?php echo $this->escape($this->row->description); ?></textarea>
				</div>

				<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_TIME_TASKS_SUBMIT'); ?>" />
					<a href="<?php echo Route::url($this->base . $this->start); ?>">
						<button class="btn btn-secondary" type="button">
							<?php echo Lang::txt('COM_TIME_TASKS_CANCEL'); ?>
						</button>
					</a>
				</p>
			</form>
		</div>
	</section>
</div>