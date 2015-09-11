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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

setlocale(LC_MONETARY, 'en_US.UTF-8');

$this->css()
     ->js()
?>

<header id="content-header">
	<h2><?php echo  Lang::txt('COM_CART'); ?></h2>
</header>

<?php

if (!empty($this->notifications))
{
	$view = new \Hubzero\Component\View(array('name'=>'shared', 'layout' => 'notifications'));
	$view->notifications = $this->notifications;
	$view->display();
}

?>

<?php
/*
$errors = $this->getError();
if (!empty($errors))
{
	echo '<section class="section messages errors">';
		echo '<div class="section-inner">';
		foreach ($errors as $error)
		{
			echo '<p class="error">' . $error . '</p>';
		}
		echo '</section>';
	echo '</section>';
}
*/
?>

<section class="main section">
	<div class="section-inner">

		<div class="grid break3">

			<div id="cartItems" class="col span8">

				<form name="shoppingCart" id="shoppingCart" method="post">
				<?php

				if (!empty($this->couponPerks['items']))
				{
					$itemsPerks = $this->couponPerks['items'];
				}

				if (!empty($this->cartInfo->items))
				{
					echo '<table id="cartContents" cellpadding="0" cellpadding="0">';

					foreach ($this->cartInfo->items as $sId => $item)
					{
						$info = $item['info'];

						if (!$item['cartInfo']->qty) {
							continue;
						}

						echo '<tr>';

						echo '<td>';
						echo '<a href="';
						echo Route::url('index.php?option=com_storefront/product/' . $info->pId);
						echo '" class="cartItem">';
						echo $info->pName;

						if (!empty($item['options']) && count($item['options']))
						{
							foreach ($item['options'] as $oName)
							{
								echo ', ' . $oName;
							}
						}

						echo '</a>';

						// Check is there is any membership info for this item
						if (!empty($this->membershipInfo[$sId]))
						{
							$str = '';
							if (!empty($this->membershipInfo[$sId]->existingExpires))
							{
								$str .= 'This will extend your current subscription (ending ' . date('M j, Y', $this->membershipInfo[$sId]->existingExpires) . ') ';
							}
							else
							{
								$str .= 'This item will be valid ';
							}

							//print_r($this->membershipInfo[$sId]);
							$str .= 'until ' . date('M j, Y', $this->membershipInfo[$sId]->newExpires);
							echo '<p class="status">' . $str . '</p>';
						}

						echo '</td>';

						echo '<td>';
						if ($info->sAllowMultiple) {
							echo 'qty: <input type="number" maxlength="2" pattern="[0-9]*" class="numericOnly" name="skus[' . $info->sId . ']" value="';;
							echo $item['cartInfo']->qty;
							echo '">';
						}
						else {
							echo '&nbsp;';
						}
						echo '</td>';

						echo '<td class="rightJustify price">';
						echo '<p>' . money_format('%n', $info->sPrice * $item['cartInfo']->qty);

						if ($item['cartInfo']->qty > 1)
						{
							echo '<br><span>' . money_format('%n', $info->sPrice) . ' each</span>';
						}

						echo '</p>';
						echo '<input type="submit" class="deleteItem link" name="delete_' . $info->sId . '" value="delete">';
						echo '</td>';

						echo '</tr>';

						// Check if there is a discount for this item
						if (!empty($itemsPerks[$sId]))
						{
							echo '<tr class="cartItemDiscount">';

							echo '<td class="cartDiscountName"><!--span>Discount:</span--> ';
							echo $itemsPerks[$sId]->name;
							echo '</td>';

							echo '<td>';
							echo '&nbsp;';
							echo '</td>';

							echo '<td class="cartDiscountDiscount rightJustify price">';
							echo money_format('-%n', $itemsPerks[$sId]->discount);
							echo '</td>';

							echo '</tr>';
						}

					}
					echo '</table>';

					echo '<input type="submit" class="btn" name="updateCart" id="updateCart" value="Update cart">';

				}
				else
				{
					echo '<p>' . Lang::txt('COM_CART_EMPTY') . '</p>';
				}
				?>

				</form>

			</div> <!-- // cartItems -->

			<div id="cartInfo" class="col span4 omega">

			<?php
				if (!empty($this->cartInfo))
				{
					echo '<div id="cartSummary" class="cartSection">';
						echo '<h3>Cart summary:</h3>';

						echo '<p>Items: <span>' . $this->cartInfo->totalItems . '</span></p>';
						echo '<p>Items subtotal: <span>' . money_format('%n', $this->cartInfo->totalCart) . '</span></p>';

						$discountsTotal = 0;
						if (!empty($this->couponPerks['info']->itemsDiscountsTotal))
						{
							echo '<p>Items discounts: <span>' .  money_format('-%n', $this->couponPerks['info']->itemsDiscountsTotal) . '</span></p>';
							$discountsTotal += $this->couponPerks['info']->itemsDiscountsTotal;
						}


						if (!empty($this->couponPerks['generic']))
						{
							$genericPerks = $this->couponPerks['generic'];

							foreach ($genericPerks as $perk)
							{
								echo '<p class="cartDiscountName"><span>Discount</span>: ' . $perk->name . ': ';
								if ($perk->discount > 0)
								{
									echo money_format('-%n', $perk->discount);
								}
								else
								{
									echo 'may be applied during checkout process';
								}
								echo '</p>';
								$discountsTotal +=  $perk->discount;
							}
						}

						if (!empty($this->couponPerks['shipping']))
						{
							$shippingPerk = $this->couponPerks['shipping'];

							echo '<p class="cartDiscountName"><span>Coupon discount</span>: ' . $shippingPerk->name . ': ';
							echo 'may be applied during checkout process';
							echo '</p>';
						}

						if ($discountsTotal)
						{
							echo '<p class="totalValue">Cart subtotal: <span>' . money_format('%n', $this->cartInfo->totalCart - $discountsTotal) . '</span></p>';
						}

						if ($this->cartInfo->totalItems)
						{
							echo '<p><a href="index.php?option=' . Request::getVar('option') . '/checkout" class="btn">Checkout</a></p>';
						}

					echo '</div>';
				}
			?>

				<div class="cartSection">
					<h4>Do you have a promo or coupon code?</h4>

					<form name="couponCodes" id="couponCodes" method="post">
						<label for="couponCode">
						<input type="text" name="couponCode" id="couponCode"></label>
						<input type="submit" name="addCouponCode" id="addCouponCode" class="btn" value="Apply">
					</form>
				</div>

			</div> <!-- // cart info -->
	</div>
</section>