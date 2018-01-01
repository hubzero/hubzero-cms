<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('orders');

Toolbar::title(Lang::txt('COM_CART') . ': Items Ordered', 'cart.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}

Toolbar::custom('downloadOrders', 'download.png', '', 'Download CSV', false);

Toolbar::spacer();
Toolbar::help('downloads');
?>
<script type="text/javascript">
	function submitbutton(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform(pressbutton);
			return;
		}
		// do field validation
		submitform(pressbutton);
	}

	$(function() {
		var dateFormat = "mm/dd/yy",
			from = $( "#filter-report-from" )
				.datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 1
				})
				.on( "change", function() {
					to.datepicker( "option", "minDate", getDate( this ) );
				}),
			to = $( "#filter-report-to" ).datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 1
				})
				.on( "change", function() {
					from.datepicker( "option", "maxDate", getDate( this ) );
				});

		function getDate( element ) {
			var date;
			try {
				date = $.datepicker.parseDate( dateFormat, element.value );
			} catch( error ) {
				date = null;
			}

			return date;
		}
	});
</script>

<?php
$this->view('_submenu')
	->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span5">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('JSEARCH_FILTER'); ?>" />

				<!-- <label for="filter_order"><?php echo Lang::txt('COM_CART_ORDER_ID'); ?>:</label>
				<input type="text" name="order" id="filter_order" value="<?php echo ($this->filters['order'] ? $this->escape($this->filters['order']) : ''); ?>" placeholder="<?php echo Lang::txt('COM_CART_ORDER_ID'); ?>" size="7" />

				<button type="button" onclick="$('#filter_search').val('');$('#filter_order').val('');$('#filter-report-from').val('<?php echo gmdate('m/d/Y', strtotime('-1 month')); ?>');$('#filter-report-to').val('<?php echo gmdate('m/d/Y'); ?>');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button> -->
			</div>
			<div class="col span7 align-right">
				<label for="filter-report-from">From:</label>
				<input type="text" name="report-from" id="filter-report-from" value="<?php echo $this->escape($this->filters['report-from']); ?>" placeholder="<?php echo Lang::txt('From'); ?>" />
				&mdash;
				<label for="filter-report-to">To:</label>
				<input type="text" name="report-to" id="filter-report-to" value="<?php echo $this->escape($this->filters['report-to']); ?>" placeholder="<?php echo Lang::txt('To'); ?>" />
				<input type="submit" value="<?php echo Lang::txt('Update'); ?>" />
			</div>
		</div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<?php if ($this->filters['order']) { ?>
				<tr>
					<th colspan="6"><?php echo Lang::txt('COM_CART_ORDER'); ?>: #<?php echo $this->filters['order']; ?>
						<button type="button" onclick="$('#filter_ordernum').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
					</th>
				</tr>
			<?php } ?>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_SKU_ID', 'sId', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_CART_PRODUCT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_CART_QUANTITY'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_CART_PRICE'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDER_ID', 'tId', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDER_PLACED', 'tLastUpdated', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDERED_BY', 'Name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php
					// Initiate paging
					echo $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$k = 0;
			$i = 0;
			foreach ($this->rows as $row)
			{
				$itemInfo = $row->itemInfo['info'];
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<span>
							<a href="<?php echo Route::url('index.php?option=com_storefront&controller=skus&task=edit&id=' . $itemInfo->sId); ?>">
								<?php echo $this->escape(stripslashes($row->sId)); ?>
							</a>
						</span>
					</td>
					<td>
						<?php
						$product = '<a href="' . Route::url('index.php?option=com_storefront&controller=products&task=edit&id=' . $itemInfo->pId) . '" target="_blank">' . $this->escape(stripslashes($itemInfo->pName)) . '</a>';
						if (!stripslashes($itemInfo->pName))
						{
							$product = '<span class="missing">Product n/a</span>';
						}
						if (!stripslashes($itemInfo->sSku))
						{
							$product .= '<br />SKU: <span class="missing">SKU n/a</span>';
						}
						else {
							$product .= '<br />SKU: ' . '<a href="' . Route::url('index.php?option=com_storefront&controller=skus&task=edit&id=' . $row->sId) . '" target="_blank">' . $this->escape(stripslashes($itemInfo->sSku)) . '</a>';
						}
						?>
						<span><?php echo $product; ?></span>
					</td>
					<td>
						<span><?php echo $this->escape(stripslashes($row->tiQty)); ?></span>
					</td>
					<td>
						<span><?php echo $this->escape(stripslashes($row->tiPrice)); ?></span>
					</td>
					<td>
						<span>
							<a href="<?php echo Route::url('index.php?option=com_cart&controller=orders&task=view&id=' . $row->tId); ?>">
								<?php echo $this->escape(stripslashes($row->tId)); ?>
							</a>
						</span>
					</td>
					<td>
						<span><?php echo $this->escape(stripslashes($row->tLastUpdated)); ?></span>
					</td>
					<td>
						<span>
							<?php if ($row->uidNumber) { ?>
							<a href="<?php echo Route::url('index.php?option=com_members&task=edit&id=' . $row->uidNumber); ?>">
							<?php } ?>
								<?php echo ($row->name ? $this->escape(stripslashes($row->name)) : Lang::txt('COM_CART_UNKNOWN')); ?>
							<?php if ($row->uidNumber) { ?>
							</a>
							<?php } ?>
						</span>
					</td>
				</tr>
				<?php
				$i++;
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<input type="hidden" name="order" id="filter_ordernum" value="<?php echo $this->filters['order']; ?>" />

	<?php echo Html::input('token'); ?>
</form>