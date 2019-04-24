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
.wishlist-removed {
	background-color: ' . $this->params->get("color_removed", "#cccccc") . ';
}
.wishlist-granted {
	background-color: ' . $this->params->get("color_granted", "#999") . ';
}
.wishlist-withdrawn {
	background-color: ' . $this->params->get("color_withdrawn", "#ffffff") . ';
}
.wishlist-pending {
	background-color: ' . $this->params->get("color_pending", "#656565") . ';
}
.wishlist-rejected {
	background-color: ' . $this->params->get("color_rejected", "#333333") . ';
}
.wishlist-accepted {
	background-color: ' . $this->params->get("color_accepted", "#f9d180") . ';
}
');

$this->js();

$total = $this->granted + $this->accepted + $this->pending + $this->removed + $this->withdrawn + $this->removed + $this->rejected;
if ($total == 0)
{
	// Show nothing if no wishes (otherwise get division by zero error) - snowwitje
	return false;
}
?>
<div class="<?php echo $this->module->module; ?>">
	<div class="overview-container">
		<div id="wishlist-container<?php echo $this->module->id; ?>" class="<?php echo $this->module->module; ?>-chart chrt" data-datasets="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>"></div>

		<script type="application/json" id="<?php echo $this->module->module; ?>-data<?php echo $this->module->id; ?>">
			{
				"datasets": [
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_PENDING')); ?>",
						"data": <?php echo round(($this->pending / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_pending", "#656565"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_GRANTED')); ?>",
						"data": <?php echo round(($this->granted / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_granted", "#999"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_ACCEPTED')); ?>",
						"data": <?php echo round(($this->accepted / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_accepted", "#f9d180"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_REMOVED')); ?>",
						"data": <?php echo round(($this->removed / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_removed", "#cccccc"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_WITHDRAWN')); ?>",
						"data": <?php echo round(($this->withdrawn / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_withdrawn", "#ffffff"); ?>"
					},
					{
						"label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_REJECTED')); ?>",
						"data": <?php echo round(($this->rejected / $total)*100, 2); ?>,
						"color": "<?php echo $this->params->get("color_rejected", "#333333"); ?>"
					}
				]
			}
		</script>

		<p class="wishlist-total"><?php echo $total; ?></p>
	</div>
	<div class="overview-container wishlist-stats-overview">
		<table>
			<tbody>
				<tr class="pending-items">
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=pending'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_PENDING_TITLE'); ?>">
							<span class="wishlist-pending"></span><?php echo Lang::txt('MOD_WISHLIST_PENDING'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=pending'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_PENDING_TITLE'); ?>">
							<?php echo $this->escape($this->pending); ?>
						</a>
					</td>
				</tr>
				<tr class="accepted-items">
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=accepted'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_ACCEPTED_TITLE'); ?>">
							<span class="wishlist-accepted"></span><?php echo Lang::txt('MOD_WISHLIST_ACCEPTED'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=accepted'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_ACCEPTED_TITLE'); ?>">
							<?php echo $this->escape($this->accepted); ?>
						</a>
					</td>
				</tr>
				<tr class="granted-items">
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=granted'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_GRANTED_TITLE'); ?>">
							<span class="wishlist-granted"></span><?php echo Lang::txt('MOD_WISHLIST_GRANTED'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=granted'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_GRANTED_TITLE'); ?>">
							<?php echo $this->escape($this->granted); ?>
						</a>
					</td>
				</tr>
				<tr class="rejected-items">
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=rejected'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_REJECTED_TITLE'); ?>">
							<span class="wishlist-rejected"></span><?php echo Lang::txt('MOD_WISHLIST_REJECTED'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=rejected'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_REJECTED_TITLE'); ?>">
							<?php echo $this->escape($this->rejected); ?>
						</a>
					</td>
				</tr>
				<tr class="withdrawn-items">
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=withdrawn'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_WITHDRAWN_TITLE'); ?>">
							<span class="wishlist-withdrawn"></span><?php echo Lang::txt('MOD_WISHLIST_WITHDRAWN'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=withdrawn'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_WITHDRAWN_TITLE'); ?>">
							<?php echo $this->escape($this->withdrawn); ?>
						</a>
					</td>
				</tr>
				<tr class="removed-items">
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=deleted'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_REMOVED_TITLE'); ?>">
							<span class="wishlist-removed"></span><?php echo Lang::txt('MOD_WISHLIST_REMOVED'); ?>
						</a>
					</th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=deleted'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_REMOVED_TITLE'); ?>">
							<?php echo $this->escape($this->removed); ?>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
