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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';
use Components\Storefront\Models\Warehouse;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups Plugin class for group members
 */
class plgGroupsCart extends \Hubzero\Plugin\Plugin
{

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param object $group Current group
	 * @return     array
	 */
	public function onGroupOptions($group)
	{
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Check if member/manager
		$isManager = in_array(User::get("id"), $group->get("managers"));
		$isMember = in_array(User::get("id"), $group->get("members"));

		// Check if this thing is for sale
		$warehouse = new Warehouse();
		$product = $warehouse->getGroupProduct($group->get('gidNumber'));

		if($product && !$isManager &!$isMember)
		{
			if (User::isGuest())
			{
				$arr['html'] = 'Get access for ' . $product->sPrice;
			}
			elseif(empty($product->externalCheckoutURL))
			{
				$arr['html'] = '<form action="/cart/" id="frm" method="post">
				<input type="hidden" name="controller" value="cart">
<input name="option" type="hidden" value="com_cart" /><input name="updateCart" type="hidden" value="true" /> <input name="skus" type="hidden" value="' . $product->sId . '" /> <input name="-expressCheckout" type="hidden" value="false" /><button class="btn" type="submit">Get access for $' . $product->sPrice . '</button></form>';
			}
			else {
				$arr['html'] = '<a href="' . $product->externalCheckoutURL . '" class="btn" target="_blank" rel="noopener noreferrer">Get access for $' . $product->sPrice . '</a>';
			}
		}

		return $arr;
	}

}

