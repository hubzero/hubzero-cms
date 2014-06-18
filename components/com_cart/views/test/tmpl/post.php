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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

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