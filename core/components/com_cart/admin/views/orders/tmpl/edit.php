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

$canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('order');

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CART').': Edit order info');

if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();

$this->css()
	->js();
?>

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
						<td><span><?php echo $this->user->get('id') ? $this->user->get('name') . ' (' . $this->user->get('username') . ')': Lang::txt('COM_CART_UNKNOWN'); ?></span></td>
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
					if (!empty($this->tInfo->tiShipping) && floatval($this->tInfo->tiShipping))
					{
					?>
						<tr>
							<th>Shipping cost:</th>
							<td><span><?php echo '$' . number_format($this->tInfo->tiShipping, 2); ?></span></td>
						</tr>
						<?php
					}
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

		<?php
		if (!empty($this->tInfo->tiPayment))
		{
			?>
			<fieldset class="adminform">
				<legend><span>Payment info</span></legend>

				<p>Payment method: <?php echo $this->escape($this->tInfo->tiPayment); ?></p>

				<?php
				if (!empty($this->tInfo->tiPaymentDetails))
				{
				?>
					<p>
						<strong>Payment details:</strong><br>
						<input type="text" name="tiPaymentDetails" value="<?php echo $this->escape($this->tInfo->tiPaymentDetails); ?>" />
					</p>
				<?php
				}
				?>
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
				$itemsOrdered = $this->items;

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
						<td><input type="text" name="tiPrice[<?php echo $itemInfo->sId; ?>]" size="10" maxlength="100" value="<?php echo $itemOrdered['transactionInfo']->tiPrice; ?>" /></td>
						<td><input type="text" name="tiQty[<?php echo $itemInfo->sId; ?>]" size="10" maxlength="100" value="<?php echo $itemOrdered['transactionInfo']->qty; ?>" /></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</fieldset>

		<?php
		// Check the notes, both SKU-specific and other
		$notes = array();
		foreach ($this->items as $sId => $item)
		{
			$meta = $item['transactionInfo']->tiMeta;

			if (!empty($meta->checkoutNotes))
			{
				$notes[] = array(
					'object' => 'transactionItem',
					'objectId' => $item['info']->sId,
					'label' => '<strong>' . $this->tInfo->tiItems[$sId]['info']->pName . ', ' . $this->tInfo->tiItems[$sId]['info']->sSku . '</strong>',
					'notes' => $meta->checkoutNotes
				);
			}
		}

		$genericNotesLabel = '';
		if (!empty($notes))
		{
			$genericNotesLabel = '<strong>' . 'Other notes/comments' . '</strong>';
		}

		if ($this->tInfo->tiNotes)
		{
			$notes[] = array(
				'object' => 'transaction',
				'objectId' => $this->tInfo->tId,
				'label' => $genericNotesLabel,
				'notes' => $this->tInfo->tiNotes
			);
		}

		if (!empty($notes))
		{
			echo '<fieldset class="adminform">';
			echo '<legend><span>Notes/Comments</span></legend>';
			foreach ($notes as $note)
			{
				//print_r($note); die;
				echo '<p>';
				echo $note['label'];
				if ($note['object'] == 'transactionItem')
				{
					echo '<textarea rows="6" name="checkoutNotes[' . $note['objectId'] . ']">' . $note['notes'] . '</textarea>';
				}
				elseif ($note['object'] == 'transaction')
				{
					echo '<textarea rows="6" name="tiNotes">' . $note['notes'] . '</textarea>';
				}
				echo '</p>';
			}
			echo '</fieldset>';
		};
		?>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->tId; ?>" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="from" value="edit" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo Html::input('token'); ?>
</form>