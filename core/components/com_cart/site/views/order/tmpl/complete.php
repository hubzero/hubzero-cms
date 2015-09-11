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
 * @author    HUBzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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

			if (!empty($this->transactionInfo))
			{

				$transactionItems = unserialize($this->transactionInfo->tiItems);
				$meta = unserialize($this->transactionInfo->tiMeta);
				$membershipInfo = $meta['membershipInfo'];

				//print_r($transactionItems); die;

				echo '<h2>Order summary</h2>';
				echo '<table id="cartContents">';
				echo '<tr><th>Item</th><th>Status</th><th>Notes</th></tr>';
				foreach ($transactionItems as $sId => $item)
				{
					$info = $item['info'];
					$skuMeta = $item['meta'];
					$action = '';

					// If course
					if ($info->ptId == 20)
					{
						$status = 'Registered';
						$action = '<a href="' . Route::url('index.php?option=com_courses/' . $item['meta']['courseId']);
						$action .= '">Go to the course page</a>';
					}
					else
					{
						$status = 'Purchased';

						if (!empty($skuMeta['purchaseNote']))
						{
							$action = $skuMeta['purchaseNote'];
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