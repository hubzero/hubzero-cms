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

setlocale(LC_MONETARY, 'en_US.UTF-8');

?>

<h1><?php echo  JText::_('COM_CART'); ?></h1>

<?php

$errors = $this->getError();
if (!empty($errors))
{
	echo '<div class="messages errors">';
		foreach ($errors as $error)
		{
			echo '<p>' . $error . '</p>';
		}
	echo '</div>';
}

?>

<form name="shoppingCart" id="shoppingCart" method="post">
<?php

if (!empty($this->couponPerks['items']))
{
	$itemsPerks = $this->couponPerks['items'];
}

//print_r($this->cartInfo->items); die;

if (!empty($this->cartInfo->items))
{
	echo '<table id="cartContents">';
	echo '<tr><th>Item</th><th>Amount</th><th>Quantity</th></tr>';
	foreach ($this->cartInfo->items as $sId => $item)
	{
		$info = $item['info'];
		
		if (!$item['cartInfo']->qty) {
			continue;
		}
		
		echo '<tr>';
		
		echo '<td>';
		echo '<a href="';
		echo JRoute::_('index.php?option=com_storefront/product/' . $info->pId);
		echo '">';
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
		echo money_format('%n', $info->sPrice);
		echo '</td>';
		
		echo '<td>';
		echo '<input type="number" name="skus[' . $info->sId . ']" value="';;
		echo $item['cartInfo']->qty;
		echo '">';
		echo '</td>';
		
		echo '</tr>';
		
		// Check if there is a discount for this item
		if (!empty($itemsPerks[$sId]))
		{
			echo '<tr class="cartItemDiscount">';
		
			echo '<td class="cartDiscountName"><span>Coupon discount:</span> ';
			echo $itemsPerks[$sId]->name;
			echo '</td>';
			
			echo '<td class="cartDiscountDiscount">';
			echo money_format('-%n', $itemsPerks[$sId]->discount);
			echo '</td>';
			
			echo '<td>';
			echo '&nbsp;';
			echo '</td>';
			
			echo '</tr>';	
		}
		
	}
	echo '</table>';
	
	echo '<input type="submit" class="btn" name="updateCart" id="updateCart" value="Update cart">';	
}
else
{
	echo '<p>' . JText::_('COM_CART_EMPTY') . '</p>';	
}
?>

</form>

<?php
	if (!empty($this->cartInfo))
	{
		echo '<div id="cartSummary">';
			echo '<h2>Cart summary:</h2>';
			
			echo '<p>Items: ' . $this->cartInfo->totalItems . '</p>';
			echo '<p>Items subtotal: ' . money_format('%n', $this->cartInfo->totalCart) . '</p>';
			
			$discountsTotal = 0;
			if (!empty($this->couponPerks['info']->itemsDiscountsTotal))
			{
				echo '<p>Items discounts: ' .  money_format('-%n', $this->couponPerks['info']->itemsDiscountsTotal) . '</p>';
				$discountsTotal += $this->couponPerks['info']->itemsDiscountsTotal;
			}
			
			
			if (!empty($this->couponPerks['generic']))
			{
				$genericPerks = $this->couponPerks['generic'];
				
				foreach ($genericPerks as $perk)
				{
					echo '<p class="cartDiscountName"><span>Coupon discount</span>: ' . $perk->name . ': ';
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
				echo '<p>Cart subtotal: ' . money_format('%n', $this->cartInfo->totalCart - $discountsTotal) . '</p>';	
			}
		
			echo '<p><a href="index.php?option=' . JRequest::getVar('option') . '/checkout" class="btn">Checkout</a></p>';
		echo '</div>';	
	}
?>

<div class="cartSection">
	<h2>Do you have a promo or coupon code?</h2>
	
	<form name="couponCodes" id="couponCodes" method="post">
		<label for="couponCode">
		<input type="text" name="couponCode" id="couponCode"></label>
		<input type="submit" name="addCouponCode" id="addCouponCode" class="btn" value="Add code">
	</form>
</div>	