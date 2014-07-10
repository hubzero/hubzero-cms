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

//JHTML::_('behavior.chart', 'resize');
JHTML::_('behavior.chart', 'pie');

$total = $this->draftInternal + $this->draftUser + $this->pending + $this->published + $this->unpublished + $this->removed;
$this->draft = $this->draftInternal + $this->draftUser;
?>
<div class="mod_resources">
	<div class="overview-container">
		<div id="resources-container<?php echo $this->module->id; ?>" style="min-width: 200px; height: 200px;"></div>

		<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				resolutionPie;

			$(document).ready(function() {
				resolutionPie = $.plot($("#resources-container<?php echo $this->module->id; ?>"), [
					{label: '<?php echo strtolower(JText::_('MOD_RESOURCES_PUBLISHED')); ?>', data: <?php echo round(($this->published / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_published", "#656565"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_RESOURCES_DRAFT')); ?>', data: <?php echo round(($this->draft / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_draft", "#999"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_RESOURCES_PENDING')); ?>', data: <?php echo round(($this->pending / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_pending", "#f9d180"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_RESOURCES_REMOVED')); ?>', data: <?php echo round(($this->removed / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_removed", "#ccc"); ?>'},
					{label: '<?php echo strtolower(JText::_('MOD_RESOURCES_UNPUBLISHED')); ?>', data: <?php echo round(($this->unpublished / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_unpublished", "#fff"); ?>'}
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

		<p class="resources-total"><?php echo $total; ?></p>
	</div>
	<div class="overview-container">
		<table class="resources-stats-overview">
			<tbody>
				<tr>
					<td>
						<a href="index.php?option=com_resources&amp;c=resources&amp;status=1" title="<?php echo JText::_('MOD_RESOURCES_PUBLISHED_TITLE'); ?>"><?php echo $this->escape($this->published); ?></a>
						<span><?php echo JText::_('MOD_RESOURCES_PUBLISHED'); ?></span>
					</td>
					<td class="pending-items">
						<a href="index.php?option=com_resources&amp;c=resources&amp;status=3" title="<?php echo JText::_('MOD_RESOURCES_PENDING_TITLE'); ?>"><?php echo $this->escape($this->pending); ?></a>
						<span><?php echo JText::_('MOD_RESOURCES_PENDING'); ?></span>
					</td>
					<td>
						<a href="index.php?option=com_resources&amp;c=resources&amp;status=2" title="<?php echo JText::_('MOD_RESOURCES_DRAFT_TITLE'); ?>"><?php echo $this->escape($this->draft); ?></a>
						<span><?php echo JText::_('MOD_RESOURCES_DRAFT'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="resources-stats-overview">
			<tbody>
				<tr>
					<td>
						<a href="index.php?option=com_resources&amp;c=resources&amp;status=0" title="<?php echo JText::_('MOD_RESOURCES_UNPUBLISHED_TITLE'); ?>"><?php echo $this->escape($this->unpublished); ?></a>
						<span><?php echo JText::_('MOD_RESOURCES_UNPUBLISHED'); ?></span>
					</td>
					<td>
						<a href="index.php?option=com_resources&amp;c=resources&amp;status=4" title="<?php echo JText::_('MOD_RESOURCES_REMOVED_TITLE'); ?>"><?php echo $this->escape($this->removed); ?></a>
						<span><?php echo JText::_('MOD_RESOURCES_REMOVED'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>