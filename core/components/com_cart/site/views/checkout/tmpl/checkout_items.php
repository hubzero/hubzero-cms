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
?>