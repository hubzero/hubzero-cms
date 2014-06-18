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
?>

<div id="plg_time_reports">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach
	} // close if count ?>
	<div class="filters grid">
		<div class="col span3">
			<div class="grouping">
				<label for="hub"><?php echo JText::_('PLG_TIME_REPORTS_HUB'); ?>:</label>
				<?php echo $this->hlist; ?>
			</div>
		</div>
		<div class="col span3">
			<div class="grouping">
				<label for="task"><?php echo JText::_('PLG_TIME_REPORTS_TASK'); ?>:</label>
				<select id="task">
					<option value=""><?php echo JText::_('PLG_TIME_REPORTS_NO_HUB_SELECTED'); ?></option>
				<select>
			</div>
		</div>
		<div class="col span3">
			<div class="grouping">
				<label for="startdate"><?php echo JText::_('PLG_TIME_REPORTS_START_DATE'); ?>:</label>
					<input type="text" id="startdate" name="startdate" class="hadDatepicker" />
			</div>
		</div>
		<div class="col span3 omega">
			<div class="grouping">
				<label for="enddate"><?php echo JText::_('PLG_TIME_REPORTS_END_DATE'); ?>:</label>
					<input type="text" id="enddate" name="enddate" class="hadDatepicker" />
			</div>
		</div>
	</div>

	<div class="container">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&active='.$this->active.'&action=savebill'); ?>" method="post">
			<table class="entries">
				<caption><?php echo JText::_('PLG_TIME_REPORTS_CREATE_BILL_CAPTION'); ?></caption>
				<thead>
					<tr>
						<td class="report_time"><?php echo JText::_('PLG_TIME_REPORTS_TIME'); ?></td>
						<td class="report_date"><?php echo JText::_('PLG_TIME_REPORTS_DATE'); ?></td>
						<td><?php echo JText::_('PLG_TIME_REPORTS_TASK'); ?></td>
						<td><?php echo JText::_('PLG_TIME_REPORTS_DESCRIPTION'); ?></td>
					</tr>
				</thead>
				<tbody id="records">
					<tr>
						<td colspan="4" class="no_records"><?php echo JText::_('PLG_TIME_REPORTS_NO_RECORDS_MATCHING_SEARCH'); ?></td>
					</tr>
				</tbody>
			</table>

			<input type="hidden" value="" id="results" name="results" />

			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_TIME_REPORTS_FINALIZE_BILL'); ?>" />
			</p>
		</form>
	</div><!-- // close container -->
</div>