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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css()
     ->js()
?>

<header id="content-header">
	<h2><?php echo  Lang::txt('COM_CART'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn" href="<?php echo Route::url('index.php?option=com_cart') . 'orders'; ?>"><?php echo Lang::txt('COM_CART_ORDERS'); ?></a>
		</p>
	</div>
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

				<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" name="shoppingCart" id="shoppingCart" method="post">
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
						echo Route::url('index.php?option=com_storefront'). '/product/' . $info->pId;
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
						echo '<p>' . '$' . number_format($info->sPrice * $item['cartInfo']->qty, 2);

						if ($item['cartInfo']->qty > 1)
						{
							echo '<br><span>' . '$' . number_format($info->sPrice, 2) . ' each</span>';
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
							echo '-$' . number_format($itemsPerks[$sId]->discount, 2);
							echo '</td>';

							echo '</tr>';
						}

					}
					echo '</table>';

					echo '<div class="options cf">';
					echo '<a href="' . Route::url('index.php?option=com_storefront') . '" class="btn">Continue shopping</a>';
					echo '<input type="submit" class="btn" name="updateCart" id="updateCart" value="Update cart">';
					echo '</div>';
				}
				else
				{
					echo '<p>' . Lang::txt('COM_CART_EMPTY') . '</p>';
				}
				?>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				</form>

			</div> <!-- // cartItems -->

			<div id="cartInfo" class="col span4 omega">

			<?php
				if (!empty($this->cartInfo) && $this->cartInfo->totalItems > 0)
				{
					echo '<div id="cartSummary" class="cartSection">';
						echo '<h3>Cart summary:</h3>';

						echo '<p>Items: <span>' . $this->cartInfo->totalItems . '</span></p>';
						echo '<p>Items subtotal: <span>' . '$' . number_format($this->cartInfo->totalCart, 2) . '</span></p>';

						$discountsTotal = 0;
						if (!empty($this->couponPerks['info']->itemsDiscountsTotal))
						{
							echo '<p>Items discounts: <span>' .  '-$' . number_format($this->couponPerks['info']->itemsDiscountsTotal, 2) . '</span></p>';
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
									echo '-$' . number_format($perk->discount, 2);
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
							echo '<p class="totalValue">Cart subtotal: <span>' . '$' . number_format($this->cartInfo->totalCart - $discountsTotal, 2) . '</span></p>';
						}

						if ($this->cartInfo->totalItems)
						{
							echo '<p><a href="' . Route::url('index.php?option=' . $this->option) . 'checkout" class="btn">Checkout</a></p>';
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