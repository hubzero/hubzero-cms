<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

?>

<!--h2>Items</h2-->

<?php

	if (!empty($this->transactionItems))
	{

		echo '<table id="cartContents">';
		echo '<tr><th>Items</th><th>Price</th><th>Quantity</th></tr>';
		foreach ($this->transactionItems as $sId => $item)
		{
			$info = $item['info'];
			$transactionInfo = $item['transactionInfo'];

			echo '<tr>';

			echo '<td>';
			echo $info->pName;

			if (!empty($item['options']) && count($item['options']))
			{
				foreach ($item['options'] as $oName)
				{
					echo ', ' . $oName;
				}
			}

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
			echo '$' . number_format($transactionInfo->tiPrice, 2);
			echo '</td>';

			echo '<td>';
			echo $transactionInfo->qty;
			echo '</td>';

			echo '</tr>';

			// Check if there is a discount for this item
			if (!empty($this->perks['items'][$sId]))
			{
				echo '<tr class="cartItemDiscount">';

				echo '<td class="cartDiscountName"><span>Coupon discount:</span> ';
				echo $this->perks['items'][$sId]->name;
				echo '</td>';

				echo '<td class="cartDiscountDiscount">';
				echo '-$' . number_format($this->perks['items'][$sId]->discount, 2);
				echo '</td>';

				echo '<td>';
				echo '&nbsp;';
				echo '</td>';

				echo '</tr>';
			}
		}

		// Display other coupons
		if (!empty($this->perks['generic']))
		{
			foreach ($this->perks['generic'] as $coupon)
			{
				if ($coupon->discount)
				{
					echo '<tr class="cartDiscount">';

					echo '<td class="cartDiscountName"><span>Coupon discount:</span> ';
					echo $coupon->name;
					echo '</td>';

					echo '<td class="cartDiscountDiscount">';
					echo '-$' . number_format($coupon->discount, 2);
					echo '</td>';

					echo '<td>';
					echo '&nbsp;';
					echo '</td>';

					echo '</tr>';
				}
			}
		}

		// Display shipping discount
		if (!empty($this->perks['shipping']) && !empty($this->tiShippingDiscount) && $this->tiShippingDiscount > 0)
		{
			if ($this->tiShippingDiscount)
			{
				//print_r($this->perks); die;
				echo '<tr class="cartDiscount">';

				echo '<td class="cartDiscountName"><span>Coupon discount:</span> ';
				echo $this->perks['shipping']->name;
				echo '</td>';

				echo '<td class="cartDiscountDiscount">';
				echo '-$' . number_format($this->tiShippingDiscount, 2);
				echo '</td>';

				echo '<td>';
				echo '&nbsp;';
				echo '</td>';

				echo '</tr>';
			}
		}

		echo '</table>';

	}
