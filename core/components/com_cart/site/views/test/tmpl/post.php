<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

setlocale(LC_MONETARY, 'en_US.UTF-8');

?>

<header id="content-header">
	<h2>Post test</h2>
</header>

<section class="section">
	<form action="<?php echo Route::url('index.php?option=com_cart'); ?>" id="frm" method="post">

		<!-- 	TO ADD PRODUCT (accepts multiple):
				name: 	pId[productID]
				value: 	quantity to set in the cart
		-->
		<input type="hidden" name="pId[1]" value="1"></input>
		<input type="hidden" name="updateCart" value="updateCart"></input>

		<!-- 	TO ADD COUPON TO CART (only one can be added for now):
				value: 	coupon code
		-->
		<!--input type="hidden" name="couponCode" value="couponCodeHere"></input>
		<input type="hidden" name="addCouponCode" value="addCouponCode"></input-->


		<input type="submit" value="Submit">
		<a id="ajax" href="#">Ajax call</a>
	</form>
</section>