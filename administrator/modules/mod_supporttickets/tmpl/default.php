<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css();
//JHTML::_('behavior.chart', 'resize');

JHTML::_('behavior.chart');
?>
<div class="<?php echo $this->module->module; ?>">
	<div id="container<?php echo $this->module->id; ?>" class="chart" style="min-width: 400px; height: 200px;"></div>
	<?php
		$top = 0;

		$closeddata = '';
		if ($this->closedmonths)
		{
			$c = array();
			foreach ($this->closedmonths as $year => $data)
			{
				foreach ($data as $k => $v)
				{
					$top = ($v > $top) ? $v : $top;
					$c[] = '[new Date(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']';
				}
			}
			$closeddata = implode(',', $c);
		}

		$openeddata = '';
		if ($this->openedmonths)
		{
			$o = array();
			foreach ($this->openedmonths as $year => $data)
			{
				foreach ($data as $k => $v)
				{
					$top = ($v > $top) ? $v : $top;
					$o[] = '[new Date(' . $year . ',  ' . ($k - 1) . ', 1),' . $v . ']'; // - $this->closedmonths[$k];
				}
			}
			$openeddata = implode(',', $o);
		}
	?>

	<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				chart,
				month_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				datasets = [
					{
						color: "orange", //#AA4643 #93ACCA
						label: "<?php echo JText::_('MOD_SUPPORTTICKETS_OPENED'); ?>",
						data: [<?php echo $openeddata; ?>]
					},
					{
						color: "#656565", //#CFCFAB
						label: "<?php echo JText::_('MOD_SUPPORTTICKETS_CLOSED'); ?>",
						data: [<?php echo $closeddata; ?>]
					}
				];

			$(document).ready(function() {
				var chart = $.plot($('#container<?php echo $this->module->id; ?>'), datasets, {
					series: {
						lines: {
							show: true,
							fill: true
						},
						points: { show: false },
						shadowSize: 0
					},
					//crosshair: { mode: "x" },
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
					xaxis: { mode: "time", tickLength: 0, tickDecimals: 0, <?php if (count($o) <= 12) { echo 'ticks: ' . count($o) . ','; } ?>
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
	<div class="clr"></div>

	<div class="breakdown">
		<table class="support-stats-overview open-tickets">
			<tbody>
				<tr>
					<td class="major">
						<a href="index.php?option=com_support&amp;controller=tickets&amp;show=<?php echo $this->topened[0]->id; ?>" title="<?php echo JText::_('MOD_SUPPORTTICKETS_OPEN_TITLE'); ?>"><?php echo $this->escape($this->topened[0]->count); ?></a>
						<span><?php echo JText::_('MOD_SUPPORTTICKETS_OPEN'); ?></span>
					</td>
					<td class="critical">
						<a href="index.php?option=com_support&amp;controller=tickets&amp;show=<?php echo $this->topened[2]->id; ?>" title="<?php echo JText::_('MOD_SUPPORTTICKETS_UNASSIGNED_TITLE'); ?>"><?php echo $this->escape($this->topened[2]->count); ?></a>
						<span><?php echo JText::_('MOD_SUPPORTTICKETS_UNASSIGNED'); ?></span>
					</td>
					<td class="newt">
						<a href="index.php?option=com_support&amp;controller=tickets&amp;show=<?php echo $this->topened[1]->id; ?>" title="<?php echo JText::_('MOD_SUPPORTTICKETS_NEW_TITLE'); ?>"><?php echo $this->escape($this->topened[1]->count); ?></a>
						<span><?php echo JText::_('MOD_SUPPORTTICKETS_NEW'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>