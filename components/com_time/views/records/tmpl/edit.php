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
\Hubzero\Document\Assets::addSystemScript('jquery.datetimepicker');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.datetimepicker.css');

$this->css()
     ->css('records')
     ->js('records')
     ->js('time');
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_records">
		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="icon-reply btn" href="<?php echo JRoute::_($this->base . $this->start); ?>">
						<?php echo JText::_('COM_TIME_RECORDS_ALL_RECORDS'); ?>
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
				<div class="grouping" id="uname-group">
					<label for="user_id"><?php echo JText::_('COM_TIME_RECORDS_USER'); ?>:</label>
					<?php if (with($proxies = Proxy::whereEquals('proxy_id', \JFactory::getUser()->get('id')))->total()) : ?>
						<select name="user_id" id="user_id">
							<option value="<?php echo JFactory::getUser()->get('id'); ?>"><?php echo JFactory::getUser()->get('name'); ?></option>
							<?php foreach ($proxies as $proxy) : ?>
								<option value="<?php echo $proxy->user_id; ?>" <?php echo ($this->row->user_id == $proxy->user_id) ? 'selected="selected"': ''; ?>>
									<?php echo $proxy->user->name; ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php else : ?>
						<?php echo $this->escape($this->row->user->get('name', \JFactory::getUser()->get('name'))); ?>
						<input type="hidden" name="user_id" value="<?php echo $this->row->get('user_id', \JFactory::getUser()->get('id')); ?>" />
					<?php endif; ?>
				</div>

				<div class="grouping clearfix" id="time-group">
					<label for="time" style="float:left;"><?php echo JText::_('COM_TIME_RECORDS_TIME'); ?>:</label>
					<div style="width: 75px; float:left; margin-right: 5px; margin-left: 5px">
						<select name="htime" id="htime" class="no-search" tabindex="1">
							<?php for ($i=0; $i < 9; $i++) : ?>
								<option value="<?php echo $i; ?>" <?php echo ($this->row->hours == $i) ? 'selected="selected"' : ''; ?>>
									<?php echo $i; ?>
								</option>
							<?php endfor; ?>
						</select>
					</div>
					<div style="width: 75px; float:left;">
						<select name="mtime" id="mtime" class="no-search" tabindex="2">
							<option value="0"  <?php echo ($this->row->minutes == 0)  ? 'selected="selected"' : ''; ?>>:00</option>
							<option value="25" <?php echo ($this->row->minutes == 25) ? 'selected="selected"' : ''; ?>>:15</option>
							<option value="5"  <?php echo ($this->row->minutes == 5)  ? 'selected="selected"' : ''; ?>>:30</option>
							<option value="75" <?php echo ($this->row->minutes == 75) ? 'selected="selected"' : ''; ?>>:45</option>
						</select>
					</div>
				</div>

				<div class="grouping clearfix" id="date-group">
					<label for="date"><?php echo JText::_('COM_TIME_RECORDS_DATE'); ?>:</label>
					<?php $date = JHTML::_('date', $this->row->get('date', JFactory::getDate()->format('Y-m-d H:00')), 'Y-m-d H:i'); ?>
					<input type="text" name="date" id="datepicker" class="hadTimepicker" value="<?php echo $date; ?>" tabindex="3" />
				</div>

				<div class="grouping" id="hub-group">
					<label for="hub_id"><?php echo JText::_('COM_TIME_RECORDS_HUB'); ?>:</label>
					<select name="hub_id" id="hub_id" tabindex="4">
						<option value=""><?php echo JText::_('COM_TIME_NO_HUB'); ?></option>
						<?php foreach (Hub::all()->order('name', 'asc') as $hub) : ?>
							<option value="<?php echo $hub->id; ?>" <?php echo ($hub->id == $this->row->task->hub_id) ? 'selected="selected"' : ''; ?>>
								<?php echo $hub->name; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="grouping" id="task-group">
					<label for="task_id"><?php echo JText::_('COM_TIME_RECORDS_TASK'); ?>:</label>
					<select name="task_id" id="task_id" tabindex="5">
						<option value=""><?php echo JText::_('COM_TIME_RECORDS_NO_HUB_SELECTED'); ?></option>
						<?php foreach ($tasks = Task::all()->order('name', 'asc') as $task) : ?>
							<option value="<?php echo $task->id; ?>" <?php echo ($task->id == $this->row->task->id) ? 'selected="selected"' : ''; ?>>
								<?php echo $task->name; ?>
							</option>
						<?php endforeach; ?>
						<?php if (!$tasks->count()) : ?>
							<option value=""><?php echo JText::_('COM_TIME_RECORDS_NO_TASKS_AVAILABLE'); ?></option>
						<?php endif; ?>
					</select>
				</div>

				<div class="grouping" id="description-group">
					<label for="description"><?php echo JText::_('COM_TIME_RECORDS_DESCRIPTION'); ?>:</label>
					<textarea name="description" id="description" rows="6" cols="50" tabIndex="6"><?php echo $this->escape($this->row->description); ?></textarea>
				</div>

				<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_TIME_RECORDS_SUBMIT'); ?>" tabIndex="7" />
					<a href="<?php echo JRoute::_($this->base . $this->start); ?>">
						<button type="button" class="btn btn-secondary">
							<?php echo JText::_('COM_TIME_RECORDS_CANCEL'); ?>
						</button>
					</a>
				</p>
			</form>
		</div>
	</section>
</div>