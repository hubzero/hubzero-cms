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
?>
<div class="<?php echo $this->module->module; ?>">
	<div class="overview-container">
		<div id="<?php echo $this->module->module; ?>-container<?php echo $this->module->id; ?>" class="chrt"></div>

		<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				<?php echo $this->module->module; ?>Pie;

			$(document).ready(function() {
				<?php echo $this->module->module; ?>Pie = $.plot($("#<?php echo $this->module->module; ?>-container<?php echo $this->module->id; ?>"), [
					{label: '<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_REGISTERED')); ?>', data: <?php echo round(($this->registered / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_registered", "#333333"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_CREATED')); ?>', data: <?php echo round(($this->created / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_created", "#999"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_UPLOADED')); ?>', data: <?php echo round(($this->uploaded / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_uploaded", "#656565"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_UPDATED')); ?>', data: <?php echo round(($this->updated / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_updated", "#cccccc"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_APPROVED')); ?>', data: <?php echo round(($this->approved / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_approved", "#ffffff"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_PUBLISHED')); ?>', data: <?php echo round(($this->published / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_published", "#f9d180"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_RETIRED')); ?>', data: <?php echo round(($this->retired / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_retired", "#e1e1e1"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_ABANDONED')); ?>', data: <?php echo round(($this->abandoned / $this->total)*100, 2); ?>, color: '<?php echo $this->params->get("color_abandoned", "#000000"); ?>'}
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
	<div class="overview-container tools-stats-overview">
		<table>
			<tbody>
				<tr>
					<td class="tools-registered">
						<a href="<?php echo Route::url('index.php?option=com_tools&status=1'); ?>">
							<?php echo $this->escape($this->registered); ?>
							<span><?php echo Lang::txt('MOD_TOOLS_COL_REGISTERED'); ?></span>
						</a>
					</td>
					<td class="tools-created">
						<a href="<?php echo Route::url('index.php?option=com_tools&status=2'); ?>">
							<?php echo $this->escape($this->created); ?>
							<span><?php echo Lang::txt('MOD_TOOLS_COL_CREATED'); ?></span>
						</a>
					</td>
					<td class="tools-uploaded">
						<a href="<?php echo Route::url('index.php?option=com_tools&status=3'); ?>">
							<?php echo $this->escape($this->uploaded); ?>
							<span><?php echo Lang::txt('MOD_TOOLS_COL_UPLOADED'); ?></span>
						</a>
					</td>
					<td class="tools-updated">
						<a href="<?php echo Route::url('index.php?option=com_tools&status=5'); ?>">
							<?php echo $this->escape($this->updated); ?>
							<span><?php echo Lang::txt('MOD_TOOLS_COL_UPDATED'); ?></span>
						</a>
					</td>
				</tr>
				<tr>
					<td class="tools-approved">
						<a href="<?php echo Route::url('index.php?option=com_tools&status=6'); ?>">
							<?php echo $this->escape($this->approved); ?>
							<span><?php echo Lang::txt('MOD_TOOLS_COL_APPROVED'); ?></span>
						</a>
					</td>
					<td class="tools-published">
						<a href="<?php echo Route::url('index.php?option=com_tools&status=7'); ?>">
							<?php echo $this->escape($this->published); ?>
							<span><?php echo Lang::txt('MOD_TOOLS_COL_PUBLISHED'); ?></span>
						</a>
					</td>
					<td class="tools-retired">
						<a href="<?php echo Route::url('index.php?option=com_tools&status=8'); ?>">
							<?php echo $this->escape($this->retired); ?>
							<span><?php echo Lang::txt('MOD_TOOLS_COL_RETIRED'); ?></span>
						</a>
					</td>
					<td class="tools-abandoned">
						<a href="<?php echo Route::url('index.php?option=com_tools&status=9'); ?>">
							<?php echo $this->escape($this->abandoned); ?>
							<span><?php echo Lang::txt('MOD_TOOLS_COL_ABANDONED'); ?></span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>