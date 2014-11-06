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
\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui');

$this->css()
     ->js();

$options = array();
$base    = 'index.php?option=com_time&controller=reports';

// If no incoming fields vars selected, we'll assume we should show all
$all  = true;
foreach (JRequest::get('GET') as $key => $value)
{
	if (strpos($key, 'fields-') !== false)
	{
		$all = false;
	}
}
?>

<div class="plg_time_csv">
	<a target="_blank" href="<?php echo JRoute::_($base . '&' . JURI::getInstance()->getQuery() . '&method=download'); ?>">
		<div class="download btn icon-save">Download</div>
	</a>
	<div class="filters">
		<form action="<?php echo JRoute::_($base); ?>">
			<input type="hidden" name="report_type" value="csv" />
			<div class="grouping">
				<label for="hub_id"><?php echo JText::_('PLG_TIME_CSV_HUB_NAME'); ?>: </label>
				<?php $options[] = JHTML::_('select.option', null, JText::_('PLG_TIME_CSV_NO_HUB_SELECTED')); ?>
				<?php foreach ($this->hubsList as $hub) : ?>
					<?php if ($this->permissions->can('view.report', 'hubs', $hub->id)) : ?>
						<?php $options[] = JHTML::_('select.option', $hub->id, JText::_($hub->name)); ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php echo JHTML::_('select.genericlist', $options, 'hub_id', null, 'value', 'text', $this->hub_id); ?>
			</div>
			<div class="grouping">
				<label for="start_date"><?php echo JText::_('PLG_TIME_CSV_START_DATE'); ?>: </label>
				<input type="text" id="start_date" name="start_date" class="hadDatepicker" value="<?php echo $this->start; ?>" />
			</div>
			<div class="grouping">
				<label for="end_date"><?php echo JText::_('PLG_TIME_CSV_END_DATE'); ?>: </label>
				<input type="text" id="end_date" name="end_date" class="hadDatepicker" value="<?php echo $this->end; ?>" />
			</div>
			<div class="grouping">
				<div><?php echo JText::_('PLG_TIME_CSV_FIELDS'); ?>:</div>
				<input type="checkbox" name="fields-hub" value="1" <?php echo ($hub = JRequest::getInt('fields-hub', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-hub"><?php echo JText::_('PLG_TIME_CSV_HUB'); ?></label>
				<br />
				<input type="checkbox" name="fields-task" value="1" <?php echo ($task = JRequest::getInt('fields-task', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-task"><?php echo JText::_('PLG_TIME_CSV_TASK'); ?></label>
				<br />
				<input type="checkbox" name="fields-user" value="1" <?php echo ($user = JRequest::getInt('fields-user', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-user"><?php echo JText::_('PLG_TIME_CSV_USER'); ?></label>
				<br />
				<input type="checkbox" name="fields-date" value="1" <?php echo ($date = JRequest::getInt('fields-date', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-date"><?php echo JText::_('PLG_TIME_CSV_DATE'); ?></label>
				<br />
				<input type="checkbox" name="fields-time" value="1" <?php echo ($time = JRequest::getInt('fields-time', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-time"><?php echo JText::_('PLG_TIME_CSV_TIME'); ?></label>
				<br />
				<input type="checkbox" name="fields-description" value="1" <?php echo ($description = JRequest::getInt('fields-description', $all)) ? 'checked="checked"' : ''; ?>/>
				<label for="fields-description"><?php echo JText::_('PLG_TIME_CSV_DESCRIPTION'); ?></label>
			</div>
			<input class="btn btn-success" type="submit" value="<?php echo JText::_('PLG_TIME_CSV_FILTER'); ?>" />
			<a href="<?php echo JRoute::_($base . '&report_type=csv'); ?>">
				<button class="btn btn-warning" type="button">
					<?php echo JText::_('PLG_TIME_CSV_CLEAR'); ?>
				</button>
			</a>
		</form>
	</div>
	<?php if (count($this->records) > 0) : ?>
		<h3>Preview</h3>
		<div class="preview">
			<div class="preview-header">
				<?php if ($hub) : ?>
					<div class="preview-field hname">
						<?php echo JText::_('PLG_TIME_CSV_HUB'); ?>
					</div>
				<?php endif; ?>
				<?php if ($task) : ?>
					<div class="preview-field pname">
						<?php echo JText::_('PLG_TIME_CSV_TASK'); ?>
					</div>
				<?php endif; ?>
				<?php if ($user) : ?>
					<div class="preview-field uname">
						<?php echo JText::_('PLG_TIME_CSV_USER'); ?>
					</div>
				<?php endif; ?>
				<?php if ($date) : ?>
					<div class="preview-field date">
						<?php echo JText::_('PLG_TIME_CSV_DATE'); ?>
					</div>
				<?php endif; ?>
				<?php if ($time) : ?>
					<div class="preview-field time">
						<?php echo JText::_('PLG_TIME_CSV_TIME'); ?>
					</div>
				<?php endif; ?>
				<?php if ($description) : ?>
					<div class="preview-field description">
						<?php echo JText::_('PLG_TIME_CSV_DESCRIPTION'); ?>
					</div>
				<?php endif; ?>
			</div>
			<?php foreach ($this->records as $record) : ?>
				<?php if ($this->permissions->can('view.report', 'hubs', $record->hid)) : ?>
					<div class="preview-row">
						<?php if ($hub) : ?>
							<div class="preview-field hname">
								<?php echo $record->hname; ?>
							</div>
						<?php endif; ?>
						<?php if ($task) : ?>
							<div class="preview-field pname">
								<?php echo $record->pname; ?>
							</div>
						<?php endif; ?>
						<?php if ($user) : ?>
							<div class="preview-field uname">
								<?php echo $record->uname; ?>
							</div>
						<?php endif; ?>
						<?php if ($date) : ?>
							<div class="preview-field date">
								<?php echo $record->date; ?>
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
		<p class="warning no-data"><?php echo JText::_('PLG_TIME_CSV_NO_DATA_AVAILABLE'); ?></p>
	<?php endif; ?>
</div>