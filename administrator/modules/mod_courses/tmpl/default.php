<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css();

JHTML::_('behavior.chart');
?>
<div class="mod_courses">
	<div class="overview-container">
		<div id="container<?php echo $this->module->id; ?>" class="chart" style="min-width: 400px; height: 200px;"></div>
	<?php
		$top = 0;

		$totals = '';
		if ($this->totals)
		{
			$c = array();
			foreach ($this->totals as $year => $data)
			{
				foreach ($data as $k => $v)
				{
					$c[] = '[new Date(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']';
				}
			}
			$totals = implode(',', $c);
		}
	?>

	<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				chart<?php echo $this->module->id; ?>,
				month_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				datasets<?php echo $this->module->id; ?> = [
					{
						color: "#656565", //#CFCFAB
						label: "<?php echo JText::_('MOD_COURSES_ENROLLED'); ?>",
						data: [<?php echo $totals; ?>]
					}
				];

			$(document).ready(function() {
				var chart<?php echo $this->module->id; ?> = $.plot($('#container<?php echo $this->module->id; ?>'), datasets<?php echo $this->module->id; ?>, {
					series: {
						bars: {
							show: true,
							fill: true
						}
					},
					grid: {
						color: 'rgba(0, 0, 0, 0.6)',
						borderWidth: 1,
						borderColor: 'transparent',
						hoverable: true,
						clickable: true
					},
					tooltip: true,
						tooltipOpts: {
						content: "%y %s in %x",
						shifts: {
							x: -60,
							y: 25
						},
						defaultTheme: false
					},
					legend: {
						show: true,
						noColumns: 2,
						position: "ne",
						backgroundColor: 'transparent',
						margin: [0, -50]
					},
					xaxis: { mode: "time", tickLength: 0, tickDecimals: 0, <?php if (count($c) <= 12) { echo 'ticks: ' . count($c) . ','; } ?>
						tickFormatter: function (val, axis) {
							var d = new Date(val);
							return month_short[d.getUTCMonth()];//d.getUTCDate() + "/" + (d.getUTCMonth() + 1);
						}
					},
					yaxis: { min: 0 }
				});
			});
		}
	</script>
	</div>
	<div class="overview-container">
		<table class="courses-stats-overview">
			<tbody>
				<tr>
					<td class="published-items">
						<a href="<?php echo JRoute::_('index.php?option=com_courses&state=1'); ?>" title="<?php echo JText::_('MOD_COURSES_PUBLISHED_TITLE'); ?>">
							<?php echo $this->escape($this->published); ?>
							<span><?php echo JText::_('MOD_COURSES_PUBLISHED'); ?></span>
						</a>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_courses&state=3'); ?>" title="<?php echo JText::_('MOD_COURSES_DRAFT_TITLE'); ?>">
							<?php echo $this->escape($this->draft); ?>
							<span><?php echo JText::_('MOD_COURSES_DRAFT'); ?></span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="courses-stats-overview">
			<tbody>
				<tr>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_courses&state=0'); ?>" title="<?php echo JText::_('MOD_COURSES_UNPUBLISHED_TITLE'); ?>">
							<?php echo $this->escape($this->unpublished); ?>
							<span><?php echo JText::_('MOD_COURSES_UNPUBLISHED'); ?></span>
						</a>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_courses&state=2'); ?>" title="<?php echo JText::_('MOD_COURSES_ARCHIVED_TITLE'); ?>">
							<?php echo $this->escape($this->archived); ?>
							<span><?php echo JText::_('MOD_COURSES_ARCHIVED'); ?></span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>