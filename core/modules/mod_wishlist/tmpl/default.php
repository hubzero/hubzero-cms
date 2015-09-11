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

$total = $this->granted + $this->accepted + $this->pending + $this->removed + $this->withdrawn + $this->removed + $this->rejected;
if ($total == 0)
{
	// Show nothing if no wishes (otherwise get division by zero error) - snowwitje
	return false;
}
?>
<div class="<?php echo $this->module->module; ?>">
	<div class="overview-container">
		<div id="wishlist-container<?php echo $this->module->id; ?>" class="chrt"></div>

		<script type="text/javascript">
		if (!jq) {
			var jq = $;
		}
		if (jQuery()) {
			var $ = jq,
				wishlistPie;

			$(document).ready(function() {
				wishlistPie = $.plot($("#wishlist-container<?php echo $this->module->id; ?>"), [
					{label: '<?php echo strtolower(Lang::txt('MOD_WISHLIST_PENDING')); ?>', data: <?php echo round(($this->pending / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_pending", "#656565"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_WISHLIST_GRANTED')); ?>', data: <?php echo round(($this->granted / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_granted", "#999"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_WISHLIST_ACCEPTED')); ?>', data: <?php echo round(($this->accepted / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_accepted", "#f9d180"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_WISHLIST_REMOVED')); ?>', data: <?php echo round(($this->removed / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_removed", "#cccccc"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_WISHLIST_WITHDRAWN')); ?>', data: <?php echo round(($this->withdrawn / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_withdrawn", "#ffffff"); ?>'},
					{label: '<?php echo strtolower(Lang::txt('MOD_WISHLIST_REJECTED')); ?>', data: <?php echo round(($this->rejected / $total)*100, 2); ?>, color: '<?php echo $this->params->get("color_rejected", "#333333"); ?>'}
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

		<p class="wishlist-total"><?php echo $total; ?></p>
	</div>
	<div class="overview-container wishlist-stats-overview">
		<table>
			<tbody>
				<tr class="pending-items">
					<th scope="row">
						<a href="<?php echo Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=' . $this->wishlist . '&filterby=pending'); ?>" title="<?php echo Lang::txt('MOD_WISHLIST_PENDING_TITLE'); ?>">
							<span style="background-color: <?php echo $this->params->get("color_pending", "#656565"); ?>;"></span><?php echo Lang::txt('MOD_WISHLIST_PENDING'); ?>
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
							<span style="background-color: <?php echo $this->params->get("color_accepted", "#f9d180"); ?>;"></span><?php echo Lang::txt('MOD_WISHLIST_ACCEPTED'); ?>
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
							<span style="background-color: <?php echo $this->params->get("color_granted", "#999"); ?>;"></span><?php echo Lang::txt('MOD_WISHLIST_GRANTED'); ?>
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
							<span style="background-color: <?php echo $this->params->get("color_rejected", "#333333"); ?>;"></span><?php echo Lang::txt('MOD_WISHLIST_REJECTED'); ?>
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
							<span style="background-color: <?php echo $this->params->get("color_withdrawn", "#ffffff"); ?>;"></span><?php echo Lang::txt('MOD_WISHLIST_WITHDRAWN'); ?>
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
							<span style="background-color: <?php echo $this->params->get("color_removed", "#cccccc"); ?>;"></span><?php echo Lang::txt('MOD_WISHLIST_REMOVED'); ?>
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
