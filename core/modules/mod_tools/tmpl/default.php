<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Html::behavior('chart', 'pie');

$this->css()
	->js();
?>
<div class="<?php echo $this->module->module; ?>">
	<div class="overview-container">
		<div id="tools-container<?php echo $this->module->id; ?>" class="<?php echo $this->module->module; ?>-chart chrt" data-datasets="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>"></div>

		<script type="application/json" id="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>">
			{
				"datasets": [
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_REGISTERED')); ?>",
						"data": <?php echo round(($this->registered / $this->total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_registered", "#333333"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_CREATED')); ?>",
						"data": <?php echo round(($this->created / $this->total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_created", "#999"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_UPLOADED')); ?>",
						"data": <?php echo round(($this->uploaded / $this->total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_uploaded", "#656565"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_UPDATED')); ?>",
						"data": <?php echo round(($this->updated / $this->total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_updated", "#cccccc"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_APPROVED')); ?>",
						"data": <?php echo round(($this->approved / $this->total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_approved", "#ffffff"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_PUBLISHED')); ?>",
						"data": <?php echo round(($this->published / $this->total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_published", "#f9d180"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_RETIRED')); ?>",
						"data": <?php echo round(($this->retired / $this->total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_retired", "#e1e1e1"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_TOOLS_COL_ABANDONED')); ?>",
						"data": <?php echo round(($this->abandoned / $this->total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_abandoned", "#000000"); ?>"
					}
				]
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