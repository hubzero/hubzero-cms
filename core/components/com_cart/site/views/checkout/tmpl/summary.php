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

$this->css();
?>

<header id="content-header">
	<h2>Review your order</h2>
</header>

<?php

if (!empty($this->notifications))
{
	$view = new \Hubzero\Component\View(array('name'=>'shared', 'layout' => 'notifications'));
	$view->notifications = $this->notifications;
	$view->display();
}

?>

<section class="main section">
	<div class="section-inner">
		<?php
		$errors = $this->getError();
		if (!empty($errors))
		{
			foreach ($errors as $error)
			{
				echo '<p class="error">' . $error . '</p>';
			}
		}
		?>

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

		$view = new \Hubzero\Component\View(array('name'=>'shared', 'layout' => 'messages'));
		$view->setError($this->getError());
		$view->display();
		?>

		<div class="grid">
			<?php
			$view = new \Hubzero\Component\View(array('name'=>'checkout', 'layout' => 'checkout_items'));
			$view->perks = $perks;
			$view->membershipInfo = $membershipInfo;
			$view->transactionItems = $this->transactionItems;
			$view->tiShippingDiscount = $this->transactionInfo->tiShippingDiscount;

			echo '<div class="col span6">';

			$view->display();

			echo '</div>';

			echo '<div class="col span6 omega orderSummary">';

			if (!empty($this->transactionInfo))
			{
				$orderTotal = $this->transactionInfo->tiSubtotal + $this->transactionInfo->tiShipping - $this->transactionInfo->tiDiscounts - $this->transactionInfo->tiShippingDiscount;
				$discount = $this->transactionInfo->tiDiscounts + $this->transactionInfo->tiShippingDiscount;

				echo '<h2>Order summary:</h2>';

				echo '<p>Order subtotal: ' . money_format('%n', $this->transactionInfo->tiSubtotal) . '</p>';

				if ($this->transactionInfo->tiShipping > 0)
				{
					echo '<p>Shipping: ' . money_format('%n', $this->transactionInfo->tiShipping) . '</p>';
				}
				if ($discount > 0)
				{
					echo '<p>Discounts: ' . money_format('%n', $discount) . '</p>';
				}

				echo '<p class="orderTotal">Order total: ' . money_format('%n', $orderTotal) . '</p>';
			}

			echo '</div>';
			?>
		</div>
		<?php
		if (in_array('shipping', $this->transactionInfo->steps))
		{
			$view = new \Hubzero\Component\View(array('name'=>'checkout', 'layout' => 'checkout_shippinginfo'));
			$view->transactionInfo = $this->transactionInfo;
			$view->display();
		}

		$orderTotal = $this->transactionInfo->tiSubtotal + $this->transactionInfo->tiShipping - $this->transactionInfo->tiDiscounts - $this->transactionInfo->tiShippingDiscount;

		if ($orderTotal > 0)
		{
			$buttonLabel = 'Proceed to payment';
			$buttonLink = Route::url('index.php?option=com_cart/checkout/confirm');
		}
		else
		{
			$buttonLabel = 'Place order';
			$buttonLink = Route::url('index.php?option=com_cart/order/place/' . $this->token);
		}
		?>
		<p class="submit">
			<a href="<?php echo $buttonLink; ?>" class="btn"><?php echo $buttonLabel; ?></a>
		</p>
	</div>
</section>