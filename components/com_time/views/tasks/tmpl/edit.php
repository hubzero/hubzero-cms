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

\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css('tasks')
     ->js('time');
?>

<div id="dialog-confirm"></div>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_tasks">
		<div id="content-header-extra">
			<ul id="useroptions">
				<li>
					<a class="icon-reply btn" href="<?php echo JRoute::_($this->base . $this->start); ?>">
						<?php echo JText::_('COM_TIME_TASKS_ALL_TASKS'); ?>
					</a>
				</li>
				<li class="last">
					<a class="delete icon-delete btn" href="<?php echo JRoute::_($this->base . '&task=delete&id=' . $this->row->id); ?>">
						<?php echo JText::_('COM_TIME_TASKS_DELETE'); ?>
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
			<form action="<?php echo JRoute::_($this->base . '&task=save'); ?>" method="post">
				<div class="grouping" id="name-group">
					<label for="name"><?php echo JText::_('COM_TIME_TASKS_NAME'); ?>:</label>
					<input type="text" name="name" id="name" value="<?php echo $this->escape($this->row->name); ?>" size="50" />
				</div>

				<div class="grouping" id="active-group">
					<label><?php echo JText::_('COM_TIME_TASKS_ACTIVE'); ?>:</label>
					<input type="radio" name="active" id="active_yes" value="1" <?php if ($this->row->active == 1 || $this->row->active === NULL) { echo "checked"; } ?> />Yes
					<input type="radio" name="active" id="active_no" value="0" <?php if ($this->row->active !== NULL && $this->row->active == 0) { echo "checked"; } ?> />No
				</div>

				<div class="grouping" id="hub-group">
					<label for="hub_id"><?php echo JText::_('COM_TIME_TASKS_HUB_NAME'); ?>:</label>
					<select name="hub_id" id="hub_id">
						<option value=""><?php echo JText::_('COM_TIME_NO_HUB'); ?></option>
						<?php foreach (Hub::whereEquals('active', 1) as $hub) : ?>
							<option <?php echo ($hub->id == $this->row->hub->id) ? 'selected="selected" ': ''; ?>value="<?php echo $hub->id; ?>">
								<?php echo $hub->name; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="grouping" id="startdate-group">
					<label for="startdate"><?php echo JText::_('COM_TIME_TASKS_START_DATE'); ?>:</label>
					<input type="text" name="start_date" id="startdate" class="hadDatepicker" value="<?php echo $this->escape($this->row->start_date); ?>" size="10" />
				</div>

				<div class="grouping" id="enddate-group">
					<label for="enddate"><?php echo JText::_('COM_TIME_TASKS_END_DATE'); ?>:</label>
					<input type="text" name="end_date" id="enddate" class="hadDatepicker" value="<?php echo $this->escape($this->row->end_date); ?>" size="10" />
				</div>

				<div class="grouping" id="priority-group">
					<label for="priority"><?php echo JText::_('COM_TIME_TASKS_PRIORITY'); ?>:</label>
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
					<label for="assignee_id"><?php echo JText::_('COM_TIME_TASKS_ASSIGNEE'); ?>:</label>
					<select name="assignee_id" id="assignee_id">
						<option value="0"><?php echo JText::_('COM_TIME_NO_ASSIGNEE'); ?></option>
						<?php foreach (\Hubzero\User\Group::getInstance($this->config->get('accessgroup', 'time'))->get('members') as $member) : ?>
							<option value="<?php echo $member; ?>" <?php echo ($this->row->assignee_id == $member) ? 'selected="selected"': '';?>>
								<?php echo JFactory::getUser($member)->get('name'); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="grouping" id="liaison-group">
					<label for="liaison_id"><?php echo JText::_('COM_TIME_TASKS_LIAISON'); ?>:</label>
					<select name="liaison_id" id="liaison_id">
						<option value="0"><?php echo JText::_('COM_TIME_NO_LIAISON'); ?></option>
						<?php foreach (Liaison::all() as $liaison) : ?>
							<option value="<?php echo $liaison->user_id; ?>" <?php echo ($this->row->liaison_id == $liaison->user_id) ? 'selected="selected"': '';?>>
								<?php echo $liaison->name; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="grouping" id="description-group">
					<label for="description"><?php echo JText::_('COM_TIME_TASKS_DESCRIPTION'); ?>:</label>
					<textarea name="description" id="description" rows="6" cols="50"><?php echo $this->escape($this->row->description); ?></textarea>
				</div>

				<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_TIME_TASKS_SUBMIT'); ?>" />
					<a href="<?php echo JRoute::_($this->base . $this->start); ?>">
						<button class="btn btn-secondary" type="button">
							<?php echo JText::_('COM_TIME_TASKS_CANCEL'); ?>
						</button>
					</a>
				</p>
			</form>
		</div>
	</section>
</div>