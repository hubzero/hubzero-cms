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
\Hubzero\Document\Assets::addSystemScript('jquery.flot.min', 'js/flot');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui');

$this->css()
     ->js();

$options = array();
$base    = 'index.php?option=com_time&controller=reports';
?>

<div class="plg_time_summary">
	<div class="filters">
		<form action="<?php echo JRoute::_($base); ?>">
			<input type="hidden" name="report_type" value="summary" />
			<div class="grouping">
				<label for="hub_id"><?php echo JText::_('PLG_TIME_SUMMARY_HUB_NAME'); ?>: </label>
				<?php $options[] = JHTML::_('select.option', null, JText::_('PLG_TIME_SUMMARY_NO_HUB_SELECTED')); ?>
				<?php foreach ($this->hubsList as $hub) : ?>
					<?php $options[] = JHTML::_('select.option', $hub->id, JText::_($hub->name)); ?>
				<?php endforeach; ?>
				<?php echo JHTML::_('select.genericlist', $options, 'hub_id', null, 'value', 'text', $this->hub_id); ?>
			</div>
			<div class="grouping">
				<label for="task_id"><?php echo JText::_('PLG_TIME_SUMMARY_TASK_NAME'); ?>: </label>
				<?php $options = array(JHTML::_('select.option', null, JText::_('PLG_TIME_SUMMARY_NO_TASK_SELECTED'))); ?>
				<?php foreach ($this->tasksList as $task) : ?>
					<?php $options[] = JHTML::_('select.option', $task->id, JText::_($task->name)); ?>
				<?php endforeach; ?>
				<?php echo JHTML::_('select.genericlist', $options, 'task_id', null, 'value', 'text', $this->task_id); ?>
			</div>
			<div class="grouping">
				<label for="start_date"><?php echo JText::_('PLG_TIME_SUMMARY_START_DATE'); ?>: </label>
				<input type="text" id="start_date" name="start_date" class="hadDatepicker" value="<?php echo $this->start; ?>" />
			</div>
			<div class="grouping">
				<label for="end_date"><?php echo JText::_('PLG_TIME_SUMMARY_END_DATE'); ?>: </label>
				<input type="text" id="end_date" name="end_date" class="hadDatepicker" value="<?php echo $this->end; ?>" />
			</div>
			<input class="btn btn-success" type="submit" value="<?php echo JText::_('PLG_TIME_SUMMARY_FILTER'); ?>" />
			<a href="<?php echo JRoute::_($base . '&report_type=summary'); ?>">
				<button class="btn btn-warning" type="button">
					<?php echo JText::_('PLG_TIME_SUMMARY_CLEAR'); ?>
				</button>
			</a>
		</form>
	</div>
	<?php if (count($this->hubs) > 0) : ?>
		<div class="charts">
			<h3>Overview</h3>
			<div class="tasks-bar"></div>
			<h3>Report</h3>
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
												<?php echo JFactory::getUser($record->user_id)->get('name') . ' - '; ?>
												<?php echo !empty($record->description) ? $record->description : '[no description available]'; ?>
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
	<?php else : ?>
		<p class="warning no-data">No data available for these parameters.</p>
	<?php endif; ?>
</div>