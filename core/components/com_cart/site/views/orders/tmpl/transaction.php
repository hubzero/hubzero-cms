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
 * @author    HUBzero
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

//print_r($this->transaction); die;
$tiTotalAmount = $this->transaction->tInfo->tiSubtotal + $this->transaction->tInfo->tiTax + $this->transaction->tInfo->tiShipping;
?>

<li class="order">
	<header>
		<h3><?php echo 'Order # ' . $this->transaction->tId; ?></h3>
		<div class="grid">
			<div class="col span-half">
				<p class="order-info">
					<span>Order placed: <?php echo date("F j, Y", strtotime($this->transaction->tLastUpdated)); ?></span>
				</p>
			</div>
			<div class="col span-half omega">
				<p class="order-info">
					<span>Order total: $<?php echo number_format($tiTotalAmount, 2); ?></span>
				</p>
			</div>
		</div>
	</header>
	<div class="content">
		<?php

		$transactionItems = $this->transaction->tInfo->tiItems;
		$meta = unserialize($this->transaction->tInfo->tiMeta);

		//print_r($transactionItems); die;

		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';
		$warehouse = new \Components\Storefront\Models\Warehouse();

		foreach ($transactionItems as $sId => $item)
		{
			$info = $item['info'];
			$action = '';

			$productType = $warehouse->getProductTypeInfo($item['info']->ptId)['ptName'];

			// If course
			if ($productType == 'Course')
			{
				if ($info->available)
				{
					$action = '<a href="' . Route::url('index.php?option=com_courses/' . $item['meta']['courseId']);
					$action .= '">Go to the course page</a>';
				}
				else
				{
					$action = 'This product is no longer available';
				}
			}
			// If software
			elseif ($productType == 'Software Download')
			{
				if ($info->available)
				{
					$action = '<a href="' . Route::url('index.php?option=com_cart') . 'download/' . $this->transaction->tInfo->tId . '/' . $info->sId;
					$action .= '" target="_blank">Download</a>';
				}
				else
				{
					$action = 'This product is no longer available';
				}

				if ($item['meta']['serialManagement'] == 'multiple' && isset($item['meta']['serials']) && !empty($item['meta']['serials']))
				{
					$action .= "<br>";
					$action .= " Serial number";
					if (count($item['meta']['serials']) > 1)
					{
						$action .= "s";
					}
					$action .= ': <strong>';
					foreach ($item['meta']['serials'] as $serial)
					{
						if (count($item['meta']['serials']) > 1)
						{
							$action .= '<br>';
						}
						$action .= $serial;
					}
					$action .= '</strong>';
				}
				elseif (isset($item['meta']['serial']) && !empty($item['meta']['serial']))
				{
					$action .= "<br>";
					$action .= " Serial number: <strong>" . $item['meta']['serial'] . '</strong>';
				}
			}
			else
			{
				if (!empty($item['meta']['purchaseNote']))
				{
					$action = $item['meta']['purchaseNote'];
				}
			}

			echo '<div class="item grid">';
			echo '<div class="col span-half">';
			if ($info->available)
			{
				echo '<a href="';
				echo Route::url('index.php?option=com_storefront') . '/product/' . $info->pId;
				echo '" class="">';
			}
			echo $info->pName;

			if (!empty($item['options']) && count($item['options']))
			{
				foreach ($item['options'] as $oName)
				{
					echo ', ' . $oName;
				}
			}
			if ($info->available)
			{
				echo '</a>';
			}
			echo '</div>';

			echo '<div class="col span-half omega">';
			echo $action;
			echo '</div>';
			echo '</div>';

		}

		?>
	</div>
</li>