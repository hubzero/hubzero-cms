<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css();

//JHTML::_('behavior.chart', 'resize');
JHTML::_('behavior.chart', 'pie');
?>
<div class="<?php echo $this->module->module; ?>">
	<div class="overview-container">
		<div id="<?php echo $this->module->module; ?>-container<?php echo $this->module->id; ?>" style="min-width: 200px; height: 200px;"></div>

		<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				<?php echo $this->module->module; ?>Pie;

			$(document).ready(function() {
				<?php echo $this->module->module; ?>Pie = $.plot($("#<?php echo $this->module->module; ?>-container<?php echo $this->module->id; ?>"), [
					{label: '<?php echo strtolower(JText::_('MOD_TOOLS_COL_REGISTERED')); ?>', data: <?php echo round(($this->registered / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_registered", "#333333"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_TOOLS_COL_CREATED')); ?>', data: <?php echo round(($this->created / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_created", "#999"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_TOOLS_COL_UPLOADED')); ?>', data: <?php echo round(($this->uploaded / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_uploaded", "#656565"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_TOOLS_COL_UPDATED')); ?>', data: <?php echo round(($this->updated / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_updated", "#cccccc"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_TOOLS_COL_APPROVED')); ?>', data: <?php echo round(($this->approved / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_approved", "#ffffff"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_TOOLS_COL_PUBLISHED')); ?>', data: <?php echo round(($this->published / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_published", "#f9d180"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_TOOLS_COL_RETIRED')); ?>', data: <?php echo round(($this->retired / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_retired", "#e1e1e1"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_TOOLS_COL_ABANDONED')); ?>', data: <?php echo round(($this->abandoned / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_abandoned", "#000000"); ?>'}
				], {
					legend: {
						show: true
					},
					series: {
						pie: {
							innerRadius: 0.5,
							show: true,
							stroke: {
								color: '#efefef'
							}
						}
					},
					grid: {
						hoverable: false
					}
				});
			});
		}
		</script>

		<p class="tools-total"><?php echo $this->total; ?></p>
	</div>
	<div class="overview-container">
		<table class="tools-stats-overview">
			<tbody>
				<tr>
					<td class="tools-registered">
						<a href="index.php?option=com_tools&amp;status=1">
							<?php echo $this->escape($this->registered); ?>
							<span><?php echo JText::_('MOD_TOOLS_COL_REGISTERED'); ?></span>
						</a>
					</td>
					<td class="tools-created">
						<a href="index.php?option=com_tools&amp;status=2">
							<?php echo $this->escape($this->created); ?>
							<span><?php echo JText::_('MOD_TOOLS_COL_CREATED'); ?></span>
						</a>
					</td>
					<td class="tools-uploaded">
						<a href="index.php?option=com_tools&amp;status=3">
							<?php echo $this->escape($this->uploaded); ?>
							<span><?php echo JText::_('MOD_TOOLS_COL_UPLOADED'); ?></span>
						</a>
					</td>
					<td class="tools-updated">
						<a href="index.php?option=com_tools&amp;status=5">
							<?php echo $this->escape($this->updated); ?>
							<span><?php echo JText::_('MOD_TOOLS_COL_UPDATED'); ?></span>
						</a>
					</td>
				</tr>
				<tr>
					<td class="tools-approved">
						<a href="index.php?option=com_tools&amp;status=6">
							<?php echo $this->escape($this->approved); ?>
							<span><?php echo JText::_('MOD_TOOLS_COL_APPROVED'); ?></span>
						</a>
					</td>
					<td class="tools-published">
						<a href="index.php?option=com_tools&amp;status=7">
							<?php echo $this->escape($this->published); ?>
							<span><?php echo JText::_('MOD_TOOLS_COL_PUBLISHED'); ?></span>
						</a>
					</td>
					<td class="tools-retired">
						<a href="index.php?option=com_tools&amp;status=8">
							<?php echo $this->escape($this->retired); ?>
							<span><?php echo JText::_('MOD_TOOLS_COL_RETIRED'); ?></span>
						</a>
					</td>
					<td class="tools-abandoned">
						<a href="index.php?option=com_tools&amp;status=9">
							<?php echo $this->escape($this->abandoned); ?>
							<span><?php echo JText::_('MOD_TOOLS_COL_ABANDONED'); ?></span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>