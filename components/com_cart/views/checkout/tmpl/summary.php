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

?>

<div id="content-header">
	<h2>Review your order</h2>
</div>

<div class="section">

<?php

$perks = false;
if (!empty($this->transactionInfo->tiPerks))
{
	$perks = $this->transactionInfo->tiPerks;
	$perks = unserialize($perks);
}

$membershipInfo = false;
if (!empty($this->transactionInfo->tiMeta))
{
	$meta = unserialize($this->transactionInfo->tiMeta);
	
	if (!empty($meta['membershipInfo']))
	{
		$membershipInfo = $meta['membershipInfo'];		
	}
}

$view = new JView(array('name'=>'shared', 'layout' => 'messages'));
$view->setError($this->getError());
$view->display();

if (in_array('shipping', $this->transactionInfo->steps))
{
	$view = new JView(array('name'=>'checkout', 'layout' => 'checkout_shippinginfo'));
	$view->transactionInfo = $this->transactionInfo;
	$view->display();
}

$view = new JView(array('name'=>'checkout', 'layout' => 'checkout_items'));

$view->perks = $perks;
$view->membershipInfo = $membershipInfo;
$view->transactionItems = $this->transactionItems;
$view->tiShippingDiscount = $this->transactionInfo->tiShippingDiscount;
$view->display();

$orderTotal = $this->transactionInfo->tiSubtotal + $this->transactionInfo->tiShipping - $this->transactionInfo->tiDiscounts - $this->transactionInfo->tiShippingDiscount;

if (!empty($this->transactionInfo))
{
	echo '<div id="orderSummary">';
		echo '<h2>Order summary:</h2>';
		
		echo '<p>Order subtotal: ' . money_format('%n', $this->transactionInfo->tiSubtotal) . '</p>';
		echo '<p>Shipping: ' . money_format('%n', $this->transactionInfo->tiShipping) . '</p>';
		echo '<p>Discounts: ' . money_format('%n', $this->transactionInfo->tiDiscounts + $this->transactionInfo->tiShippingDiscount) . '</p>';
		
		echo '<p>Order total: ' . money_format('%n', $orderTotal) . '</p>';		
	echo '</div>';	
}

if ($orderTotal > 0)
{
	$buttonLabel = 'Proceed to payment';
	$buttonLink = JRoute::_('index.php?option=com_cart/checkout/confirm');
}
else 
{
	$buttonLabel = 'Place order';
	$buttonLink = JRoute::_('index.php?option=com_cart/order/place/' . $this->token);
}


echo '<a href="';
echo $buttonLink;
echo '" class="btn">';
echo $buttonLabel;
echo '</a>';

?>

</div>