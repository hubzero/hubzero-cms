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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CART').': View order', 'courses.png');

Toolbar::cancel();

$this->css();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-40 fltlft">
		<fieldset class="adminform">
			<legend><span>Order Details</span></legend>

			<table class="formed">
				<tbody>
				<tr>
					<th>Order number:</th>
					<td><span><?php echo $this->tInfo->tId; ?></span></td>
				</tr>
				<tr>
					<th>Order placed:</th>
					<td><span><?php echo $this->tInfo->tLastUpdated; ?></span></td>
				</tr>
				<tr>
					<th>Ordered by:</th>
					<td><span><?php echo $this->user->get('name'); ?> (<?php echo $this->user->get('username'); ?>)</span></td>
				</tr>
				<tr>
					<th>Order subtotal:</th>
					<td><span><?php echo '$' . number_format($this->tInfo->tiSubtotal, 2); ?></span></td>
				</tr>
				<?php
				if (!empty($this->tInfo->tiTax) && $this->tInfo->tiTax)
				{
				?>
					<tr>
						<th>Tax:</th>
						<td><span><?php echo '$' . number_format($this->tInfo->tiTax, 2); ?></span></td>
					</tr>
				<?php
				}
				?>
				<?php
				if (!empty($this->tInfo->tiShipping) && floatval($this->tInfo->tiShipping))
				{
				?>
					<tr>
						<th>Shipping cost:</th>
						<td><span><?php echo '$' . number_format($this->tInfo->tiShipping, 2); ?></span></td>
					</tr>
					<?php
				}
				?>
				<?php
				if (!empty($this->tInfo->tiDiscounts) && floatval($this->tInfo->tiDiscounts))
				{
				?>
					<tr>
						<th>Discounts:</th>
						<td><span><?php echo '$' . number_format($this->tInfo->tiDiscounts, 2); ?></span></td>
					</tr>
					<?php
				}
				?>
				<tr>
					<th>Order total:</th>
					<td><span><?php echo '$' . number_format($this->tInfo->tiTotal, 2); ?></span></td>
				</tr>
				</tbody>
			</table>

		</fieldset>

		<?php
		if (!empty($this->tInfo->tiShippingToFirst))
		{
		?>
			<fieldset class="adminform">
				<legend><span>Shipping info</span></legend>

				<p>
					<strong>Ship to:</strong><br>
					<?php
						echo $this->tInfo->tiShippingToFirst . ' ' . $this->tInfo->tiShippingToLast . '<br>';
						echo $this->tInfo->tiShippingAddress . '<br>';
						echo $this->tInfo->tiShippingCity . ', ' . $this->tInfo->tiShippingState . ' ' . $this->tInfo->tiShippingZip . '<br>';
					?>
				</p>

			</fieldset>
		<?php
		}
		?>
	</div>
	<div class="col width-60 fltrt">
		<fieldset class="adminform">
			<legend><span>Items Ordered</span></legend>

			<table class="formed">
				<thead>
					<tr>
						<th>Product</th>
						<th>Price</th>
						<th>QTY</th>
					</tr>
				</thead>
				<tbody>

			<?php
				$itemsOrdered = $this->tInfo->tiItems;
				//print_r($itemsOrdered); die;

				foreach ($itemsOrdered as $itemOrdered)
				{
					$itemInfo = $itemOrdered['info'];
			?>
					<tr>
						<td>
							<?php
							if ($itemInfo->available)
							{
								$product = '<a href="' . Route::url('index.php?option=com_storefront&controller=products&task=edit&id=' . $itemInfo->pId) . '" target="_blank">' . $this->escape(stripslashes($itemInfo->pName)) . '</a>';
								$product .= ', ' . '<a href="' . Route::url('index.php?option=com_storefront&controller=skus&task=edit&id=' . $itemInfo->sId) . '" target="_blank">' . $this->escape(stripslashes($itemInfo->sSku)) . '</a>';
							}
							else {
								$product = $this->escape(stripslashes($itemInfo->pName)) .  ', ' . $this->escape(stripslashes($itemInfo->sSku));
								$product .= ' <br><em>&nbsp;&mdash;&nbsp;Item is no longer available</em>';
							}
							?>
							<span><?php echo $product; ?></span>
						</td>
						<td><span><?php echo $itemInfo->sPrice; ?></span></td>
						<td><span><?php echo $itemOrdered['cartInfo']->qty; ?></span></td>
					</tr>
			<?php
				}
			?>

				</tbody>
			</table>

		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->tId; ?>" />
	<input type="hidden" name="task" value="save" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo Html::input('token'); ?>
</form>