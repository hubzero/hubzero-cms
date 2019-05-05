<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

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
						$action .= '" target="_blank" download="download" rel="noopener">Download</a>';

						if (isset($item['meta']['serialManagement']) && $item['meta']['serialManagement'] == 'multiple' && isset($item['meta']['serials']) && !empty($item['meta']['serials']))
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