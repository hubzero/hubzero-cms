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
			echo money_format('%n', $transactionInfo->tiPrice);
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
				echo money_format('-%n', $this->perks['items'][$sId]->discount);
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
					echo money_format('-%n', $coupon->discount);
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
				echo money_format('-%n', $this->tiShippingDiscount);
				echo '</td>';

				echo '<td>';
				echo '&nbsp;';
				echo '</td>';

				echo '</tr>';
			}
		}

		echo '</table>';

	}
?>