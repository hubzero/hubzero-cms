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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//print_r($this->transaction); die;
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
					<span>Order total: $<?php echo number_format($this->transaction->tInfo->info->tiTotalAmount, 2); ?></span>
				</p>
			</div>
		</div>
	</header>
	<div class="content">
		<?php

		$transactionItems = unserialize($this->transaction->tInfo->info->tiItems);
		$meta = unserialize($this->transaction->tInfo->info->tiMeta);

		//print_r($transactionItems); die;

		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';
		$warehouse = new \Components\Storefront\Models\Warehouse();

		foreach ($transactionItems as $sId => $item)
		{
			$info = $item['info'];
			$action = '';

			$productType = $warehouse->getProductTypeInfo($item['info']->ptId)['ptName'];

			$skuinfo = $warehouse->getSkuInfo($sId);

			if ($skuinfo)
			{
				// If course
				if ($productType == 'Course')
				{
					$action = '<a href="' . Route::url('index.php?option=com_courses/' . $item['meta']['courseId']);
					$action .= '">Go to the course page</a>';
				}
				// If software
				elseif ($productType == 'Software Download')
				{
					$action = '<a href="' . Route::url('index.php?option=com_cart') . 'download/' . $this->transaction->tInfo->info->tId . '/' . $info->sId;
					$action .= '" target="_blank">Download</a>';

					if (isset($item['meta']['serial']) && !empty($item['meta']['serial']))
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
			}
			else
			{
				$action = 'This product is no longer available';
			}

			echo '<div class="item grid">';
			echo '<div class="col span-half">';
			if ($skuinfo)
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
			if ($skuinfo)
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

