<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
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
		<div id="resources-container<?php echo $this->module->id; ?>" class="chrt"></div>

		<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				resolutionPie;

			$(document).ready(function() {
				resolutionPie = $.plot($("#resources-container<?php echo $this->module->id; ?>"), [
					{label: '<?php echo strtolower(Lang::txt('MOD_RESOURCES_PUBLISHED')); ?>', data: <?php echo round(($this->published / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_published", "#656565"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_RESOURCES_DRAFT')); ?>', data: <?php echo round(($this->draft / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_draft", "#999"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_RESOURCES_PENDING')); ?>', data: <?php echo round(($this->pending / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_pending", "#f9d180"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_RESOURCES_REMOVED')); ?>', data: <?php echo round(($this->removed / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_removed", "#ccc"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_RESOURCES_UNPUBLISHED')); ?>', data: <?php echo round(($this->unpublished / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_unpublished", "#fff"); ?>'}
				], {
					legend: {
						show: false
					},
					series: {
						pie: {
							innerRadius: 0.5,
							show: true,
							label: {
								show: false
							},
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
	<div class="overview-container resources-stats-overview">
		<table>
			<tbody>
				<tr>
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=1'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_PUBLISHED_TITLE'); ?>">
							<span style="background-color: <?php echo $this->params->get("color_published", "#656565"); ?>;"></span><?php echo Lang::txt('MOD_RESOURCES_PUBLISHED'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=1'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_PUBLISHED_TITLE'); ?>">
							<?php echo $this->escape($this->published); ?>
						</a>
					</td>
				</tr>
				<tr>
					<th scope="row" class="pending-items">
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=3'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_PENDING_TITLE'); ?>">
							<span style="background-color: <?php echo $this->params->get("color_pending", "#f9d180"); ?>;"></span><?php echo Lang::txt('MOD_RESOURCES_PENDING'); ?>
						</a>
					</th>
					<td class="pending-items">
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=3'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_PENDING_TITLE'); ?>">
							<?php echo $this->escape($this->pending); ?>
						</a>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=2'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_DRAFT_TITLE'); ?>">
							<span style="background-color: <?php echo $this->params->get("color_draft", "#999"); ?>;"></span><?php echo Lang::txt('MOD_RESOURCES_DRAFT'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=2'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_DRAFT_TITLE'); ?>">
							<?php echo $this->escape($this->draft); ?>
						</a>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=0'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_UNPUBLISHED_TITLE'); ?>">
							<span style="background-color: <?php echo $this->params->get("color_removed", "#ccc"); ?>;"></span><?php echo Lang::txt('MOD_RESOURCES_UNPUBLISHED'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=0'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_UNPUBLISHED_TITLE'); ?>">
							<?php echo $this->escape($this->unpublished); ?>
						</a>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=4'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_REMOVED_TITLE'); ?>">
							<span style="background-color: <?php echo $this->params->get("color_unpublished", "#fff"); ?>;"></span><?php echo Lang::txt('MOD_RESOURCES_REMOVED'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_resources&c=resources&status=4'); ?>" title="<?php echo Lang::txt('MOD_RESOURCES_REMOVED_TITLE'); ?>">
							<?php echo $this->escape($this->removed); ?>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>