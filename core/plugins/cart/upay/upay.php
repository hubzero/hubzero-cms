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

/**
 * Cart plugin for Payment: UPay
 */
class plgCartUpay extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Render payment options
	 *
	 * @param   object  $cart
	 * @param   object  $user
	 * @return  array
	 */
	public function onRenderPaymentOptions($cart, $user)
	{
		$view = $this->view('default', 'payment')
			->set('user', $user)
			->set('cart', $cart);

		$payment = array();
		$payment['options'] = $view->loadTemplate();
		$payment['title'] = $this->params->get('title', 'Offline');

		return $payment;
	}

	/**
	 * Return a list of filters that can be applied
	 *
	 * @return  array
	 */
	public function onProcessPayment($transaction, $user)
	{
		return true;
	}

	/**
	 * Get the Posting URL
	 *
	 * @return  string
	 */
	private function getPostURL()
	{
		if ($this->params->get('env') == 'LIVE')
		{
			$url = 'https://secure.touchnet.com/C21261_upay/web/index.jsp';
		}
		else
		{
			$url = 'https://secure.touchnet.com:8443/C21261test_upay/web/index.jsp';
		}

		return $url;
	}

	/**
	 * Generate the validation key
	 *
	 * @param   array   $transaction
	 * @return  string
	 */
	private function generateValidationKey($transaction)
	{
		$base = $this->params->get('validationKey') . $transaction['EXT_TRANS_ID'] . $transaction['AMT'];
		return base64_encode(md5($base, true));
	}
}
