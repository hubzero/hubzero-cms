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

?>

<header id="content-header">
	<h2>Thank you!</h2>
</header>

<section class="main section">
	<div class="section-inner">


		<p>Thank you for your order.</p>
		<p>You will receive an email confirmation shortly at the email address associated with your account. Your transaction is now complete.</p>

		<section class="section">
		<?php

		//print_r($this->transactionInfo); die;

			if (!empty($this->transactionInfo))
			{

				$transactionItems = unserialize($this->transactionInfo->tiItems);
				$meta = unserialize($this->transactionInfo->tiMeta);
				$membershipInfo = $meta['membershipInfo'];

				//print_r($transactionItems); die;

				echo '<h2>Order summary</h2>';
				echo '<table id="cartContents">';
				echo '<tr><th>Item</th><th>Status</th><th>Notes</th></tr>';

				require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';
				$warehouse = new \Components\Storefront\Models\Warehouse();

				foreach ($transactionItems as $sId => $item)
				{
					$info = $item['info'];
					$action = '';

					$productType = $warehouse->getProductTypeInfo($item['info']->ptId)['ptName'];
					//print_r($item); die;

					// If course
					if ($productType == 'Course')
					{
						$status = 'Registered';
						$action = '<a href="' . Route::url('index.php?option=com_courses/' . $item['meta']['courseId']);
						$action .= '">Go to the course page</a>';
					}
					// If software
					elseif ($productType == 'Software Download')
					{
						$status = 'Ready';
						$action = '<a href="' . Route::url('index.php?option=com_cart') . 'download/' . $this->transactionInfo->tId . '/' . $info->sId;
						$action .= '" target="_blank">Download</a>';

						if (isset($item['meta']['serial']) && !empty($item['meta']['serial']))
						{
							$action .= "<br>";
							$action .= " Serial number: <strong>" . $item['meta']['serial'] . '</strong>';
						}
					}
					else {
						$status = 'Purchased';
						if (!empty($item['meta']['purchaseNote']))
						{
							$action = $item['meta']['purchaseNote'];
						}
					}

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
					echo '</td>';

					echo '<td>';
						echo $status;

						// Check is there is any membership info for this item
						if (!empty($membershipInfo[$sId]))
						{
							//echo ', valid until ' . date('M j, Y', $membershipInfo[$sId]->newExpires);
						}

					echo '</td>';

					echo '<td>';
						echo $action;
					echo '</td>';

					echo '</tr>';

				}

				echo '</table>';

			}
		?>
		</section>

	</div>
</section>