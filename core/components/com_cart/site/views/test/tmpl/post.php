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

<header id="content-header">
	<h2>Post test</h2>
</header>

<section class="section">
	<form action="/cart/" id="frm" method="post">

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