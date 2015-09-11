<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();

//Html::behavior('chart', 'resize');
Html::behavior('chart', 'pie');

$total = $this->draftInternal + $this->draftUser + $this->pending + $this->published + $this->unpublished + $this->removed;

$this->draft = $this->draftInternal + $this->draftUser;
?>
<div class="mod_resources">
	<div class="overview-container">
		<div id="resources-container<?php echo $this->module->id; ?>" class="chrt"></div>
		<?php if ($total > 0): ?>
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
	<?php endif; ?>
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