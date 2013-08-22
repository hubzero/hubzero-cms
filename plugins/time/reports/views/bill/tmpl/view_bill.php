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

$dateFormat = '%B %e, %Y at %l:%M:%S %P';
$dateFormat2 = '%m-%d-%Y';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'F j, Y \a\t g:i:s A';
	$dateFormat2 = 'm-d-Y';
	$tz = null;
}
?>

<div id="plg_time_reports">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-file btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=reports&action=csvbill&id='.$this->report->id); ?>">
					<?php echo JText::_('PLG_TIME_REPORTS_DOWNLOAD_CSV'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div>
		<table class="report">
			<caption>
				<?php echo JText::_(ucfirst($this->report->report_type)
					. ' generated on '
					. JHTML::_('date', $this->report->time_stamp, $dateFormat, $tz)
					. ' for ' . $this->hubname);
				?>
			</caption>
			<thead>
				<tr>
					<td class="report_time"><div><?php echo JText::_('PLG_TIME_REPORTS_TIME'); ?></div></td>
					<td class="report_date"><div><?php echo JText::_('PLG_TIME_REPORTS_DATE'); ?></div></td>
					<td><div><?php echo JText::_('PLG_TIME_REPORTS_TASK'); ?></div></td>
					<td><div><?php echo JText::_('PLG_TIME_REPORTS_DESCRIPTION'); ?></div></td>
				</tr>
			</thead>
			<tbody>
				<?php 
				// Set the odd/even row class
				$cls = 'odd';

				// Interate through the records
				foreach($this->masterlist as $list) {
					echo "<tr class='report_user_subsection'><td colspan='4'><div class='user_header'>{$list['name']}</div></td></tr>";
					foreach($list['records'] as $record) {
						// Cut the description off if it's too long
						if(strlen($record->description) > 75)
						{
							$record->description = trim(substr($record->description,0,75))."...";
						} ?>
					<tr class="<?php echo $cls; ?>">
						<td class="report_time"><?php echo $record->time; ?></td>
						<td class="report_date"><?php echo JHTML::_('date', $record->date, $dateFormat2, $tz); ?></td>
						<td><?php echo $record->pname; ?></td>
						<td><?php echo $record->description; ?></td>
					</tr>
					<?php 
						// Invert class
						$cls = ($cls == 'even') ? 'odd' : 'even';
					} // close foreach ?>
					<tr>
						<td class='report_user_total'><?php echo JText::_('PLG_TIME_REPORTS_USER_TOTAL_TIME') . ' ' . $list['name'] . ': ' . $list['total']; ?></td>
						<td colspan='3'></td>
					</tr>
				<?php } // close foreach ?>
				<tr class="total">
					<td class="report_total_time"><?php echo JText::_('PLG_TIME_REPORTS_TOTAL_TIME'); ?>: <?php echo $this->totalHours; ?></td>
					<td colspan="3"></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
