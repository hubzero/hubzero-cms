<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Html::behavior('chart', 'pie');

$this->css();

$this->css('
.resource-published {
	background-color: ' . $this->params->get("color_published", "#656565") . ';
}
.resource-unpublished {
	background-color: ' . $this->params->get("color_unpublished", "#fff") . ';
}
.resource-draft {
	background-color: ' . $this->params->get("color_draft", "#999") . ';
}
.resource-pending {
	background-color: ' . $this->params->get("color_pending", "#f9d180") . ';
}
.resource-removed {
	background-color: ' . $this->params->get("color_removed", "#ccc") . ';
}
');

$this->js();

$total = $this->draftInternal + $this->draftUser + $this->pending + $this->published + $this->unpublished + $this->removed;

$this->draft = $this->draftInternal + $this->draftUser;
?>
<div class="mod_resources">
	<div class="overview-container">
		<div id="resources-container<?php echo $this->module->id; ?>" class="<?php echo $this->module->module; ?>-chart chrt" data-datasets="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>"></div>
		<?php if ($total > 0): ?>
			<script type="application/json" id="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>">
			{
				"datasets": [
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_PUBLISHED')); ?>",
						"data": <?php echo round(($this->published / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_published", "#656565"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_DRAFT')); ?>",
						"data": <?php echo round(($this->draft / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_draft", "#999"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_PENDING')); ?>",
						"data": <?php echo round(($this->pending / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_pending", "#f9d180"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_REMOVED')); ?>",
						"data": <?php echo round(($this->removed / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_removed", "#ccc"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_UNPUBLISHED')); ?>",
						"data": <?php echo round(($this->unpublished / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_unpublished", "#fff"); ?>"
					}
				]
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
							<span class="resource-published"></span><?php echo Lang::txt('MOD_RESOURCES_PUBLISHED'); ?>
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
							<span class="resource-pending"></span><?php echo Lang::txt('MOD_RESOURCES_PENDING'); ?>
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
							<span class="resource-draft"></span><?php echo Lang::txt('MOD_RESOURCES_DRAFT'); ?>
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
							<span class="resource-removed"></span><?php echo Lang::txt('MOD_RESOURCES_UNPUBLISHED'); ?>
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
							<span class="resource-unpublished"></span><?php echo Lang::txt('MOD_RESOURCES_REMOVED'); ?>
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