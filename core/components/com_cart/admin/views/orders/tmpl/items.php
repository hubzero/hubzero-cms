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
</script>

<?php
$this->view('_submenu')
	->display();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_SKU_ID', 'sId', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">Product</th>
				<th scope="col">QTY</th>
				<th scope="col">Price</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDER_ID', 'tId', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_ORDER_PALCED', 'tLastUpdated', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
//for ($i=0, $n=count($this->rows); $i < $n; $i++)
$i = 0;

foreach ($this->rows as $row)
{
	$itemInfo = $row->itemInfo['info'];
	//print_r($row); die;
?>
	<tr class="<?php echo "row$k"; ?>">
		<td>
			<?php
			$sId = '<a href="' . Route::url('index.php?option=com_storefront&controller=skus&task=edit&id=' . $itemInfo->sId) . '"">' . $this->escape(stripslashes($row->sId)) . '</a>';
			?>
			<span><?php echo $sId; ?></span>
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
				$product .= ', <span class="missing">SKU n/a</span>';
			}
			else {
				$product .= ', ' . '<a href="' . Route::url('index.php?option=com_storefront&controller=skus&task=edit&id=' . $row->sId) . '" target="_blank">' . $this->escape(stripslashes($itemInfo->sSku)) . '</a>';
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
			<?php
			$tId = '<a href="' . Route::url('index.php?option=com_cart&controller=orders&task=view&id=' . $row->tId) . '"">' . $this->escape(stripslashes($row->tId)) . '</a>';
			?>
			<span><?php echo $tId; ?></span>
		</td>
		<td>
			<span><?php echo $this->escape(stripslashes($row->tLastUpdated)); ?></span>
		</td>
		<td>
			<span><?php echo $this->escape(stripslashes($row->name)); ?></span>
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

	<?php echo Html::input('token'); ?>
</form>